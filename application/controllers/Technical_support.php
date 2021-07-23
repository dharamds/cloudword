<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Technical_support extends CI_Controller {
	public function index()
	{
		$data["page_data"] = $this->db->get_where("page_template",array("page_code" => "TECHNICAL_SUPPORT"))->row();	
		$this->load->view("technical_support",$data);
	}
	
}
