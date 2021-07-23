<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 3000);
require APPPATH . 'libraries/REST_Controller.php';
class API extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $received_Token = $this->input->request_headers();
        $this->userapikey = $received_Token["Token"];
    }

    //check for autarization and return user data
    public function checkAuthorization(){

        $received_Token = $this->input->request_headers();
        if(!empty($received_Token["Token"])){
            $userapikey = $received_Token["Token"];
            $getdatabyapikey = $this->db->get_where("client",array("userapikey" => $userapikey));
            if($getdatabyapikey->num_rows() > 0){
                $userdata = $getdatabyapikey->row();
                return $userdata;
            }else{
                $response['code']=403;
                $response['message']='Invalid Request, Authorization Failed';
                echo json_encode($response);
                exit;
            }
        }else{
           $response['code']=403;
           $response['message']='Invalid Request, Authorization Failed';
           echo json_encode($response);
           exit;
        }
    } 
    public function testingapi_get(){

        echo phpinfo();
       
        // $authData = $this->checkAuthorization();
        // $jsondata = json_decode(file_get_contents("php://input"));

        // echo '<pre>';
        // print_r($authData);
        // print_r($jsondata);
        // exit;

    }




    public function addproject_post(){
       
        $authData = $this->checkAuthorization();
        $jsondata = json_decode(file_get_contents("php://input"));


        if($authData->client_id > 0 && $authData->userapikey == $this->userapikey){
            $name  = trim($jsondata->project_name);
            $url   = trim(strtolower($jsondata->url));
            $slug  = trim(str_replace(' ', '_', strtolower($jsondata->slug) ));

            if( $name != '' && $url != '' && $slug != '' ) {

                if (!empty($url)) {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                    $res = curl_exec($ch);
                    $header_size = curl_getinfo($ch);
                    $errorcodes = array(0);
                    if (in_array($header_size["http_code"], $errorcodes)) {
                        $response['status'] = $this->lang->line("failed");
                        $response['message']= $this->lang->line("Webpage not reachable");
                        echo json_encode($response);
                        exit;
                    }
                }
                $added_date = strtotime(date("Y-m-d"));
                $date = date("Y-m-d");
                $folder_name = $slug . "_" . $added_date;

                mkdir("./projects/" . $folder_name);
                mkdir("./projects/" . $folder_name . "/ftp_server");
                mkdir("./projects/" . $folder_name . "/ftp_server/temp");
                mkdir("./projects/" . $folder_name . "/ftp_server/syncbackup");
                mkdir("./projects/" . $folder_name . "/mysql_server");
                mkdir("./projects/" . $folder_name . "/mysql_server/db");
                mkdir("./projects/" . $folder_name . "/mysql_server/dbrestore");
                mkdir("./projects/" . $folder_name . "/mysql_server/dbcheck");
                
                $data = array(  "project_name"  => $this->encryption->encrypt($name), 
                                "slug"          => $this->encryption->encrypt($slug), 
                                "added_date"    => $date, 
                                "folder_name"   => $this->encryption->encrypt($folder_name), 
                                "datetimestamp" => $added_date, 
                                "client_id"     => $authData->client_id, 
                                "url"           => $this->encryption->encrypt($url)
                            );  
                $this->db->insert("project", $data);
                $project_id = $this->db->insert_id();
                if($project_id > 0){
                    $this->db->insert("ftp_server", ['client_id' => $authData->client_id, "project_id" => $project_id]);
                    $this->db->insert("mysql_server", ['client_id' => $authData->client_id, "project_id" => $project_id]);
                    $response['status'] =$this->lang->line("success");
                    $response['message']=$this->lang->line("project_add_msg");
                    echo json_encode($response);
                    exit;
                }else{
                    $response['status'] = $this->lang->line("failed");
                    $response['message']=$this->lang->line("something_wrong");
                    echo json_encode($response);
                    exit;
                }
                
            }else{
                $response['status'] = $this->lang->line("failed");
                $response['message']="Incomplete data provided";
                echo json_encode($response);
                exit;
            }

        }else{
            $response['status'] = $this->lang->line("failed");
            $response['message']= $this->lang->line("Authorization Failed");
            echo json_encode($response);
            exit;
        }

        //END
    }


     public function updateproject_post(){
       
        $authData = $this->checkAuthorization();
        $jsondata = json_decode(file_get_contents("php://input"));

        if($authData->client_id > 0 && $authData->userapikey == $this->userapikey){
            $project_id  = $jsondata->project_id;
            $name        = trim($jsondata->project_name);
            $url         = trim(strtolower($jsondata->url));

            if( $name != '' && $url != '' && $project_id > 0 && is_numeric($project_id)) {

                if (!empty($url)) {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                    $res = curl_exec($ch);
                    $header_size = curl_getinfo($ch);
                    $errorcodes = array(0);
                    if (in_array($header_size["http_code"], $errorcodes)) {
                        $response['status'] = $this->lang->line("failed");
                        $response['message']=$this->lang->line("Webpage not reachable");
                        echo json_encode($response);
                        exit;
                    }
                }

                $data = array(  
                            "project_name" => $this->encryption->encrypt($name),
                            "url" => $this->encryption->encrypt($url)
                        );

                $this->db->where("project_id", $project_id);
                $this->db->where("client_id", $authData->client_id);
                if ($this->db->update("project", $data)) {
                    $response['status'] = $this->lang->line("success");
                    $response['message']= $this->lang->line("project_update_msg");
                    echo json_encode($response);
                    exit;
                } else {
                    $response['status'] = $this->lang->line("failed");
                    $response['message']=$this->lang->line("something_wrong");
                    echo json_encode($response);
                    exit;
                }

            }else{
                $response['status'] = $this->lang->line("failed");
                $response['message']="Incomplete data provided";
                echo json_encode($response);
                exit;
            }

        }else{
            $response['status'] = $this->lang->line("failed");
            $response['message']= $this->lang->line("Authorization Failed");
            echo json_encode($response);
            exit;
        }

        //END
    }



     public function setftp_post(){
       
        $authData = $this->checkAuthorization();
        $jsondata = json_decode(file_get_contents("php://input"));

        if($authData->client_id > 0 && $authData->userapikey == $this->userapikey){


            $client_id      = $authData->client_id;
            $project_id     = $jsondata->project_id;
            $protocol_type  = trim($jsondata->protocol_type);
            $hostname       = trim($jsondata->hostname);
            $username       = trim($jsondata->username);
            $password       = trim($jsondata->password);
            $domain_url     = trim($jsondata->domain_url);
            $root_path      = trim($jsondata->root_path);
            $autobkphrs     = trim($jsondata->auto_backup_hrs);
            //$projectdata    = $this->db->get_where("ftp_server", array("ftp_id" => $ftp_id))->row();


            if($project_id != '' && $protocol_type != '' && $hostname != '' && $username != ''  && $password != '' && $domain_url != '' && $root_path != '' && $autobkphrs != ''){

                if ($protocol_type == "ftp") {
                    $port_no = 21;
                } else if ($protocol_type == "sftp") {
                    $port_no = 22;
                }

                $config['hostname'] = $hostname;
                $config['username'] = $username;
                $config['password'] = $password;
                $config['port'] = $port_no;
                $config['passive'] = TRUE;
                $config['debug'] = FALSE;
                $ftpstatus = 0;

                if ($this->ftp->connect($config)) {
                    $ftpstatus = 1;
                } else {
                    $config['passive'] = FALSE;
                    if ($this->ftp->connect($config)) {
                        $ftpstatus = 1;
                    } else {
                        $ftpstatus = 0;
                    }
                }

                 if ($ftpstatus == 1) {
                    $this->db->where("project_id", $project_id);
                    $this->db->update("project", ["ftp_status" => 1]);
                    $data = array(
                        "protocol_type" => $this->encryption->encrypt($protocol_type), 
                        "hostname"      => $this->encryption->encrypt($hostname), 
                        "username"      => $this->encryption->encrypt($username), 
                        "password"      => $this->encryption->encrypt($password), 
                        "port_no"       => $this->encryption->encrypt($port_no), 
                        "url"           => $this->encryption->encrypt($domain_url), 
                        "root_path"     => $this->encryption->encrypt($root_path), 
                        "auto_backup_hours" => $autobkphrs
                    );

                    $this->db->where("client_id", $client_id);
                    $this->db->where("project_id", $project_id);
                    if ($this->db->update("ftp_server", $data)) {
                        $output = array("status" => "success", "message" => $this->lang->line("ftp_update_success_msg"));
                        echo json_encode($output);
                        exit;
                    } else {
                        $output = array("status" => "failed", "message" => $this->lang->line("something_wrong"));
                        echo json_encode($output);
                        exit;
                    }
                } else {
                    $output = array("status" => "failed", "message" => $this->lang->line("cred_wrong"));
                    echo json_encode($output);
                    exit;
                }



            }else{

                $response['status'] = $this->lang->line("failed");
                $response['message']="Incomplete data provided";
                echo json_encode($response);
                exit;
                
            }


        }else{
            $response['status'] = $this->lang->line("failed");
            $response['message']= $this->lang->line("Authorization Failed");
            echo json_encode($response);
            exit;
        } 

        //end
    }   





    public function setdb_post(){

        $authData = $this->checkAuthorization();
        $jsondata = json_decode(file_get_contents("php://input"));

        if($authData->client_id > 0 && $authData->userapikey == $this->userapikey){

            $client_id      = $authData->client_id;
            $project_id     = $jsondata->project_id;
            $mdatabase_name = trim($jsondata->database_name);
            $mhostname      = trim($jsondata->hostname);
            $musername      = trim($jsondata->username);
            $mpassword      = trim($jsondata->password);
            $mautobkphrs    = trim($jsondata->auto_backup_hrs);

            if($project_id != '' && $mdatabase_name != '' && $mhostname != '' && $musername != ''  && $mpassword != '' && $mautobkphrs != ''){

                $sqlcheck = 0;
                if ($mhostname == "localhost" || $mhostname == "127.0.0.1" || $mhostname == "") {
                    $getproj = $this->db->get_where("project", array("project_id" => $project_id))->row();
                    if (!empty($getproj)) {
                        //project data
                        if ($getproj->project_name != '') {
                            $getproj->project_name = $this->encryption->decrypt($getproj->project_name);
                        }
                        if ($getproj->slug != '') {
                            $getproj->slug = $this->encryption->decrypt($getproj->slug);
                        }
                        if ($getproj->folder_name != '') {
                            $getproj->folder_name = $this->encryption->decrypt($getproj->folder_name);
                        }
                        if ($getproj->url != '') {
                            $getproj->url = $this->encryption->decrypt($getproj->url);
                        }
                    }
                    //echo '<pre>';
                    //print_r($getproj);
                    //exit;
                    $ftpdata = $this->db->get_where("ftp_server", array("project_id" => $project_id))->row();
                    if ($getproj->ftp_status == 1) {
                        $config['hostname'] = $this->encryption->decrypt($ftpdata->hostname);
                        $config['username'] = $this->encryption->decrypt($ftpdata->username);
                        $config['password'] = $this->encryption->decrypt($ftpdata->password);
                        $config['port'] = $this->encryption->decrypt($ftpdata->port_no);
                        $config['passive'] = TRUE;
                        $config['debug'] = TRUE;
                        if ($this->ftp->connect($config)) {
                            $fdatadata = $this->ftp->list_files("/");
                            if (!is_array($fdatadata)) {
                                $config['passive'] = FALSE;
                                $this->ftp->connect($config);
                                $fdatadata = $this->ftp->list_files("/");
                            }
                            $file = './projects/sqlexecute.php';
                            $current = file_get_contents($file);
                            $filedata = str_replace('{username}', $musername, $current);
                            $filedata = str_replace('{password}', $mpassword, $filedata);
                            $filedata = str_replace('{database}', $mdatabase_name, $filedata);
                            $myfile = fopen("./projects/" . $getproj->folder_name . "/mysql_server/dbcheck/sqlexecute.php", "w");
                            fwrite($myfile, $filedata);
                            fclose($myfile);
                            if (in_array("public_html", $fdatadata) || in_array("/public_html", $fdatadata)) {
                                $this->ftp->mirror("./projects/" . $getproj->folder_name . "/mysql_server/dbcheck/", "/public_html/");
                            } else {
                                $this->ftp->mirror("./projects/" . $getproj->folder_name . "/mysql_server/dbcheck/", "/");
                            }
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $this->encryption->decrypt($ftpdata->root_path) . "/sqlexecute.php");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $output = curl_exec($ch);
                            $info = curl_getinfo($ch);
                            curl_close($ch);
                            if ($info['http_code'] <> 404) {
                                $sdsdf = json_decode($output);
                                if ($sdsdf->status == "success") {
                                    $sqlcheck = 1;
                                } else {
                                    $sqlcheck = 0;
                                }
                            } else {
                                $sqlcheck = 3;
                            }
                        }
                    } else {
                        $sqlcheck = 2;
                    }
                } else {
                    set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                        throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                    }, E_WARNING);
                    //$link = mysqli_connect($mhostname, $musername, $mpassword);
                    try {
                        $conn = mysqli_connect($mhostname, $musername, $mpassword, $mdatabase_name);
                        if ($conn) {
                            $sqlcheck = 1;
                        }
                    }
                    catch(\Throwable $e) {
                        $output = array("status" => "failed", "message" => $e->getMessage());
                        echo json_encode($output);
                        exit;
                    }
                    restore_error_handler();
                }

                 if ($sqlcheck == 1) {
                    $this->db->where("project_id", $project_id);
                    $this->db->update("project", ["sql_status" => 1]);
                    $data = array(
                            "mdatabase_name" => $this->encryption->encrypt($mdatabase_name), 
                            "mhostname" => $this->encryption->encrypt($mhostname), 
                            "musername" => $this->encryption->encrypt($musername), 
                            "mpassword" => $this->encryption->encrypt($mpassword), 
                            "auto_backup_hours" => $mautobkphrs
                        );
                    
                    $this->db->where("client_id", $client_id);
                    $this->db->where("project_id", $project_id);
                    if ($this->db->update("mysql_server", $data)) {
                        $output = array("status" => "success", "message" => $this->lang->line("sql_setup_msg"));
                    } else {
                        $output = array("status" => "failed", "message" => $this->lang->line("something_wrong"));
                    }
                } else {
                    if ($sqlcheck == 3) {
                        $msg = $this->lang->line("ftp_root_wrong");
                    } else if ($sqlcheck == 2) {
                        $msg = $this->lang->line("ftp_setup_not_done");
                    } else {
                        $msg = $this->lang->line("sql_cred_wrong");
                    }
                    $output = array("status" => "failed", "message" => $msg);
                }
                echo json_encode($output);


            }else{
                $response['status'] = $this->lang->line("failed");
                $response['message']="Incomplete data provided";
                echo json_encode($response);
                exit;
            }   


        }else{
            $response['status'] = $this->lang->line("failed");
            $response['message']= $this->lang->line("Authorization Failed");
            echo json_encode($response);
            exit;
        } 

        //end
    }




    public function restoredatabasebackup_post(){

        $authData = $this->checkAuthorization();
        $jsondata = json_decode(file_get_contents("php://input"));


        if($authData->client_id > 0 && $authData->userapikey == $this->userapikey){

            $client_id  = $authData->client_id;
           


        }else{
            $response['status'] = $this->lang->line("failed");
            $response['message']= $this->lang->line("Authorization Failed");
            echo json_encode($response);
            exit;
        }   
    }




    public function listdatabasebackup_post(){

        $authData = $this->checkAuthorization();
        $jsondata = json_decode(file_get_contents("php://input"));


        if($authData->client_id > 0 && $authData->userapikey == $this->userapikey){

            $client_id  = $authData->client_id;
            $user_id    = $client_id;
            $data["backups"] = $this->db->query("select bf.*,p.project_name,p.slug,p.folder_name from backupsql bf inner join project p on p.project_id = bf.project_id AND p.status = 'active' where bf.client_id =  ".$user_id."")->result();

           if(!empty($data["backups"])){
                foreach ($data["backups"] as $ky => $val) {
                    
                    if($val->project_name != ''){
                         $data["backups"][$ky]->project_name  = $this->encryption->decrypt($val->project_name);
                    }

                    if($val->slug != ''){
                         $data["backups"][$ky]->slug = $this->encryption->decrypt($val->slug);
                    }

                    if($val->folder_name != ''){
                         $data["backups"][$ky]->folder_name = $this->encryption->decrypt($val->folder_name);
                    }

                    if($val->file_name != ''){
                         $data["backups"][$ky]->file_name = $this->encryption->decrypt($val->file_name);
                    }

                }
            }else{
                $data['message']= $this->lang->line("no_records_found");
            }

            echo json_encode($data);
            exit;


        }else{
            $response['status'] = $this->lang->line("failed");
            $response['message']= $this->lang->line("Authorization Failed");
            echo json_encode($response);
            exit;
        }   
    }




    
    public function backupdb_post(){

        $authData = $this->checkAuthorization();
        $jsondata = json_decode(file_get_contents("php://input"));

        if($authData->client_id > 0 && $authData->userapikey == $this->userapikey){

            $client_id      = $authData->client_id;
            $project_id     = $jsondata->project_id;

            if($project_id != '' && $client_id != ''){


                $getserver = $this->db->get_where("mysql_server", array("project_id" => $project_id))->row();
                $getftpserver = $this->db->get_where("ftp_server", array("project_id" => $project_id))->row();
                $getproject = $this->general->get_decrypt_project_data_by_id($project_id);
                //$user_id = $getproject->client_id;
                $user_id = $client_id;
                $getusrdd = $this->db->get_where('client', array("client_id" => $user_id))->row();
                $checkdbstorage = $this->general->checkclientdb_storage($user_id);
                //to decrypt data
                if (!empty($getserver)) {
                    if ($getserver->mdatabase_name != '') {
                        $getserver->mdatabase_name = $this->encryption->decrypt($getserver->mdatabase_name);
                    }
                    if ($getserver->mhostname != '') {
                        $getserver->mhostname = $this->encryption->decrypt($getserver->mhostname);
                    }
                    if ($getserver->musername != '') {
                        $getserver->musername = $this->encryption->decrypt($getserver->musername);
                    }
                    if ($getserver->mpassword != '') {
                        $getserver->mpassword = $this->encryption->decrypt($getserver->mpassword);
                    }
                }
                //to decrypt data
                if (!empty($getftpserver)) {
                    //ftp details
                    if ($getftpserver->url != '') {
                        $getftpserver->url = $this->encryption->decrypt($getftpserver->url);
                    }
                    if ($getftpserver->protocol_type != '') {
                        $getftpserver->protocol_type = $this->encryption->decrypt($getftpserver->protocol_type);
                    }
                    if ($getftpserver->username != '') {
                        $getftpserver->username = $this->encryption->decrypt($getftpserver->username);
                    }
                    if ($getftpserver->password != '') {
                        $getftpserver->password = $this->encryption->decrypt($getftpserver->password);
                    }
                    if ($getftpserver->hostname != '') {
                        $getftpserver->hostname = $this->encryption->decrypt($getftpserver->hostname);
                    }
                    if ($getftpserver->root_path != '') {
                        $getftpserver->root_path = $this->encryption->decrypt($getftpserver->root_path);
                    }
                    if ($getftpserver->port_no != '') {
                        $getftpserver->port_no = $this->encryption->decrypt($getftpserver->port_no);
                    }
                }
                if ($checkdbstorage["status"] == true || $getusrdd->role_id == 1) {
                    $startdate = date("Y-m-d H:i:s");
                    if ($getserver->mhostname == "localhost" || $getserver->mhostname == "127.0.0.1") {
                        $file = './projects/mysqlserverbackup.php';
                        $current = file_get_contents($file);
                        $filedata = str_replace('{username}', $getserver->musername, $current);
                        $filedata = str_replace('{password}', $getserver->mpassword, $filedata);
                        $filedata = str_replace('{database}', $getserver->mdatabase_name, $filedata);
                        $myfile = fopen("./projects/" . $getproject->folder_name . "/mysql_server/db/Mysqldailbackudatabasefile.php", "w");
                        fwrite($myfile, $filedata);
                        fclose($myfile);
                        $config['hostname'] = $getftpserver->hostname;
                        $config['username'] = $getftpserver->username;
                        $config['password'] = $getftpserver->password;
                        $config['port'] = $getftpserver->port_no;
                        $config['passive'] = TRUE;
                        $config['debug'] = TRUE;
                        if ($this->ftp->connect($config)) {
                            $fdatadata = $this->ftp->list_files("/");
                            if (!is_array($fdatadata)) {
                                $config['passive'] = FALSE;
                                $this->ftp->connect($config);
                                $fdatadata = $this->ftp->list_files("/");
                            }
                            if (in_array("public_html", $fdatadata) || in_array("/public_html", $fdatadata)) {
                                $this->ftp->mirror("./projects/" . $getproject->folder_name . "/mysql_server/db/", "/public_html/");
                            } else {
                                $this->ftp->mirror("./projects/" . $getproject->folder_name . "/mysql_server/db/", "/");
                            }
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $getftpserver->root_path . "/Mysqldailbackudatabasefile.php");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $output = curl_exec($ch);
                            curl_close($ch);
                            $sdsdf = json_decode($output);
                            if ($sdsdf->status == "success") {
                                if (in_array("public_html", $fdatadata) || in_array("/public_html", $fdatadata)) {
                                    $dbfil = "/public_html/dbbackupcloudserviceworld.sql";
                                    $this->ftp->download($dbfil, "./projects/" . $getproject->folder_name . "/mysql_server/dbbackupcloudserviceworld.sql", "auto");
                                } else {
                                    $dbfil = "/dbbackupcloudserviceworld.sql";
                                    $this->ftp->download($dbfil, "./projects/" . $getproject->folder_name . "/mysql_server/dbbackupcloudserviceworld.sql", "auto");
                                }
                                if ($checkdbstorage["storage_avail"] >= $this->ftp->sizes($dbfil) || $getusrdd->role_id == 1) {
                                    $newtimestamp = time();
                                    $newfilename = $getproject->slug . "_" . $newtimestamp . ".sql";
                                    rename("./projects/" . $getproject->folder_name . "/mysql_server/dbbackupcloudserviceworld.sql", "./projects/" . $getproject->folder_name . "/mysql_server/" . $newfilename);
                                    $insdata = array("project_id" => $getproject->project_id, "client_id" => $getproject->client_id, "file_name" => $this->encryption->encrypt($newfilename), "timestamp_date" => $newtimestamp);
                                    $this->db->insert("backupsql", $insdata);
                                    $msg = $this->lang->line("DB Backup have Successfully taken on " . date("Y-m-d H:i:s"));
                                    $subject = $this->lang->line("Auto DB Backup Process when host name is localhost or 127.0.0.1 through FTP Process");
                                    $to = $this->session->userdata("email");
                                    $checkerror = 0;
                                } else {
                                    $msg = $this->lang->line("Do not have Sufficient DB Storage Space");
                                    $subject = $this->lang->line("Auto DB Backup Process");
                                    $to = $this->session->userdata("email");
                                    $checkerror = 1;
                                }
                            } else {
                                $msg = $this->lang->line("You have provided wrong Root Path of your website");
                                $subject = $this->lang->line("Auto DB Backup Process when host name is localhost or 127.0.0.1 through FTP Process");
                                $to = $this->session->userdata("email");
                                $checkerror = 1;
                            }
                        } else {
                            $msg = $this->lang->line("No Files & Folders are in FTP credentials provided ");
                            $subject = $this->lang->line("Auto DB Backup Process when host name is localhost or 127.0.0.1");
                            $to = $this->session->userdata("email");
                            $checkerror = 1;
                        }
                    } else {
                        $dbhost = $getserver->mhostname;
                        $dbuser = $getserver->musername;
                        $dbpass = $getserver->mpassword;
                        $dbname = $getserver->mdatabase_name;
                        $tables = '*';
                        $file_name = $getproject->slug . "_" . time() . ".sql";
                        $path = "./projects/" . $getproject->folder_name . "/mysql_server/" . $file_name;
                        $returndata = $this->backup_tables($dbhost, $dbuser, $dbpass, $dbname, $tables, $path, $checkdbstorage["storage_avail"], $project_id, $user_id);
                        if ($returndata["status"] == "success") {
                            $msg = $returndata["msg"];
                            $subject = $returndata["subject"];
                            $to = $returndata["to"];
                            $checkerror = 0;
                            $insdata = array("project_id" => $project_id, "client_id" => $user_id, "file_name" =>  $this->encryption->encrypt($file_name), "timestamp_date" => time(), "startdate" => $startdate, "enddate" => date("Y-m-d H:i:s"));
                            $this->db->insert("backupsql", $insdata);
                        } else {
                            $msg = $returndata["msg"];
                            $subject = $returndata["subject"];
                            $to = $returndata["to"];
                            $checkerror = 1;
                        }
                    }
                } else {
                    $msg = $this->lang->line("Do not have Sufficient DB Storage Space");
                    $subject = $this->lang->line("Auto DB Backup Process");
                    $to = $this->session->userdata("email");
                    $checkerror = 1;
                }
                $this->send_mail($msg, $subject, $to);
                if ($checkerror == 1) {
                    $msg_data = ["user_id" => $user_id, "project_id" => $project_id, "error_title" => $subject, "descriptions" => $msg];
                    $this->db->insert("db_errors", $msg_data);
                    $msg_data["status"] = "failed";
                    $msg_data["msg"] = $msg;
                } else {
                    $msg_data = ["status" => "success", "msg" => $msg];
                }


                $response['status'] = $msg_data["status"];
                $response['message']= $msg_data["msg"];
                echo json_encode($response);
                exit;
                

            }else{
                $response['status'] = $this->lang->line("failed");
                $response['message']="Incomplete data provided";
                echo json_encode($response);
                exit;
            }  


        }else{
            $response['status'] = $this->lang->line("failed");
            $response['message']= $this->lang->line("Authorization Failed");
            echo json_encode($response);
            exit;
        }

        //END
    }

     


    public function backup_tables($host, $user, $pass, $dbname, $tables = '*', $path, $storage_avalable, $project_id, $user_id) {
        $getuser = $this->db->get_where("client", array("client_id" => $user_id))->row();
        $link = mysqli_connect($host, $user, $pass, $dbname);
        $startdate = date("Y-m-d H:i:s");
        if (mysqli_connect_errno()) {
            $msg = $this->lang->line("Failed to connect to MySQL: ") . mysqli_connect_error();
            $subject = $this->lang->line("DB Backup Process");
            $to = $this->session->userdata("email");
            $checkerror = 1;
            return array("status" => "failed", "msg" => $msg, "subject" => $subject, "to" => $to, "checkerror" => $checkerror);
        } else {
            mysqli_query($link, "SET NAMES 'utf8'");
            if ($tables == '*') {
                $tables = array();
                $result = mysqli_query($link, 'SHOW TABLES');
                while ($row = mysqli_fetch_row($result)) {
                    $tables[] = $row[0];
                }
            } else {
                $tables = is_array($tables) ? $tables : explode(',', $tables);
            }
            $return = '';
            foreach ($tables as $table) {
                $result = mysqli_query($link, 'SELECT * FROM ' . $table);
                $num_fields = mysqli_num_fields($result);
                $num_rows = mysqli_num_rows($result);
                $return.= 'DROP TABLE IF EXISTS ' . $table . ';';
                $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE ' . $table));
                $return.= "\n\n" . $row2[1] . ";\n\n";
                $counter = 1;
                //Over tables
                for ($i = 0;$i < $num_fields;$i++) { //Over rows
                    while ($row = mysqli_fetch_row($result)) {
                        if ($counter == 1) {
                            $return.= 'INSERT INTO ' . $table . ' VALUES(';
                        } else {
                            $return.= '(';
                        }
                        //Over fields
                        for ($j = 0;$j < $num_fields;$j++) {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = str_replace("\n", "\\n", $row[$j]);
                            if (isset($row[$j])) {
                                $return.= '"' . $row[$j] . '"';
                            } else {
                                $return.= '""';
                            }
                            if ($j < ($num_fields - 1)) {
                                $return.= ',';
                            }
                        }
                        if ($num_rows == $counter) {
                            $return.= ");\n";
                        } else {
                            $return.= "),\n";
                        }
                        ++$counter;
                    }
                }
                $return.= "\n\n\n";
            }
            $fileName = $path;
            $handle = fopen($fileName, 'w+');
            fwrite($handle, $return);
            if (file_exists($fileName)) {
                $msg = $this->lang->line("DB Backup have Successfully taken on") . date("Y-m-d H:i:s");
                $subject = $this->lang->line("Auto DB Backup Process");
                $to = $this->session->userdata("email");
                $checkerror = 0;
                return array("status" => "success", "msg" => $msg, "subject" => $subject, "to" => $getuser->email, "checkerror" => $checkerror, "file_name" => $fileName, "startdate" => $startdate);
            } else {
                $msg = $this->lang->line("Something went wrong");
                $subject = $this->lang->line("Auto DB Backup Process");
                $to = $this->session->userdata("email");
                $checkerror = 1;
                return array("status" => "failed", "msg" => $msg, "subject" => $subject, "to" => $getuser->email, "checkerror" => $checkerror);
            }
        }
    }



     public function send_mail($msg, $subject, $to) {
        $get_setting = $this->db->query("select name_value from site_setting where setting_id IN(7,8)")->result();
        $this->email->set_newline("\r\n");
        $this->email->from($get_setting[0]->name_value, $get_setting[1]->name_value);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($msg);
        $this->email->send();
    }






    public function backupftp_post(){

        $authData = $this->checkAuthorization();
        $jsondata = json_decode(file_get_contents("php://input"));

        if($authData->client_id > 0 && $authData->userapikey == $this->userapikey){

            $client_id      = $authData->client_id;
            $project_id     = $jsondata->project_id;
            $rtfolderremote = $jsondata->rootfolder;
            //$data           = $this->input->post("folderid");

            if($project_id != '' && $client_id != ''){


                $rtfolderremote = base64_decode($this->input->post("rootfolder"));
                $projectdata = $this->db->get_where('project', array("project_id" => $project_id))->row();
                $user_id = $client_id;
                $getusrdd = $this->db->get_where('client', array("client_id" => $user_id))->row();
                $projectftp = $this->db->get_where('ftp_server', array("project_id" => $project_id))->row();
                
                if (!empty($projectdata)) {
                    //project data
                    if ($projectdata->project_name != '') {
                        $projectdata->project_name = $this->encryption->decrypt($projectdata->project_name);
                    }
                    if ($projectdata->slug != '') {
                        $projectdata->slug = $this->encryption->decrypt($projectdata->slug);
                    }
                    if ($projectdata->folder_name != '') {
                        $projectdata->folder_name = $this->encryption->decrypt($projectdata->folder_name);
                    }
                    if ($projectdata->url != '') {
                        $projectdata->url = $this->encryption->decrypt($projectdata->url);
                    }
                }
                //to decrypt data
                if (!empty($projectftp)) {
                    //ftp details
                    if ($projectftp->url != '') {
                        $projectftp->url = $this->encryption->decrypt($projectftp->url);
                    }
                    if ($projectftp->protocol_type != '') {
                        $projectftp->protocol_type = $this->encryption->decrypt($projectftp->protocol_type);
                    }
                    if ($projectftp->username != '') {
                        $projectftp->username = $this->encryption->decrypt($projectftp->username);
                    }
                    if ($projectftp->password != '') {
                        $projectftp->password = $this->encryption->decrypt($projectftp->password);
                    }
                    if ($projectftp->hostname != '') {
                        $projectftp->hostname = $this->encryption->decrypt($projectftp->hostname);
                    }
                    if ($projectftp->root_path != '') {
                        $projectftp->root_path = $this->encryption->decrypt($projectftp->root_path);
                    }
                    if ($projectftp->port_no != '') {
                        $projectftp->port_no = $this->encryption->decrypt($projectftp->port_no);
                    }
                }
                if ($rtfolderremote == "/") {
                    $rootfolder = "./projects/" . $projectdata->folder_name . "/ftp_server/syncbackup/";
                } else {
                    $folders = explode("/", $rtfolderremote);
                    $rootfolder = "./projects/" . $projectdata->folder_name . "/ftp_server/syncbackup/";
                    if (count($folders) > 0) {
                        foreach ($folders as $folid => $folvalue) {
                            if ($folvalue != "") {
                                $rootfolder = $rootfolder . $folvalue;
                                if (!is_dir($rootfolder)) {
                                    mkdir($rootfolder);
                                }
                                $rootfolder.= "/";
                            }
                        }
                    }
                }
                $config['hostname'] = $projectftp->hostname;
                $config['username'] = $projectftp->username;
                $config['password'] = $projectftp->password;
                $config['port'] = $projectftp->port_no;
                $config['passive'] = TRUE;
                $config['debug'] = TRUE;
                $folderdata = array();
                $checkerror = 0;
                if ($this->ftp->connect($config)) {
                    $fdatadata = $this->ftp->list_files($rtfolderremote);
                    if (!is_array($fdatadata)) {
                        $config['passive'] = FALSE;
                        $this->ftp->connect($config);
                        $fdatadata = $this->ftp->list_files($rtfolderremote);
                    }
                    if ($getusrdd->role_id == 1) {
                        $remotesize = 0;
                        $availablesize["storage_avail"] = 1;
                    } else {
                        $remotesize = $this->general->getftp_size($project_id, $rtfolderremote);
                        $availablesize = $this->general->checkclient_storage($user_id);
                    }
                    $rootfolder = preg_replace('~/+~', '/', $rootfolder);
                    $rtfolderremote = preg_replace('~/+~', '/', $rtfolderremote);

                    if ($availablesize["storage_avail"] >= $remotesize || $getusrdd->role_id == 1) {
                        if (count($fdatadata) > 0) {
                            $startdate = date("Y-m-d H:i:s");
                            $data = explode(",", $data);
                            foreach ($fdatadata as $fkey => $fvalue) {
                                $lval = explode("/", $fvalue);
                                $lval = $lval[count($lval) - 1];
                                if (in_array($fkey, $data)) {
                                    if($lval == "." || $lval == ".."){
                                        continue;
                                    }else{

                                    
                                    if(strpos($lval, ".") !== false){
                                        $fsdd = $rtfolderremote . $lval;
                                        $stdata = array("user_id" => $user_id,"project_id" => $project_id,"type" => "file","filepath" => $fsdd, "msg" => "file download is in processing","status" => "process");
                                        $this->db->insert("ftp_backup_processing",$stdata);    
                                        $stinsid = $this->db->insert_id(); 

                                        $this->ftp->download($fsdd,$rootfolder . $lval, "auto");
                                    } else {
                                        $lf = preg_replace('~/+~', '/', $rootfolder . $lval . "/");
                                        $rf = preg_replace('~/+~', '/', $rtfolderremote . $lval . "/");
                                        array_push($folderdata, array("localrootfolder" => $lf, "remoterootfolder" => $rf));
                                         $stdata = array("user_id" => $user_id,"project_id" => $project_id,"type" => "folder","filepath" => $rf, "msg" => "Folder creation is in processing","status" => "process");
                                        $this->db->insert("ftp_backup_processing",$stdata);    
                                        $stinsid = $this->db->insert_id();
                                        if (!is_dir($rootfolder . $lval)) {
                                            mkdir($rootfolder . $lval);
                                        }
                                    }
                                    $this->db->where("process_id",$stinsid)->update("ftp_backup_processing",["status" => "success"]);

                                  }
                                }
                            }
                            $getftpdata = $this->ftploop($project_id, $folderdata, $startdate,$user_id);
                            if ($getftpdata["status"] == "success") {
                                $msg = $getftpdata["msg"];
                                $subject = $getftpdata["description"];
                                $to = $getftpdata["to"];
                                $checkerror = 0;
                            }
                        } else {
                            $msg = $this->lang->line("No Files & Folders are in FTP credentials provided");
                            $subject = $this->lang->line("FTP Backup Process");
                            //$to = $this->session->userdata("email");
                            $to = $authData->email;
                            $checkerror = 1;
                        }
                    } else {
                        $msg = $this->lang->line("Do not have Sufficient FTP Storage Space");
                        $subject = $this->lang->line("FTP Backup Process");
                        //$to = $this->session->userdata("email");
                        $to = $authData->email;
                        $checkerror = 1;
                    }
                } else {
                    $msg = $this->lang->line("FTP credentials provided is wrong");
                    $subject = $this->lang->line("FTP Backup Process");
                    //$to = $this->session->userdata("email");
                    $to = $authData->email;
                    $checkerror = 1;
                }
                $this->send_mail($msg, $subject, $to);
                if ($checkerror == 1) {
                    $msg_data = ["user_id" => $user_id, "project_id" => $project_id, "error_title" => $subject, "descriptions" => $msg];
                    $this->db->insert("ftp_errors", $error_data);
                    $msg_data["status"] = "failed";
                    $msg_data["msg"] = $msg;
                } else if ($checkerror == 0) {
                    $msg_data = ["status" => "success", "msg" => $msg];
                }


                $response['status'] = $msg_data["status"];
                $response['message']= $msg_data["msg"];
                echo json_encode($response);
                exit;


                

            }else{
                $response['status'] = $this->lang->line("failed");
                $response['message']="Incomplete data provided";
                echo json_encode($response);
                exit;
            }  


        }else{
            $response['status'] = $this->lang->line("failed");
            $response['message']= $this->lang->line("Authorization Failed");
            echo json_encode($response);
            exit;
        }

        //END
    }




    public function ftploop($project_id, $data = array(), $startdate,$user_id) {
        if (count($data) > 0) {
            $folderdata = array();
            foreach ($data as $key => $value) {
                $fdatadata = $this->ftp->list_files($data[$key]["remoterootfolder"]);
                foreach ($fdatadata as $fkey => $fvalue) {
                    $lval = explode("/", $fvalue);
                    $lval = $lval[count($lval) - 1];
                    if ($lval == '.' || $lval == '..') {
                        continue;
                    } else {
                        if (strpos($lval, ".") !== false) {
                                $remtval = preg_replace('~/+~', '/', $data[$key]["remoterootfolder"] . $lval);
                                 $stdata = array("user_id" => $user_id,"project_id" => $project_id,"type" => "file","filepath" => $remtval, "msg" => "file download is in processing","status" => "process");
                                $this->db->insert("ftp_backup_processing",$stdata);    
                                $stinsid = $this->db->insert_id();


                                $this->ftp->download($remtval, preg_replace('~/+~', '/', $data[$key]["localrootfolder"] . $lval));
                           
                        } else {
                            $remtval = preg_replace('~/+~', '/', $data[$key]["remoterootfolder"] . $lval)."/";

                            $stdata = array("user_id" => $user_id,"project_id" => $project_id,"type" => "folder","filepath" => $remtval, "msg" => "Folder creation is in processing","status" => "process");
                                $this->db->insert("ftp_backup_processing",$stdata);    
                                $stinsid = $this->db->insert_id();
                            $localrootfolder = $data[$key]["localrootfolder"] . $lval;
                            if (!is_dir(preg_replace('~/+~', '/', $localrootfolder))) {
                                mkdir(preg_replace('~/+~', '/', $localrootfolder));
                            }
                            array_push($folderdata, array("localrootfolder" => $localrootfolder."/", "remoterootfolder" => $remtval));
                        }
                         $this->db->where("process_id",$stinsid)->update("ftp_backup_processing",["status" => "success"]);

                    }
                }
            }
            if (count($folderdata) > 0) {
                $this->ftploop($project_id, $folderdata, $startdate,$user_id);
            } else {
                $this->load->library("zipp");
                $getdd = $this->get_decrypt_project_data_by_id($project_id);
                $currtimestamp = time();
                $file_name = "syncbackup" . $currtimestamp . "_" . $project_id . ".zip";
                $baserootpath = str_replace('application\\', "", APPPATH) . "projects\\" . $getdd->folder_name . "\\ftp_server\syncbackup";
                $zipPath = str_replace('application\\', "", APPPATH) . "projects\\" . $getdd->folder_name . "\\ftp_server\\" . $file_name;
                $this->zipp->create($baserootpath, $zipPath);
                $bkpdata = array("project_id" => $getdd->project_id, "client_id" => $getdd->client_id, "timestamp_date" => $currtimestamp, "file_name" => $this->encryption->encrypt($file_name), "startdate" => $startdate, "enddate" => date("Y-m-d H:i:s"));
                $this->db->insert("backupftp", $bkpdata);
                return array("status" => "success", "msg" => $this->lang->line("backup_success_taken"), "to" => $this->session->userdata("email"), "description" => $this->lang->line("FTP Backup Process"));
            }
        }
    }













}