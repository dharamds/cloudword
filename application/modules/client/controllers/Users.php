<?php
class Users extends MX_Controller 
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata("role_type") == "admin" && $this->session->userdata("role_type") != "client"){
			redirect(base_url()."client/login");
		}else{
			 $this->site_setting = $this->db->get("site_setting")->result();
		}
	}
	public function index(){
		
		$user_id = $this->session->userdata("user_id");
		$data["userlist"] = $this->db->get_where("client",array("parent_id" => $user_id))->result();
		$data["page"] 	  = "users"; 
		$this->load->view("client/users/list",$data);		
	}
	public function create(){
		$user_id = $this->session->userdata("user_id");
		$data["page"] 	  = "users";
		$data["planlist"] = $this->db->get_where("plans",array("active" => 1,"is_deleted !=" => 1,"user_id" => $user_id ))->result();
		$data["roles"] 	  = $this->db->query("select * from roles where role_id IN(2)")->result(); 
		$this->load->view("client/users/create",$data);	
	}
	public function valid_email($str) {
		return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}
	public function save($type){
		$user_id = $this->session->userdata("user_id");
		$error_data = array();
		$fname = !empty(trim($this->input->post("f_name")))  ? $this->input->post("f_name")  : $error_data["f_name_msg"]  = $this->lang->line("f_name_blank");
		$lname = !empty(trim($this->input->post("l_name")))  ? $this->input->post("l_name") :  $error_data["l_name_msg"] = $this->lang->line("l_name_blank");
		$phone = !empty(trim($this->input->post("phone")))  ? $this->input->post("phone") :  $error_data["phone_msg"] = $this->lang->line("phone_blank");
		$email = !empty(trim($this->input->post("email")))  ? $this->input->post("email") :  $error_data["email_msg"] = $this->lang->line("email_blank");
		$address = !empty(trim($this->input->post("address")))  ? $this->input->post("address") :  $error_data["address_msg"] = $this->lang->line("address_blank");
		$city = !empty(trim($this->input->post("city")))  ? $this->input->post("city") :  $error_data["city_msg"] = $this->lang->line("city_blank");
		$zipcode = !empty(trim($this->input->post("zipcode")))  ? $this->input->post("zipcode") :  $error_data["zipcode_msg"] = $this->lang->line("zipcode_blank");
		if (!array_key_exists("email_msg",$error_data))
  		{
  			if(!$this->valid_email($email)){
			   $error_data["email_msg"] = $this->lang->line("email_not_proper");
			}
  		}


		$check_company = $this->input->post("check_company");
		$check_plan = $this->input->post("check_plan");
		$user_role = !empty($this->input->post("user_role"))  ? $this->input->post("user_role") :  $error_data["user_role_msg"] = $this->lang->line("user_role_blank");
		if(isset($check_company)){
			$company_name = !empty(trim($this->input->post("company_name"))) ? $this->input->post("company_name") : $error_data["company_name_msg"] = $this->lang->line("company_name_blank");
			$company_vat_number = $this->input->post("company_vat_number");
			$company_street = !empty(trim($this->input->post("company_street"))) ? $this->input->post("company_street") : $error_data["company_street_msg"] = $this->lang->line("company_street_blank");
			$company_town = !empty(trim($this->input->post("company_town"))) ? $this->input->post("company_town") : $error_data["company_town_msg"] = $this->lang->line("company_town_blank");
			$company_zipcode = !empty(trim($this->input->post("company_zipcode"))) ? $this->input->post("company_zipcode") : $error_data["company_zipcode_msg"] = $this->lang->line("company_zipcode_blank");
			$company_country = !empty(trim($this->input->post("company_country"))) ? $this->input->post("company_country") : $error_data["company_country_msg"] = $this->lang->line("company_country_blank");
			$company_responsible_person = !empty(trim($this->input->post("company_responsible_person"))) ? $this->input->post("company_responsible_person") : $error_data["company_responsible_person_msg"] = $this->lang->line("company_responsible_person_blank");
			$is_company = "yes"; 
			 $company_logo_filename = "";
			 $config['upload_path']   = './uploads/company_logo/'; 
             $config['allowed_types'] = 'gif|jpg|jpeg|png';
             if($_FILES["company_logo"]['name'] !=""){
                $new_name = time().$_FILES["company_logo"]['name'];
                $config['file_name'] = $new_name;
                $this->load->library('upload', $config);
                $this->upload->do_upload('company_logo');
                if ( ! $this->upload->do_upload('company_logo')){
                	//$error_data["company_logo_msg"] = $this->upload->display_errors();
	                $error_data["company_logo_msg"] = $this->lang->line("The filetype you are attempting to upload is not allowed");
                }else{
                	$image = $this->upload->data();
                	$srcpath =  '/uploads/company_logo/'.$image['file_name'];
                	$despath =  '/uploads/company_logo/thumbnail/';
                	$this->general->resizeImage($srcpath,$despath);
                	$company_logo_filename = $image["file_name"];
                }  
             }
		}else{
			$company_name = "";
			$company_vat_number = "";
			$company_street = "";
			$company_town = "";
			$company_zipcode = "";
			$company_country = "";
			$company_responsible_person ="";
			$company_logo_filename = "";
			$is_company = "no"; 
		}
		$getreseller_storage = $this->db->query("select cs.ftp_storage,cs.db_storage,cs.users from client_storage cs where cs.user_id = ".$user_id." AND cs.mode = 'reseller'")->row();
		$getusers = $this->db->get_where("client",array("parent_id" => $user_id))->result();
		if(count($getusers) >= $getreseller_storage->users){
			$error_data["user_count_msg"] = $this->lang->line("maximum_user_limit_msg");
		}

		if(isset($check_plan)){
			$plan_id = !empty($this->input->post("plan_id")) ? $this->input->post("plan_id") : $error_data["plan_id_msg"] = $this->lang->line("no_plan_selected");
			if(!empty($this->input->post("plan_id"))){
				$getplan_details = $this->db->get_where("plans",array("id" =>$plan_id))->row();
				if(count($getusers) > 0){
				foreach($getusers as $usr) {
					$usrids[] = $usr->client_id;
				}
				$get_storage = $this->db->query("select sum(cs.ftp_storage) as ftp_storage,sum(cs.db_storage) as db_storage from client_storage cs  where cs.user_id IN(".implode(",",$usrids).") AND cs.mode = 'client' ")->row();
				$avalable_storage_ftp = $getreseller_storage->ftp_storage - $get_storage->ftp_storage;
				$avalable_storage_db  = $getreseller_storage->db_storage - $get_storage->db_storage;
				if($getplan_details->ftp_space_bytes > $avalable_storage_ftp){
					$error_data["ftp_space_not_available_msg"] = $this->lang->line("Required FTP storage Space")."(".$this->general->formatBytes($getplan_details->ftp_space_bytes)." ) ".$this->lang->line("is greater than available storage space")." (".$this->general->formatBytes($avalable_storage_ftp)." )";
				}
				if($getplan_details->db_space_bytes > $avalable_storage_db){
					$error_data["db_space_not_available_msg"] = $this->lang->line("Required FTP storage Space")."(".$this->general->formatBytes($getplan_details->db_space_bytes)." ) ".$this->lang->line("is greater than available storage space")." (".$this->general->formatBytes($avalable_storage_db)." )";
				}
			}
			}
		}
		if(empty($error_data["email_msg"]) && empty($error_data["user_role_msg"])){
			$checkemail 	 = $this->db->get_where("client",array("role_id" => $user_role,"username" => $email))->num_rows();
			$error_dataemail = 	$checkemail > 0 ? $error_data["email_msg"] = $this->lang->line("email_exist") : '' ;
		}
		$data = array(
			"username" =>$email, 
			"fname" => $fname,
			"lname" => $lname,
			"phone" => $phone,
			"email" => $email,
			"address" => $address,
			"city" => $city,
			"zipcode" => $zipcode,
			"company_name" => $company_name,
			"company_vat_number" => $company_vat_number,
			"company_street" => $company_street,
			"company_town" => $company_town,
			"company_zipcode" => $company_zipcode,
			"company_country" => $company_country,
			"company_responsible_person" => $company_responsible_person,
			"company_logo" => $company_logo_filename,
			"is_company" => $is_company,
			"parent_id" => $user_id,
			"role_id" => 4									
		);
		if(count($error_data) >  0){
			$msg = 'testing';
			echo json_encode(array("status" => "failed","msg" => $msg,"error_data" => $error_data));
		}else{
			$getreseller = $this->db->get_where("client",array("client_id" => $user_id))->row();
			if($type == "edit"){
				$client_id = $this->input->post("euser_id"); 
				$this->db->where("client_id",$client_id);
				$this->db->update("client",$data);
				$msg = $this->lang->line("up_user_msg");
			}else{
				$data["role_id"]   = $user_role;
				$data["parent_id"]   = $user_id;
				$data["added_date"] = date("Y-m-d");
				$this->db->insert("client",$data);
				$usrid = $this->db->insert_id();
				$this->send_create_password_mail($usrid);
				if(isset($check_plan)){
					 $data = array(
                            "payer_email" =>$getreseller->email, 
                            "payer_id" =>$user_id, 
                            "payer_status" =>"success", 
                            "first_name" =>$getreseller->fname, 
                            "last_name" => $getreseller->lname, 
                            "address_name" =>  $getreseller->company_street, 
                            "address_street" =>$getreseller->address, 
                            "address_city" =>  $getreseller->company_town, 
                            "address_country_code" =>  $getreseller->company_country, 
                            "address_zip" =>   $getreseller->company_zipcode, 
                            "residence_country" => $getreseller->country, 
                            "txn_id" =>$user_id, 
                            "mc_currency" => $this->site_setting[14]->name_value,  
                            "payment_type" =>  "offline",
                            "payment_status" => "success", 
                            "payment_gross" => $getplan_details->price,
                            "quantity" => 1, 
                            "user_id" => $usrid,
                            "plan_id" =>$plan_id,
                            "client_invoice" => "unpaid",
                            "payment_date" => date("Y-m-d H:i:s")
                         );
					 $insinvoice = $this->db->insert("invoice_details",$data);
					 $invoice_id = $this->db->insert_id();
					 if($insinvoice){
			              $expiry_date = date('Y-m-d', strtotime("+".$getplan_details->expiry_days." days"));
			              $plan_type   =$getplan_details->period == "month" ? "monthly" : "yearly";
			              $dt = array("user_id" =>$usrid,
			                          "plan_id" => $plan_id,
			                          "plan_type" => $plan_type,
			                          "plandata" => json_encode($getplan_details),
			                          "payment_info" => json_encode($data),
			                          "start_date" => date("Y-m-d"),
			                          "expiry_date"=>$expiry_date,
			                          "payment_status" => "success",
			                          "status" => "active",
			                          "invoice_id" => $invoice_id
			                           );
			             $ss = $this->db->insert("subscription_details",$dt);
			             $dtstorage = array(
			                                 "ftp_storage" => $getplan_details->ftp_space_bytes,
			                                 "db_storage" =>$getplan_details->db_space_bytes ,
											 'ftp_unit'=>$getplan_details->ftp_unit,
											 'db_unit'=>$getplan_details->db_unit,
			                                 "user_id" =>$usrid ,
			                                 "added_date" =>date("Y-m-d"),
			                                 "plan_id" => $getplan_details->id
			                               ); 
			             $ss = $this->db->insert("client_storage",$dtstorage);
			        }
				}
				$msg = $this->lang->line("add_user_msg");
			}
			echo json_encode(array("status" => "success","msg" => $msg));	
		}		
	}



	public function update($cid){

		$cid = base64_decode($cid);
		if(!empty( $this->input->post() )){
			$user_id = $this->session->userdata("user_id");
			$error_data = array();
			$fname = !empty(trim($this->input->post("f_name")))  ? $this->input->post("f_name")  : $error_data["f_name_msg"]  = $this->lang->line("f_name_blank");
			$lname = !empty(trim($this->input->post("l_name")))  ? $this->input->post("l_name") :  $error_data["l_name_msg"] = $this->lang->line("l_name_blank");
			$phone = !empty(trim($this->input->post("phone")))  ? $this->input->post("phone") :  $error_data["phone_msg"] = $this->lang->line("phone_blank");
			$address = !empty(trim($this->input->post("address")))  ? $this->input->post("address") :  $error_data["address_msg"] = $this->lang->line("address_blank");
			$city = !empty(trim($this->input->post("city")))  ? $this->input->post("city") :  $error_data["city_msg"] = $this->lang->line("city_blank");
			$zipcode = !empty(trim($this->input->post("zipcode")))  ? $this->input->post("zipcode") :  $error_data["zipcode_msg"] = $this->lang->line("zipcode_blank");
			$check_company = $this->input->post("check_company");
			$check_plan = $this->input->post("check_plan");
			$status = $this->input->post("status");
			if(isset($check_company)){
				$company_name = !empty(trim($this->input->post("company_name"))) ? $this->input->post("company_name") : $error_data["company_name_msg"] = $this->lang->line("company_name_blank");
				$company_vat_number = $this->input->post("company_vat_number");
				$company_street = !empty(trim($this->input->post("company_street"))) ? $this->input->post("company_street") : $error_data["company_street_msg"] = $this->lang->line("company_street_blank");
				$company_town = !empty(trim($this->input->post("company_town"))) ? $this->input->post("company_town") : $error_data["company_town_msg"] = $this->lang->line("company_town_blank");
				$company_zipcode = !empty(trim($this->input->post("company_zipcode"))) ? $this->input->post("company_zipcode") : $error_data["company_zipcode_msg"] = $this->lang->line("company_zipcode_blank");
				$company_country = !empty(trim($this->input->post("company_country"))) ? $this->input->post("company_country") : $error_data["company_country_msg"] = $this->lang->line("company_country_blank");
				$company_responsible_person = !empty(trim($this->input->post("company_responsible_person"))) ? $this->input->post("company_responsible_person") : $error_data["company_responsible_person_msg"] = $this->lang->line("company_responsible_person_blank");
				$is_company = "yes"; 
				 $company_logo_filename = "";
				 $config['upload_path']   = './uploads/company_logo/'; 
	             $config['allowed_types'] = 'gif|jpg|jpeg|png';
	             if($_FILES["company_logo"]['name'] !=""){
	                $new_name = time().$_FILES["company_logo"]['name'];
	                $config['file_name'] = $new_name;
	                $this->load->library('upload', $config);
	                $this->upload->do_upload('company_logo');
	                if ( ! $this->upload->do_upload('company_logo')){
	                	$error_data["company_logo_msg"] = $this->lang->line("The filetype you are attempting to upload is not allowed");
	                	
	                }else{
	                	$image = $this->upload->data();
	                	$srcpath =  '/uploads/company_logo/'.$image['file_name'];
                		$despath =  '/uploads/company_logo/thumbnail/';
                		$this->general->resizeImage($srcpath,$despath); 
	                	$company_logo_filename = $image["file_name"];
	                }
	             }else{
	             	$company_logo_filename = $this->input->post("old_logo");
	             }
			}else{
				$company_name = "";
				$company_vat_number = "";
				$company_street = "";
				$company_town = "";
				$company_zipcode = "";
				$company_country = "";
				$company_responsible_person ="";
				$company_logo_filename = "";
				$is_company = "no"; 
			}
			$getreseller_storage = $this->db->query("select cs.ftp_storage,cs.db_storage,cs.users from client_storage cs where cs.user_id = ".$user_id." AND cs.mode = 'reseller'")->row();

			$data = array(
				"fname" => $fname,
				"lname" => $lname,
				"phone" => $phone,
				"address" => $address,
				"city" => $city,
				"zipcode" => $zipcode,
				"company_name" => $company_name,
				"company_vat_number" => $company_vat_number,
				"company_street" => $company_street,
				"company_town" => $company_town,
				"company_zipcode" => $company_zipcode,
				"company_country" => $company_country,
				"company_responsible_person" => $company_responsible_person,
				"company_logo" => $company_logo_filename,
				"status" => $status,	
				"is_company" => $is_company								
			);
			if(count($error_data) >  0){
				$msg = 'testing';
				echo json_encode(array("status" => "failed","msg" => $msg,"error_data" => $error_data));

			}else{
					$getreseller = $this->db->get_where("client",array("client_id" => $user_id))->row();
					$client_id = $this->input->post("euser_id"); 
					$this->db->where("client_id",$client_id);
					$this->db->update("client",$data);
					$msg = $this->lang->line("up_user_msg");
					echo json_encode(array("status" => "success","msg" => $msg));	
			}	
		}else{
			$this->db->where("client_id", $cid);
			$data["userdata"] = $this->db->get("client")->row_array();
			$data["client_storage"] = $this->db->get_where("client_storage", array("user_id"=>$cid))->row_array();
			$data["page"] 	  = "users"; 
			$this->load->view("client/users/update",$data);
		}




			
	}







	public function delete($client_id){
		$this->db->where("client_id",$client_id);
		if($this->db->delete("client")){
			echo json_encode(array("status" => "success","msg" => $this->lang->line("del_user_msg")));
		}else{
			echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
		}		    	
	}
	public function profile(){
		$client_id = $this->session->userdata("user_id");
		$data["getclient"] = $this->db->get_where("client",array("client_id" => $client_id))->row();
		$data["page"] = "users"; 
		$this->load->view("client/users/profile",$data);
	}
	public function update_profile(){
		$user_id = $this->session->userdata("user_id");
		$getuser = $this->db->get_where("client",array("client_id" => $user_id))->row();
		$fname = $this->input->post("f_name");
		$lname = $this->input->post("l_name");
		$phone = $this->input->post("phone");
		$email = $this->input->post("email");
		$address = $this->input->post("address");
		$landmark = $this->input->post("landmark");
		$city = $this->input->post("city");
		$zipcode = $this->input->post("zipcode");
		$birth = !empty($this->input->post("dob")) ? $this->input->post("dob") : '';

		//FILE UPLOAD
		if($_FILES["userprofilec"]['name'] !=""){
			$filename = $getuser->fname."_".time()."_".$user_id;
			$config['upload_path']          = './uploads/user/profile/';
			$config['allowed_types']        = 'gif|jpg|png|PNG';
			$config['file_name']         	=$filename;   
			$this->load->library('upload', $config);
			if(!$this->upload->do_upload('userprofilec'))
			{
				$error = array('error' => $this->upload->display_errors());
				// echo "<pre>";
				// print_r($error);die();
				//$img = $getuser->img;
			}else{
				if($getuser->img != ""){
					unlink('./uploads/user/profile/'.$getuser->img);
				}
				$upload_data = $this->upload->data();
							$srcpath =  '/uploads/user/profile/'.$upload_data['file_name'];
	                		$despath =  '/uploads/user/profile/thumbnail/';
	                		$this->general->resizeImage($srcpath,$despath); 

				$img = $upload_data["file_name"]; 
				$this->session->unset_userdata('icon');
				$this->session->set_userdata('icon',$img);    
			}
		}else{
			$img = $getuser->img; 
		}
		//COMPANY
		$check_company = $this->input->post("check_company");

		if(isset($check_company)){

			$company_name = !empty($this->input->post("company_name")) ? $this->input->post("company_name") : $error_data["company_name_msg"] = $this->lang->line("company_name_blank");
			$company_vat_number = !empty($this->input->post("company_vat_number")) ? $this->input->post("company_vat_number") : $error_data["company_vat_number_msg"] = $this->lang->line("company_vat_number_blank");
			$company_street = !empty($this->input->post("company_street")) ? $this->input->post("company_street") : $error_data["company_street_msg"] = $this->lang->line("company_street_blank");
			$company_town = !empty($this->input->post("company_town")) ? $this->input->post("company_town") : $error_data["company_town_msg"] = $this->lang->line("company_town_blank");
			$company_zipcode = !empty($this->input->post("company_zipcode")) ? $this->input->post("company_zipcode") : $error_data["company_zipcode_msg"] = $this->lang->line("company_zipcode_blank");
			
			$company_country = !empty($this->input->post("company_country")) ? $this->input->post("company_country") : $error_data["company_country_msg"] = $this->lang->line("company_country_blank");

			$company_responsible_person = !empty($this->input->post("company_responsible_person")) ? $this->input->post("company_responsible_person") : $error_data["company_responsible_person_msg"] = $this->lang->line("company_responsible_person_blank");

			$is_company = "yes"; 


			 $company_logo_filename = "";
			 $config['upload_path']   = './uploads/company_logo/'; 
             $config['allowed_types'] = 'gif|jpg|jpeg|png';
             if($_FILES["company_logo"]['name'] !=""){
                $new_name = time().$_FILES["company_logo"]['name'];
                $config['file_name'] = $new_name;
                $this->load->library('upload', $config);
                $this->upload->do_upload('company_logo');
                if ( ! $this->upload->do_upload('company_logo')){
                	//$error_data["company_logo_msg"] = $this->upload->display_errors();
                	$error_data["company_logo_msg"] = $this->lang->line("The filetype you are attempting to upload is not allowed");
                	
                }else{
                	$image = $this->upload->data(); 
                	$company_logo_filename = $image["file_name"];
                		$srcpath =  '/uploads/company_logo/'.$image['file_name'];
                		$despath =  '/uploads/company_logo/thumbnail/';
                		$this->general->resizeImage($srcpath,$despath);
                }
             }else{
             	$company_logo_filename = $this->input->post("old_logo");
             }
		}else{
			$company_name 		= "";
			$company_vat_number = "";
			$company_street 	= "";
			$company_town 		= "";
			$company_zipcode 	= "";
			$company_country 	= "";
			$company_responsible_person ="";
			$company_logo_filename = "";
			$is_company 		= "no"; 
		}
		//COMPANY END


		$data = array(	"fname" => $fname,
			"lname" => $lname,
			"phone" => $phone,
			"email" => $email,
			"address" => $address,
			"landmark" => $landmark,
			"city" => $city,
			"zipcode" => $zipcode,
			"birth" =>$birth,
			"img" => $img,
			"company_name" => $company_name,
			"company_vat_number" => $company_vat_number,
			"company_street" => $company_street,
			"company_town" => $company_town,
			"company_zipcode" => $company_zipcode,
			"company_country" => $company_country,
			"company_logo" => $company_logo_filename,
			"company_responsible_person" 	=> $company_responsible_person,
			"is_company" => $is_company								
		);

		$this->db->where("client_id",$user_id);
		if($this->db->update("client",$data)){
			$this->session->unset_userdata('fname');
			$this->session->set_userdata('fname',$fname);
			$this->session->unset_userdata('lname');
			$this->session->set_userdata('lname',$lname);
			echo json_encode(array("status" => "success","msg" => $this->lang->line("profile_updated")));	
		}else{
			echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
		}
	}
	public function change_password(){
		$user_id = $this->session->userdata("user_id");
		$cpasswordverify = $this->input->post("cpasswordverify");
		$ccpasswordverify = $this->input->post("ccpasswordverify");
		$oldpasswordverify = $this->input->post("oldpasswordverify");
		$result_ = $this->db->where("client_id",$user_id)->get('client')->row();
        if (!password_verify($oldpasswordverify, $result_->password)){
        	echo json_encode(array("status" => "failed","msg" => $this->lang->line("old_password_not_match")) );
        	die;
        }
		if($cpasswordverify == $ccpasswordverify){
			$password = password_hash($cpasswordverify, PASSWORD_DEFAULT);
			$passtext = base64_encode($cpasswordverify);
			$data = array("password" => $password,"pass_text" => $passtext);
			$this->db->where("client_id",$user_id);
			if($this->db->update("client",$data)){
				echo json_encode(array("status" => "success","msg" => $this->lang->line("pass_changed")));
			}else{
				echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
			}

		}else{
			echo json_encode(array("status" => "failed","msg" => $this->lang->line("password_not_matched")));
		}

	}
	public function plan_details($user_id){
		$plan_id = $this->db->query("select * from subscription_details sd where sd.user_id = ".$user_id." AND status = 'active' ")->row()->plan_id;
		$data["plan_data"] = $this->db->query("select * from plans p where p.id = ".$plan_id."")->row();

		$data["modules"] = $this->db->query("select * from modules m where m.module_id IN(".$data["plan_data"]->modules.") ")->result();

		$data['client_storage'] =  $this->db->query("select * from client_storage cs where cs.user_id = ".$user_id."")->row();
		$data["ftp_storage"] = $this->format_size($data['client_storage']->ftp_storage);
		$data["db_storage"] = $this->format_size($data['client_storage']->db_storage);
		$data["storage_running"] = $this->getsizes($user_id);
		$data["page"]      = "dashboard";
		$this->load->view("client/users/plan_details",$data);
	}
	public function getsizes($user_id){
		$getallprojects = $this->db->get_where("project",array("client_id" => $user_id))->result();
		$rootfolder = "./projects/";
		$sqlsize = 0;
		$ftpsize = 0;
		foreach($getallprojects as $proj) {
			$ftpfolder = $rootfolder.$proj->folder_name."/ftp_server/";
			$sqlfolder = $rootfolder.$proj->folder_name."/mysql_server/";
			if(is_dir($ftpfolder)){
				$sqlsize   += $this->foldersize($sqlfolder);
				$ftpsize   += $this->foldersize($ftpfolder);
			}
		}
		$getstorage =  $this->db->get_where("client_storage",array("user_id" => $user_id))->row();
		$fsize = $ftpsize == 0 ? 1 : $ftpsize;
		$dsize = $sqlsize == 0 ? 1 : $sqlsize;  
		$ftpper     =  ($fsize / $getstorage->ftp_storage ) * 100 ;
		$dbper     =  ($dsize / $getstorage->db_storage) * 100;
		if($ftpper > 90 ){
			$ftpwarning = "<strong style='color:red;font-size:15px;'> ".$this->lang->line("disk_usage")."(".round($ftpper,3)."%)</strong>";
		}else{
			$ftpwarning = "<strong style='color:green;font-size:15px;'> ".$this->lang->line("disk_usage")."(".round($ftpper,3)."%)</strong>";
		} 
		if($dbper > 90 ){
			$dbwarning = "<strong style='color:red;font-size:15px;'> ".$this->lang->line("disk_usage")."(".round($dbper,3)."%)</strong>";
		}else{
			$dbwarning = "<strong style='color:green;font-size:15px;'> ".$this->lang->line("disk_usage")."(".round($dbper,3)."%)</strong>";
		} 
		return array("ftpsize" => $this->format_size($ftpsize),"sqlsize" => $this->format_size($sqlsize),"ftp_percent" => $ftpper,"db_percent" => $dbper,"ftpmsg" => $ftpwarning,"dbmsg" => $dbwarning);
	}
	public function foldersize($path){
		$total_size = 0;
		$files = scandir($path);
		$cleanPath = rtrim($path, '/'). '/';
		foreach($files as $t) {
			if ($t<>"." && $t<>"..") {
				$currentFile = $cleanPath . $t;
				if (is_dir($currentFile)) {
					$size = $this->foldersize($currentFile);
					$total_size += $size;
				}
				else {
					$size = filesize($currentFile);
					$total_size += $size;
				}
			}   
		}

		return $total_size;
	}
	public function format_size($size) { 
		$units = explode(' ', 'B KB MB GB TB PB');
		$mod = 1024;
		for ($i = 0; $size > $mod; $i++){
			$size /= $mod;
		}
		$endIndex = strpos($size, ".")+3;
		return substr( $size, 0, $endIndex).' '.$units[$i];
	}
	public function send_create_password_mail($client_id){
			if($client_id != '' && $client_id > 0){
				$usrdata = $this->db->get_where("client",array("client_id" => $client_id))->row();
				if(!empty($usrdata->username)){
					
					$username = $usrdata->username;

					$code = base64_encode($username);

                    $updateData = [
                        'client_id'=> $usrdata->client_id,
                        'code' => $code
                    ];
                    $this->db->where("client_id",$usrdata->client_id);
                    $this->db->delete("user_set_password");
                    if($this->db->insert("user_set_password",$updateData))
                    {   
                    		$get_setting = $this->db->query("select name_value from site_setting where setting_id IN(7,8)")->result();
                            $link  = base_url()."client/login/set_password/".$code;
                             sendMail($usrdata->email,'REGISTRATION_PASSWORD_SET',["user_name" => $usrdata->fname,'link' => $link]);
                             return true;
                    }else{
                    	return false;
	                }
				}
			}
			return false;
	}

}