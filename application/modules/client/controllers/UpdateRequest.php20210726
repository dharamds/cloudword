<?php
class UpdateRequest extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->library("general");
        if( !in_array($this->session->userdata("role_type"), ["reseller","client"]) ){
            redirect(base_url()."client/login");
        }else{
            $this->user_id = $this->session->userdata("user_id");
            $this->site_setting = $this->db->get("site_setting")->result();
        }
    }


    public function index(){
        $data["requestlist"] = $this->db->where('client_id',$this->session->userdata("user_id"))->get("space_update_requests")->result();
    	$data["page"] 	  = "update_request"; 
    	$this->load->view("client/updateRequests/list",$data);		
    }


	public function customer_request(){
		$data["requestlist"] = array();
		$requestCustomers = $this->db->select('client_id')->where('parent_id',$this->session->userdata("user_id"))->get("client")->result();
		if($requestCustomers){
			foreach($requestCustomers as $item){
				$ids[] = $item->client_id;
			}
		}
		
		if(count($ids) > 0){
			
			$this->db->select('space_update_requests.*, client.client_id, client.fname, client.lname, client.email');
			$this->db->from('space_update_requests');
			$this->db->where_in('space_update_requests.client_id', $ids);
			$this->db->join('client', 'client.client_id = space_update_requests.client_id', 'inner');
			$this->db->order_by('space_update_requests.request_id', 'desc');
			$query = $this->db->get();
						
        $data["requestlist"] = $query->result();
		}
		
		
    	$data["page"] 	  = "update_request"; 
    	$this->load->view("client/updateRequests/customer_list",$data);		
    }

    /*public function add($id = null){

        $user_id = $this->user_id;

        if($this->input->post()){


            if ( !is_numeric($this->input->post("size")) ) {
               echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
               die;
            }


            $req_size = $this->input->post("size");
            $size_unit = $this->input->post("size_unit");

            $total_bytes = $this->general->byteconvert( $req_size ,$size_unit);

            $this->db->insert("space_update_requests",[
                'client_id' => $user_id,
                'request_size' => $total_bytes,
            ]);

            $insert_id = $this->db->insert_id();

            if ($insert_id) {
                $this->session->set_flashdata('success',  $this->lang->line("request_success"));
                redirect(base_url()."client/updateRequest");

                //MAIL TO ADMIN
                $this->load->helper('basic_helper');
                $client = $this->db->select(['email','fname','lname'])->where('client_id', $this->input->post("client_id") )->get('site_setting')->row();

                $send_email = $client->email;

                sendMail($send_email, $template_code = 'SPACE_UPDATE_REQUEST_TO_ADMIN', $data = [
                    'user_name' => $client->fname." ".$client->lname,
                    'size'  => $this->general->formatBytes($total_bytes),
                ]);


            } else {
                $this->session->set_flashdata('failed', $this->lang->line('something_wrong'));
                redirect(base_url()."client/updateRequest");
            }
          
        }
            
    }*/

    
    public function save_update_request()
    {
        ini_set('display_errors', 'Off');
        $user_id = $this->session->userdata("user_id");

        if($this->input->post()){


            //CHECK IF ALREADY REQUESTED
            $getallr = $this->db->where(["client_id" => $user_id, 'status' => 0])->get('space_update_requests')->num_rows();
           //echo $getallr; exit;
            if ($getallr) {
               echo json_encode(array("status" => "failed","msg" => $this->lang->line('already_requested')));
               die;
            }

            //VALIDATION
            $this->load->library('form_validation');
            $this->form_validation->set_rules('ftp_size', 'FTP Space Limit', 'trim|numeric');
            $this->form_validation->set_rules('db_size', 'DB Space Limit', 'trim|numeric');
            //$this->form_validation->set_rules('no_of_users', 'No of Users', 'trim|numeric');
            if($this->form_validation->run() == FALSE){
                echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
                die;
            }

            $ftp_size = $this->input->post("ftp_space");
            $db_size = $this->input->post("db_space");
            $ftp_space_unit = $this->input->post("ftp_space_unit");
            $db_space_unit = $this->input->post("db_space_unit");
            $no_of_users = $this->input->post("no_of_users");

            if ( empty($ftp_size) && empty($db_size) ) {
                echo json_encode(array("status" => "failed","msg" => $this->lang->line('at_least_field_require'))); die;
            }

            //INSERT
            try {

                $this->db->insert("space_update_requests",[
                    'client_id' => $user_id,
                    'ftp_size' => $this->general->byteconvert( $ftp_size ,$ftp_space_unit),
                    'db_size' => $this->general->byteconvert( $db_size ,$db_space_unit),
                    'user_count' => $no_of_users,
                    'status' => 0,
                ]);

                //$insert_id = $this->db->insert_id();
                echo json_encode(array("status" => "success","msg" => $this->lang->line('request_success')));die;

            } catch (Exception $e) {
                echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong'))); die;
            }
          
        }
    }

