<?php
include_once 'dbClass.php';
include_once 'configClass.php';
include_once 'constants.php';
include_once 'LogClass.php';
class gdmLoader {
    private $dbConnector;
    private $conf;
    private $packager;
    private $fileHandler;
    private $fileID;
    private $billerID;
    private $billerIFormat;
    private $filename;
    private $log;
    
    function __construct($datafile) {
        $this->log = new Logger();
        //$this->conf['application'] = new configLoader('../config/application.json');
        $this->filename = $datafile;
        try {
            $this->conf['database'] = new configLoader('../config/db.json');
            $this->packager = new configLoader('../config/incoming_packager.json');
            $this->dbConnector = new dbRequest($this->conf['database']->structure['dbtype'],
                                           $this->conf['database']->structure['dbhost'],
                                           $this->conf['database']->structure['dbport'],
                                           $this->conf['database']->structure['dbname'],
                                           $this->conf['database']->structure['dbuser'],
                                           $this->conf['database']->structure['dbpass']);
            $this->fileValidation($this->getBillerFromFile());
        } catch (Exception $e) {
            $this->log->writeLog(LOGERROR, $e->getTraceAsString());
        }
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function getBillerFromFile(){
        $this->fileHandler = fopen($this->filename, 'r');
        $fileString = fgets($this->fileHandler);
        //fclose($this->fileHandler);
        if (substr($fileString, 0, 2) == "01") {
            $vBillerCode = substr($fileString, 2, 5);
            $this->dbConnector->setQuery("select id,iformat from t_billers where externalid = $1", Array(trim($vBillerCode)));
            $this->billerID = $this->dbConnector->execQry();
            $this->billerIFormat = (string)$this->billerID[0]['iformat'];
            $this->billerID = (int)$this->billerID[0]['id'];
            var_dump($vBillerCode);
            var_dump($this->billerID);
            var_dump($this->billerIFormat);
            
        }else {
            fclose($this->fileHandler);
            $this->log->writeLog(LOGERROR, "Biller name from file is invalid, please verify file integrity and try again.");
            exit(1);
        }
        return $fileString;
    }
    
    private function fileValidation($fileString){
        $recordsCount = 0;
        $recordsFailed = 0;
        $recordsAmounts = (float)0;
        //$this->fileHandler = fopen($this->filename, 'r');
        //$fileString = fgets($this->fileHandler);
        //echo "$fileString\n";
            //if (substr($fileString, 0, 2) == "01" and strlen($fileString) == 73) {
                $vHeader = $this->parseRecord($fileString, substr($fileString, 0, 2));
                //$this->dbConnector->setQuery("select id,iformat from t_billers where externalid = $1", Array(trim($vHeader['companyname'])));
                //$this->billerID = $this->dbConnector->execQry();
                //var_dump($this->billerID);
                //$this->billerIFormat = (string)$this->billerID[0]['iformat'];
                //$this->billerID = (int)$this->billerID[0]['id'];
                while($this->billerID and !feof($this->fileHandler)){
                    $fileString = fgets($this->fileHandler);
                    if(strlen($fileString) > 200 /*259*/ and substr($fileString, 0, 2) == "02"){
                         $vRecord = $this->parseRecord($fileString, substr($fileString, 0, 2));
                         $recordsCount += 1;
                         $recordsAmounts += (float)$vRecord['amount'];
                     }else {
                         $recordsFailed += 1;
                     }
                }
            //}
        fclose($this->fileHandler);
        echo "###########   Header Parsed   ##############\n";
        echo "Partner: ".$vHeader['companyname']."\n";
        echo "Total records: ".(int)$vHeader['recordscount']."\n";
        echo "Total amount: ".(float)$vHeader['totalamount']."\n";
        echo "########### Validation Result ##############\n";
        echo "Valid records: $recordsCount\n";
        echo "Valid amount: $recordsAmounts\n";
        echo "Bad records: $recordsFailed\n";
        echo "############################################\n\n\n";
        
        //var_dump($this->billerIFormat);
        //var_dump($this->billerID);
    }
    
    private function parseRecord($vRecord, $vType){
        $resultRecord = Array();
        switch ($vType){
            case FILE_HEADER:
                foreach ($this->packager->structure[$this->billerIFormat]['header']['structure'] as $vPackage) {
                    $resultRecord[$vPackage['name']] = utf8_encode(trim(substr($vRecord, $vPackage['position'], $vPackage['length'])));
                }
                break;
            case FILE_RECORD:
                foreach ($this->packager->structure[$this->billerIFormat]['body']['structure'] as $vPackage) {
                    $resultRecord[$vPackage['name']] = utf8_encode(trim(substr($vRecord, $vPackage['position'], $vPackage['length'])));
                }
                break;
            default:
                break;
        }
        return $resultRecord;
    }
    
    private function storeFileInfo($vRecord){
        $this->dbConnector->setQuery("select nextval('seq_general')", Array());
        $vid = $this->dbConnector->execQry();
        if($vid){
            $this->dbConnector->setQuery("insert into t_files (id, filename, loadeddate, records, totalamount, generationdate) "
                    ."values ($1,$2,current_timestamp,$3,$4,$5)", Array((int)$vid[0]['nextval'],
                        $this->filename,
                        (int)$vRecord['recordscount'], 
                        (float)$vRecord['totalamount'], 
                        $vRecord['processdatetime']));
            $this->dbConnector->execQry();
            $this->fileID = (int)$vid[0]['nextval'];
        }
        
    }
    
    private function storeRecordInfo($fileID, $Record){
        $this->dbConnector->setQuery('update t_clients set status = $1 where nic = $2 and id_billers = $3 and status = $4', Array("X", $Record['nic'], (int)$this->billerID,"P"));
        $this->dbConnector->execQry();
        $this->dbConnector->setQuery('insert into t_clients (id,nic,id_billers,clientname,amount,status,id_files,billcutdate)'
                                            . 'values (default,$1,$2,$3,$4,default,$5,current_timestamp)', Array($Record['nic'],
                                                (int)$this->billerID, $Record['clientname'], (float)$Record['amount'],
                                                (int)$fileID));
        $this->dbConnector->execQry();
        return true;
    }
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function process(){
        $trxProcessedRecords = 1;
        $trxCutCounter = 1;
        echo "Start : ".date("Y-m-d H:i:s")."\n";
        try {
            $this->fileHandler = fopen($this->filename, 'r');
            
            $this->dbConnector->startTransactions();
            do {
                $filestring = fgets($this->fileHandler);
                $RecordType = substr($filestring, 0, 2);
                $Record = $this->parseRecord($filestring, $RecordType);
                switch ($RecordType){
                    case FILE_HEADER:
                        $this->storeFileInfo($Record);
                        break;
                    case FILE_RECORD:
                        $this->storeRecordInfo($this->fileID, $Record);
                        if($trxCutCounter == 1000){
                            $this->dbConnector->commitTransactions();
                            $this->dbConnector->startTransactions();
                            $trxCutCounter = 1;
                            echo "           Cut[$trxProcessedRecords] : ".date("Y-m-d H:i:s")."\n";
                        }else {
                            $trxCutCounter += 1;
                            $trxProcessedRecords += 1;
                        }
                        break;
                    default:
                        var_dump($filestring);
                        //return false;
                }
            }while(!feof($this->fileHandler));
            $this->dbConnector->commitTransactions();
            fclose($this->fileHandler);
        } catch (Exception $e){
            $this->dbConnector->rollbacTransactions();
            $this->log->writeLog(LOGERROR, $e->getTraceAsString());
        }
        echo "Finish : ".date("Y-m-d H:i:s")."\n";
        return true;
    }
    
 //End of the Class   
 }
