<?php
class Contacts extends MX_Controller 
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "client" && $this->session->userdata("user_id") != ""){
			redirect(base_url()."admin/login");
		}
	}

	public function index(){

		$data["contactlist"] = $this->db->get("contacts")->result();
		$data["page"] 	  = "contacts"; 
		$this->load->view("admin/contacts/list",$data);
				
	}

	public function reply($param1 = '')
	{

		if ($param1 == 'save') {

			$client_id = $this->session->userdata("user_id");
			$message = $this->input->post("message");
			$contact_id = $this->input->post("contact_id");

			$cont_inq = $this->db->where("contact_id",$contact_id)->get('contacts')->row();

			//Send reply mail
			$this->load->helper('basic_helper');

	        sendMail($cont_inq->email, 'CONTACT_US_REPLY', [
						'user_name' => $cont_inq->name,
						'reply_data' => $message,
						'user_message' => $cont_inq->message,
						]);


	        //save to db
			$data["client_id"] = $client_id;
			$data["message"]  = $message;
			$data["contact_id"]   = $contact_id;

			if ($this->db->insert("contact_reply",$data)) {
				echo json_encode(array("status" => "success","msg" => $this->lang->line("reply_success")));
			} else {
				echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
			}
			
		} else {

			$contactid= base64_decode($param1);

			$data_query = $this->db->select('*, t1.message as reply_msg, t2.message as msg')
				     ->from('contact_reply as t1')
				     ->where('t1.contact_id', $contactid)
				     ->join('contacts as t2', 't1.contact_id = t2.contact_id', 'LEFT')
				     ->get();


			$data["page"] 	  = "contacts"; 
			$data["contactdata"] 	  = $data_query->result(); 
			$data["contact_id"] 	  = $contactid; 

			$this->load->view("admin/contacts/contact_reply",$data);
		}
	}

	
	public function delete($contact_id){
		
		$this->db->where("contact_id",$contact_id);
		if($this->db->delete("contacts")){

			$this->db->where("contact_id",$contact_id);
			$this->db->delete("contact_reply");
			
			echo json_encode(array("status" => "success","msg" => $this->lang->line("del_success")));
		}else{
			echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
		}		    	
	}
	

}