<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class general{
    function __construct(){
        $this->CI =& get_instance();
    }
    function byteconvert($size,$type)
    {
    
        $type = strtolower($type);
        switch ($type) {
        case "b":
            $output = $size;
            break;
        case "kb":
            $output = $size*1024;
            break;
        case "mb":
            $output = $size*1024*1024;
            break;
        case "gb":
            $output = $size*1024*1024*1024;
            break;
        case "tb":
            $output = $size*1024*1024*1024*1024;
            break;
    }
    return $output;
}
                    function convert_size($size) { 
                             $bytes = $size;
                                $bytes /= 1024;
                                if ($bytes >= 1024 * 1024) {
                                    $bytes /= 1024;
                                   return number_format($bytes / 1024, 1) . ' GB';
                                } elseif($bytes >= 1024 && $bytes < 1024 * 1024) {
                                   return number_format($bytes / 1024, 1) . ' MB';
                                } else {
                                   return number_format($bytes, 1) . ' KB';
                                }



                            }


   function getftp_size($project_id,$path){
            //$getftpdata = $this->CI->db->get_where("ftp_server",array("project_id" => $project_id))->row();
            $getftpdata = $this->get_decrypt_ftp_data_by_id($project_id);
            $config['hostname'] = $getftpdata->hostname;
            $config['username'] = $getftpdata->username;
            $config['password'] =  $getftpdata->password;
            $config['port']     = $getftpdata->port_no;
            $config['passive']  = TRUE;
            $config['debug']    = TRUE;
            if($this->CI->ftp->connect($config)){
                $rtfolderremote = $path;
                $fdatadata = $this->CI->ftp->list_files($rtfolderremote);
                if(!is_array($fdatadata)){
                    $config['passive']  = FALSE;
                    $this->CI->ftp->connect($config);
                    $fdatadata = $this->CI->ftp->list_files($rtfolderremote);
                 }
            }
            $folderdata = array();
            $bytes_value = 0;
                    foreach($fdatadata as $fkey => $fvalue) {
                                if($fkey > 1){
                                if(strpos($fvalue,".") !== false){
                                    $res =  $this->CI->ftp->sizes($rtfolderremote.$fvalue);
                                    if($res != -1) {
                                        $bytes_value += $res;
                                    }
                                }else{
                                    array_push($folderdata,array("remoterootfolder" => $rtfolderremote.$fvalue."/"));
                                }
                                }                   
                    }
                    if(count($folderdata) > 0 ){
                        
                       $allbytes = $this->getsz($bytes_value,$folderdata,$project_id);
                       return $allbytes;
                    }
   }
    function getsz($bytes_value = 0,$data = array(),$project_id){
            $folderdata = array();
            $getftpdata = $this->get_decrypt_ftp_data_by_id($project_id);
            $config['hostname'] = $getftpdata->hostname;
            $config['username'] = $getftpdata->username;
            $config['password'] =  $getftpdata->password;
            $config['port']     = $getftpdata->port_no;
            $config['passive']  = TRUE;
            $config['debug']    = TRUE;
            if($this->CI->ftp->connect($config)){
            foreach($data as $key => $value) {
                $fdatadata = $this->CI->ftp->list_files($data[$key]["remoterootfolder"]);
                foreach($fdatadata as $fkey => $fvalue){
                    if($fkey > 1){
                        if(strpos($fvalue,".") !== false){
                                $res =  $this->CI->ftp->sizes($data[$key]["remoterootfolder"].$fvalue);
                                        if($res != -1) {
                                            $bytes_value += $res;
                                        }    
                        }else if(!empty($fvalue)){

                            array_push($folderdata,array("remoterootfolder" => $data[$key]["remoterootfolder"].$fvalue."/"));
                        }
                    }
                }
            }
        }
            if(count($folderdata) > 0){
                $this->getsz($bytes_value,$folderdata);
            }else{
                return $bytes_value;
            }  
        }
function get_local_directory_size($path){
    $total_size = 0;
    $files = scandir($path);
    $cleanPath = rtrim($path, '/'). '/';
    foreach($files as $t) {
        if ($t<>"." && $t<>"..") {
            $currentFile = $cleanPath . $t;
            if (is_dir($currentFile)){
                if($currentFile == "mysql_server"){
                    continue;
                }else{
                    $size = $this->get_local_directory_size($currentFile);
                    $total_size += $size;
                }
            }
            else {
                $size = filesize($currentFile);
                $total_size += $size;
            }
        }   
    }
    return $total_size;
 }
function checkclient_storage($user_id){
    $get  = $this->CI->db->get_where("client_storage",array("user_id" => $user_id,"mode" => "client"))->row();
    //$getprojects = $this->CI->db->get_where("project",array("client_id" => $user_id))->result();
    $getprojects = $this->get_decrypt_project_data_by_userid($user_id);
    $overallsize = 0;
    foreach($getprojects as $stor) {
       $overallsize += $this->get_local_directory_size("./projects/".$stor->folder_name."/");   
    }
    $sz  = $get->ftp_storage - $overallsize;

    if($get->ftp_storage > $overallsize){
        
        return ["status" => true,"storage_avail" => $sz];
    }else{
        return ["status" => false,"storage_avail" => $sz];
    }
}
function checkclientdb_storage($user_id){
    $get  = $this->CI->db->get_where("client_storage",array("user_id" => $user_id,"mode" => "client"))->row();
    //$getprojects = $this->CI->db->get_where("project",array("client_id" => $user_id))->result();
    $getprojects = $this->get_decrypt_project_data_by_userid($user_id);
    $overallsize = 0;
    foreach($getprojects as $stor) {
       $overallsize += $this->get_local_directory_size("./projects/".$stor->folder_name."/mysql_server/");   
    }
    $sz  = $get->db_storage - $overallsize;
    if($get->ftp_storage > $overallsize){
        return ["status" => true,"storage_avail" => $sz];
    }else{
        return ["status" => false,"storage_avail" => $sz];
    }
}

    

     public function get_decrypt_project_data_by_userid($user_id){
        $projectdata = array();
        if($user_id != ''){
            $projectdata = $this->CI->db->get_where("project",array("client_id" => $user_id))->result();

            if(!empty($projectdata)){
                
                foreach ($projectdata as $ky => $val) {

                     //project data
                    if($projectdata[$ky]->project_name != ''){
                        $projectdata[$ky]->project_name = $this->CI->encryption->decrypt($val->project_name);
                    }

                    if($projectdata[$ky]->slug != ''){
                        $projectdata[$ky]->slug = $this->CI->encryption->decrypt($val->slug);
                    }

                    if($projectdata[$ky]->folder_name != ''){
                        $projectdata[$ky]->folder_name = $this->CI->encryption->decrypt($val->folder_name);
                    }

                    if($projectdata[$ky]->url != ''){
                        $projectdata[$ky]->url = $this->CI->encryption->decrypt($val->url);
                    }


                }

            }
        }
        return $projectdata;
   }



    public function get_decrypt_project_data_by_id($project_id){
        $projectdata = array();
        if($project_id != ''){
            $projectdata = $this->CI->db->get_where("project",array("project_id" => $project_id))->row();

            if(!empty($projectdata)){
                //project data
                if($projectdata->project_name != ''){
                    $projectdata->project_name = $this->CI->encryption->decrypt($projectdata->project_name);
                }

                if($projectdata->slug != ''){
                    $projectdata->slug = $this->CI->encryption->decrypt($projectdata->slug);
                }

                if($projectdata->folder_name != ''){
                    $projectdata->folder_name = $this->CI->encryption->decrypt($projectdata->folder_name);
                }

                if($projectdata->url != ''){
                    $projectdata->url = $this->CI->encryption->decrypt($projectdata->url);
                }
            }
        }
        return $projectdata;
   }



    public function get_decrypt_ftp_data_by_id($project_id){
        
        $getftpserver = array();
        if($project_id != ''){
            $getftpserver = $this->CI->db->get_where("ftp_server", array("project_id" => $project_id))->row();

           //to decrypt data
            if(!empty($getftpserver)){
                
                    //ftp details
                    if($getftpserver->url != ''){
                         $getftpserver->url = $this->CI->encryption->decrypt($getftpserver->url);
                    }

                    if($getftpserver->protocol_type != ''){
                        $getftpserver->protocol_type = $this->CI->encryption->decrypt($getftpserver->protocol_type);
                    }

                    if($getftpserver->username != ''){
                         $getftpserver->username = $this->CI->encryption->decrypt($getftpserver->username);
                    }

                    if($getftpserver->password != ''){
                        $getftpserver->password = $this->CI->encryption->decrypt($getftpserver->password);
                    }

                    if($getftpserver->hostname != ''){
                        $getftpserver->hostname = $this->CI->encryption->decrypt($getftpserver->hostname);
                    }

                    if($getftpserver->root_path != ''){
                       $getftpserver->root_path = $this->CI->encryption->decrypt($getftpserver->root_path);
                    }   

                    if($getftpserver->port_no != ''){
                       $getftpserver->port_no = $this->CI->encryption->decrypt($getftpserver->port_no);
                    }   
            }
        }
        return $getftpserver;


    }



    public function get_decrypt_mysql_data_by_id($project_id){
        
        $getserver = array();
        if($project_id != ''){
            $getserver = $this->CI->db->get_where("mysql_server", array("project_id" => $project_id))->row();
            //to decrypt data
            if(!empty($getserver)){
                
                    if($getserver->mdatabase_name != ''){
                        $getserver->mdatabase_name = $this->CI->encryption->decrypt($getserver->mdatabase_name);
                    }

                    if($getserver->mhostname != ''){
                        $getserver->mhostname      = $this->CI->encryption->decrypt($getserver->mhostname);
                    }

                    if($getserver->musername != ''){
                        $getserver->musername      = $this->CI->encryption->decrypt($getserver->musername);
                    }

                    if($getserver->mpassword != ''){
                        $getserver->mpassword      = $this->CI->encryption->decrypt($getserver->mpassword);
                    } 
            }
        }
        return $getserver;
    }

    //function to check plan is expired or not
    public function check_if_plan_expire(){
        
        $plansubcribed = $this->CI->session->plansubcribed;
        if($plansubcribed == 0){
             return "noplansubcribed";
        }else{
            $plan_expiry_date = $this->CI->session->expiry_date;
            $cash_advance_flag = $this->CI->session->cash_advance_flag;
            $cash_advance_expiry_date = $this->CI->session->cash_advance_expiry_date;
            $todaydate = strtotime(date("Y-m-d"));
            $expiredate = $cash_advance_flag == 0 ? strtotime($plan_expiry_date) : strtotime($cash_advance_expiry_date);

            if($todaydate >= $expiredate){
                return "expired";
            } else {
                return "active";
            }
        }
        
    }


     //function to check plan is expired or not
    public function check_if_plan_expire_and_redirect(){
        
        $plansubcribed = $this->CI->session->plansubcribed;
        if($plansubcribed == 0){
             
             //return "noplansubcribed";
             redirect(base_url()."client/dashboard/");
             exit;

        }else{

            $plan_expiry_date = $this->CI->session->expiry_date;
            $plan_id = $this->CI->session->plan_id;
            $cash_advance_flag = $this->CI->session->cash_advance_flag;
            $cash_advance_expiry_date = $this->CI->session->cash_advance_expiry_date;
            $todaydate = strtotime(date("Y-m-d"));
           // $expiredate = strtotime($plan_expiry_date);
             $expiredate = $cash_advance_flag == 0 ? strtotime($plan_expiry_date) : strtotime($cash_advance_expiry_date);

            if($todaydate >= $expiredate){
                //if plan expired then redirect    
                //return "expired";
                redirect(base_url()."client/dashboard/client_plan/".$plan_id);
                exit;    

            } else {
                return "active";
            } 

        }


       
    }
    
    public function formatBytes($bytes, $precision = 2) {
        $unit = ["B", "KB", "MB", "GB","TB","PB","EB","ZB","YB"];
        $exp = floor(log($bytes, 1024)) | 0;
        return round($bytes / (pow(1024, $exp)), $precision).$unit[$exp];
    }


     //function to check plan is expired or not
    public function check_for_allow_module(){

        $user_id = $this->CI->session->user_id;
        //to get sub details;
        $subdata = $this->CI->db->query("SELECT * FROM subscription_details WHERE user_id = '".$user_id."' AND status = 'active' ORDER BY sub_id DESC")->row();

        $allowmodule =  array();
        if(!empty($subdata)){
            $plandata = json_decode($subdata->plandata);    
            $modules = $plandata->modules;
            $allowmodule = explode(',', $modules);
        }


        //echo '<pre>';
        //print_r($allowmodule);
        //exit;    
        return $allowmodule;

    }


     public function check_for_allow_module_and_redirect($moduleid){

        $user_id = $this->CI->session->user_id;
        //to get sub details;
        $subdata = $this->CI->db->query("SELECT * FROM subscription_details WHERE user_id = '".$user_id."' AND status = 'active' ORDER BY sub_id DESC")->row();

        $allowmodule =  array();
        if(!empty($subdata)){
            $plandata = json_decode($subdata->plandata);    
            $modules = $plandata->modules;
            $allowmodule = explode(',', $modules);
        }

        if(!in_array($moduleid , $allowmodule)){
            $this->CI->session->set_flashdata('notallow', true);
            redirect(base_url()."client/dashboard/");
            exit;    
        }else{
            return true;
        }

            

     }
    public function resizeImage($source_path,$target_path){
      $source_path = $_SERVER['DOCUMENT_ROOT'] . $source_path;
      $target_path = $_SERVER['DOCUMENT_ROOT'] . $target_path;
      $config_manip = array(
          'image_library' => 'gd2',
          'source_image' => $source_path,
          'new_image' => $target_path,
          'maintain_ratio' => TRUE,
          'create_thumb' => TRUE,
          'thumb_marker' => '',
          'width' => 150,
          'height' => 150
      );
      $this->CI->load->library('image_lib', $config_manip);
      if (!$this->CI->image_lib->resize()) {
          echo $this->CI->image_lib->display_errors();
      }
      $this->CI->image_lib->clear();
   }
}