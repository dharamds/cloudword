<?php
class UserApi extends MX_Controller {
    function __construct() {
        parent::__construct();
        if ($this->session->userdata("user_id") == "") {
            redirect(base_url() . "client/login");
        } else {
            $this->general->check_if_plan_expire_and_redirect();
            $moduleid = 7;
            $this->general->check_for_allow_module_and_redirect($moduleid);
        }
    }
    public function index() {
        $user_id = $this->session->userdata("user_id");
        $apidata = $this->db->query("SELECT client_id, userapikey FROM client WHERE client_id = " . $user_id . "");
        $data["page"] = "userapi";
        $data["userapi"] = $apidata->result();
        
        //echo '<pre>';
        //print_r($data);
        //exit;
        $this->load->view("client/userapi", $data);

    }


    public function update() {
        $user_id    = $this->session->userdata("user_id");
        $client_id  = $this->input->post("client_id");
        $api_key    = $this->input->post("api_key");
       
            if ($api_key != ''){
                $data = array("userapikey" => $api_key );
                $this->db->where("client_id", $user_id);
                if ($this->db->update("client", $data)) {
                    echo json_encode(array("status" => "success", "msg" => $this->lang->line("api_key_update_msg") ));
                } else {
                    echo json_encode(array("status" => "failed", "msg" => $this->lang->line("something_wrong") ));
                }
            }else{
                echo json_encode(array("status" => "failed", "msg" => $this->lang->line("api_key_blank") ));
            }
    }
    




    
   





}
