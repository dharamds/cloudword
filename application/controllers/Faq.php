<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Faq extends CI_Controller {
	public function index()
	{
		$data["page_data"] = $this->db->get_where("page_template",array("page_code" => "FAQ"))->row();	
		$this->load->view("faq",$data);
	}
	
}
