<?php
class PaypalReturn extends MX_Controller {
    function __construct() {
        parent::__construct();
        $this->load->library("general");
        $this->load->library('paypal_lib');
		ini_set("display_errros", "Off");
    }
    public function success() {
        $paypalInfo = $_REQUEST;
		
        $user_id = $this->session->userdata("user_id");
        $this->session->sess_destroy();
        $data["page"] = "dashboard";
        $data["paypalInfo"] = $paypalInfo;
        $this->load->view("client/payment_success", $data);
    }
    function cancel($id = NULL) {
        $data["page"] = "dashboard";
        $this->load->view("client/payment_cancled", $data);
    }
    function ipn() {
        // Paypal posts the transaction data
        $paypalInfo = $this->input->post();
		$this->db->insert("ipndata", ["data" => json_encode($paypalInfo) ]); 
        if (!empty($paypalInfo)) {
            // Validate and get the ipn response
            $ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);
            // Check whether the transaction is valid
            if($ipnCheck){
                // Insert the transaction data in the database
                 if($paypalInfo["payment_status"]){
                    $invoice_details = $this->db->get_where("invoice_details", array("txn_id" => $paypalInfo["txn_id"]))->num_rows();
                    if($invoice_details == 0){
                    list($user_id, $plan_id, $sub_id) = explode('_', $paypalInfo["custom"]);

                    $invoice_status = ($paypalInfo["payment_status"] === 'Completed') ? 'paid' : 'unpaid';
                    $data = array(
                                 "payer_email" => $paypalInfo["payer_email"],
                                 "payer_id" => $paypalInfo["payer_id"],
                                 "payer_status" => $paypalInfo["payer_status"],
                                 "first_name" => $paypalInfo["first_name"],
                                 "last_name" => $paypalInfo["last_name"],
                                 "address_name" => $paypalInfo["address_name"],
                                 "address_street" => $paypalInfo["address_street"],
                                 "address_city" => $paypalInfo["address_city"],
                                 "address_state" => $paypalInfo["address_state"],
                                 "address_country_code" => $paypalInfo["address_country_code"],
                                 "address_zip" => $paypalInfo["address_zip"],
                                 "residence_country" => $paypalInfo["residence_country"],
                                 "txn_id" => $paypalInfo["txn_id"],
                                 "mc_currency" => $paypalInfo["mc_currency"],
                                 "mc_gross" => $paypalInfo["mc_gross"],
                                 "txn_type" => $paypalInfo["txn_type"],
                                 "payment_date" => $paypalInfo["payment_date"],
                                 "user_id" 		=> $user_id,
                                 "plan_id" 		=> $plan_id,
                                 "client_invoice" 	=> $invoice_status
                             );
                      
                        //end shree code            
                    $insinvoice = $this->db->insert("invoice_details", $data);
                    $invid = $this->db->insert_id();
                    if ($invid > 0) {
                        
                        $getplan_details = $this->db->get_where("plans", array("id" => $plan_id))->row();
                        $time_period = $getplan_details->time_period;
                        $period = $getplan_details->period;
                        $expiry_date = date('Y-m-d', strtotime("+" . $time_period . " " . $period . ""));
                        $status = ($paypalInfo["payment_status"] === 'Completed') ? 'active' : 'deactive';
                       // $this->db->where("client_id", $user_id)->update("client", ["status" => $status]);
                        $payment_status = ($paypalInfo["payment_status"] === 'Completed') ? 'success' : $paypalInfo["payment_status"];
                    
                        $dt = array("user_id" => $user_id, "plan_id" => $plan_id, "plandata" => json_encode($getplan_details), "payment_info" => json_encode($paypalInfo), "start_date" => date("Y-m-d"), "expiry_date" => $expiry_date, "payment_status" => $payment_status, "status" => $status,"invoice_id" => $invid);
                         //shree code                 
                        $ss = $this->db->where("sub_id", $sub_id)->update("subscription_details", $dt);
                        //end shree code    
                        //SEND SUBSCRIBED EMAIL
                        $client_id = $user_id;
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
                        $email_data2 = [
                                        'user_name' => $client_->fname . " " . $client_->lname, 
                                        'amount' => $paypalInfo["mc_gross"], 
                                        'currency' => $paypalInfo["mc_currency"], 
                                    ];
                        sendMail($client_->email, 'PAYMENT_SUCCESS', $email_data2);
                        //to deactivate all old subcription
                        if ($subscription_id != '') {
                            $this->db->where("user_id", $user_id);
                            $this->db->where("sub_id !=", $sub_id);
                            $this->db->update("subscription_details", ["status" => "deactive"]);
                        }
                        //to update storage as per new plan.
                        $gestore = $this->db->get_where('client_storage', ['user_id' => $user_id,"mode" => 'client'])->row();
                        if($gestore->num_rows()){
                            $dtstorage = array("ftp_storage" => $getplan_details->ftp_space_bytes, "db_storage" => $getplan_details->db_space_bytes, "added_date" => date("Y-m-d"), "plan_id" => $getplan_details->id);
                            $this->db->where("user_id", $user_id);
                            $this->db->where("mode", 'client');
                        $ss = $this->db->update("client_storage", $dtstorage);
                        }else{
                            $dtstorage = array("ftp_storage" => $getplan_details->ftp_space_bytes, "db_storage" => $getplan_details->db_space_bytes,"user_id"=> $user_id, "added_date" => date("Y-m-d"), "plan_id" => $getplan_details->id);
                            $this->db->insert("client_storage",$dtstorage);
                        }
						
                        //need to update session
                        // if ($ss) {
                        //     $this->session->set_userdata('plan_id', $getplan_details->id);
                        //     $this->session->set_userdata('plan_name', $getplan_details->name);
                        //     $this->session->set_userdata('expiry_date', $expiry_date);
                        //     $this->session->set_userdata('sub_id', $subscription_id);
                        //     $this->session->set_userdata('plansubcribed', 1);
                        // }
                    }

                }

                }
            }
        }
    }
}
