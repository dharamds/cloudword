<?php
class Plan extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        if( $this->session->userdata("role_type") != "admin" ){
            redirect(base_url()."admin/login");
        }
    }
    public function index(){
        $data["planlist"] = $this->db->get_where("plans",array("is_deleted" => 0))->result();
    	$data["page"] 	  = "plan"; 
    	$this->load->view("admin/plan/list",$data);		
    }
    public function add($id = null){
    	$user_id = $this->session->userdata("user_id");
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('general');
        if($this->input->post()){
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('description', 'trim|Description', 'required');
            $this->form_validation->set_rules('ftp_space_limit', 'FTP Space Limit', 'trim|required|numeric');
            $this->form_validation->set_rules('sql_space_limit', 'SQL Space Limit', 'trim|required|numeric');
            $this->form_validation->set_rules('price', 'Price', 'trim|required|numeric');
            $this->form_validation->set_rules('time_period', 'Time Period', 'trim|required|numeric');
            $this->form_validation->set_rules('ftp_unit', 'FTP Unit', 'required');
            $this->form_validation->set_rules('db_unit', 'DB Unit', 'required');

            if ($this->form_validation->run() == FALSE)
            {   $data["page"] 	  = "plan";
                $data['id'] = $id; 
                $data["plandata"] = [];
                $data["moduledata"] = $this->db->get("modules")->result();
                if($id != '') {
                    $plan_id = $id; 
                    $data["plandata"] = $this->db->get_where("plans",array("id" => $plan_id))->row();
                    $data["additinalInfo"] = $this->db->get_where("plan_additional_info",array("plan_id" => $plan_id))->array();
                }
                $this->load->view("admin/plan/create", $data);	
            } else {

                if($id == ''){
                    $data = $this->input->post();
                    unset($data['additinalInfo']);
                    $data["user_id"] = $user_id;
                    $data["created_by"] = "admin";
                 
                    $data["ftp_space_bytes"] = $this->general->byteconvert($this->input->post("ftp_space_limit"),$this->input->post("ftp_unit"));
                    $data["db_space_bytes"] = $this->general->byteconvert($this->input->post("sql_space_limit"),$this->input->post("db_unit"));
                   
                    if($this->input->post("period")=="month"){
                        $data["expiry_days "] = $this->input->post("time_period") * 30;
                    }elseif($this->input->post("period")=="year"){
                        $data["expiry_days "] = $this->input->post("time_period") * 365;
                    }

                    if($data['price'] != '' && $data['price'] > 0 ){
                        $data['price_yearly'] = $data['price'] * 12;
                    }

                    $data["modules"] = '1,2,3,4,5,6,7';//implode(",",$this->input->post("modules"));
                    $additinalInfo = $this->input->post('additinalInfo');
                     $config['upload_path']   = './uploads/plan/'; 
                     $config['allowed_types'] = 'png|jpg';
					 
                     if($_FILES["icon"]['name'] !=""){
                        $new_name = time().$_FILES["icon"]['name'];
                        $config['file_name'] = $new_name;
                        $this->load->library('upload', $config);
                        $this->upload->do_upload('icon');
                        $image = $this->upload->data(); 
                        $data["icon"] = $image["file_name"];
                        
                         $srcpath =  '/uploads/plan/'.$image['file_name'];
                        $despath =  '/uploads/plan/thumbnail/';
                        $this->general->resizeImage($srcpath,$despath); 
                     }
                    
                    unset($data["ci_csrf_token"]);

                    $this->db->insert("plans",$data);
                    $insert_id = $this->db->insert_id();

                    if ($insert_id) {
                        foreach ($additinalInfo as $ky => $infodata) {
                            if ($infodata['key'] != '' && $infodata['val'] != '') {
                                    $this->db->insert('plan_additional_info', [
                                        'plan_id'           => $insert_id,
                                        'key_feature'       => $infodata['key'],
                                        'short_description' => $infodata['val']
                                    ]);
                            }
                        }     
                    } 
                    $this->session->set_flashdata('success',  $this->lang->line("plan_add_msg"));
                    redirect(base_url()."admin/plan");
                } else{

                    $plan_id = $id;
                    $data = $this->input->post();
                    $data["user_id"] = $user_id;
                    $data["created_by"] = "admin";
                    unset($data['additinalInfo']);
                    $data["ftp_space_bytes"] = $this->general->byteconvert($this->input->post("ftp_space_limit"),$this->input->post("ftp_unit"));
                    $data["db_space_bytes"] = $this->general->byteconvert($this->input->post("sql_space_limit"),$this->input->post("db_unit"));
                    
                    if($data['price'] != '' && $data['price'] > 0 ){
                        $data['price_yearly'] = $data['price'] * 12;
                    }
                    
                    if($this->input->post("period")=="month"){
                        $data["expiry_days "] = $this->input->post("time_period") * 30;
                    }elseif($this->input->post("period")=="year"){
                        $data["expiry_days "] = $this->input->post("time_period") * 365;
                    }

                    $additinalInfo = $this->input->post('additinalInfo');
                    $data["modules"] = '1,2,3,4,5,6,7';//implode(",",$this->input->post("modules"));
                    $getplan = $this->db->get_where("plans",array("id" => $plan_id))->row();
					
					
                    if($_FILES["icon"]['name'] !=""){
                        $config['upload_path']   = './uploads/plan/'; 
                       $config['allowed_types'] = 'png|jpg';
                        $new_name = time().$_FILES["icon"]['name'];
                        $config['file_name'] = $new_name;
                        $this->load->library('upload', $config);
                        $this->upload->do_upload('icon');
                        $image = $this->upload->data(); 
                        $data["icon"] = $image["file_name"];

                        $srcpath =  '/uploads/plan/'.$image['file_name'];
                        $despath =  '/uploads/plan/thumbnail/';
                        $this->general->resizeImage($srcpath,$despath); 

                     }else{
                        //$data["icon"] = $getplan->icon;
                     }

                    unset($data["ci_csrf_token"]);

                    $this->db->where("id",$plan_id);
                    $this->db->update("plans",$data);
//print_r($data); exit;

                    if ($plan_id) {
                        $this->db->where('plan_id', $plan_id);
                        $this->db->delete('plan_additional_info');
						
                        foreach ($additinalInfo as $ky => $infodata) {
                            if ($infodata['key'] != '' && $infodata['val'] != '') {
                                    $this->db->insert('plan_additional_info', [
                                        'plan_id'           => $plan_id,
                                        'key_feature'       => $infodata['key'],
                                        'short_description' => $infodata['val']
                                    ]);
                            }
                        }     
                    }

                    $this->session->set_flashdata('success', $this->lang->line('plan_update_msg'));
                    redirect(base_url()."admin/plan");
                }
            }
        } else {
            $data["page"] 	  = "plan";
            $data['id'] = $id; 
            $data["plandata"] = [];
            $data["moduledata"] = $this->db->get("modules")->result();
            $data["currency"]= $this->db->query("select (select currency_symbol from currencies where code = ss.name_value) as currency_symbol from site_setting ss where ss.setting_id = 15")->row()->currency_symbol; 
            if($id != '') {
                $plan_id = $id; 
                $data["plandata"] = $this->db->get_where("plans",array("id" => $plan_id))->row();
                $data["additinalInfo"] = $this->db->get_where("plan_additional_info",array("plan_id" => $plan_id))->result();
            }

            if($this->input->get('type') == 'view') {
                $this->load->view("admin/plan/view", $data);	
            } else {
                $this->load->view("admin/plan/create", $data);	
            }
        }
    }
    public function delete($plan_id){
		$this->db->where("id",$plan_id);
		if($this->db->update("plans",["is_deleted" => 1])){
            
            echo json_encode(array("status" => "success","msg" => $this->lang->line('plan_delete_msg')));
		}else{
			echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
		}		    	
    }

    public function subscription_details(){
            $getplan_subscription = $this->db->query("select * from subscription_details sd inner join client c on sd.user_id = c.client_id order by sd.sub_id DESC")->result();  
            $data["currency"]= $this->db->query("select (select currency_symbol from currencies where code = ss.name_value) as currency_symbol from site_setting ss where ss.setting_id = 15")->row()->currency_symbol; 
            $data["subscription_plan_details"]     = $getplan_subscription;
          
            $data["page"]     = "plan";
            $this->load->view("admin/plan/subscription_details", $data);
    }
    public function extend_warrenty(){
            $sub_id = $this->input->post("sub_id");
            
            $getplan = $this->db->get_where("subscription_details",array("sub_id" => $sub_id))->row();
            $jsondata = json_decode($getplan->plandata);
            $time_period = $jsondata->time_period;
            $period = $jsondata->period;
            $expiry_date = date('Y-m-d', strtotime("+".$time_period." ".$period.""));
            $this->db->where("sub_id",$sub_id);
            if($this->db->update("subscription_details",["cash_advance_flag" => 0,"expiry_date" => $expiry_date])){
                echo json_encode(array("status" => "success","msg" => $this->lang->line("plan_warrenty_extended")));
            }else{
                echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
            }
    }

    public function payment_status(){
        $sub_id = $this->input->post("sub_id");
        
        $getplan = $this->db->get_where("subscription_details",array("sub_id" => $sub_id))->row();
        $jsondata = json_decode($getplan->plandata);
        $time_period = $jsondata->time_period;
        $period = $jsondata->period;
        $start_date = date('Y-m-d');
        $expiry_date = date('Y-m-d', strtotime("+".$time_period." ".$period.""));
        $dtstorage = array("ftp_storage" => $jsondata->ftp_space_bytes, "db_storage" => $jsondata->db_space_bytes, "user_id" =>  $getplan->user_id, "added_date" => date("Y-m-d"), "plan_id" => $jsondata->id);
        $this->db->insert("client_storage",$dtstorage);

        $this->db->where("sub_id",$sub_id);
        if($this->db->update("subscription_details",["cash_advance_flag" => 0,"expiry_date" => $expiry_date,'status'=>"active",'start_date'=>$start_date,"payment_status"=>"success"])){

            $this->db->where("client_id",$getplan->user_id);
            $this->db->update("client",['status'=>"active"]);


            echo json_encode(array("status" => "success","msg" => $this->lang->line("plan_warrenty_extended")));
        }else{
            echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
        }
    }

}