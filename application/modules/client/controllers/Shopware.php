<?php
class Shopware extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "admin" && $this->session->userdata("user_id") != ""){
            redirect(base_url()."client/login");
        }else{
            $this->general->check_if_plan_expire_and_redirect();
            $moduleid = 5;
            $this->general->check_for_allow_module_and_redirect($moduleid);
        }
    }
    public function index(){
            $user_id = $this->session->userdata("user_id");
            $data["page"] = "shopware";
            $data["msg"] = "";
            $data["slist"] = $this->db->get_where("shopware_projects",array("client_id" => $user_id))->result();
            // echo "<pre>";
            // print_r($data["slist"]);die();
            $this->load->view("client/shopware/list",$data);
    }
    public function create(){
    		$user_id = $this->session->userdata("user_id");
            $data["page"] = "shopware";
            $data["msg"] = "";
            $data["pdata"] = array();
            $data["error_data"] = array();
    		$this->load->view("client/shopware/create",$data);
    }
    public function save(){
            $user_id = $this->session->userdata("user_id");
            $project_name = $this->input->post("project_name");
            $url = $this->input->post("url");
            $version = $this->input->post("version");
            $key_id = trim($this->input->post("key_id"));
            $access_key = trim($this->input->post("access_key"));
            if($version == 6){
                $checkcnn = $this->check_connection($url,$key_id,$access_key,"save");
                if($checkcnn == "failed"){
                        $error_data["key_id"] = $this->lang->line("access_key_not_proper");
                }
            }else if($version == 5){
                $checkcnn = $this->check_connection_5($url,$key_id,$access_key,"save");
                if(!$checkcnn){
                        $error_data["key_id"] = $this->lang->line("username_or_api_key_wrong");
                }    
            }
            $checkweb = $this->check_web($url);
            $error_data = array();
            if($checkweb == "failed"){
                 $error_data["url"] = $this->lang->line("shop_url_not_running");
            }
            if(count($error_data) > 0){
                   $data["page"] = "shopware";
                   $data["error_data"] = $error_data;
                   $data["msg"] = "";
                   $data["pdata"] = $this->input->post();
                $this->load->view("client/shopware/create",$data);
            }else{
                $dataa = array(
                                "project_name"  =>$this->encryption->encrypt($project_name), 
                                "url"           =>$this->encryption->encrypt($url), 
                                "client_id" =>$user_id,
                                "added_date" => date("Y-m-d"),
                                "version" => $version
                );
                if($version == 5){
                    $dataa["username"]= $this->encryption->encrypt($key_id);
                    $dataa["api_key"] = $this->encryption->encrypt($access_key);
                }else if($version == 6){
                    $dataa["key_id"]= $this->encryption->encrypt($key_id);
                    $dataa["access_key"] = $this->encryption->encrypt($access_key);
                }
                if($this->db->insert("shopware_projects",$dataa)){
                     $data["msg"] = $this->lang->line("shopware_cred_success_added");
                    $data["page"] = "shopware";
                    $data["pdata"] = array(); 
                    $data["error_data"] = array();
                    $this->load->view("client/shopware/create",$data);
                }else{ 
                    $data["page"] = "shopware";
                    $data["pdata"] = $this->input->post(); 
                    $error_data["errors"] = $this->lang->line("something_wrong");
                    $data["error_data"] = $error_data;
                    $this->load->view("client/shopware/create",$data);
                }
            }
    }
    public function check_connection_5($url,$key_id,$access_key,$type=''){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url.'/api/orders',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.base64_encode($key_id.":".$access_key)),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $check = json_decode($response);
        return $check->success;
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
                  CURLOPT_URL => $url.'api/oauth/token',
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

    public function delete(){
        $user_id = $this->session->userdata("user_id");
        $sproject_id = $this->input->post("sproject_id");
        $this->db->where("sproject_id",$sproject_id);
        $this->db->where("client_id",$user_id);
        if($this->db->delete("shopware_projects")){
              echo json_encode(array("status" => "success","msg" => $this->lang->line("shopware_proj_deleted")));
        }else{
              echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
        }
    }

    public function overview($project_id){
        $user_id = $this->session->userdata("user_id");
        $data["page"] = "shopware";
        $data["sdata"] = $this->db->get_where("shopware_projects",array("client_id" => $user_id,"sproject_id" => $project_id))->row();
        $this->load->view("client/shopware/overview",$data);
    }
    public function getoverviewdata(){
        $user_id = $this->session->userdata("user_id");
        $sproject_id = $this->input->post("sproject_id");
        $dd = $this->db->get_where("shopware_projects",array("client_id" => $user_id,"sproject_id" => $sproject_id))->row();
        $url        = $this->encryption->decrypt($dd->url);
        
        if($dd->version == 6){
            $key_id     = $this->encryption->decrypt($dd->key_id);
            $access_key = $this->encryption->decrypt($dd->access_key);
                $checkcnn = $this->check_connection($url,$key_id,$access_key,"overview");
                    if(isset($checkcnn->access_token)){
                            $alldata  = $this->ovdata($url,$checkcnn->access_token);
                            echo json_encode(array("status" => "success","msg" => $this->lang->line("retrieve_success"),"data" => $alldata));
                    }else{
                        echo json_encode(array("status" => "failed","msg" => $this->lang->line("not_connect_shopware")));
                    } 
        }else{
            $username     = $this->encryption->decrypt($dd->username);
            $api_key = $this->encryption->decrypt($dd->api_key);
            $checkcnn = $this->getoverviewdata_shop($url,$username,$api_key);
                    if(count($checkcnn) > 0){
                            $alldata  = $checkcnn;
                            echo json_encode(array("status" => "success","msg" => $this->lang->line("retrieve_success"),"data" => $alldata));
                    }else{
                        echo json_encode(array("status" => "failed","msg" => $this->lang->line("not_connect_shopware")));
                    } 

        }
        
    }
    public function getoverviewdata_shop($url,$key_id,$access_key){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url.'/api/orders?filter[0][property]=clearedDate&filter[0][expression]=>=&filter[0][value]='.date("Y-m-d"),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.base64_encode($key_id.":".$access_key)),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $check = json_decode($response);
        $totalr = $check->total;
        $tr = 0;
        $i = 0;
        foreach($check->data as $key){
           $tr += $key->invoiceAmount;
        }
        $dat = array("total_orders" => $totalr,"total_revenue" => round($tr,2));
        return $dat;
    }

    public function ovdata($url,$access_token){
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url.'/api/v3/search/order',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                                    "filter":[
                                                {
                                                 "type":"range",
                                                 "field":"orderDate",
                                                 "parameters":{"gte":"'.date("Y-m-d").'"}
                                                 }
                                             ]
                                    }',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$access_token,
                'Content-Type: application/json',
                'Accept: application/json',
                'Cache-Control: no-cache'
              ),
            ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);
        $totalr = $data->total;
        $tr = 0;
        $i = 0;
        foreach($data->data as $key){
           $tr += $key->amountTotal;
        }
        $dat = array("total_orders" => $totalr,"total_revenue" => round($tr,2));
        return $dat;  
    }

    public function edit($sproject_id){
        $user_id = $this->session->userdata("user_id");
        $data["projdata"] = $this->db->get_where("shopware_projects",array("client_id" => $user_id,"sproject_id" => $sproject_id))->row();

        $data["page"] = "shopware";
        $data["msg"]  = "";
        $data["pdata"] = array();
        $data["error_data"] = array();
        $this->load->view("client/shopware/edit",$data);
    }


    public function update(){
            $user_id = $this->session->userdata("user_id");
            $sproject_id = $this->input->post("sproject_id");
            $project_name = $this->input->post("project_name");
            $url = $this->input->post("url");
            $version = $this->input->post("version");
            $key_id = trim($this->input->post("key_id"));
            $access_key = trim($this->input->post("access_key"));
            $checkcnn = $this->check_connection($url,$key_id,$access_key,"save");
            $checkweb = $this->check_web($url);
            $error_data = array();
            $prdata = $this->db->get_where("shopware_projects",array("client_id" => $user_id,"sproject_id" => $sproject_id))->row();
            if($checkweb == "failed"){
                 $error_data["url"] = $this->lang->line("shop_url_not_running");
            }
            if($checkcnn == "failed"){
                $error_data["key_id"] = $this->lang->line("access_key_not_proper");
            }
            if(count($error_data) > 0){
                   $data["page"] = "shopware";
                   $data["projdata"] = $prdata;
                   $data["error_data"] = $error_data;
                   $data["msg"] = "";
                   $data["pdata"] = $this->input->post();
                $this->load->view("client/shopware/edit",$data);
            }else{
                $dataa = array(
                                "project_name"  =>$this->encryption->encrypt($project_name), 
                                "url"           =>$this->encryption->encrypt($url), 
                                "key_id"        =>$this->encryption->encrypt($key_id), 
                                "access_key"    =>$this->encryption->encrypt($access_key),
                                "client_id" =>$user_id, 
                                "version" =>$version,
                );


                $this->db->where("sproject_id",$sproject_id);
                if($this->db->update("shopware_projects",$dataa)){
                    $data["msg"] = $this->lang->line("shopware_cred_success_updated");
                    $prdata = $this->db->get_where("shopware_projects",array("client_id" => $user_id,"sproject_id" => $sproject_id))->row();
                    $data["projdata"] = $prdata; 
                    $data["page"] = "shopware";
                    $data["pdata"] = array(); 
                    $data["error_data"] = array();
                    $this->load->view("client/shopware/edit",$data);
                }else{ 
                    $data["msg"] = "";
                    $data["page"] = "shopware";
                    $prdata = $this->db->get_where("shopware_projects",array("client_id" => $user_id,"sproject_id" => $sproject_id))->row();
                    $data["projdata"] = $prdata;
                    $data["pdata"] = $this->input->post(); 
                    $error_data["errors"] = $this->lang->line("something_wrong");
                    $data["error_data"] = $error_data;
                    $this->load->view("client/shopware/edit",$data);
                }
            }
    }
    public function add_api_key(){
                    $user_id = $this->session->userdata("user_id");
                    $get_api_key = $this->db->get_where("api_keys",array("client_id" => $user_id));
                    if($get_api_key->num_rows()){
                        $keys = $get_api_key->row();
                        $data["api_key"] = $keys->api_key;
                        $data["domain_url"] = $keys->domain_url;
                        $data["api_id"] = $keys->api_id;
                    }else{
                        $data["api_key"] = '';
                        $data["domain_url"]='';
                        $data["api_id"] = 0;
                    }
                    $data["page"] = 'shopware';
                    $this->load->view("client/shopware/api_key",$data);
    }
    public function generatekey(){
        $user_id = $this->session->userdata("user_id");
        $clientdata  = $this->db->get_where("client",array("client_id" => $user_id))->row();
        $generatekey = base64_encode(time()."&_&".$clientdata->client_id."&_&".$clientdata->fname."&_&".$clientdata->lname."&_&".$clientdata->email."&_&".$clientdata->zipcode);
        echo json_encode(array("status" => "success","key" => $generatekey));  
    }
    public function save_key(){
         $user_id = $this->session->userdata("user_id");
         $domain_url = $this->input->post("domain_url");
         $data= array("domain_url" => $domain_url);
         $get = $this->db->get_where("api_keys",array("client_id" =>$user_id));
         if($get->num_rows() > 0){
            $this->db->where("client_id",$user_id);
           $chk = $this->db->update("api_keys",$data);
           $successmsg = $this->lang->line("api_key_updated");
         }else{
            $data["client_id"] = $user_id;
            $chk = $this->db->insert("api_keys",$data);
            $successmsg = $this->lang->line("api_key_added");
         }
         if($chk){
               echo json_encode(array("status" => "success","msg" => $successmsg));   
         }else{
            echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
         }
    }
    public function fetch_record(){
        $project_id = $this->input->post("project_id");
        $from_date = $this->input->post("from_date");
        $to_date = $this->input->post("to_date");
          $status_wise_filter = $this->input->post("status_wise_filter");
          $getprojects = $this->db->get_where("shopware_projects",array("sproject_id" => $project_id))->row();
          if($getprojects->version == 6){
                $g_token = $this->check_connection_fetch($this->encryption->decrypt($getprojects->url),$this->encryption->decrypt($getprojects->key_id),$this->encryption->decrypt($getprojects->access_key));
                if($g_token["status"] == "success"){
                    $getall_records= $this->get_records($this->encryption->decrypt($getprojects->url),$g_token["data"]->access_token,$status_wise_filter,$from_date,$to_date);
                    $customer_count = $this->get_shopware_customer_all_six($this->encryption->decrypt($getprojects->url),$g_token["data"]->access_token);
                    if($getall_records->total > 0){
                        $orderdata = array();
                        $orderdata["total"] = $getall_records->total;
                        $total_revenue = 0;
                        foreach($getall_records->data as $key) {
                           $orderdatas[] = array("order_number" => $key->orderNumber,"customer_name" => $key->orderCustomer->firstName." ".$key->orderCustomer->lastName,"customer_email" => $key->orderCustomer->email,"price" => round($key->amountTotal,2),"shipping_cost" => round($key->shippingTotal,2), "order_date" => $key->orderDateTime,"order_status" => $key->stateMachineState->technicalName);
                           $total_revenue += round($key->amountTotal,2);
                        }
                    
                    $orderdata["total_revenue"] = round($total_revenue,2);
                    $orderdata["orders_data"] = $orderdatas;
                    $orderdata["customer_count"]  = $customer_count;

                    echo json_encode(array("status" => "success","order_data" => $orderdata));
                    }else{
                        echo json_encode(array("status" => "success" , "msg" => "No records found"));
                    }
                }else{
                     echo json_encode(array("status" => "failed" , "msg" => "Authenticated credentials are wrong Please check and tryt once again"));
                }  
          }else if($getprojects->version == 5){

            $getall_records= $this->get_records_five($this->encryption->decrypt($getprojects->url),$this->encryption->decrypt($getprojects->username),$this->encryption->decrypt($getprojects->api_key),$status_wise_filter, $from_date,$to_date);
            if($getall_records->total > 0){
                        $orderdata = array();
                        $orderdata["total"] = $getall_records->total;
                        $total_revenue = 0;
                        foreach($getall_records->data as $key){
                            $customer_info = $this->get_shopware_customer($key->customerId,$this->encryption->decrypt($getprojects->url),$this->encryption->decrypt($getprojects->username),$this->encryption->decrypt($getprojects->api_key));
                            
                            $orderdatas[] = array(
                                                    "order_number" => $key->id,
                                                    "customer_name" => $customer_info->data->firstname." ".$customer_info->data->lastname,
                                                    "customer_email" => $customer_info->data->email,
                                                    "price" => round($key->invoiceAmount,2),
                                                    "shipping_cost" => round($key->invoiceShipping,2),
                                                    "order_date" => $key->orderTime,
                                                    "order_status" => $key->orderStatusId
                                                );
                            $total_revenue += round($key->invoiceAmount,2);
                        }
                        $orderdata["total_revenue"] =  round($total_revenue,2);
                        $orderdata["orders_data"] = $orderdatas;
                        $orderdata["customer_count"] = $this->get_shopware_customer_all($this->encryption->decrypt($getprojects->url),$this->encryption->decrypt($getprojects->username),$this->encryption->decrypt($getprojects->api_key));


                        echo json_encode(array("status" => "success","order_data" => $orderdata));
            }else{
                echo json_encode(array("status" => "success" , "msg" => "No records found"));
            }
          }
    }
     public function get_records($url,$access_token,$status_wise_filter,$from ='',$to=''){
            if($status_wise_filter == "all_records"){
                    $para = array();
            }else if($status_wise_filter == "today"){
                    $para = '{
                                    "filter":[
                                                {
                                                 "type":"range",
                                                 "field":"orderDate",
                                                 "parameters":{"gte":"'.date("Y-m-d").'"}
                                                 }
                                             ]
                                    }';
            }else if($status_wise_filter == "choose_date"){
                    $para = '{
                                    "filter":[
                                                {
                                                 "type":"range",
                                                 "field":"orderDate",
                                                 "parameters":{"gte":"'.$from.'","lte" : "'.$to.'" }
                                                 }
                                             ]
                                    }';
            }
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url.'api/v3/search/order',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $para,
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$access_token,
                'Content-Type: application/json',
                'Accept: application/json',
                'Cache-Control: no-cache'
              ),
            ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);
        return $data;  
    }
    public function check_connection_fetch($url,$key_id,$access_key,$type=''){
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
                  CURLOPT_URL => $url.'api/oauth/token',
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
                    return array("status" => "success","data" => $ss);
                }else{
                    return array("status" => "failed","data" => $ss);
                }  
    }

     public function get_records_five($url,$key_id,$access_key,$status_wise_filter, $from_date,$to_date){
            if($status_wise_filter == "all_records"){
                    $para = "";
            }else if($status_wise_filter == "today"){
                    $para = '?filter[0][property]=clearedDate&filter[0][expression]=>=&filter[0][value]='.date("Y-m-d");
            }else if($status_wise_filter == "choose_date"){
                    $para = '?filter[0][property]=clearedDate&filter[0][expression]=>=&filter[0][value]='.$from_date;
            }
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url.'/api/orders'.$para,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.base64_encode($key_id.":".$access_key)),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $check = json_decode($response);
        return $check;
    }

    public function get_shopware_customer($cust_id,$url,$key_id,$access_key){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url.'/api/customers/'.$cust_id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.base64_encode($key_id.":".$access_key)),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $check = json_decode($response);
        return $check;
    }
    public function get_shopware_customer_all($url,$key_id,$access_key){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url.'/api/customers',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.base64_encode($key_id.":".$access_key)),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $check = json_decode($response);
        return count($check->data);
    }
   
    public function get_shopware_customer_all_six($url,$access_token){
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url.'api/v3/search/customer',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => '',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$access_token,
                'Content-Type: application/json',
                'Accept: application/json',
                'Cache-Control: no-cache'
              ),
            ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);
        return count($data->data);  
    }


}