public function getFormat()
    {
        $this->load->library('general');
        $unit_to_con = $this->input->post("unit");
        $size_ = $this->input->post("size_in_bytes");
        $size_unit = $this->input->post("size_unit");

        if( $size_unit != "b"){
           $size_in_bytes = $this->general->byteconvert( $size_ ,$size_unit);
        }else{
            $size_in_bytes = $size_;
        }        

        $final_val = $this->convertToUnit( $size_in_bytes ,$unit_to_con);

        echo json_encode(["status" => "success","data" => $final_val, 'unit' => $unit_to_con ]);
    }
	
public function update(){

        //$this->load->library('general');
        //$this->general->byteconvert($this->input->post("sql_space_limit"),$this->input->post("db_unit"))
        //$this->general->formatBytes($this->input->post("sql_space_limit"))

        if($this->input->post()){


            //VALIDATION
            $this->load->library('form_validation');
            $this->form_validation->set_rules('ftp_size', 'FTP Space Limit', 'trim|numeric');
            $this->form_validation->set_rules('db_size', 'DB Space Limit', 'trim|numeric');
            //$this->form_validation->set_rules('no_of_users', 'No of Users', 'trim|numeric');
            if($this->form_validation->run() == FALSE){
                echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
                die;
            }

            $requestId = $this->input->post("req_id");
            $client_id = $this->input->post("client_id");
            $ftp_size = $this->input->post("ftp_space") ?? 0;
            $db_size = $this->input->post("db_space") ?? 0;
            $ftp_space_unit = $this->input->post("ftp_space_unit");
            $db_space_unit = $this->input->post("db_space_unit");
            $no_of_users = $this->input->post("no_of_users") ?? 0;

            if ( empty($ftp_size) && empty($db_size) ) {
                echo json_encode(array("status" => "failed","msg" => $this->lang->line('at_least_field_require'))); die;
            }



            try {

               

                //UPDATE CLIENT_STAORAGE IF APPROVED
                $ftp_storage = $this->general->byteconvert( $ftp_size ,$ftp_space_unit);
                $db_storage = $this->general->byteconvert( $db_size ,$db_space_unit);

                $this->db->where(['user_id'=>$client_id]);
                $this->db->set('ftp_storage', 'ftp_storage+'.$ftp_storage, FALSE);
                $this->db->set('db_storage', 'db_storage+'.$db_storage, FALSE);
                $this->db->set('users', 'users+'.$no_of_users, FALSE);
                $this->db->update('client_storage');

				 $this->db->where('request_id',$requestId)->update("space_update_requests",[
                    'status' => $this->input->post("request_status"),
                    'ftp_size' => $this->general->byteconvert( $ftp_size ,$ftp_space_unit),
                    'db_size' => $this->general->byteconvert( $db_size ,$db_space_unit),
                    'user_count' => $no_of_users,
                ]);
				
                //SEND EMAIL
                $this->load->helper('basic_helper');
                $client = $this->db->select(['email','fname','lname'])->where('client_id', $this->input->post("client_id") )->get('client')->row();

                $send_email = $client->email;
				$send_email = 'dharamds1104@gmail.com';
                if ($this->input->post("request_status") == 1) { //Approved

                    sendMail($send_email, $template_code = 'SPACE_UPDATE_REQUEST_APPROVED_TO_CLIENT', $data = [
                        'user_name' => $client->fname." ".$client->lname,
                        'db_space'  => $this->general->formatBytes($db_storage),
                        'ftp_space'  => $this->general->formatBytes($ftp_storage),
                        'no_of_customers'  => $no_of_users,
                    ]);

                } elseif( $this->input->post("request_status") == 2 ) { //unApproved

                    sendMail($send_email, $template_code = 'SPACE_UPDATE_REQUEST_UNAPPROVED_TO_CLIENT', $data = [
                        'user_name' => $client->fname." ".$client->lname,
                        'db_space'  => $this->general->formatBytes($db_storage),
                        'ftp_space'  => $this->general->formatBytes($ftp_storage),
                        'no_of_customers'  => $no_of_users,
                    ]);
                }

                echo json_encode(array("status" => "success","msg" => $this->lang->line('update_success')));


            } catch (Exception $e) {
                echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
            }
            
        }           
    }

    public function delete($request_id){

		$this->db->where("request_id",$request_id);

		if($this->db->delete('space_update_requests')){
            
            echo json_encode(array("status" => "success","msg" => $this->lang->line('delete_success')));
		}else{
			echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
		}		    	
    }

}