<?php
class Plan extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->library("general");
        if($this->session->userdata("role_type") != "reseller"){
            redirect(base_url()."client/login");
        }else{
            $this->user_id = $this->session->userdata("user_id");
            $this->site_setting = $this->db->get("site_setting")->result();
        }
    }
    public function index(){
        $data["planlist"] = $this->db->get_where("plans",array("active" => 1,"is_deleted !=" => 1,"user_id" => $this->user_id ))->result();
    	$data["page"] 	  = "plan"; 
    	$this->load->view("client/plan/list",$data);		
    }
    public function add($id = null){
        $user_id = $this->user_id;
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
            if($this->form_validation->run() == FALSE)
            {   $data["page"] 	  = "plan";
                $data['id'] = $id; 
                $data["plandata"] = [];
                $data["moduledata"] = $this->db->get("modules")->result();
                if($id != '') { 
                    $plan_id = $id; 
                    $data["plandata"] = $this->db->get_where("plans",array("id" => $plan_id))->row();
                    $data["additinalInfo"] = $this->db->get_where("plan_additional_info",array("plan_id" => $plan_id))->array();
                }
                $this->load->view("client/plan/create", $data);	
            }else{
                $time_period = $this->input->post("time_period");
                $period = $this->input->post("period");
                $curdate = date("Y-m-d");
                $getdate = date("Y-m-d",strtotime("+".$time_period." ".$period.""));
                $startss = strtotime($curdate);
                $endss = strtotime($getdate);
                $days_between = ceil(abs($endss - $startss) / 86400);
                if($id == ''){
                    $data = $this->input->post();
                    $data["user_id"] = $user_id;
                    $data["created_by"] = "reseller";
                    unset($data['additinalInfo']);
                    $data["ftp_space_bytes"] = $this->general->byteconvert($this->input->post("ftp_space_limit"),$this->input->post("ftp_unit"));
                    $data["db_space_bytes"] = $this->general->byteconvert($this->input->post("sql_space_limit"),$this->input->post("db_unit"));
                    if($data['price'] != '' && $data['price'] > 0 ){
                        $data['price_yearly'] = $data['price'] * 12;
                    }
                    $data["expiry_days"] = $days_between;
                    $data["modules"] = '1,2,3,4,5,6,7';
                    $additinalInfo = $this->input->post('additinalInfo');
                     $config['upload_path']   = './uploads/plan/'; 
                     $config['allowed_types'] = 'png|jpg|jpeg|gif|PNG|tif|eps|bmp|tiff';
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
                    redirect(base_url()."client/plan");
                } else{

                    $plan_id = $id;
                    $data = $this->input->post();
                    $data["user_id"] = $user_id;
                    $data["created_by"] = "reseller";
                    unset($data['additinalInfo']);
                    $data["ftp_space_bytes"] = $this->general->byteconvert($this->input->post("ftp_space_limit"),$this->input->post("ftp_unit"));
                    $data["db_space_bytes"] = $this->general->byteconvert($this->input->post("sql_space_limit"),$this->input->post("db_unit"));
                    $data["expiry_days"] = $days_between;
                    if($data['price'] != '' && $data['price'] > 0){
                        $data['price_yearly'] = $data['price'] * 12;
                    }
                    $additinalInfo = $this->input->post('additinalInfo');
                    $data["modules"] = '1,2,3,4,5,6,7';
                    $getplan = $this->db->get_where("plans",array("id" => $plan_id))->row();
                    if($_FILES["icon"]['name'] !=""){
                        $config['upload_path']   = './uploads/plan/'; 
                        $config['allowed_types'] = 'png|jpg|jpeg|gif|PNG|tif|eps|bmp|tiff';
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
                        $data["icon"] = $getplan->icon;
                    }
                    unset($data["ci_csrf_token"]);
                    $this->db->where("id",$plan_id);
                    $this->db->update("plans",$data);
                    if($plan_id) {
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
                    redirect(base_url()."client/plan");
                }
            }
        }else {
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
                $this->load->view("client/plan/view", $data);	
            } else {
                $this->load->view("client/plan/create", $data);	
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

    public function testbytes(){
         $this->load->library('general');   
         $data = 2*1024*1024*1024*1024;
         echo $data;
    }
    public function testdates(){   
    
                $time_period = 2;
                $period = "year";
                $curdate = date("Y-m-d");
                $getdate = date("Y-m-d",strtotime("+".$time_period." ".$period.""));
                $startss = strtotime($curdate);
                $endss = strtotime($getdate);
                $days_between = ceil(abs($endss - $startss) / 86400);
                echo $days_between;
    }
public function details(){
                $plan_id = $this->input->post("plan_id");    
                $plandata =   $this->db->get_where("plans",array("id" => $plan_id))->row();
                if(!empty($plandata)){
                     $data["plan_name"] =  $plandata->name; 
                     $data["ftp_space_limit"] =  $this->general->formatBytes($plandata->ftp_space_bytes); 
                     $data["db_space_limit"] =  $this->general->formatBytes($plandata->db_space_bytes);  
                     $data["price"] =  $plandata->price ." ".$this->site_setting[14]->name_value;  
                     $data["expiry_days"] =  $plandata->expiry_days;
                     echo json_encode(array("status" => "success","data" => $data));
                }else{
                    echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
                }
}


public function subscription_details(){
	
		$requestCustomers = $this->db->select('client_id')->where('parent_id',$this->session->userdata("user_id"))->get("client")->result();
		if($requestCustomers){
			foreach($requestCustomers as $item){
				$ids[] = $item->client_id;
			}
		}
		
		if(count($ids) > 0){
			
			$this->db->select('subscription_details.*, client.client_id, client.fname, client.lname, client.email');
			$this->db->from('subscription_details');
			$this->db->where_in('subscription_details.user_id', $ids);
			$this->db->join('client', 'client.client_id = subscription_details.user_id', 'inner');
			$this->db->order_by('subscription_details.sub_id', 'desc');
			$query = $this->db->get();
						
        $data["subscription_plan_details"] = $query->result();
		}
	

			
            $data["currency"]= $this->db->query("select (select currency_symbol from currencies where code = ss.name_value) as currency_symbol from site_setting ss where ss.setting_id = 15")->row()->currency_symbol; 
          
            $data["page"]     = "subscriptions";
            $this->load->view("client/plan/subscription_details", $data);
    }

}