<?php

class Connection extends CI_Model 
{
    function __construct() {
        parent::__construct(); 
        
    }
    
    function check($hostname, $username, $password, $port_no, $key_filepath=NULL)
    {

        $ftpstatus = 0;
        if($key_filepath){
			include(APPPATH.'third_party/phpseclib/Crypt/RSA.php');
					 $rsa = new Crypt_RSA();
					$rsa->loadKey(file_get_contents($key_filepath));

					$ssh = new Net_SSH2($hostname);
					if (!$ssh->login($username, $password, $rsa)) {
						$ftpstatus = 0;
					}else{
						$ftpstatus = 1;
					}
					 
			}else{
        if($port_no == 21) {
            $callingftp = $this->ftp;
        }elseif($port_no == 22) {
            $callingftp = $this->ftpbackup;
        }
        $config['hostname'] = $hostname;
        $config['username'] = $username;
        $config['password'] = $password;
        $config['port'] = $port_no;
        $config['passive'] = TRUE;
        $config['debug'] = FALSE;



        if ($callingftp->connect($config)) {
            $ftpstatus = 1;
        } else {
            $config['passive'] = FALSE;
            if ($callingftp->connect($config)) {
                $ftpstatus = 1;
            } else {
                $ftpstatus = 0;
            }
        }
        return (bool)$ftpstatus;
	}
}
   
}
