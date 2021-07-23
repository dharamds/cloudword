<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pricing extends CI_Controller {

	function  __construct(){
    parent::__construct();  
    $this->load->library('paypal_lib');
    $this->load->helper(array('form','url'));
    $this->load->library('form_validation');
  }
  public function index()
  {
   $data["currency"]= $this->db->query("select (select currency_symbol from currencies where code = ss.name_value) as currency_symbol from site_setting ss where ss.setting_id = 15")->row()->currency_symbol; 
   $data["plans"] = $this->db->get_where("plans",array("active" => 1,"is_deleted" => 0,"created_by" => 'admin'))->result();		
   $this->load->view("pricing",$data);
 }
 public function subscribe($plan_id){
   $plan_id = base64_decode($plan_id);
   $data["plan_id"] = $plan_id;
   $data["currency"]= $this->db->query("select (select currency_symbol from currencies where code = ss.name_value) as currency_symbol from site_setting ss where ss.setting_id = 15")->row()->currency_symbol; 
   $data["plandata"] = $this->db->get_where("plans",array("id" => $plan_id))->row_array();	
   $data["data"] = $this->input->post();
   $data["error_data"] = array();
   $expiry_date = '';
   if($data["plandata"]["time_period"] > 0 && $data["plandata"]["period"] != ""){
      $time_period = $data["plandata"]["time_period"];
      $period = $data["plandata"]["period"];
      $expiry_date = date('d-m-Y', strtotime("+".$time_period." ".$period.""));
   }
   $data["expiry_date"] = $expiry_date;
   $this->load->view("subscribe",$data);
 }
 public function buy($plan_id){
        // Set variables for paypal form
  $error_data = array();
  if(trim($this->input->post("f_name")) == ''){
    $error_data["f_name"] = $this->lang->line("f_name_blank");
  }else{
    $fname = $this->input->post("f_name");
  }

  if(trim($this->input->post("l_name")) == ''){
    $error_data["l_name"] = $this->lang->line("l_name_blank");
  }else{
    $lname = $this->input->post("l_name");
  }

  if(trim($this->input->post("phone")) == ''){
    $error_data["phone"] = $this->lang->line("phone_blank");
  }else{
    $phone = $this->input->post("phone");
  }

  if(trim($this->input->post("email")) == ''){
    $error_data["email"] = $this->lang->line("email_blank");
  }else{
    $email = $this->input->post("email");
  }

  if(trim($this->input->post("address")) == ''){
    $error_data["address"] = $this->lang->line("address_blank");
  }else{
    $address = $this->input->post("address");
  }

  if(trim($this->input->post("city")) == ''){
    $error_data["city"] = $this->lang->line("city_blank");
  }else{
    $city = $this->input->post("city");
  }

  if(trim($this->input->post("zipcode")) == ''){
    $error_data["zipcode"] = $this->lang->line("zipcode_blank");
  }else{
    $zipcode = $this->input->post("zipcode");
  }

  if(trim($this->input->post("password")) == ''){
    $error_data["password"] = $this->lang->line("password_blank");
  }else{
    $password = $this->input->post("password");
  }

  if(trim($this->input->post("cpassword")) == ''){
    $error_data["cpassword"] = $this->lang->line("cpassword_blank");
  }else{
    $cpassword = $this->input->post("cpassword");
  }


  if(empty($error_data["email"])){
  	$checkemail = $this->db->get_where("client",array("username" => $email))->num_rows();
    if($checkemail > 0){
      $error_data["email"] = $this->lang->line("email_exist");
    }
  }
  if(count($error_data) > 0){
    $data["plan_id"] = $plan_id;
    $data["error_data"] = $error_data;
    $data["plandata"] = $this->db->get_where("plans",array("id" => $plan_id))->row_array();
    $data["data"] = $this->input->post();
    $data["currency"]= $this->db->query("select (select currency_symbol from currencies where code = ss.name_value) as currency_symbol from site_setting ss where ss.setting_id = 15")->row()->currency_symbol; 
     $expiry_date = '';
     if($data["plandata"]["time_period"] > 0 && $data["plandata"]["period"] != ""){
        $time_period = $data["plandata"]["time_period"];
        $period = $data["plandata"]["period"];
        $expiry_date = date('d-m-Y', strtotime("+".$time_period." ".$period.""));
     }
     $data["expiry_date"] = $expiry_date;
  	 $this->load->view("subscribe",$data);
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
                "status" => "deactive",
                "username" => $email,
                "role_id" => 2                           
        );

  //if is company checked
  if($this->input->post('check_company') == 1){
    $data = array_merge($data, ['is_company' => 1]);
    if(trim($this->input->post("company_name")) != ''){ $data = array_merge($data, ['company_name' => $this->input->post("company_name")]); }
    if(trim($this->input->post("company_vat_number")) != ''){ $data = array_merge($data, ['company_vat_number' => $this->input->post("company_vat_number")]); }
    if(trim($this->input->post("company_street")) != ''){ $data = array_merge($data, ['company_street' => $this->input->post("company_street")]); }
    if(trim($this->input->post("company_town")) != ''){ $data = array_merge($data, ['company_town' => $this->input->post("company_town")]); }
    if(trim($this->input->post("company_zipcode")) != ''){ $data = array_merge($data, ['company_zipcode' => $this->input->post("company_zipcode")]); }
    if(trim($this->input->post("company_country")) != ''){ $data = array_merge($data, ['company_country' => $this->input->post("company_country")]); }
  }
  $insreg = $this->db->insert("client",$data);
  if($insreg){

        $postdata = $this->input->post();
        $id = $this->db->insert_id();
     
        $returnURL = base_url().'paypal/success/'.$plan_id;
        $cancelURL = base_url().'paypal/cancel/'.$id;
        $notifyURL = base_url().'paypal/ipn';
        $plandata = $this->db->get_where("plans",array("id" => $plan_id))->row();
        $planprice=$plandata->price;

       
        /*EMAIL TO USER*/
        $email_data = [
            'user_name' => $fname." ".$lname,
            'email' => $email,
            'username' => $email,
            "phone" => $phone,
            "address" => $address,
            "city" => $city,
            "zipcode" => $zipcode, 
        ];

    if($this->input->post('check_company') == 1){
      
      $email_data['company_name'] = $this->input->post("company_name") != '' ? $this->input->post("company_name") : ''; 
      $email_data['company_vat_number'] = $this->input->post("company_vat_number") != '' ? $this->input->post("company_vat_number") : ''; 
      $email_data['company_street'] = $this->input->post("company_street") != '' ? $this->input->post("company_street") : ''; 
      $email_data['company_town'] = $this->input->post("company_town") != '' ? $this->input->post("company_town") : ''; 
      $email_data['company_zipcode'] = $this->input->post("company_zipcode") != '' ? $this->input->post("company_zipcode") : ''; 
      $email_data['company_country'] = $this->input->post("company_country") != '' ? $this->input->post("company_country") : ''; 

    }

    sendMail($email, 'USER_REGISTRATION', $email_data); 

       // Add fields to paypal form
    if( $this->input->post('submitaction') == 2){

                $user_id = $id;
                $plan_id = $plandata->id;
                $getplan_details = $this->db->get_where("plans", array("id" => $plan_id))->row();
                $get_setting = $this->db->query("select * from site_setting where setting_id IN(7,8)")->result();
                $time_period = $getplan_details->time_period;
                $period = $getplan_details->period;
                $expiry_date = date('Y-m-d', strtotime("+" . $time_period . " " . $period . ""));
                $cash_advance_expiry_date = date('Y-m-d', strtotime("+2 days"));
                $dt = array(
                  "user_id" => $id,
                  "plan_id" => $plan_id,
                  "plandata" => json_encode($plandata),
                  "start_date" => date("Y-m-d"),
                  "expiry_date" => $expiry_date,
                  "payment_info" => '',
                  "payment_status" => "pending",
                  "status" => "deactive",
                  "invoice_id" => 0,
                
                  );

                $ss1 = $this->db->insert("subscription_details", $dt);
                $ss1_id = $this->db->insert_id();
                $this->paypal_lib->add_field('return', $returnURL);
                $this->paypal_lib->add_field('cancel_return', $cancelURL);
                $this->paypal_lib->add_field('notify_url', $notifyURL);
                $this->paypal_lib->add_field('item_name', $plandata->name);
                $this->paypal_lib->add_field('custom', $id.'_'.$plandata->id.'_'.$ss1_id);
                $this->paypal_lib->add_field('user_id', $id);
                $this->paypal_lib->add_field('plan_id', $plandata->id);
                $this->paypal_lib->add_field('item_number', $ss1_id);
                $this->paypal_lib->add_field('amount',  $planprice);
                $this->paypal_lib->add_field('first_name',  "kashish");
                $this->paypal_lib->paypal_auto_form();
  }else if($this->input->post('submitaction') == 1){
                $user_id = $id;
                $plan_id = $plandata->id;
                    $getplan_details = $this->db->get_where("plans", array("id" => $plan_id))->row();
                    $get_setting = $this->db->query("select * from site_setting where setting_id IN(7,8)")->result();
                    $time_period = $getplan_details->time_period;
                    $period = $getplan_details->period;
                    $expiry_date = date('Y-m-d', strtotime("+" . $time_period . " " . $period . ""));
                    $cash_advance_expiry_date = date('Y-m-d', strtotime("+2 days"));
                    $this->db->where("client_id", $user_id)->update("client", ["status" => "active"]);
                    $dt = array(
                                  "user_id" => $user_id,
                                  "plan_id" => $plan_id,
                                  "plandata" => json_encode($getplan_details),
                                  "payment_info" => '',
                                  "start_date" => date("Y-m-d"),
                                  "expiry_date" => $expiry_date,
                                  "payment_status" => "success",
                                  "status" => "active",
                                  "invoice_id" => 0,
                                  "cash_advance_flag" => 1,
                                  "cash_advance_expiry_date" => $cash_advance_expiry_date
                          );
                    $ss = $this->db->insert("subscription_details", $dt);
                    $dtstorage = array(
                                        "ftp_storage" => $getplan_details->ftp_space_bytes,
                                        "db_storage" => $getplan_details->db_space_bytes,
                                        "user_id" => $user_id,
                                        "added_date" => date("Y-m-d"),
                                        "plan_id" => $getplan_details->id
                                      );
                    $ss = $this->db->insert("client_storage", $dtstorage);
                    if($ss) {
                        $cc = $this->db->get_where("client", array("client_id" => $user_id))->row();
                        $link = base_url() . "client/login";
                        $getdatabyusernamer = $this->db->query("select c.*,(select role_name from roles where role_id = c.role_id) as role_type from client as c where client_id = " . $user_id . " AND  c.role_id = 2 ");
                        $getuserregtemplate = $this->db->query("select * from email_templates where id = 25 ")->row();
                        $usrdata = $getdatabyusernamer->row();
                        $this->db->where("client_id", $user_id);
                        $this->db->update("client", ["status" => "active"]);
                        $this->load->view("cash_on_advance_success");
                    }
  }

  }else{
    $d["msg"] = $this->lang->line("something_wrong");
    $this->load->view("subscribe",$d);
  }
}

}


public function save_subscription_data($client_id,$postdata, $plandata){
  return true;
}



public function test(){
  echo $this->lang->line("test")."ss";
}
}
