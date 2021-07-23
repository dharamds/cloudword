<?php
class Users extends MX_Controller 
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "client" && $this->session->userdata("user_id") != ""){
			redirect(base_url()."admin/login");
		}
	}
	public function index(){
		$this->db->where("client_id!=", 1);
		$this->db->where("client_id!=", $this->session->userdata("user_id"));
		if(!empty($this->input->post("selected"))){
			$selected_role = $this->input->post("selected");
			$this->db->where("role_id =", $selected_role);
		}else{$selected_role = '';}
		$this->db->order_by("client_id", "desc");
		$data["userlist"] = $this->db->get("client")->result();
		$data["page"] 	  = "users"; 
		$data["roles"] = $this->db->get("roles")->result();
		$data["selected_role"] = $selected_role;
		$this->load->view("admin/users/list",$data);		
	}
	public function get_by_role_list(){
		if(!empty($this->input->post("selected"))){
			$selected_role = $this->input->post("selected");
			$this->db->where("role_id =", $selected_role);
		}else{$selected_role = '';}

		$this->db->where("role_id =", $selected_role);
		$this->db->order_by("client_id", "desc");
		$data["userlist"] = $this->db->get("client")->result();
		$data["roles"] = $this->db->get("roles")->result();
		$data["selected_role"] = $selected_role;
		$this->load->view("admin/users/list_by_role",$data);



	}


	public function listusers(){
		$data["page"] 	  = "users"; 
		$data["roles"] = $this->db->get("roles")->result();
		$data["selected_role"] = '';
		$this->load->view("admin/users/listusers",$data);		
	}
	public function getlist(){
		$this->load->model("admin/usersmodel");
		$roles = $this->db->get("roles")->result();
		$roles_array = array();
		foreach ($roles as $role){
			$roles_array[$role->role_id] = $role->role_name;
		}
		$columns = array( 
                            0 =>'client_id', 
                            1 =>'name',
                            2=> 'email',
                            3=> 'phone',
                            4=> 'city',
                            5=>'role_id'
                        );
		$limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];
        $dir = $this->input->post('order')[0]['dir'];
        $totalData = $this->usersmodel->allusers_count();
        $totalFiltered = $totalData; 
        if(empty($this->input->post('search')['value']))
        {            
            $posts = $this->usersmodel->allusers($limit,$start,$order,$dir);
        }
        else {
            $search = $this->input->post('search')['value']; 


            $posts =  $this->usersmodel->users_search($limit,$start,$search,$order,$dir);

            $totalFiltered = $this->usersmodel->allusers_search_count($search);
        }
        $data = array();
        if(!empty($posts))
        {
        	$cnttt = 1;
            foreach ($posts as $post)
            {
                $nestedData['sr_no'] = $cnttt;
                $nestedData['name'] = $post->fname." ".$post->lname;
                $nestedData['email'] = $post->email;
                $nestedData['phone'] = $post->phone;
                $nestedData['city'] = $post->city;
                $nestedData['user_roles'] = $roles_array[$post->role_id];
                $nestedData['action'] = '<a style="min-width: 40px;" href="'.base_url().'admin/users/update/'.base64_encode($post->client_id).'" data-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-primary"><i class="flaticon-pencil"></i></a><a style="min-width: 40px;" href="javascript:" onclick="deleteUser('.$post->client_id.')" data-toggle="tooltip" data-placement="top" title="Delete" class="btn btn-danger"><i class="flaticon-trash"></i></a>';
                $data[] = $nestedData;
                $cnttt++;

            }
        }
        $json_data = array(
                    "draw"            => intval($this->input->post('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        echo json_encode($json_data); 
	}
	public function create(){
		$data["page"] 	  = "users";
		$data["roles"] 	  = $this->db->query("select * from roles where role_id NOT IN(2,4,5)")->result(); 
		$this->load->view("admin/users/create",$data);	
	}
public function valid_email($str) {
			return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
		}

	public function save($type){
		$user_id = $this->session->userdata("user_id");
		$error_data = array();
		$fname = !empty($this->input->post("f_name"))  ? $this->input->post("f_name")  : $error_data["f_name_msg"]  = $this->lang->line("f_name_blank");
		$lname = !empty($this->input->post("l_name"))  ? $this->input->post("l_name") :  $error_data["l_name_msg"] = $this->lang->line("l_name_blank");
		$phone = !empty($this->input->post("phone"))  ? $this->input->post("phone") :  $error_data["phone_msg"] = $this->lang->line("phone_blank");
		$email = !empty($this->input->post("email"))  ? $this->input->post("email") :  $error_data["email_msg"] = $this->lang->line("email_blank");
		$address = !empty($this->input->post("address"))  ? $this->input->post("address") :  $error_data["address_msg"] = $this->lang->line("address_blank");
		$city = !empty($this->input->post("city"))  ? $this->input->post("city") :  $error_data["city_msg"] = $this->lang->line("city_blank");
		$zipcode = !empty($this->input->post("zipcode"))  ? $this->input->post("zipcode") :  $error_data["zipcode_msg"] = $this->lang->line("zipcode_blank");
		
		$zipcode = is_numeric($this->input->post("zipcode"))  ? $this->input->post("zipcode") :  $error_data["zipcode_msg"] = $this->lang->line("zipcode_valid");
		
		
		$check_company = $this->input->post("check_company");
		$user_role = !empty($this->input->post("user_role"))  ? $this->input->post("user_role") :  $error_data["user_role_msg"] = $this->lang->line("user_role_blank");
		
		
		if (!array_key_exists("email_msg",$error_data))
  		{
  			if(!$this->valid_email($email)){
			   $error_data["email_msg"] = $this->lang->line("email_not_proper");
			}
  		}

		if(isset($check_company)){
			$company_name = !empty($this->input->post("company_name")) ? $this->input->post("company_name") : $error_data["company_name_msg"] = $this->lang->line("company_name_blank");
			$company_vat_number = $this->input->post("company_vat_number");
			$company_street = !empty($this->input->post("company_street")) ? $this->input->post("company_street") : $error_data["company_street_msg"] = $this->lang->line("company_street_blank");
			$company_town = !empty($this->input->post("company_town")) ? $this->input->post("company_town") : $error_data["company_town_msg"] = $this->lang->line("company_town_blank");
			$company_zipcode = !empty($this->input->post("company_zipcode")) ? $this->input->post("company_zipcode") : $error_data["company_zipcode_msg"] = $this->lang->line("company_zipcode_blank");
			$company_country = !empty($this->input->post("company_country")) ? $this->input->post("company_country") : $error_data["company_country_msg"] = $this->lang->line("company_country_blank");

			$company_responsible_person = !empty($this->input->post("company_responsible_person")) ? $this->input->post("company_responsible_person") : $error_data["company_responsible_person_msg"] = $this->lang->line("company_responsible_person_blank");


			$company_zipcode = is_numeric($this->input->post("company_zipcode")) ? $this->input->post("company_zipcode") : $error_data["company_zipcode_msg"] = $this->lang->line("zipcode_valid");



			$is_company = "yes"; 

			 $company_logo_filename = "";
			 $config['upload_path']   = './uploads/company_logo/'; 
             $config['allowed_types'] = 'gif|jpg|jpeg|png|PNG';
             if($_FILES["company_logo"]['name'] !=""){
                $new_name = time().$_FILES["company_logo"]['name'];
                $config['file_name'] = $new_name;
                $this->load->library('upload', $config);
                $this->upload->do_upload('company_logo');
                if (!$this->upload->do_upload('company_logo')){
                	$error_data["company_logo_msg"] = $this->lang->line("The filetype you are attempting to upload is not allowed");
                	//$error = array('error' => $this->upload->display_errors());
	                //$error_data["company_logo_msg"] = $error;
                }else{
                	$image = $this->upload->data(); 
                	$company_logo_filename = $image["file_name"];
                	$srcpath =  '/uploads/company_logo/'.$image['file_name'];
                	$despath =  '/uploads/company_logo/thumbnail/';
                	$this->general->resizeImage($srcpath,$despath); 
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
		if($user_role == 3){
				$this->load->library('general');
				$userscount = !empty($this->input->post("userscount")) ? $this->input->post("userscount") : $error_data["userscount_msg"] = $this->lang->line("userscount_blank");
				$ftp_space_limit = !empty($this->input->post("ftp_space_limit")) ? $this->input->post("ftp_space_limit") : $error_data["ftp_space_limit_msg"] = $this->lang->line("ftp_space_limit_blank");

				$ftp_unit = !empty($this->input->post("ftp_unit")) ? $this->input->post("ftp_unit") : $error_data["ftp_unit_msg"] = $this->lang->line("ftp_unit_blank");
				$sql_space_limit = !empty($this->input->post("sql_space_limit")) ? $this->input->post("sql_space_limit") : $error_data["sql_space_limit_msg"] = $this->lang->line("sql_space_limit_blank");
				$db_unit = !empty($this->input->post("db_unit")) ? $this->input->post("db_unit") : $error_data["db_unit_msg"] = $this->lang->line("db_unit_blank");
				$ftp_space_bytes = $this->general->byteconvert($ftp_space_limit,$ftp_unit);
                $db_space_bytes = $this->general->byteconvert($sql_space_limit,$db_unit);
		}
		if(empty($error_data["email_msg"]) && empty($error_data["user_role_msg"])){
			$checkemail 	 = $this->db->get_where("client",array("role_id" => $user_role,"username" => $email))->num_rows();
			$error_dataemail = 	$checkemail > 0 ? $error_data["email_msg"] = $this->lang->line("email_exist") : '' ;
		}
		$data = array(
			"username" => $email,
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
			"company_logo" => $company_logo_filename,
			"company_responsible_person" 	=> $company_responsible_person,
			"is_company" => $is_company									
		);
		if(count($error_data) >  0){
			$msg = 'testing';
			echo json_encode(array("status" => "failed","msg" => $msg,"error_data" => $error_data));
			exit;
		}else{
			if($type == "edit"){
				$client_id = $this->input->post("euser_id"); 
				$this->db->where("client_id",$client_id);
				$this->db->update("client",$data);
				$msg = $this->lang->line("up_user_msg");
			}else{
				$password = $this->input->post("password") ?? $error_data["password"] = $this->lang->line("password_blank");
				$data["pass_text"] = base64_encode($password);
				$data["password"]  = password_hash($password, PASSWORD_DEFAULT);
				$data["role_id"]   = $user_role;
				$data["parent_id"]   = $user_id;
				$data["added_date"] = date("Y-m-d");
				$this->db->insert("client",$data);
				$client_id = $this->db->insert_id();
				$msg = $this->lang->line("add_user_msg");


				//Send mail
				$this->load->helper('basic_helper');
		        sendMail($email, 'USER_CREATION', [
		            'user_name' => $fname." ".$lname,
		            'username' => $email,
		            'password' => $password,
		        ]);

			}

			if($user_role == 3){
                $stor_array = array(
							    "ftp_storage" 	=>  $ftp_space_bytes,
							    "db_storage" 	=>  $db_space_bytes,
							    "user_id" 		=> $client_id,
							    "ftp_unit" 		=> $ftp_unit,
							    "db_unit" 		=> $db_unit,
							    "mode" 			=> "reseller",
							    "added_date" 	=> date("Y-m-d"),
							    "users" 		=> $this->input->post("userscount")
					);
                //to create for client mode
                $stor_array_client = array(
							    "ftp_storage" 	=> '',
							    "db_storage" 	=> '',
							    "user_id" 		=> $client_id,
							    "mode" 			=> "client",
							    "added_date" 	=> date("Y-m-d"),
							    "users" 		=> 0
					);
                $this->db->insert("client_storage",$stor_array);
                $this->db->insert("client_storage",$stor_array_client);
                $this->db->insert("reseller_request",["user_id" => $client_id,"status" => "approve"]);
                $this->db->insert("reseller_setting",["reseller_id" => $client_id]);	


                

			}
			echo json_encode(array("status" => "success","msg" => $msg));	exit;
		}		
	}



	public function update($cid){
		
		$cid = base64_decode($cid);
		if(!empty( $this->input->post() )){


			// echo '<pre>';
			// print_r( $this->input->post() );
			// exit;


			$user_id = $this->session->userdata("user_id");
			$error_data = array();
			$fname = !empty($this->input->post("f_name"))  ? $this->input->post("f_name")  : $error_data["f_name_msg"]  = $this->lang->line("f_name_blank");
			$lname = !empty($this->input->post("l_name"))  ? $this->input->post("l_name") :  $error_data["l_name_msg"] = $this->lang->line("l_name_blank");
			$phone = !empty($this->input->post("phone"))  ? $this->input->post("phone") :  $error_data["phone_msg"] = $this->lang->line("phone_blank");
			$address = !empty($this->input->post("address"))  ? $this->input->post("address") :  $error_data["address_msg"] = $this->lang->line("address_blank");
			$city = !empty($this->input->post("city"))  ? $this->input->post("city") :  $error_data["city_msg"] = $this->lang->line("city_blank");
			$zipcode = !empty($this->input->post("zipcode"))  ? $this->input->post("zipcode") :  $error_data["zipcode_msg"] = $this->lang->line("zipcode_blank");
			$check_company = $this->input->post("check_company");
			$user_role = !empty($this->input->post("user_role"))  ? $this->input->post("user_role") :  $error_data["user_role_msg"] = $this->lang->line("user_role_blank");
			$status = $this->input->post("status");

			if(isset($check_company)){
				$company_name = !empty($this->input->post("company_name")) ? $this->input->post("company_name") : $error_data["company_name_msg"] = $this->lang->line("company_name_blank");
				$company_vat_number = $this->input->post("company_vat_number");
				$company_street = !empty($this->input->post("company_street")) ? $this->input->post("company_street") : $error_data["company_street_msg"] = $this->lang->line("company_street_blank");
				$company_town = !empty($this->input->post("company_town")) ? $this->input->post("company_town") : $error_data["company_town_msg"] = $this->lang->line("company_town_blank");
				$company_zipcode = !empty($this->input->post("company_zipcode")) ? $this->input->post("company_zipcode") : $error_data["company_zipcode_msg"] = $this->lang->line("company_zipcode_blank");
				$company_country = !empty($this->input->post("company_country")) ? $this->input->post("company_country") : $error_data["company_country_msg"] = $this->lang->line("company_country_blank");
				$company_responsible_person = !empty($this->input->post("company_responsible_person")) ? $this->input->post("company_responsible_person") : $error_data["company_responsible_person_msg"] = $this->lang->line("company_responsible_person_blank");

				$is_company = "yes"; 


				 $company_logo_filename = "";
				 $config['upload_path']   = './uploads/company_logo/'; 
	             $config['allowed_types'] = 'gif|jpg|jpeg|png|PNG';
	             if($_FILES["company_logo"]['name'] !=""){
	                $new_name = time().$_FILES["company_logo"]['name'];
	                $config['file_name'] = $new_name;
	                $this->load->library('upload', $config);
	                $this->upload->do_upload('company_logo');
	                if ( ! $this->upload->do_upload('company_logo')){
	                	//$error_data["company_logo_msg"] = $this->upload->display_errors();
	                	$error_data["company_logo_msg"] = $this->lang->line("The filetype you are attempting to upload is not allowed");
	                	//$error = array('error' => $this->upload->display_errors());

	                //$error_data["company_logo_msg"] = $error;
	                	
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
			if($user_role == 3){
					$this->load->library('general');
					$userscount = !empty($this->input->post("userscount")) ? $this->input->post("userscount") : $error_data["userscount_msg"] = $this->lang->line("userscount_blank");
					$ftp_space_limit = !empty($this->input->post("ftp_space_limit")) ? $this->input->post("ftp_space_limit") : $error_data["ftp_space_limit_msg"] = $this->lang->line("ftp_space_limit_blank");

					$ftp_unit = !empty($this->input->post("ftp_unit")) ? $this->input->post("ftp_unit") : $error_data["ftp_unit_msg"] = $this->lang->line("ftp_unit_blank");
					$sql_space_limit = !empty($this->input->post("sql_space_limit")) ? $this->input->post("sql_space_limit") : $error_data["sql_space_limit_msg"] = $this->lang->line("sql_space_limit_blank");
					$db_unit = !empty($this->input->post("db_unit")) ? $this->input->post("db_unit") : $error_data["db_unit_msg"] = $this->lang->line("db_unit_blank");
					$ftp_space_bytes = $this->general->byteconvert($ftp_space_limit,$ftp_unit);
	                $db_space_bytes = $this->general->byteconvert($sql_space_limit,$db_unit);
			}
			
			$data = array(
				"fname" 	=> $fname,
				"lname" 	=> $lname,
				"phone" 	=> $phone,
				"address" 	=> $address,
				"city" 		=> $city,
				"zipcode" 	=> $zipcode,
				"company_name" 			=> $company_name,
				"company_vat_number" 	=> $company_vat_number,
				"company_street" 		=> $company_street,
				"company_town" 			=> $company_town,
				"company_zipcode" 		=> $company_zipcode,
				"company_country" 		=> $company_country,
				"company_responsible_person" 	=> $company_responsible_person,
				"company_logo" => $company_logo_filename,
				"status" => $status,
				"is_company" 					=> $is_company									
			);

			if(!empty($this->input->post("password"))){
				$data["password"] = password_hash($this->input->post("password"), PASSWORD_DEFAULT);
			}

			if(count($error_data) >  0){
				$msg = 'testing';
				echo json_encode(array("status" => "failed","msg" => $msg,"error_data" => $error_data));

			}else{

				$client_id = $this->input->post("euser_id"); 
				$this->db->where("client_id",$client_id);
				$this->db->update("client",$data);
				$msg = $this->lang->line("up_user_msg");


				// if($type == "edit"){
				// 	$client_id = $this->input->post("euser_id"); 
				// 	$this->db->where("client_id",$client_id);
				// 	$this->db->update("client",$data);
				// 	$msg = $this->lang->line("up_user_msg");
				// }else{
				// 	$password = $this->input->post("password") ?? $error_data["password"] = $this->lang->line("password_blank");
				// 	$data["pass_text"] = base64_encode($password);
				// 	$data["password"]  = password_hash($password, PASSWORD_DEFAULT);
				// 	$data["role_id"]   = $user_role;
				// 	$data["parent_id"]   = $user_id;
				// 	$data["added_date"] = date("Y-m-d");
				// 	$this->db->insert("client",$data);
				// 	$client_id = $this->db->insert_id();
				// 	$msg = $this->lang->line("add_user_msg");
				// }

				if($user_role == 3){
	                $stor_array = array(
								    "ftp_storage" 	=>  $ftp_space_bytes,
								    "db_storage" 	=>  $db_space_bytes,
								    "user_id" 		=> $client_id,
									"ftp_unit" 		=> $ftp_unit,
									"db_unit" 		=> $db_unit,
								    "mode" 			=> "reseller",
								    "added_date" 	=> date("Y-m-d"),
								    "users" 		=> $this->input->post("userscount")
						);

	                $this->db->where("user_id",$client_id);	
	                $this->db->update("client_storage",$stor_array);
				}
				echo json_encode(array("status" => "success","msg" => $msg));	
			}	



		}else{

			$this->db->where("client_id", $cid);
			$data["userdata"] = $this->db->get("client")->row_array();
			$data["client_storage"] = $this->db->get_where("client_storage", array("user_id"=>$cid))->row_array();
			if(!empty($data["client_storage"]['ftp_storage'])){
				$data["client_storage"]['ftp_storage']=  $this->byteconversion($data["client_storage"]['ftp_storage'], $data["client_storage"]['ftp_unit'],2);
			}
			if(	!empty($data["client_storage"]['ftp_storage'])){
				$data["client_storage"]['db_storage'] =$this->byteconversion($data["client_storage"]['db_storage'],$data["client_storage"]['db_unit']);
			}
			// echo "<pre>"; print_r($data); exit;
			$data["page"] 	  = "users"; 
			$this->load->view("admin/users/update",$data);

			//echo '<pre>';
			//print_r($data);
			//exit;



		}


		
	}


	public function byteconversion($bytes, $to, $decimal_places = 1) {
		$formulas = array(
			'kb' => number_format($bytes / 1024, $decimal_places),
			'mb' => number_format($bytes / 1048576, $decimal_places),
			'gb' => number_format($bytes / 1073741824, $decimal_places),
			'tb' => number_format($bytes / 1099511627776 , $decimal_places)
		);
	
		return isset($formulas[$to]) ? $formulas[$to] : 0;
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
		$this->load->view("admin/users/profile",$data);
	}
	public function update_profile(){
		$user_id = $this->session->userdata("user_id");
		$getuser = $this->db->get_where("client",array("client_id" => $user_id))->row();
		$fname = $this->input->post("f_name");
		$lname = $this->input->post("l_name");
		$phone = $this->input->post("phone");
		//$email = $this->input->post("email");
		$address = $this->input->post("address");
		$landmark = $this->input->post("landmark");
		$city = $this->input->post("city");
		$zipcode = $this->input->post("zipcode");
		$birth = !empty($this->input->post("dob")) ? $this->input->post("dob") : '';

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



		if($_FILES["userprofilec"]['name'] !=""){
			$filename = $getuser->fname."_".time()."_".$user_id;
			$config['upload_path']          = './uploads/user/profile/';
			$config['allowed_types']        = 'gif|jpg|png';
			$config['file_name']         	=$filename;   
			$this->load->library('upload', $config);
			if(!$this->upload->do_upload('userprofilec'))
			{
				//$error = array('error' => $this->upload->display_errors());
				$img = $getuser->img;
				// echo "<pre>";
				// print_r($error);
			}else{
				if($getuser->img != ""){
					unlink('./uploads/user/profile/'.$getuser->img);
				}
				$upload_data = $this->upload->data();
				$img = $upload_data["file_name"];
					$srcpath =  '/uploads/user/profile/'.$upload_data['file_name'];
                	$despath =  '/uploads/user/profile/thumbnail/';
                	$this->general->resizeImage($srcpath,$despath);
				$this->session->unset_userdata('icon');
				$this->session->set_userdata('icon',$img);    
			}
		}else{
			$img = $getuser->img; 
		}
		

		$data = array(	"fname" => $fname,
			"lname" => $lname,
			"phone" => $phone,
			/*"email" => $email,*/
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
		//echo "<pre>";
		//print_r($data);die();

		$this->db->where("client_id",$user_id);
		if($this->db->update("client",$data)){
			$this->session->unset_userdata('fname');
			$this->session->set_userdata('fname',$fname);
			$this->session->unset_userdata('lname');
			$this->session->set_userdata('lname',$lname);    

			$this->session->unset_userdata('avatar');
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
		$this->load->view("admin/users/plan_details",$data);
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
	public function assign_role($user_id){
		$getreseller = $this->db->get_where("client",array("user_id" => $user_id,"role_id" => 3));
		if($getreseller->num_rows() > 0){



		}else{
			echo json_encode(array("status" => "failed","msg" => $this->lang->line("no_records_found")));
		}
}

public function currencies(){
		$data["userlist"] = $this->db->get("client")->result();
		$data["page"] 	  = "users";
		$this->load->view("admin/users/currencies",$data);	
}



}