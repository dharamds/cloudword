<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed
class Users extends MX_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $header_data = $this->input->request_headers();

    }
    public function register() {
		$header_data = $this->input->request_headers();
          $error_data = array();
          $jsondata = json_decode(file_get_contents("php://input"));
          if(trim($jsondata->f_name) == ''){
            $error_data["f_name"] = $this->lang->line("f_name_blank");
          }else{
            $fname = $jsondata->f_name;
          }

          if(trim($jsondata->l_name) == ''){
            $error_data["l_name"] = $this->lang->line("l_name_blank");
          }else{
            $lname = $jsondata->l_name;
          }

          if(trim($jsondata->phone) == ''){
            $error_data["phone"] = $this->lang->line("phone_blank");
          }else{
            $phone = $jsondata->phone;
          }

          if(trim($jsondata->email) == ''){
            $error_data["email"] = $this->lang->line("email_blank");
          }else{
            $email = $jsondata->email;
          }

          if(trim($jsondata->address) == ''){
            $error_data["address"] = $this->lang->line("address_blank");
          }else{
            $address = $jsondata->address;
          }

          if(trim($jsondata->city) == ''){
            $error_data["city"] = $this->lang->line("city_blank");
          }else{
            $city = $jsondata->city;
          }

          if(trim($jsondata->zipcode) == ''){
            $error_data["zipcode"] = $this->lang->line("zipcode_blank");
          }else{
            $zipcode = $jsondata->zipcode;
          }

          if(trim($jsondata->password) == ''){
            $error_data["password"] = $this->lang->line("password_blank");
          }else{
            $password = $jsondata->password;
          }

          if(trim($jsondata->cpassword) == ''){
            $error_data["cpassword"] = $this->lang->line("cpassword_blank");
          }else{
            $cpassword = $jsondata->cpassword;
          }

         
          if($jsondata->check_company == 1){
             $company_name = !empty(trim($jsondata->company_name)) ? $jsondata->company_name : $error_data["company_name_msg"] = $this->lang->line("company_name_blank");
            $company_vat_number = $jsondata->company_vat_number;
            $company_street = !empty(trim($jsondata->company_street)) ? $jsondata->company_street : $error_data["company_street_msg"] = $this->lang->line("company_street_blank");
            $company_town = !empty(trim($jsondata->company_town)) ? $jsondata->company_town : $error_data["company_town_msg"] = $this->lang->line("company_town_blank");
            $company_zipcode = !empty(trim($jsondata->company_zipcode)) ? $jsondata->company_zipcode : $error_data["company_zipcode_msg"] = $this->lang->line("company_zipcode_blank");
            $company_country = !empty(trim($jsondata->company_country)) ? $jsondata->company_country : $error_data["company_country_msg"] = $this->lang->line("company_country_blank");
            $company_responsible_person = !empty(trim($jsondata->company_responsible_person)) ? $jsondata->company_responsible_person : $error_data["company_responsible_person_msg"] = $this->lang->line("company_responsible_person_blank");
            $is_company = "yes";
          }else{
            $company_name = "";
            $company_vat_number = "";
            $company_street = "";
            $company_town = "";
            $company_zipcode = "";
            $company_country = "";
            $company_responsible_person ="";
            $is_company = "no"; 
          }

          if(!array_key_exists("email_msg", $error_data)){
                $checkemail      = $this->db->get_where("client",array("username" => $email))->num_rows();
                $error_dataemail =  $checkemail > 0 ? $error_data["email_msg"] = $this->lang->line("email_exist") : '';
           }
          if(count($error_data) > 0 ){
                echo json_encode(array("status" => false,"error_data" => $error_data ));
          }else{
                $data = array(
                    "fname" => $fname,
                    "lname" => $lname,
                    "phone" => $phone,
                    "email" => $email,
                    "address" => $address,
                    "city" => $city,
                    "zipcode" => $zipcode, 
                    "pass_text" => base64_encode($password),
                    "password" => password_hash($password, PASSWORD_DEFAULT),
                    "status" => "active",
                    "username" => $email,
                    "role_id" => 2,
                    "company_name" => $company_name,
                    "company_vat_number" => $company_vat_number,
                    "company_street" => $company_street,
                    "company_town" => $company_town,
                    "company_zipcode" => $company_zipcode,
                    "company_country" => $company_country,
                    "company_responsible_person" => $company_responsible_person,
                    "is_company" => $is_company                          
                );

                $this->db->insert("client",$data);
                $user_id = $this->db->insert_id();
				
				//adding auth key
				$authKeyData['client_id'] = $user_id;
				$authKeyData['domain_url'] = $header_data['Url'];
				$this->db->insert("api_keys",$authKeyData);
				
				
                $plan_id = $jsondata->plan_id;
                $getplan_details = $this->db->get_where("plans", array("id" => $plan_id))->row();
                $paypalInfo = array(
                              "payer_email" => $email,
                              "payer_id" => $user_id,
                              "payer_status" => "verified",
                              "first_name" => $fname,
                              "last_name" => $lname,
                              "address_name" => $address,
                              "address_city" => $city,
                              "address_state" => "",
                              "address_country_code" => "GER",
                              "address_zip" => $zipcode,
                              "residence_country" => 'germany',
                              "txn_id" => $user_id."_".$plan_id,
                              "mc_currency" => "EUR",
                              "mc_gross" => $getplan_details->price,
                              "txn_type" => "api_accept",
                              "payment_date" => date("Y-m-d H:i:s"),
                              "user_id" => $user_id,
                              "plan_id" => $plan_id
                            );
                    $insinvoice = $this->db->insert("invoice_details", $paypalInfo);
                    $invoice_id = $this->db->insert_id();

                    if($insinvoice){
                        $get_setting = $this->db->query("select * from site_setting where setting_id IN(7,8)")->result();
                        $time_period = $getplan_details->time_period;
                        $period = $getplan_details->period;
                        $expiry_date = date('Y-m-d', strtotime("+" . $time_period . " " . $period . ""));
                        $this->db->where("client_id", $user_id)->update("client", ["status" => "active"]);
                        $dt = array("user_id" => $user_id, "plan_id" => $plan_id, "plandata" => json_encode($getplan_details), "payment_info" => json_encode($paypalInfo), "start_date" => date("Y-m-d"), "expiry_date" => $expiry_date, "payment_status" => "success", "status" => "active", "invoice_id" => $invoice_id);
                        $ss = $this->db->insert("subscription_details", $dt);
                        //SEND EMAIL SUBSCRIPTION
                        $client_id = $user_id;
                        $plan_id = $plan_id;
                        $client_ = $this->db->get_where('client', ['client_id' => $user_id])->row();
                        $plan_ = $this->db->get_where('plans', ['id' => $plan_id])->row();
                        if ($plan_->time_period > 0 && $plan_->period != "") {
                            $time_period = $plan_->time_period;
                            $period = $plan_->period;
                            $expiry_date_ = date('d-m-Y', strtotime("+" . $time_period . " " . $period . ""));
                        } else {
                            $expiry_date_ = 'NA';
                        }
                        $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/' . $plan_->icon) : '';
                        $email_data = ['user_name' => $client_->fname . " " . $client_->lname, 'plan_name' => $plan_->name, 'expiry_date' => $expiry_date_, 'plan_description' => $plan_->description, 'plan_ftp_space_limit' => $plan_->ftp_space_limit . $plan_->ftp_unit, 'plan_db_space_limit' => $plan_->sql_space_limit . $plan_->db_unit, 'plan_time_period' => $plan_->time_period . $plan_->period, 'plan_price_monthly' => $plan_->price, 'plan_icon' => '<img src="' . $plan_icon . '" id="img_home" width="50" height="50">', ];
                        sendMail($client_->email, 'PLAN_SUBSCRIPTION_EMAIL', $email_data);
                        sendMail($client_->email, 'PAYMENT_SUCCESS', [
                                      'user_name' => $client_->fname . " " . $client_->lname, 
                                      'amount' => $paypalInfo["mc_gross"], 
                                      'currency' => $paypalInfo["mc_currency"], 
                                    ]);
                        $dtstorage = array("ftp_storage" => $getplan_details->ftp_space_bytes, "db_storage" => $getplan_details->db_space_bytes, "user_id" => $user_id, "added_date" => date("Y-m-d"), "plan_id" => $getplan_details->id);
                        $ss = $this->db->insert("client_storage", $dtstorage);
                        if($ss){
                                $user_data = $this->db->query("select c.*,cs.ftp_storage,cs.db_storage,sd.expiry_date,sd.plandata,sd.status from client c inner join subscription_details sd on sd.user_id = c.client_id AND sd.status = 'active' inner join client_storage cs on c.client_id = cs.user_id AND cs.mode = 'client' where client_id = ".$user_id." ")->row_array();

                                $user_data["plandata"]  = json_decode($user_data["plandata"]);
                                echo json_encode(array("status" => true,"msg" => "User registered Successfully","user_data" => $user_data)); 
                        }
                    }

          }

    }
}
