<?php
include_once "configClass.php";
include_once "httpClientClass.php";
include_once "smppClientClass.php";
class ussdRequest {
    private $menuMap;
    private $redis;
    private $sessionCache = [];
    private $msisdn;
    
    function __construct($vmsisdn){
        $this->msisdn = $vmsisdn;
        $this->menuMap = new configLoader('config/menuBuild.json');
        try {
            $this->redis = new Redis();
            $this->redis->connect('10.226.192.121', 6379);
        } catch (Exception $ex) {
            echo($ex->getTraceAsString());
        }
    }
    
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    private function printMenu($vMenuId, $vSessionid) {
        foreach ($this->menuMap->structure[$vMenuId] as $option){
            if($option['Title'] == 'Volver atras' or $option['Title'] == 'Ver mas'){
                echo "<br>";
            }
            //echo '<a href="http://10.226.192.205/mann-ussd-http-1.0.1/servlet/HttpController?menuKey='.$option['id'].'&sessionid='.$vSessionid.'">'.$option['Title'].'</a><br>';
            echo '<a href="http://localhost/ussdMenuCreator?menuKey='.$option['id'].'&sessionid='.$vSessionid.'">'.$option['Title'].'</a><br>';
        }
    }
    
    private function validateMenuKey($vMenuId, $vParentMenu, $vSessionid){
        foreach ($this->menuMap->structure[$vParentMenu] as $key => $option){
            if($option['id'] == $vMenuId){
                if($vParentMenu == 'main' or $vParentMenu == 'main2'){
                    $this->redis->set($vSessionid."_main-option",$option['Title']);
                }
                $this->redis->set($vSessionid."_action-function", $option['Func']);
                return $option['type'];
            }
        }
    }
    
    private function actionMenuKey($vSessionid){
        $vStory = $this->redis->get($vSessionid."_action-function");
        $vStoryList = new configLoader('config/storiesFiles.json');
        //var_dump($vStory);
        //var_dump($vStoryList->structure[$vStory]);
        //exit;
        foreach ($vStoryList->structure[$vStory] as $vPages){
            $smsPage = "";
            $webPage = "";
            foreach ($vPages as $vLine){
                $smsPage .= $vLine."\n";
                $webPage .=$vLine."<br>";
            }
            $this->sentSMS($smsPage);
            echo mb_convert_encoding($smsPage, "ASCII");
            //echo "<p>".$webPage."</p><br><br>";
        }
        
    }
    
    private function sentSMS($vMessage){
        //$smsEngine = new httpClient();
        $smsc =  new smppClient('localhost', '5016', 'mpayment', 'mpayment', 'Logica', 5, 1, 'tPago', 1, 1);
        //$smsEngine->setURL("http://10.226.192.121:8051/api/send-sms");
        //$vJSONRecord = json_encode(["phone"=> $this->msisdn, "partner-code"=> 2, "body"=> mb_convert_encoding($vMessage, "ASCII")]);
        //$result = $smsEngine->httpRequest('POST', ["Content-Type: application/json; charset=utf-8"], $vJSONRecord);
        $smsc->sendSM($this->msisdn, $vMessage);
        //if($result['HTTPRSP'] == 200){
        //    echo " ---- Delivered<br>";
        //}else {
        //    echo " ---- Fail Attempt<br>";
        //    echo "     +++++ Returned error: ".$result['DATA']."<br>";
        //}
        unset($vJSONRecord);
        unset($smsEngine);
        unset($result);
    }
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    public function getMenu($vSessionid, $vMenuKey = null) {
        if(!($vMenuKey == null)){
            $this->redis->set($vSessionid."_last-menu", $this->redis->get($vSessionid."_current-menu"));
            $this->redis->set($vSessionid."_current-menu", $vMenuKey);
            $vParentMenu = $this->redis->get($vSessionid."_last-menu");
        }
        $vMenuId = $this->redis->get($vSessionid."_current-menu");
        
        echo "<HTML>";
        echo "<HEAD><TITLE></TITLE></HEAD>";
        echo "<BODY>";
        switch ($vMenuId){
            case 'main':
                echo "Bienvenido a<br>Aprendiendo con 512<br>Selecciona una opcion:<br>";
                $this->printMenu($vMenuId, $vSessionid);
                break;
            case 'Si':
                $this->actionMenuKey($vSessionid);
                break;
            default:
                switch ($this->validateMenuKey($vMenuId, $vParentMenu, $vSessionid)){
                    case 'M':
                        echo $this->redis->get($vSessionid."_main-option")."<br><br>Seleccione una opcion:<br>";
                        $this->printMenu($vMenuId, $vSessionid);
                        break;
                    default:
                        echo "Opcion no configurada.";
                        break;
                }
                break;
        }
        echo "</BODY>";
        echo "</HTML>";
    }
    
    public function createSession($vMsisdn){
        $session = date('Ymdhis').$vMsisdn;
        $this->redis->set($session."_current-menu", "main");
        return $session;
    }
}
