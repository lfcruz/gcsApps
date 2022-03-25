<?php
class dbRequest {
    private $connectorStructure;
    private $queryStructure;
    private $connectorString;
    private $dbConnector;
    private $qryCounter = 0;
    private $pgQrySession;
    
    function __construct($vDBType, $vDBIp, $vDBPort, $vDBName, $vDBUser, $vDBPassword){
        $this->connectorStructure['dbType'] = $vDBType;
        $this->connectorStructure['dbIP'] = $vDBIp;
        $this->connectorStructure['dbPort'] = $vDBPort;
        $this->connectorStructure['dbName'] = $vDBName;
        $this->connectorStructure['dbUser'] = $vDBUser;
        $this->connectorStructure['dbPassword'] = $vDBPassword;
        $this->pgQrySession = uniqid(rand(1000, 9999));
        $result = null;
        switch ($this->connectorStructure['dbType']) {
            case "ORA":
                $this->connectorString = '(DESCRIPTION = (CONNECT_TIMEOUT=5) (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST ='.$this->connectorStructure['dbIP'].')(PORT = '.$this->connectorStructure['dbPort'].')))(CONNECT_DATA=(SID= '.$this->connectorStructure['dbName'].')))';
                $this->dbConnector = oci_connect($this->connectorStructure['dbUser'], $this->connectorStructure['dbPassword'], $this->connectorString);
                if (!$this->dbConnector){
                    echo 'Failed connection.......';
                }else {
                    $this->setQuery("select * from dual",Array());
                    $result = $this->execQry();
                }
                break;
            case "PGS":
                $this->connectorString = "host=".$this->connectorStructure['dbIP']." port=".$this->connectorStructure['dbPort']." dbname=".$this->connectorStructure['dbName']." user=".$this->connectorStructure['dbUser']." password=".$this->connectorStructure['dbPassword'];
                $this->dbConnector = pg_connect($this->connectorString, PGSQL_CONNECT_FORCE_NEW);
                if (!$this->dbConnector){
                    echo 'Failed connection.......';
                }else {
                    $this->setQuery("select datname from pg_database", Array());
                    $result = $this->execQry();
                }
                break;
            case "MSQL":
                $this->dbConnector = mssql_connect($this->connectorStructure['dbIP'].":".$this->connectorStructure['dbPort']."\MSSQLSERVER", $this->connectorStructure['dbUser'], $this->connectorStructure['dbPassword']);
                if (!$this->dbConnector){
                    echo 'Failed connection.......';
                }else {
                    $result = mssql_select_db($this->connectorStructure['dbName'], $this->dbConnector);
                }
                break;
            default:
                break;
        }
        return $result;
    }
    
    function __destruct() {
        switch ($this->connectorStructure['dbType']) {
            case "ORA":
                oci_close($this->dbConnector);
                break;
            case "PGS":
                pg_close($this->dbConnector);
                break;
            case "MSQL":
                mssql_close($this->dbConnector);
                break;
            default:
                break;
        }
    }
    
    private function oraExec(){
        $oraQuery = oci_parse($this->dbConnector,$this->queryStructure['dbQuery']);
        oci_execute($oraQuery);
        $rowsnum = oci_fetch_all($oraQuery,$recordFetched);
        if ($rowsnum == 0) {
            $recordFetched = Array();
        }
        return $recordFetched;
    }
    
    private function pgExec(){
        pg_prepare($this->dbConnector, $this->queryStructure['qryName'], $this->queryStructure['dbQuery']);
        $queryResult = pg_execute($this->dbConnector,$this->queryStructure['qryName'],$this->queryStructure['qryParameters']);
        if (!$queryResult){
            $recordFetched = Array();
        }else {
            if(substr($this->queryStructure['dbQuery'], 0, 6) <> 'select') {
                $recordFetched = true;
            }else {
                $recordFetched = pg_fetch_all($queryResult);
            }
        }
        pg_free_result($queryResult);
        return $recordFetched;
    }
    
    private function msqlExec(){
        $msqQuery = mssql_query($this->queryStructure['dbQuery'], $this->dbConnector);
        if (!$msqQuery or mssql_rows_affected($this->dbConnector) == 0) {
            $recordFetched = Array();
        }else {
            $recordFetched = Array();
            while($rowFetched = mssql_fetch_array($msqQuery,MSSQL_ASSOC)){
                array_push($recordFetched, $rowFetched);
            }
        }
        mssql_free_result($msqQuery);
        return $recordFetched;
    }
    
    public function setQuery($query, $parameters){
        $this->qryCounter += 1;
        $this->queryStructure = Array("dbQuery" => $query,
                                      "qryParameters" => $parameters,
                                      "qryName" => "qry_".$this->pgQrySession.$this->qryCounter);
        
    }
    
    public function execQry(){
        $recordString = false;
        switch ($this->connectorStructure['dbType']) {
            case "ORA":
                $recordString = $this->oraExec();
                break;
            case "PGS":
                $recordString = $this->pgExec();
                break;
            case "MSQL":
                $recordString = $this->msqlExec();
                break;
            default:
                $recordString = false;
                break;
        }
        return $recordString;
    }
    
    public function startTransactions(){
        $this->setQuery('BEGIN', Array());
        $this->execQry();
    }
    
    public function commitTransactions(){
        $this->setQuery('COMMIT', Array());
        $this->execQry();
    }
    
    public function rollbacTransactions(){
        $this->setQuery('ROLLBACK', Array());
        $this->execQry();
    }
}
?>
