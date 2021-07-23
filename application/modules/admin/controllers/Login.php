<?php
class Login extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        
    }
    public function index(){
        if($this->session->userdata("role_type") == "reseller" && $this->session->userdata("role_type") != "client"){
            redirect(base_url()."admin/dashboard");
        }

        $data["error_login"] = "";
    	$this->load->view("admin/login",$data);
    }
    public function login_set(){
    		$username = $this->input->post("username");
    		$password = $this->input->post("password");
    		$getdatabyusername = $this->db->get_where("client",array("username" => $username,"role_id" => 1));

    		if($getdatabyusername->num_rows() > 0){
    			$usrdata = $getdatabyusername->row();
    			if(password_verify($password, $usrdata->password)){
    				$dd = array(
    								"fname" => $usrdata->fname,
    								"lname" => $usrdata->lname,
    								"email" => $usrdata->email,
    								"phone" => $usrdata->phone,
    								"user_id"   => $usrdata->client_id,
                                    "role_type" => "admin",
                                    "language" => $usrdata->language,
                                    "lang" => $usrdata->language,
                                    "icon" => $usrdata->img
    							);
    				$this->session->set_userdata($dd);
    				redirect(base_url()."admin/dashboard");
    			}else{
    				$data["error_login"] = $this->lang->line("wrong_password_msg");
                    $this->load->view("admin/login",$data);
    			}
    		}else{
                if(isset($username)){
    			$data["error_login"] = $username." ".$this->lang->line("email_not_register");
                $this->load->view("admin/login",$data);
                }else{
                    redirect(base_url()."admin/login");
                }
    		}
    }
    public function logout(){
       $this->session->sess_destroy();
        redirect(base_url('admin/login'));
    }
      public function forgot_password(){
        $data["error_login"] = "";
        $this->load->view("/forgot_password",$data);
    }


    public function get_send_forgot_password_link()
    {    
        
        if( $this->input->post("username") != '') 
        {
           
            $username = $this->input->post("username");
            $getdatabyusername = $this->db->get_where("client",array("username" => $username));

            if($getdatabyusername->num_rows() > 0){
                $usrdata = $getdatabyusername->row();
                if($usrdata->is_deleted == 0 && $usrdata->status == 'active' ){
                    
                    $code = base64_encode($username);

                    $updateData = [
                        'client_id'=> $usrdata->client_id,
                        'code' => $code
                    ];

                    $this->db->where("client_id",$usrdata->client_id);
                    $this->db->delete("user_reset_password_requests");
                    
                    if($this->db->insert("user_reset_password_requests",$updateData))
                    {   

                            $get_setting = $this->db->query("select name_value from site_setting where setting_id IN(7,8)")->result();
                            $link  = base_url()."admin/login/reset_password/".$code;
                       
                            $subject = $this->lang->line("Forgot Password Request");
                            $msg = $this->lang->line("Hello").",<br/>
                            <br/>
                            ".$this->lang->line("You recently requested to reset your password for Cloud Service World account")." <a href='$link' target='_blank'>".$this->lang->line("Click here to reset it")."</a> 
                            <br/>
                            <br/>".
                            $this->lang->line("If you did not request a password reset, please ignore this email or reply to let us know. This password reset link will expire after successfull reset")."
                            <br/>
                            <br/>
                            ".
                            $this->lang->line("Thank you").",<br/>
                            ".
                           /* $this->lang->line("Cloud Service World");
                            $this->email->set_newline("\r\n");
                            $this->email->from($get_setting[0]->name_value, $get_setting[1]->name_value);
                            $this->email->to($usrdata->email);
                            $this->email->subject($subject);
                            $this->email->message($msg);*/


                            //$client = $this->db->get_where('client', ["client_id"=>$usrdata->client_id])->row();

                             $chmail = sendMail($usrdata->email, $template_code = 'FORGOT_PASSWORD', $data = [
                                'user_name' => $usrdata->fname." ".$usrdata->lname,
                                'reset_link'  => "<a href='".$link."' target='_blank'>".$this->lang->line("Click here to reset it")."</a> ",
                            ]);
                            
                            if($chmail){
                                echo json_encode(['code'=>200,'message'=>$this->lang->line("Password reset link sent to your email, Please check your email")]);
                                exit;
                            }else{
                                echo json_encode(['code'=>400,'message'=>$this->lang->line("Something went wrong, Can not send email!")]);
                                exit;
                            }
                    }
                    exit;

                }else{
                    echo json_encode(['code'=>400,'message'=>$this->lang->line("Account_deactivate_msg")]);
                    exit;
                }
            }   
            echo json_encode(['code'=>400,'message'=>$username." ".$this->lang->line("email_not_register")]);
            exit;
        }

        exit;
    }
    public function reset_password($secrete_key)
    {
       
        $getdata = $this->db->get_where("user_reset_password_requests",array("code" => $secrete_key));
        
        if($getdata->num_rows() > 0){
            //$record = $getdata->row();
            $data['pageActionStatus'] = 'notexpired';
        }
        else
        {
            $data['pageActionStatus'] = 'expired';
            //redirect(base_url('/login'));
        }

        $data["error_login"] = "";
        $data["secrete_key"] = $secrete_key;
        $this->load->view("/reset_password",$data);
    }



    public function getCheckUserNameExist()
    {
          
        $username = $this->input->post("username");
        $secrete_key = $this->input->post("secrete_key");
        $getdata = $this->db->get_where("user_reset_password_requests",array("code" => $secrete_key));

        if($getdata->num_rows() > 0 && (trim($username) == trim(base64_decode($secrete_key))) )
        {
            echo 'true';
            exit;
        }
        else
        {
            echo 'false';
            exit;
        }

        echo 'false';
        exit;
    }




    public function get_change_password()
    {
        
        if( $this->input->post("username") != '' && $this->input->post("password") != '') 
        {
           
            $username = $this->input->post("username");
            $password = $this->input->post("password");
            $getdatabyusername = $this->db->get_where("client",array("username" => $username));
            if($getdatabyusername->num_rows() > 0){
                $usrdata = $getdatabyusername->row();
                $updateData = [
                    'password' => password_hash($this->input->post("password"), PASSWORD_DEFAULT)
                ];
                $this->db->where("client_id",$usrdata->client_id);
                $updatepass = $this->db->update("client",$updateData);
                if($updatepass) 
                {
                    $get_setting = $this->db->query("select name_value from site_setting where setting_id IN(7,8)")->result();
                    $link  = base_url()."admin/login/";
                    $subject = $this->lang->line("Password Reset Successfully");
                    $msg = $this->lang->line("Hello").",<br/>
                    <br/>".
                    $this->lang->line("Your password has been reset successfully for Cloud Service World account")." <a href='$link' target='_blank'> ".$this->lang->line("Click here to login with new password")."</a> 
                    <br/>
                    <br/>
                    ".
                            $this->lang->line("Thank you").",<br/>
                            ".
                            $this->lang->line("Cloud Service World");

                    $this->email->set_newline("\r\n");
                    $this->email->from($get_setting[0]->name_value, $get_setting[1]->name_value);
                    $this->email->to($usrdata->email);
                    $this->email->subject($subject);
                    $this->email->message($msg);
                    $this->email->send();
                        
                    $this->db->where("client_id",$usrdata->client_id);
                    $this->db->delete("user_reset_password_requests");
                    echo json_encode(['code'=>200,'message'=>$this->lang->line('Your password has been successfully changed.')]);
                    exit;
                }
            }
            echo json_encode(['code'=>400,'message'=>$this->lang->line('Something went wrong, please try again')]);
            exit;
        }

        echo json_encode(['code'=>400,'message'=>$this->lang->line('Something went wrong, please try again')]);
        exit;
    }
}