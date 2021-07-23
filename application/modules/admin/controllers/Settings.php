<?php
class Settings extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "client" && $this->session->userdata("user_id") != ""){
            redirect(base_url()."admin/login");
        }
    }
    public function index(){
        $user_id = $this->session->userdata("user_id");
        $data["page"]      = "settings";
        $data["settings"] = $this->db->get("site_setting")->result();
        $data["currencies"] =$this->db->get("currencies")->result();
        $this->load->view("admin/settings/site_setting",$data);
    }
    public function update(){
    	 $setting_id = $this->input->post("setting_id");
    	 $name_value = $this->input->post("name_value");
    	 $this->db->where("setting_id",$setting_id);
    	 if($this->db->update("site_setting",["name_value" => $name_value])){
    	 	echo json_encode(array("status" => "success","msg" => "Setting Updated Successfully"));
    	 }else{
    	 	echo json_encode(array("status" => "failed","msg" => "Something went wrong"));
    	 }
    }

    public function email_templates($param1 = '', $param2 = ''){

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
        elseif ($param1 == 'update') {

             $template_id = $this->input->post("template_id");
       
             $update_arr = [
                'subject' => $this->input->post("subject"),
                'message' => $this->input->post("message"),
             ];
             $this->db->where("id",$template_id);

             if($this->db->update("email_templates",$update_arr)){
                echo json_encode(array("status" => "success","msg" => $this->lang->line("update_success") ) );
             }else{
                echo json_encode(array("status" => "failed","msg" => "Something went wrong"));
             }

        } 
        else {
            $data["page"]      = "email_templates";
            $data["email_templates"] = $this->db->get("email_templates")->result();
            $this->load->view("admin/settings/email_templates",$data);
        }
        
    }

    public function page_templates(){
            $data["page"]      = "page_template";
            $data["page_templates"] = $this->db->get("page_template")->result();
            $this->load->view("admin/settings/page_templates",$data);   
    }
    public function page_template_edit($page_id = NULL){
            $templates = $this->db->get_where("page_template",array("page_id" => $page_id))->row();
            $data["page"]      = "page_template";
            $data["page_templates"] = $templates;
            $this->load->view("admin/settings/edit_page_templates",$data);
    }
    public function page_template_update($page_id = NULL){
            $page_id = $this->input->post("page_id");
            $update_arr = [
                'title' => $this->input->post("subject"),
                'html_template' => $this->input->post("message"),
             ];
             $this->db->where("page_id",$page_id);
             if($this->db->update("page_template",$update_arr)){
                echo json_encode(array("status" => "success","msg" => $this->lang->line("update_success") ) );
             }else{
                echo json_encode(array("status" => "failed","msg" => "Something went wrong"));
             }
    }

    /*public function sendMail($email = '', $template_code = '', $data = [])
    {
        $this->load->library('email');
            
        $settings = $this->db->select('name_value')->where_in('slug',['mail_from_name', 'email', 'smtp_host', 'mail_protocol', 'smtp_port', 'smtp_user', 'smtp_pass'] )->get("site_setting")->result();

        $template_ = $this->db->where(["temp_code"=>$template_code, 'status' => 1])->get('email_templates')->row();

        $from_name = $settings[0]->name_value;
        $from_email = $settings[1]->name_value;
        $message = $template_->message;
        $subject = $template_->subject;

        foreach ($data as $key => $setVars) {
            $message = str_replace('{' . $key . '}', $setVars, $message);
        }

        $message = html_entity_decode($message);

        $this->email->initialize(
            array(
                'protocol'  => $settings[3]->name_value,
                'smtp_host' => $settings[2]->name_value,
                'smtp_user' => $settings[5]->name_value,
                'smtp_pass' => $settings[6]->name_value,
                'smtp_port' => $settings[4]->name_value,
                'crlf'      => "\r\n",
                'newline'   => "\r\n",
                'mailtype'  => 'html'
            )
        );

        $this->email->clear();
        $this->email->set_newline("\r\n");
        $this->email->from($from_email, $from_name);
        $this->email->to($email);
        $this->email->cc('datalogy11@yopmail.com');
        //$this->email->bcc('error.log.sk@gmail.com');
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->send();
        $this->email->clear();

        return true;
    }*/


}