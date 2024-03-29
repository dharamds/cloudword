<?php
class UpdateRequest extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "client" && $this->session->userdata("user_id") != ""){
            redirect(base_url()."admin/login");
        }
    }


    public function index(){
        //$this->load->library('general')
        $this->db->order_by("status", "asc");

        $data["requestlist"] = $this->db->order_by('request_id', 'desc')->get("space_update_requests")->result();
        for($i=0; $i<count($data["requestlist"]);$i++){
            if(!empty($data["requestlist"][$i]->ftp_unit) || !empty($data["requestlist"][$i]->db_unit)){

                $data["requestlist"][$i]->ftp_size = $this->byteconversion($data["requestlist"][$i]->ftp_size,$data["requestlist"][$i]->ftp_unit);
                $data["requestlist"][$i]->db_size = $this->byteconversion($data["requestlist"][$i]->db_size,$data["requestlist"][$i]->db_unit);
            }
        }
    	$data["page"] 	  = "update_request"; 
        // echo "<pre>"; print_r( $data["requestlist"]);exit;
    	$this->load->view("admin/updateRequests/list",$data);		
    }

    //Byte conversion 
    public function byteconversion($bytes, $to, $decimal_places = 1) {
		$formulas = array(
			'kb' => number_format($bytes / 1024, $decimal_places),
			'mb' => number_format($bytes / 1048576, $decimal_places),
			'gb' => number_format($bytes / 1073741824, $decimal_places),
			'tb' => number_format($bytes / 1099511627776 , $decimal_places)
		);
	
		return isset($formulas[$to]) ? $formulas[$to] : 0;
	}
 
    
    public function delete($request_id){

		$this->db->where("request_id",$request_id);

		if($this->db->delete('space_update_requests')){
            
            echo json_encode(array("status" => "success","msg" => $this->lang->line('delete_success')));
		}else{
			echo json_encode(array("status" => "failed","msg" => $this->lang->line('something_wrong')));
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

    function convertToUnit($size,$type)
    {
    
            $type = strtolower($type);
            switch ($type) {
            case "b":
                $output = $size;
                break;
            case "kb":
                $output = $size/1024;
                break;
            case "mb":
                $output = $size/1024/1024;
                break;
            case "gb":
                $output = $size/1024/1024/1024;
                break;
            case "tb":
                $output = $size/1024/1024/1024/1024;
                break;
        }
        return $output;
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

                $client_storage = $this->db->get_where("client_storage",array("user_id" =>  $client_id ))->row();
                $this->db->where(['user_id'=>$client_id]);
                $this->db->set('ftp_storage', $client_storage->ftp_storage + $ftp_storage, FALSE);
                $this->db->set('db_storage', $client_storage->db_storage + $db_storage, FALSE);
                $this->db->set('users',  $client_storage + $no_of_users, FALSE);

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


}