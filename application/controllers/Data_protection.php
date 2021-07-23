<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Data_protection extends CI_Controller {
	public function index()
	{
		$data["page_data"] = $this->db->get_where("page_template",array("page_code" => "DATA_PROTECTION"))->row();	
		$this->load->view("data_protection",$data);
	}
	
}
