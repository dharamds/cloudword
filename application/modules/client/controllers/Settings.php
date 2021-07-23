<?php
class Settings extends MX_Controller 
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "admin" && $this->session->userdata("user_id") != ""){
            redirect(base_url()."client/login");
		}else{
			 $this->site_setting = $this->db->get("site_setting")->result();
		}
	}
	public function index(){
		$user_id = $this->session->userdata("user_id");
		$data["currencies"] =$this->db->get("currencies")->result();
		$data["settings"] = $this->db->get_where("reseller_setting",array("reseller_id" => $user_id))->row();
		$data["page"] = "site_setting";
		$this->load->view("client/settings/list",$data);		
	}
	public function update(){
    	 $user_id = $this->session->userdata("user_id");
    	 $name_value = $this->input->post("name_value");
    	 $keydata = $this->input->post("keydata");
    	 $this->db->where("reseller_id",$user_id);
    	 if($this->db->update("reseller_setting",[$keydata => $name_value])){
    	 	echo json_encode(array("status" => "success","msg" => $this->lang->line("setting_up_msg")));
    	 }else{
    	 	echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
    	 }
    }
}