<?php
class Resellers extends MX_Controller 
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "client" && $this->session->userdata("user_id") != ""){
			redirect(base_url()."admin/login");
		}
	}
	public function request(){
		$data["reseller_requestlist"] = $this->db->query("select *,concat(c.fname,' ',c.lname) as full_name  from reseller_request rr inner join client c on c.client_id = rr.user_id where rr.status ='disapprove'  ")->result();
		$data["page"] 	  = "resellers"; 
		$this->load->view("admin/resellers/request",$data);		
	}
	public function assign(){
		 		$this->load->library('general');
				$user_id    = $this->input->post("user_id");
				$userscount = $this->input->post("userscount");
				$ftp_space_limit = $this->input->post("ftp_space_limit");
				$ftp_unit = $this->input->post("ftp_unit");
				$sql_space_limit = $this->input->post("sql_space_limit");
				$db_unit = $this->input->post("db_unit");
				$get_storage = $this->db->get_where("client_storage",array("user_id" => $user_id,"mode" => "reseller"));
				$ftp_space_bytes = $this->general->byteconvert($ftp_space_limit,$ftp_unit);
                $db_space_bytes = $this->general->byteconvert($sql_space_limit,$db_unit);
				if($get_storage->num_rows() == 0){
					$stor_array = array(
							    "ftp_storage" =>  $ftp_space_bytes,
							    "db_storage" =>  $db_space_bytes,
							    "user_id" => $user_id,
							    "mode" => "reseller",
							    "added_date" => date("Y-m-d"),
							    "users" => $userscount
					);
					if($this->db->insert("client_storage",$stor_array)){
							$this->db->where("user_id",$user_id);
							$this->db->update("reseller_request",["status" => 'approve']);
							$this->db->where("client_id",$user_id);
							if($this->db->update("client",["role_id" => 3])){
									$this->db->insert("reseller_setting",["reseller_id" => $user_id]);
								echo json_encode(array("status" => "success","msg" => $this->lang->line('assigned_reseller_msg')));
							}else{
								echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));	
							}
					}else{
						echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
					}
				} 
	}
}