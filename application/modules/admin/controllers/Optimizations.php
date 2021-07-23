<?php
class Optimizations extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        if($this->session->userdata("role_type") != "admin"){
            redirect(base_url()."admin/login");
        }
    }
  
    public function alive_system($project_id){
        $project_id = base64_decode($project_id);
        $project_details = $this->db->get_where("project",array("project_id" =>$project_id))->row();
        $data["project_details"] = $project_details;
        $data["headerdata"] = $this->checksystemresponse($this->encryption->decrypt($project_details->url));
    	$data["page"] 	  = "projects"; 
    	$this->load->view("admin/optimizations/alive_system",$data); 
    }
    public function checksystemresponse($url){
                                    $header_size = array();
                                    if ($url != '') {
                                        $url_data = parse_url($url);
                                        if(!isset($url_data["scheme"])){
                                            $url = 'http://'.$url;
                                        }

                                        $ch = curl_init($url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt($ch, CURLOPT_VERBOSE, 1);
                                        curl_setopt($ch, CURLOPT_HEADER, 1);
                                        $response = curl_exec($ch);
                                        $header_size = curl_getinfo($ch);
                                        if($header_size["http_code"] != 200){
                                            $url_data = parse_url($url);
                                            if($url_data["scheme"] === "http"){
                                                $ss = "https://".$url_data["host"];
                                                return $this->checksystemresponse($ss);
                                            }
                                        }
                                    }
                                    return $header_size;
                                }
}