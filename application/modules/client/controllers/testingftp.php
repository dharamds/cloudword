<?php
class Testingftp extends MX_Controller 
{
    

    function __construct()
    {
        parent::__construct();
       
    }
    

    public function index(){
        
        //echo 'hello';

        $rtfolderremote = '';

        $config['hostname'] = 'w0148643.kasserver.com';
        $config['username'] = 'f01387ac';
        $config['password'] = 'FvZsVC6f9yksVGN5';
        //$config['port'] = $projectftp->port_no;
        $config['passive'] = TRUE;
        $config['debug'] = FALSE;


        if ($this->ftp->connect($config)) {

            echo 'here in';

            $fdatadata = $this->ftp->list_files($rtfolderremote);

            echo "<pre>";
            print_r($fdatadata);
            exit;


            if (!is_array($fdatadata)) {
                $config['passive'] = FALSE;
                $this->ftp->connect($config);
                $fdatadata = $this->ftp->list_files($rtfolderremote);
            }






        }//end if





    }


    

   
}