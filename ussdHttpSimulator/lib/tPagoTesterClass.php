<?php
include_once 'httpClientClass.php';
include_once 'configLoader.php';
Class tPagoTester {
    private $trxid;
    private $trxmap;
    private $msisdn;
    private $measureddata;
    
    function __construct($vtrxid) {
        $this->msisdn = $this->getRandomMSISDN();
        $this->trxmap = $this->getTransactionMap();
        $this->trxid = $vtrxid;
    }
    
    // Privates Functions ----------------------------------------------
    private function getTransactionMap(){
        $confMap = new configLoader('conf/trxMap.json');
        return $confMap->structure;
    }
    
    private function getRandomMSISDN(){
        $msisdnList = new configLoader('conf/msisdnInfo.json');
        return $msisdnList->structure[rand(1,6)];
    }
    
    private function parseMenu($vresult){
        $vmenu = Array();
        $dom = new DOMDocument;
        $dom->loadHTML($vresult);
        foreach ($dom->getElementsByTagName('a') as $node) {
            $vmenu[trim($node->nodeValue)] = $node->getAttribute('href');
        }
        if(empty($vmenu)){
            foreach ($dom->getElementsByTagName('form') as $node) {
                $vmenu['url'] = Array("url" => $node->getAttribute('action'));
            }
            foreach ($dom->getElementsByTagName('input') as $node) {
                $vmenu[$node->getAttribute('name')] = Array($node->getAttribute('name') => $node->getAttribute('value'));
            }
        }
        return $vmenu;
    }
    
    private function getNextFlow($vFlowID){
        $result = "";
        switch ($vFlowID){
            case "Input-Pin":
                $result = $this->msisdn['Pin'];
                break;
            case "Input-Amount":
                $result = "100";
                break;
            case "Input-TC":
                $result = $this->msisdn['CreditCard'];
                break;
            case "Input-LO":
                $result = $this->msisdn['Loan'];
                break;
            case "Input-Funding":
                $result = $this->msisdn['FundingAcct'];
                break;
            case "Input-FundingTC":
                $result = $this->msisdn['CreditCard'];
                break;        
            case "Input-Target":
                $result = $this->msisdn['TargetAcct'];
                break;
            case "Input-Telco":
                $result = $this->msisdn['Telefonica'];
                break;
            case "Input-Msisdn":
                $result = $this->msisdn['Rechmsisdn'];
                break;
            case "Input-Facturador":
                $result = $this->msisdn['Facturador'];
                break;
            case "Input-Nic":
                $result = $this->msisdn['Contrato'];
                break;
            default:
                $result = $vFlowID;
                break;
        }
        return $result;
    }
    
    // Public Fucntions ------------------------------------------------
    public function process(){
        $flowTerminate = false;
        $menu = Array();
        $trxmapPointer = 0;
        $execFlow = "";
        $newurl = "";

        //Calling Main Menu ---------------------------------------------------------
        $ussdClaro = new httpClient($this->trxmap['MainMenu'], $this->msisdn['msisdn']);
        $result = $ussdClaro->httpRequest("POST");
        error_log("---- New session for MSISDN: ".$this->msisdn['msisdn']." with TRANSACTION: ".$this->trxid.".\n", 3, 'log/ussdGWY.log');
        error_log(">>> MainMenu:\n", 3, 'log/ussdGWY.log');
        
        //Transaction Flow ----------------------------------------------------------
        while(!$flowTerminate){
            $menu = $this->parseMenu($result);
            error_log(print_r($menu, true)."\n\n", 3, 'log/ussdGWY.log');
    
            if(empty($menu)) { exit(1);}
            if(array_key_exists('url', $menu)){
                $trxmapPointer += 1;
                $execFlow = $this->getNextFlow($this->trxmap[$this->trxid][$trxmapPointer]);
                var_dump($execFlow);
                foreach($menu as $urlkey){
                    if(key($urlkey) == "url"){
                        $newurl .= $urlkey['url'];
                    }else {
                        if(key($urlkey) <> 'sessionId'){
                            $newurl .= "&".key($urlkey)."=".$execFlow;
                        }else {
                            $newurl .= "&".key($urlkey)."=".$urlkey[key($urlkey)];
                        }
                    }
                }
                $ussdClaro->setURL(str_replace('.1.20', '.1.8', $newurl), $this->msisdn['msisdn']);
                $newurl = "";
            }else {
                $trxmapPointer += 1;
                $execFlow = $this->getNextFlow($this->trxmap[$this->trxid][$trxmapPointer]);
                echo "\n\n-$execFlow-\n\n";
                //$ussdClaro->setURL(str_replace('.1.20', '.1.8', $menu[$flow->structure[$argTrx][$flowPointer]]));
                $ussdClaro->setURL(str_replace('.1.20', '.1.8', $menu[$execFlow]));
            }
            $result = $ussdClaro->httpRequest("POST");
            if($this->trxmap[$this->trxid][$trxmapPointer] == "End") {
                $flowTerminate = true;
            }
            error_log(">>> ".$this->trxmap[$this->trxid][$trxmapPointer]."\n", 3, 'log/ussdGWY.log');
        }
        return $flowTerminate;
    }
}
?>