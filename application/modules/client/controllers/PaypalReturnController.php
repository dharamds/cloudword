<?php
class PaypalReturnController extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->library("general");
    }

    public function success(){
        
        
        $paypalInfo = $this->input->post();

        $user_id = $paypalInfo["custom"];

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
                'plan_time_period' => $plan_->time_period.$plan_->period,
                'plan_price_monthly' => $plan_->price,
                'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
            ];

            sendMail($client_->email, 'PLAN_SUBSCRIPTION_EMAIL', $email_data);
            //sendMail('swapnildanwe23@gmail.com', 'PLAN_SUBSCRIPTION_EMAIL', $email_data);
            //sendMail('testerswap@mailinator.com', 'PLAN_SUBSCRIPTION_EMAIL', $email_data);




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
                  /* $this->session->set_userdata('plan_id', $getplan_details->id);
                   $this->session->set_userdata('plan_name', $getplan_details->name);
                   $this->session->set_userdata('expiry_date', $expiry_date);
                   $this->session->set_userdata('sub_id', $subscription_id);
                   $this->session->set_userdata('plansubcribed', 1);*/
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