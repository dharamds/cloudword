<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

	public function index()
	{
		$this->load->view("about");
	}
	public function success()
	{
		$this->load->view("success");
	}
	public function sndmail()
	{
							sendMail("kashish@datalogysoftware.com",'USER_REGISTRATION',["user_name" => "kashish@datalogysoftware.com"]);
	}


}
