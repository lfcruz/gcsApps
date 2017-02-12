<?php

class sftpEndPoint {
    private $hostname;
    private $user;
    private $password;
    public $directory;
    public $filename;
    private $server;
    private $service;
    
    function __construct($vhostname, $vuser, $vpassword) {
        $vresult = true;
        $this->hostname = $vhostname;
        $this->user = $vuser;
        $this->password = $vpassword;
        $this->server = ssh2_connect($this->hostname, 22);
        if(!$this->server){
            throw new Exception("Could not connect to $this->hostname.");
        } elseif (!ssh2_auth_password($this->server, $this->user, $this->password)) {
            throw new Exception("Could not authenticate with username or password.");
        }else {
            $this->service = ssh2_sftp($this->server);
            if (!$this->service) {
                throw new Exception("Could not initialize SFTP subsystem.");
            } else {
                $vresult = true;
            }
        }
        return $vresult;
    }
    
    public function setDirectory($vdirectory) {
        $this->directory = $vdirectory;
        return;
    }
    
    public function setFileName($vfilename) {
        $this->filename = $vfilename;
    }
    
    public function getFile() {
        //$fileData = false;
        /*$fileSrc = fopen("ssh2.sftp://".$this->user.":".$this->password."@".$this->hostname.":22".ssh2_sftp_realpath($this->service,".")."/".$this->filename, 'r');
        if (!$fileSrc) {
            throw new Exception("Could not open file: $this->filename");
        }else {
            echo filesize("ssh2.sftp://".$this->user.":".$this->password."@".$this->hostname.":22".ssh2_sftp_realpath($this->service,".")."/".$this->filename);
            $fileData = fread($fileSrc, filesize("ssh2.sftp://".$this->user.":".$this->password."@".$this->hostname.":22".ssh2_sftp_realpath($this->service,".")."/".$this->filename));
        }
        fclose($fileSrc);*/
        $fileData = file_get_contents("ssh2.sftp://".$this->user.":".$this->password."@".$this->hostname.":22".ssh2_sftp_realpath($this->service,".")."/".$this->filename);
        return $fileData;
    }
    
    public function putFile($vfileStream) {
        /*$vresult = false;
        $fileSrc = fopen("ssh2.sftp://".$this->user.":".$this->password."@".$this->hostname.":22".ssh2_sftp_realpath($this->service,".")."/".$this->filename, 'w');
        if (!$fileSrc) {
            throw new Exception("Could not open file: $this->filename");
        }elseif(fwrite($fileSrc, $vfileStream) === false) {
            throw new Exception("Could not send data.");
        } else {
            $vresult = true;
        }
        fclose($fileSrc);*/
        $vresult = file_put_contents("ssh2.sftp://".$this->user.":".$this->password."@".$this->hostname.":22".ssh2_sftp_realpath($this->service,".")."/".$this->filename, $vfileStream);
        return $vresult;
    }
}
?>
