<?php
class ApiModules extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "admin" && $this->session->userdata("user_id") != ""){
            redirect(base_url()."client/login");
        }else{
            $this->general->check_if_plan_expire_and_redirect();
            $moduleid = 6;
            $this->general->check_for_allow_module_and_redirect($moduleid);
        }
    }
    public function modules(){
    		$user_id = $this->session->userdata("user_id");
            $data["modulesdata"] = $this->db->get_where("third_api_modules",array("status" => "active"))->result();
            $data["page"] = "api_modules";
    		$this->load->view("client/apiModules/modules",$data);
    }
    public function shopware($type = ''){
        if($type == "add_credentials"){
            $user_id = $this->session->userdata("user_id");
            $data["page"] = "api_modules";
            $data["msg"] = "";
            $data["pdata"] = array();
            $data["error_data"] = array();
            $this->load->view("client/apiModules/shopware/add_credentials",$data);
        }else if($type == "save_credentials"){

            // echo '<pre>';
            // print_r(  $this->input->post() );
            // exit;

            $user_id = $this->session->userdata("user_id");
            $project_name = $this->input->post("project_name");
            $url = $this->input->post("url");
            $version = $this->input->post("version");
            $key_id = trim($this->input->post("key_id"));
            $access_key = trim($this->input->post("access_key"));
            $module_id  = $this->input->post("module_id");
            $checkcnn = $this->check_connection($url,$key_id,$access_key,"save");
            $checkweb = $this->check_web($url);

            $error_data = array();
            if($checkweb == "failed"){
                 $error_data["url"] = $this->lang->line("shop_url_not_running");
            }
            if($checkcnn == "failed"){
                $error_data["key_id"] = $this->lang->line("access_key_not_proper");
            }
            if(count($error_data) > 0){
                   $data["page"] = "shopware";
                   $data["error_data"] = $error_data;
                   $data["msg"] = "";
                   $data["pdata"] = $this->input->post();
                 $this->load->view("client/apiModules/shopware/add_credentials",$data);
            }else{
                $dataa = array(
                                "project_name"  =>  $this->encryption->encrypt($project_name), 
                                "url"           =>  $this->encryption->encrypt($url), 
                                "key_id"        =>  $this->encryption->encrypt($key_id), 
                                "key_secret"    =>  $this->encryption->encrypt($access_key),
                                "client_id"     => $user_id,
                                "version"       => $version,
                                "added_date"    => date("Y-m-d"),
                                "module_id"     => $module_id 
                );
                if($this->db->insert("api_modules_credentials",$dataa)){
                     $data["msg"] = $this->lang->line("shop_cred_success_added");
                    $data["page"] = "shopware";
                    $data["pdata"] = array(); 
                    $data["error_data"] = array();
                     $this->load->view("client/apiModules/shopware/add_credentials",$data);
                }else{ 
                    $data["page"] = "shopware";
                    $data["pdata"] = $this->input->post(); 
                    $error_data["errors"] = $this->lang->line("something_wrong");
                    $data["error_data"] = $error_data;
                     $this->load->view("client/apiModules/shopware/add_credentials",$data);
                }
            }
        }
    }
    public function check_connection($url,$key_id,$access_key,$type=''){
                $bodydata = array(
                                    "grant_type" => "client_credentials",
                                    "client_id" =>$key_id,
                                    "client_secret" => $access_key
                                );
               
                $headerdata = array(
                            'Accept: application/json',
                            'Content-Type: application/json'
                            );
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $url.'/api/oauth/token',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>json_encode($bodydata),
                  CURLOPT_HTTPHEADER => $headerdata,
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $ss = json_decode($response);
                if(isset($ss->access_token)){
                    $dt = $type == "save" ? "success" : $ss; 
                    return $dt;
                }else{
                    $dt = $type == "save" ? "failed" : $ss;
                    return $dt;
                }  
    }
    public function check_web($url){
        $url = $url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch);
        if($header_size["http_code"] > 0){
            return "success";
        }else{
            return "failed";
        }
    }
}