<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Remotecall extends CI_Controller {
	public function index()
	{
            $config['hostname'] = 'devdemo.pro';
            $config['username'] = 'cloud_world';
            $config['password'] = 'Wk3bVd';
            $config['port'] = 21;
            $config['passive'] = FALSE;
            $config['debug'] = FALSE;
            if($this->ftp->connect($config)) {
                    	echo "<pre>";
                        print_r($this->ftp->raw_files("/"));
            }
	}
}
