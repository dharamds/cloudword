<?php
class Dashboard extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->library("general");
        if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "admin" && $this->session->userdata("user_id") != ""){
            redirect(base_url()."client/login");
        }
        ini_set('display_errors', 'Off');
    }
    public function index(){
     

        $user_id = $this->session->userdata("user_id");
        $data["user_count"] = $this->db->get_where("client",array("role_id" => 2,"parent_id" => $user_id))->num_rows();
        $getallprojects = $this->db->query("select p.* from project p where p.client_id = ".$user_id."");
        $data["ftp_used"] = $this->db->query("select sum(total_size) as ftpsize from backupftp where client_id = ".$user_id." ")->row()->ftpsize;
        $data["db_used"] = $this->db->query("select sum(total_size) as dbsize from backupsql where client_id = ".$user_id." ")->row()->dbsize;
		
		$getstorage =  $this->db->get_where("client_storage",array("user_id" => $user_id))->row();
		$data["ftp_unused"] =isset($getstorage->ftp_storage) ? $getstorage->ftp_storage : "";
		$data["db_unused"] = isset($getstorage->db_storage) ?$getstorage->db_storage : "" ;
        $data["project_count"] = $getallprojects->num_rows();
        $data["page"]      = "dashboard";
        $data["ftp_count"] = $this->db->get_where("backupftp",array("client_id" => $user_id))->num_rows();
        $data["sql_count"] = $this->db->get_where("backupsql",array("client_id" => $user_id))->num_rows(); 
        $data['isplanactive'] = $ifplanexpire = $this->general->check_if_plan_expire($user_id );
      //  $ifplanexpire = $this->general->check_if_plan_expire();
        $ifplanexpirecss = '';
        if($ifplanexpire == 'expired' || $ifplanexpire == 'noplansubcribed'){ $ifplanexpirecss = '0';}else{ $ifplanexpirecss = 1;}
        $allowmodule = $this->general->check_for_allow_module();
        $data['ifplanexpirecss']= $ifplanexpirecss;
        $data['allowmodule']= $allowmodule;

        // echo '<pre>';print_r( $ifplanexpire);exit;
    	$this->load->view("client/dashboard_new",$data);
    }
    public function getsizes(){
        header('Content-type: application/json');
            $user_id = $this->session->userdata("user_id");
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
            
           /* $ftpper = 92;
            $dbper = 92;*/

            $request_btn_for_space_update_db ='';
            $request_btn_for_space_update_ftp = '';

            if($ftpper > 90 ){
                $ftpwarning = "<strong style='color:red;font-size:15px;'> ".$this->lang->line("disk_usage")."(".round($ftpper,3)."%)</strong>";

                if ($this->session->userdata("role_type") == 'reseller') {
                   $request_btn_for_space_update_ftp = "<button class='btn btn-warning' id='request_update'> ".$this->lang->line("request_space_update")."</button>";
                }

            }else{
                $ftpwarning = "<strong style='color:green;font-size:15px;'> ".$this->lang->line("disk_usage")."(".round($ftpper,3)."%)</strong>";
            } 

            if($dbper > 90 ){
                $dbwarning = "<strong style='color:red;font-size:15px;'> ".$this->lang->line("disk_usage")."(".round($dbper,3)."%)</strong>";

                if ($this->session->userdata("role_type") == 'reseller') {
                    $request_btn_for_space_update_db = "<button class='btn btn-warning' id='request_update'> ".$this->lang->line("request_space_update")."</button>";
                }

            }else{
                $dbwarning = "<strong style='color:green;font-size:15px;'> ".$this->lang->line("disk_usage")."(".round($dbper,3)."%)</strong>";
            } 

            echo json_encode(
                array(
                    "ftpsize" => $this->format_size($ftpsize),
                    "sqlsize" => $this->format_size($sqlsize),
                    "ftp_percent" => $ftpper,
                    "db_percent" => $dbper,
                    "ftpmsg" => $ftpwarning,
                    "dbmsg" => $dbwarning,
                    "request_btn_for_space_update_ftp" => $request_btn_for_space_update_ftp,
                    "request_btn_for_space_update_db" => $request_btn_for_space_update_db,
                )
            );
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
            $bytes = $size;
            $bytes /= 1024;
            if ($bytes >= 1024 * 1024) {
                $bytes /= 1024;
               return number_format($bytes / 1024, 1) . ' GB';
            } elseif($bytes >= 1024 && $bytes < 1024 * 1024) {
               return number_format($bytes / 1024, 1) . ' MB';
            } else {
               return number_format($bytes, 1) . ' KB';
            }
    }



    public function client_plan($plan_id = NULL){
        $user_id = $this->session->userdata("user_id");
        $data["plan_data"] = $this->db->query("select * from plans p where p.id = ".$plan_id."")->row();
        $data['client_storage'] =  $this->db->query("select * from client_storage cs where cs.user_id = ".$user_id."")->row();
        $data['subscription_details'] =  $this->db->query("select * from subscription_details sd where sd.user_id = ".$user_id." AND sd.status = 'active' ORDER BY sd.sub_id DESC")->row_array();
        $data["ftp_storage"] = $this->format_size($data['client_storage']->ftp_storage);
        $data["db_storage"] = $this->format_size($data['client_storage']->db_storage);
        $data["page"]      = "dashboard";

        $getinv = $this->db->query("select * from invoice_details id where invoice_id = ".$data['subscription_details']['invoice_id']." ")->result_array();
        if(count($getinv) > 0 && $getinv[0]["payment_type"] == "offline"){
           
             $data['payment_info'] = $getinv[0];
        }else{
             $data['payment_info'] = json_decode($data['subscription_details']['payment_info'], 1);
        }
        $data['plandata'] = json_decode($data['subscription_details']['plandata'], 1);
       
        $data['isplanactive'] = $this->general->check_if_plan_expire();
        
        //echo '<pre>';
        //print_r( $data['plandata'] );
        //print_r( $data['payment_info'] );
        //exit;


        $this->load->view("client/client_plan",$data);
    }
    public function change_language(){
    	$langg = $this->input->post("langvar");
    	$user_id = $this->session->userdata("user_id");
    	$newdata = array(
        'lang'  => $langg
    	);
		 $this->session->set_userdata($newdata);
		
		
			echo json_encode(array("status" => "success"));
		
    }
    public function requestreseller(){
        $user_id = $this->session->userdata("user_id");
        $getreseller = $this->db->get_where("reseller_request",array("user_id" => $user_id));
        if($getreseller->num_rows() == 0){
                if($this->db->insert("reseller_request",["user_id" => $user_id]) ){
                    $data["msg"] = $this->lang->line("reseller_success");
                    $data["status"] = $this->lang->line("success");
                    $data["color"] = "green";
                }else{
                    $data["msg"] = $this->lang->line("something_wrong");
                    $data["status"] = $this->lang->line("failed");
                    $data["color"] = "red";
                }
        }else{
            $data["msg"] = $this->lang->line("reseller_already");
            $data["status"] = $this->lang->line("already_sent");
            $data["color"] = "orange";
        }
        $data["page"]      = "dashboard";
        $this->load->view("client/dashboard/resellerrequest",$data);

    }


    public function view_plan(){
        $user_id = $this->session->userdata("user_id");
        $parent_id = $this->session->userdata("parent_id");
        $role_by = $this->session->userdata("role_by");
        $data["currency"]= $this->db->query("select (select currency_symbol from currencies where code = ss.name_value) as currency_symbol from site_setting ss where ss.setting_id = 15")->row()->currency_symbol; 
        
        if($role_by == 3){
            $data["plans"] = $this->db->get_where("plans",array("active" => 1,"is_deleted!="=>1, "created_by" => "reseller","user_id" => $parent_id))->result();     
        }else{
            $data["plans"] = $this->db->get_where("plans",array("active" => 1,"is_deleted!="=>1, "created_by" => "admin"))->result();     
        }
        //echo "<pre>";
        //echo $parent_id;die();
        $data['isplanactive'] = $this->general->check_if_plan_expire();
        
        //echo '<pre>';
        //print_r( $data['plandata'] );
        //print_r( $data['payment_info'] );
        //exit;

        $data["page"] = "dashboard";
        $this->load->view("client/view_plan",$data);
    }


    
     public function renew_subscription($plan_id){
        
        $plan_id = base64_decode($plan_id);
        $data["plandata"] = $this->db->get_where("plans",array("id" => $plan_id))->row();
        $data["currency"]= $this->db->query("select (select currency_symbol from currencies where code = ss.name_value) as currency_symbol from site_setting ss where ss.setting_id = 15")->row()->currency_symbol; 
        
         $expiry_date = '';
         if($data["plandata"]->time_period > 0 && $data["plandata"]->period != ""){
            $time_period = $data["plandata"]->time_period;
            $period = $data["plandata"]->period;
            $expiry_date = date('d-m-Y', strtotime("+".$time_period." ".$period.""));
         }
         $data["expiry_date"] = $expiry_date;

        //echo '<pre>';
        //print_r( $this->session );
        //exit;
        
        $data["page"] = "dashboard";
        $this->load->view("client/renew_subscription",$data);
    }


    public function buyplan(){

        $postdata = $this->input->post();

        if($postdata['plan_id'] != '' && $postdata['price'] != ''){

            $this->load->library('paypal_lib');
            $user_id = $this->session->userdata("user_id");
            $plan_id = $this->input->post("plan_id");
            //$plan_type = $this->input->post("plan_type");
             $plan_type = "";

            $id = $user_id;
            $returnURL = base_url().'client/paypalReturn/success/';
            $cancelURL = base_url().'client/paypalReturn/cancel/';
            $notifyURL = base_url().'client/paypalReturn/ipn';
            $plandata = $this->db->get_where("plans",array("id" => $plan_id))->row();
            $planprice=$plandata->price;

            $plan_id = $plandata->id;
            $getplan_details = $this->db->get_where("plans", array("id" => $plan_id))->row();
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
            // if($plan_type == 'annually' ){ $planprice=$plandata->price*12; 
            // }elseif($plan_type == 'monthly'){ $planprice=$plandata->price; }

            // Add fields to paypal form
            $this->paypal_lib->add_field('return', $returnURL);
            $this->paypal_lib->add_field('cancel_return', $cancelURL);
            $this->paypal_lib->add_field('notify_url', $notifyURL);
            $this->paypal_lib->add_field('item_name', $plandata->name);
            $this->paypal_lib->add_field('custom', $id.'_'.$plan_id.'_'.$ss1_id);
            $this->paypal_lib->add_field('user_id', $id);
            $this->paypal_lib->add_field('plan_id', $plandata->id);
            $this->paypal_lib->add_field('item_number',  $ss1_id);
            $this->paypal_lib->add_field('amount',  $planprice);
            
            //$this->paypal_lib->add_field('on0', $plan_type);
            //$this->paypal_lib->add_field('plan_type', $plan_type);
            // Render paypal form
            $this->paypal_lib->paypal_auto_form();
            
        }
    
    }


    public function success(){
        
        // Get the transaction data
        //$queries = [];
        //parse_str($_SERVER['QUERY_STRING'], $queries); print_r($queries);
        //exit;
        $user_id = $this->session->userdata("user_id");
        $paypalInfo = $this->input->post();

         // echo '<pre>';
         // print_r($paypalInfo);
         // die();
        
         if(empty($paypalInfo["item_number"])){
            $paypalInfo["item_number"] = $paypalInfo["item_number1"];
         }
        

        $data = array(
                            "payer_email" =>$paypalInfo["payer_email"], 
                            "payer_id" =>$paypalInfo["payer_id"], 
                            "payer_status" =>$paypalInfo["payer_status"], 
                            "first_name" =>$paypalInfo["first_name"], 
                            "last_name" => $paypalInfo["last_name"], 
                            "address_name" =>  $paypalInfo["address_name"], 
                            "address_street" =>$paypalInfo["address_street"], 
                            "address_city" =>  $paypalInfo["address_city"], 
                            "address_state" => $paypalInfo["address_state"], 
                            "address_country_code" =>  $paypalInfo["address_country_code"], 
                            "address_zip" =>   $paypalInfo["address_zip"], 
                            "residence_country" => $paypalInfo["residence_country"], 
                            "txn_id" =>$paypalInfo["txn_id"], 
                            "mc_currency" =>   $paypalInfo["mc_currency"], 
                            "mc_gross" =>  $paypalInfo["mc_gross"], 
                            "protection_eligibility" =>$paypalInfo["protection_eligibility"], 
                            "payment_gross" => $paypalInfo["payment_gross"], 
                            "payment_status" =>$paypalInfo["payment_status"], 
                            "pending_reason" =>$paypalInfo["pending_reason"], 
                            "payment_type" =>  $paypalInfo["payment_type"], 
                            "handling_amount" =>   $paypalInfo["handling_amount"], 
                            "shipping" =>  $paypalInfo["shipping"], 
                            "quantity" => 1, 
                            "mc_gross_" =>$paypalInfo["mc_gross"], 
                             "txn_type" =>  $paypalInfo["txn_type"], 
                             "payment_date" =>  $paypalInfo["payment_date"], 
                             "verify_sign" =>   $paypalInfo["verify_sign"], 
                             "notify_version" => $paypalInfo["notify_version"],
                             "user_id" => $user_id,
                             "plan_id" =>$paypalInfo["item_number"]
                         );

        $insinvoice = $this->db->insert("invoice_details",$data);
        if($insinvoice){
            
            $getplan_details = $this->db->get_where("plans",array("id" =>$paypalInfo["item_number"]))->row();

            $time_period    = $getplan_details->time_period;
            $period         = $getplan_details->period;
            $expiry_date    = date('Y-m-d', strtotime("+".$time_period." ".$period.""));

            // if($paypalInfo["option_name1"] == 'annually' ){ 
            //     $expiry_date = date('Y-m-d', strtotime("+1 year"));
            // }elseif($paypalInfo["option_name1"] == 'monthly'){ 
            //     $expiry_date = date('Y-m-d', strtotime("+1 month"));
            // }
            

              $dt = array("user_id" =>$user_id,
                            //"user_id" =>$paypalInfo["custom"],
                          "plan_id" => $paypalInfo["item_number"],
                          //"plan_type" => $paypalInfo["option_name1"],
                          "plandata" => json_encode($getplan_details),
                          "payment_info" => json_encode($paypalInfo),
                          "start_date" => date("Y-m-d"),
                          "expiry_date"=>$expiry_date,
                          "payment_status" => "success",
                          "status" => "active"
                           );
             $ss = $this->db->insert("subscription_details",$dt);



            //SEND SUBSCRIBED EMAIL
            $client_id = $user_id;
            $plan_id = $paypalInfo["item_number"];

            $client_ = $this->db->get_where('client', ['client_id'=> $user_id ])->row();
            $plan_ = $this->db->get_where('plans', ['id'=> $plan_id ])->row();


            if($plan_->time_period > 0 && $plan_->period != ""){
                $time_period = $plan_->time_period;
                $period = $plan_->period;
                $expiry_date_ = date('d-m-Y', strtotime("+".$time_period." ".$period.""));
            }
            else{
                $expiry_date_ = 'NA';   
            }
            
            $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';

            $email_data = [
                'user_name' => $client_->fname." ".$client_->lname,
                'plan_name' => $plan_->name,
                'expiry_date' => $expiry_date_,
                'plan_description' => $plan_->description,
                'plan_ftp_space_limit' => $plan_->ftp_space_limit.$plan_->ftp_unit,
                'plan_db_space_limit' => $plan_->sql_space_limit.$plan_->db_unit,
                'plan_time_period' => $plan_->time_period,
                'plan_price_monthly' => $plan_->price,
                'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
            ];

            sendMail($client_->email, 'PLAN_SUBSCRIPTION_EMAIL', $email_data);


             //to deactivate all old subcription
             $subscription_id = $this->db->insert_id();
             if($subscription_id != ''){
                $this->db->where("user_id",$user_id);
                $this->db->where("sub_id !=",$subscription_id);
                $this->db->update("subscription_details",["status" => "deactive"]);
             }

             //to update storage as per new plan.
             $dtstorage = array(
                                 "ftp_storage" => $getplan_details->ftp_space_bytes,
                                 "db_storage" =>$getplan_details->db_space_bytes ,
                                 //"user_id" =>$paypalInfo["custom"] ,
                                 "user_id" =>$user_id ,
                                 "added_date" =>date("Y-m-d"),
                                 "plan_id" => $getplan_details->id
                               ); 
             $this->db->where("user_id",$user_id);
             $this->db->where("mode",'client');
             $ss = $this->db->update("client_storage",$dtstorage);


             //need to update session
             if($ss){
                   $this->session->set_userdata('plan_id', $getplan_details->id);
                   $this->session->set_userdata('plan_name', $getplan_details->name);
                   $this->session->set_userdata('expiry_date', $expiry_date);
                   $this->session->set_userdata('sub_id', $subscription_id);
                   $this->session->set_userdata('plansubcribed', 1);
             } 
              

        }

        $data["page"] = "dashboard";
        $this->load->view("client/payment_success",$data);
        //echo 'payment Done';
        //$this->load->view("success");

    }
     function cancel($id=NULL){
        // Load payment failed view
        $data["page"] = "dashboard";
        $this->load->view("client/payment_cancled",$data);

     }
     
     function ipn(){
        // Paypal posts the transaction data
        $paypalInfo = $this->input->post();
        if(!empty($paypalInfo)){
            // Validate and get the ipn response
            $ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);
            // Check whether the transaction is valid
            if($ipnCheck){
                // Insert the transaction data in the database
                $data['user_id']        = $paypalInfo["custom"];
                $data['product_id']        = $paypalInfo["item_number"];
                $data['txn_id']            = $paypalInfo["txn_id"];
                $data['payment_gross']    = $paypalInfo["mc_gross"];
                $data['currency_code']    = $paypalInfo["mc_currency"];
                $data['payer_email']    = $paypalInfo["payer_email"];
                $data['payment_status'] = $paypalInfo["payment_status"];

                $this->product->insertTransaction($data);
            }
        }
    }



}