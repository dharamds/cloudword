<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cronemail extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
   public function plan_expiry(){
        $subscription_details  = $this->db->query("select * from subscription_details where status = 'active'")->result();
        $NewDate=Date('Y-m-d', strtotime('+3 days'));
        foreach($subscription_details as $sub) {
            if($sub->expiry_date == $NewDate){
                $get_user =$this->db->get_where("client",array("client_id" => $sub->user_id))->row();
                if(!empty($get_user)){    
                    $plansub = $this->db->get_where('subscription_details', ['user_id'=> $get_user->client_id, 'status' => 'active' ])->row();
                    $plan_ = $this->db->get_where('plans', ['id'=> $plansub->plan_id ])->row();

                    $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';
                    $email_data = [
                        'user_name' => $get_user->fname." ".$get_user->lname,
                        'plan_name' => isset($plan_->name) ? $plan_->name : '',
                        'expiry_date' => displayDate($sub->expiry_date,false),
                        'plan_description' => isset($plan_->description) ? $plan_->description : '',
                        'plan_ftp_space_limit' => isset($plan_->ftp_space_limit) ? $plan_->ftp_space_limit.$plan_->ftp_unit : '',
                        'plan_db_space_limit' => isset($plan_->sql_space_limit) ? $plan_->sql_space_limit.$plan_->db_unit : '',
                        'plan_time_period' => isset($plan_->time_period) ? $plan_->time_period : '',
                        'plan_price_monthly' => isset($plan_->price) ? $plan_->price : '',
                        'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
                    ];
                    sendMail($get_user->email, 'PLAN_EXPIRY_EMAILS_TO_CUSTOMER', $email_data);
                }
            }   

        }
   }
   public function lower_storage_mail(){
        $get_users = $this->db->query("select * from client where status = 'active' AND role_id IN(2,3)")->result();  
        foreach($get_users as $u){
             $get_storageftp = $this->db->query("select COALESCE(sum(total_size),0) as size from backupftp bf where bf.client_id = ".$u->client_id." ")->result();
             $get_storagedb = $this->db->query("select COALESCE(sum(total_size),0) as size from backupsql bf where bf.client_id = ".$u->client_id." ")->result();
             $get_client_storage =  $this->db->get_where("client_storage",array("user_id" => $u->client_id,"mode" => "client"))->row();
              if(!empty($get_client_storage) ){
                 $get_percent  = round(($get_storageftp[0]->size /$get_client_storage->ftp_storage) * 100);
                 $get_percentdb  = round(($get_storagedb[0]->size /$get_client_storage->db_storage) * 100);
                 	if($get_percent >= 95){
                        $plansub = $this->db->get_where('subscription_details', ['user_id'=> $u->client_id, 'status' => 'active' ])->row();
                        $plan_ = $this->db->get_where('plans', ['id'=> $plansub->plan_id ?? '' ])->row();
                        $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';
                        $email_data = [
                            'user_name' => $u->fname." ".$u->lname,
                            'db_percent' => 'NA',
                            'ftp_percent' => $get_percent."%",
                            'no_of_customers' => 'NA',
                            'plan_name' => isset($plan_->name) ? $plan_->name : '',
                            'expiry_date' => $plansub->expiry_date,
                            'plan_description' => isset($plan_->description) ? $plan_->description : '',
                            'plan_ftp_space_limit' => isset($plan_->ftp_space_limit) ? $plan_->ftp_space_limit.$plan_->ftp_unit : '',
                            'plan_db_space_limit' => isset($plan_->sql_space_limit) ? $plan_->sql_space_limit.$plan_->db_unit : '',
                            'plan_time_period' => isset($plan_->time_period) ? $plan_->time_period : '',
                            'plan_price_monthly' => isset($plan_->price) ? $plan_->price : '',
                            'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
                        ];
                       sendMail($u->email, 'LOW_STORAGE_SPACE_EMAIL_TO_CUSTOMER', $email_data);
                    }
                    if($get_percentdb >= 95){
                     	$plansub = $this->db->get_where('subscription_details', ['user_id'=> $u->client_id, 'status' => 'active' ])->row();
                        $plan_ = $this->db->get_where('plans', ['id'=> $plansub->plan_id ?? '' ])->row();
                        $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';
                         $email_data = [
                            'user_name' => $u->fname." ".$u->lname,
                            'db_percent' => $get_percentdb."%",
                            'ftp_percent' => 'NA',
                            'no_of_customers' => 'NA',
                            'plan_name' => isset($plan_->name) ? $plan_->name : '',
                            'expiry_date' => $plansub->expiry_date,
                            'plan_description' => isset($plan_->description) ? $plan_->description : '',
                            'plan_ftp_space_limit' => isset($plan_->ftp_space_limit) ? $plan_->ftp_space_limit.$plan_->ftp_unit : '',
                            'plan_db_space_limit' => isset($plan_->sql_space_limit) ? $plan_->sql_space_limit.$plan_->db_unit : '',
                            'plan_time_period' => isset($plan_->time_period) ? $plan_->time_period : '',
                            'plan_price_monthly' => isset($plan_->price) ? $plan_->price : '',
                            'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
                        ];
                        sendMail($u->email, 'LOW_STORAGE_SPACE_EMAIL_TO_CUSTOMER', $email_data);
                    }
                }
        }
    }

}