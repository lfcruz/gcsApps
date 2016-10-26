<?php
class dbRequest {
    private $connectorStructure;
    private $queryStructure;
    
    function __construct($vDBType, $vDBIp, $vDBPort, $vDBName, $vDBUser, $vDBPassword){
        $this->connectorStructure['dbType'] = $vDBType;
        $this->connectorStructure['dbIP'] = $vDBIp;
        $this->connectorStructure['dbPort'] = $vDBPort;
        $this->connectorStructure['dbName'] = $vDBName;
        $this->connectorStructure['dbUser'] = $vDBUser;
        $this->connectorStructure['dbPassword'] = $vDBPassword;
        $result = null;
        switch ($this->connectorStructure['dbType']) {
            case "ORA":
                $this->setQuery("select * from dual",Array());
                $result = $this->execQry();
                break;
            case "PGS":
                $this->setQuery("select datname from pg_database", Array());
                $result = $this->execQry();
                break;
            case "MSQL":
                //$this->setQuery("select @@VERSION", Array());
                $result = true;
                break;
            default:
                break;
        }
        return $result;
    }
    
    private function oraExec(){
        $connectorString = '(DESCRIPTION = (CONNECT_TIMEOUT=5) (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST ='.$this->connectorStructure['dbIP'].')(PORT = '.$this->connectorStructure['dbPort'].')))(CONNECT_DATA=(SID= '.$this->connectorStructure['dbName'].')))';
        $dbConnector = oci_connect($this->connectorStructure['dbUser'], $this->connectorStructure['dbPassword'], $connectorString);
        if (!$dbConnector){
            echo 'Failed connection.......';
        }else {
            $oraQuery = oci_parse($dbConnector,$this->queryStructure['dbQuery']);
        }
        oci_execute($oraQuery);
        $rowsnum = oci_fetch_all($oraQuery,$recordFetched);
        if ($rowsnum == 0) {
            $recordFetched = Array();
        }else {
            if(!(substr($this->queryStructure['dbQuery'], 0, 6) == "select")){
                $oraQuery = oci_parse($dbConnector,'commit');
                oci_execute($oraQuery);
                $rowsnum = oci_fetch_all($oraQuery,$recordCommit);
                $recordFetched = true;
            }
        }
        oci_close($dbConnector);
        return $recordFetched;
    }
    
    private function pgExec(){
        $connectorString = "host=".$this->connectorStructure['dbIP']." port=".$this->connectorStructure['dbPort']." dbname=".$this->connectorStructure['dbName']." user=".$this->connectorStructure['dbUser']." password=".$this->connectorStructure['dbPassword'];
        $dbConnector = pg_connect($connectorString);
        if (!$dbConnector){
            echo 'Failed connection.......';
        }else {
            pg_prepare($dbConnector,$this->queryStructure['qryName'],$this->queryStructure['dbQuery']);
        }
        $queryResult = pg_execute($dbConnector,$this->queryStructure['qryName'],$this->queryStructure['qryParameters']);
        if (!$queryResult){
            $recordFetched = Array();
        }else {
            $recordFetched = pg_fetch_all($queryResult);
            if(!(substr($this->queryStructure['dbQuery'], 0, 6) == "select")){
                $recordFetched = true;
                pg_prepare($dbConnector,'commit','commit');
                pg_exec($dbConnector, 'commit');
            }
        }
        pg_close($dbConnector);
        return $recordFetched;
    }
    
    private function msqlExec(){
        $dbConnector = mssql_connect($this->connectorStructure['dbIP'].":".$this->connectorStructure['dbPort']."\MSSQLSERVER", $this->connectorStructure['dbUser'], $this->connectorStructure['dbPassword']);
        if (!$dbConnector){
            echo 'Failed connection.......';
            exit(1);
        }else {
            mssql_select_db($this->connectorStructure['dbName'], $dbConnector);
            $msqQuery = mssql_query($this->queryStructure['dbQuery'], $dbConnector);
        }
        if (!$msqQuery or mssql_rows_affected($dbConnector) == 0) {
            $recordFetched = Array();
        }else {
            $recordFetched = Array();
            while($rowFetched = mssql_fetch_array($msqQuery,MSSQL_ASSOC)){
                array_push($recordFetched, $rowFetched);
            }
        }
        mssql_free_result($msqQuery);
        mssql_close($dbConnector);
        return $recordFetched;
    }
    
    public function setQuery($query, $parameters){
        $this->queryStructure = Array("dbQuery" => $query,
                                      "qryParameters" => $parameters,
                                      "qryName" => "qry_".rand(100000,999999));
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
}
?>
