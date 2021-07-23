<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paypal extends CI_Controller{
    
     function  __construct(){
        parent::__construct();  
        $this->load->library('paypal_lib');
     }
    function success(){
        // Get the transaction data
        $paypalInfo = $this->input->post();
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
                            "quantity" => $paypalInfo["quantity1"], 
                            "mc_gross_" =>$paypalInfo["mc_gross_1"], 
                            "num_cart_items" =>$paypalInfo["num_cart_items"], 
                             "txn_type" =>  $paypalInfo["txn_type"], 
                             "payment_date" =>  $paypalInfo["payment_date"], 
                             "verify_sign" =>   $paypalInfo["verify_sign"], 
                             "notify_version" => $paypalInfo["notify_version"],
                             "user_id" => $paypalInfo["custom"],
                             "plan_id" =>$paypalInfo["item_number1"]
                         );
        $insinvoice = $this->db->insert("invoice_details",$data);
        if($insinvoice){
                $getplan_details = $this->db->get_where("plans",array("id" =>$paypalInfo["item_number1"]))->row();

              $expiry_date = date('Y-m-d', strtotime("+".$getplan_details->expiry_days." days"));
              $dt = array("user_id" =>$paypalInfo["custom"],
                          "plan_id" => $paypalInfo["item_number1"],
                          "start_date" => date("Y-m-d"),
                          "expiry_date"=>$expiry_date,
                          "payment_status" => "success",
                          "status" => "active"
                           );
             $ss = $this->db->insert("subscription_details",$dt);
             $dtstorage = array(
                                 "ftp_storage" => $getplan_details->ftp_space_bytes,
                                 "db_storage" =>$getplan_details->db_space_bytes ,
                                 "user_id" =>$paypalInfo["custom"] ,
                                 "added_date" =>date("Y-m-d"),
                                 "plan_id" => $getplan_details->plan_id
                               ); 
             $ss = $this->db->insert("client_storage",$dt);
             if($ss){
                    $this->db->where("client_id",$paypalInfo["custom"]);
                    $this->db->update("client",["status" => "active"]);
             }  

        }else{

        }
    }
     function cancel($id=NULL){
        // Load payment failed view
       $paypalInfo = $this->input->post();
       $this->db->where("client_id",$id);
       if($this->db->delete("client")){
          redirect(base_url()."pricing");
       }
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