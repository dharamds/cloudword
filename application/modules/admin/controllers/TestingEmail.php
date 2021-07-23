<?php
class TestingEmail extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "client" && $this->session->userdata("user_id") != ""){
            redirect(base_url()."admin/login");
        }
    }

    public function index($param1 = '', $param2 = ''){


        /*PLAN_SUBSCRIPTION_EMAIL*/
        $client_id = 3;
        $plan_id = 7;

        $client_ = $this->db->get_where('client', ['client_id'=> $client_id ])->row();
        $plan_ = $this->db->get_where('plans', ['id'=> $plan_id ])->row();


        if($plan_->time_period > 0 && $plan_->period != ""){
            $time_period = $plan_->time_period;
            $period = $plan_->period;
            $expiry_date = date('d-m-Y', strtotime("+".$time_period." ".$period.""));
        }
        else{
            $expiry_date = 'NA';   
        }
        
        $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';

        $email_data = [
            'user_name' => $client_->fname." ".$client_->lname,
            'plan_name' => $plan_->name,
            'expiry_date' => $expiry_date,
            'plan_description' => $plan_->description,
            'plan_ftp_space_limit' => $plan_->ftp_space_limit.$plan_->ftp_unit,
            'plan_db_space_limit' => $plan_->sql_space_limit.$plan_->db_unit,
            'plan_time_period' => $plan_->time_period,
            'plan_price_monthly' => $plan_->price,
            'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
        ];

        sendMail('dstestemail@mailinator.com', 'PLAN_SUBSCRIPTION_EMAIL', $email_data);


        /* PLAN_EXPIRY_EMAILS_TO_CUSTOMER */
        $client_id = 3;
        $plan_id = 7;

        $client_ = $this->db->get_where('client', ['client_id'=> $client_id ])->row();
        $plan_ = $this->db->get_where('plans', ['id'=> $plan_id ])->row();

        if($plan_->time_period > 0 && $plan_->period != ""){
            $time_period = $plan_->time_period;
            $period = $plan_->period;
            $expiry_date = date('d-m-Y', strtotime("+".$time_period." ".$period.""));
        }
        else{
            $expiry_date = 'NA';   
        }
        
        $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';

        $email_data = [
            'user_name' => $client_->fname." ".$client_->lname,
            'plan_name' => $plan_->name,
            'expiry_date' => $expiry_date,
            'plan_description' => $plan_->description,
            'plan_ftp_space_limit' => $plan_->ftp_space_limit.$plan_->ftp_unit,
            'plan_db_space_limit' => $plan_->sql_space_limit.$plan_->db_unit,
            'plan_time_period' => $plan_->time_period,
            'plan_price_monthly' => $plan_->price,
            'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
        ];

        sendMail('dstestemail@mailinator.com', 'PLAN_EXPIRY_EMAILS_TO_CUSTOMER', $email_data);
        


        /* LOW_STORAGE_SPACE_EMAIL_TO_RESELLER */
        $client_id = 3;
        $plan_id = 7;

        $client_ = $this->db->get_where('client', ['client_id'=> $client_id ])->row();
        $plan_ = $this->db->get_where('plans', ['id'=> $plan_id ])->row();

        if($plan_->time_period > 0 && $plan_->period != ""){
            $time_period = $plan_->time_period;
            $period = $plan_->period;
            $expiry_date = date('d-m-Y', strtotime("+".$time_period." ".$period.""));
        }
        else{
            $expiry_date = 'NA';   
        }
        
        $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';

        $email_data = [
            'user_name' => $client_->fname." ".$client_->lname,
            'plan_name' => $plan_->name,
            'expiry_date' => $expiry_date,
            'plan_description' => $plan_->description,
            'plan_ftp_space_limit' => $plan_->ftp_space_limit.$plan_->ftp_unit,
            'plan_db_space_limit' => $plan_->sql_space_limit.$plan_->db_unit,
            'plan_time_period' => $plan_->time_period,
            'plan_price_monthly' => $plan_->price,
            'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
        ];

        sendMail('dstestemail@mailinator.com', 'LOW_STORAGE_SPACE_EMAIL_TO_RESELLER', $email_data);




        /* LOW_STORAGE_SPACE_EMAIL_TO_CUSTOMER */
        $client_id = 3;
        $plan_id = 7;

        $client_ = $this->db->get_where('client', ['client_id'=> $client_id ])->row();
        $plan_ = $this->db->get_where('plans', ['id'=> $plan_id ])->row();
        
        $getallprojects = $this->db->get_where("project",array("client_id" => $client_id))->result();
        $getstorage =  $this->db->get_where("client_storage",array("user_id" => $client_id))->row();

        if($plan_->time_period > 0 && $plan_->period != ""){
            $time_period = $plan_->time_period;
            $period = $plan_->period;
            $expiry_date = date('d-m-Y', strtotime("+".$time_period." ".$period.""));
        }
        else{
            $expiry_date = 'NA';   
        }
        
        $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';

            //PERCENT DB AND FTP
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

            $fsize = $ftpsize == 0 ? 1 : $ftpsize;
            $dsize = $sqlsize == 0 ? 1 : $sqlsize;  

            $ftpper     =  ($fsize / $getstorage->ftp_storage ) * 100 ;
            $dbper     =  ($dsize / $getstorage->db_storage) * 100;
            
            $ftpper = 92;
            $dbper = 92;


        $email_data = [
            'user_name' => $client_->fname." ".$client_->lname,
            'db_percent' => $dbper."%",
            'ftp_percent' => $ftpper."%",
            'no_of_customers' => $getstorage->users,
            'plan_name' => $plan_->name,
            'expiry_date' => $expiry_date,
            'plan_description' => $plan_->description,
            'plan_ftp_space_limit' => $plan_->ftp_space_limit.$plan_->ftp_unit,
            'plan_db_space_limit' => $plan_->sql_space_limit.$plan_->db_unit,
            'plan_time_period' => $plan_->time_period,
            'plan_price_monthly' => $plan_->price,
            'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
        ];

        sendMail('dstestemail@mailinator.com', 'LOW_STORAGE_SPACE_EMAIL_TO_CUSTOMER', $email_data);

        







        die();

        if ($param1 == 'edit') {
            
            $temp_id = $param2;
            $this->db->where("id",$temp_id);

            $templates = $this->db->get('email_templates')->row();

            if ($templates->temp_code == 'FORGOT_PASSWORD') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{reset_link}' => $this->lang->line("reset_link"),
                ];
            }
            elseif ($templates->temp_code == 'USER_REGISTRATION') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{username}' => $this->lang->line("username"),
                    '{email}' => $this->lang->line("email"),
                    '{company_name}' => $this->lang->line("company_name"),
                      '{company_vat_number}' => $this->lang->line("company_vat_number"), 
                      '{company_street}' => $this->lang->line("company_street"), 
                      '{company_town}' => $this->lang->line("company_town"), 
                      '{company_zipcode}' => $this->lang->line("company_zipcode"), 
                      '{company_country}' => $this->lang->line("company_country"), 
                ];
            }
            elseif ($templates->temp_code == 'CONTACT_EMAIL') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                ];
            }
            elseif ($templates->temp_code == 'PAYMENT_SUCCESS') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{amount}' => $this->lang->line("amount"),
                    '{currency}' => $this->lang->line("currency"),
                ];
            }
            elseif ($templates->temp_code == 'CONTACT_US_REPLY') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{reply_data}' => $this->lang->line("reply_data"),
                    '{user_message}' => $this->lang->line("user_message"),
                ];
            }
            elseif ($templates->temp_code == 'CONTACT_US_USER_EMAIL') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{email}' => $this->lang->line("email"),
                    '{message_content}' => $this->lang->line("message_content"),
                ];
            }
            elseif ($templates->temp_code == 'PLAN_SUBSCRIPTION_EMAIL') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{plan_name}' => $this->lang->line("plan_name"),
                    '{expiry_date}' => $this->lang->line("expiry_date"),
                    '{plan_description}' => $this->lang->line("description"),
                    '{plan_ftp_space_limit}' => $this->lang->line("ftp_space_limit"),
                    '{plan_db_space_limit}' => $this->lang->line("db_space_limit"),
                    '{plan_time_period}' => $this->lang->line("time_period"),
                    '{plan_price_monthly}' => $this->lang->line("price"),
                    '{plan_icon}' => $this->lang->line("icon"),
                ];
            }
            elseif ($templates->temp_code == 'LOW_STORAGE_SPACE_EMAIL_TO_CUSTOMER') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{db_percent}' => $this->lang->line("db_percent"),
                    '{ftp_percent}' => $this->lang->line("ftp_percent"),
                    '{no_of_customers}' => $this->lang->line("no_of_customers"),
                    '{plan_name}' => $this->lang->line("plan_name"),
                    '{expiry_date}' => $this->lang->line("expiry_date"),
                    '{plan_description}' => $this->lang->line("description"),
                    '{plan_ftp_space_limit}' => $this->lang->line("ftp_space_limit"),
                    '{plan_db_space_limit}' => $this->lang->line("db_space_limit"),
                    '{plan_time_period}' => $this->lang->line("time_period"),
                    '{plan_price_monthly}' => $this->lang->line("price"),
                    '{plan_icon}' => $this->lang->line("icon"),
                ];
            }
            elseif ($templates->temp_code == 'LOW_STORAGE_SPACE_EMAIL_TO_RESELLER') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{plan_name}' => $this->lang->line("plan_name"),
                    '{expiry_date}' => $this->lang->line("expiry_date"),
                    '{plan_description}' => $this->lang->line("description"),
                    '{plan_ftp_space_limit}' => $this->lang->line("ftp_space_limit"),
                    '{plan_db_space_limit}' => $this->lang->line("db_space_limit"),
                    '{plan_time_period}' => $this->lang->line("time_period"),
                    '{plan_price_monthly}' => $this->lang->line("price"),
                    '{plan_icon}' => $this->lang->line("icon"),
                ];
            }
            elseif ($templates->temp_code == 'PLAN_EXPIRY_EMAILS_TO_CUSTOMER') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{expiry_date}' => $this->lang->line("expiry_date"),
                    '{plan_name}' => $this->lang->line("plan_name"),
                    '{plan_description}' => $this->lang->line("description"),
                    '{plan_ftp_space_limit}' => $this->lang->line("ftp_space_limit"),
                    '{plan_db_space_limit}' => $this->lang->line("db_space_limit"),
                    '{plan_time_period}' => $this->lang->line("time_period"),
                    '{plan_price_monthly}' => $this->lang->line("price"),
                    '{plan_icon}' => $this->lang->line("icon"),
                ];
            }
            elseif ($templates->temp_code == 'SPACE_UPDATE_REQUEST_APPROVED_TO_CLIENT') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{db_space}' => $this->lang->line("db_space"),
                    '{ftp_space}' => $this->lang->line("ftp_space"),
                    '{no_of_customers}' => $this->lang->line("no_of_customers"),
                ];
            }
            elseif ($templates->temp_code == 'SPACE_UPDATE_REQUEST_UNAPPROVED_TO_CLIENT') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{db_space}' => $this->lang->line("db_space"),
                    '{ftp_space}' => $this->lang->line("ftp_space"),
                    '{no_of_customers}' => $this->lang->line("no_of_customers"),
                ];
            } 
            elseif ($templates->temp_code == 'USER_CREATION') {
                $shortcodes = [
                    '{user_name}' => $this->lang->line("user_name"),
                    '{username}' => $this->lang->line("username"),
                    '{password}' => $this->lang->line("password"),
                ];
            }
            else{
                $shortcodes = [];
            }

            $data["page"]      = "edit_email_templates";
            $data["email_templates"] = $templates;
            $data["shortcodes"] = $shortcodes;

            $this->load->view("admin/settings/edit_email_templates",$data);

        }
        
    }

}