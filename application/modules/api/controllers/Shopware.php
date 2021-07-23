<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed
class Shopware extends MX_Controller {
    function __construct() {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $header_data = $this->input->request_headers();
		$url = trim($header_data["Url"]);
		if($url){
			$api_keysdata = $this->db->query("select * from api_keys where domain_url = '".$url."'")->row();
		$gtuser = $this->db->get_where("client", array("client_id" => $api_keysdata->client_id));
		 $usrdata = $gtuser->row();
		 if($usrdata){
			 $this->user_data = $usrdata;
		 }else{
			 echo json_encode(array("status" => false, "message" => "Url not authenticated"));
                        exit;
		 }
		
		}
	
    }
    public function auth() {
		 $header_data = $this->input->request_headers();
		
		$username = trim(base64_decode($header_data["Username"]));
        $password = trim(base64_decode($header_data["Password"]));
        $gtuser = $this->db->get_where("client", array("username" => $username));
        if($gtuser->num_rows() > 0){
            $usrdata = $gtuser->row();
            if(password_verify($password, $usrdata->password)){
                    if ($usrdata->status == 'active') {
                        $this->user_data = $usrdata;
                        $this->request_url = $header_data["Url"];
                        $this->token_data = $this->db->query("select * from api_keys where client_id = '".$usrdata->client_id."'")->row();
                    }else{
                        echo json_encode(array("status" => false, "message" => "User is deactivated by Administrator"));
                        exit;
                    }
            }else{
               echo json_encode(array("status" => false, "message" => "Password is wrong"));
                exit; 
            }
        }else{
            echo json_encode(array("status" => false, "message" => "Username is wrong"));
                exit;
        }
		
		
        $getuser = $this->db->get_where("client", array("client_id" => $this->user_data->client_id));
        if($getuser->num_rows() > 0){
            $chksite = $this->db->query("select * from api_keys where client_id = '". $this->user_data->client_id."' AND domain_url like '%".$this->request_url."%' ");
            if($chksite->num_rows()){
                echo json_encode(array("status" => true, "message" => "Auth API Key successfully verified", "user_data" => $getuser->row(),"auth_url" =>$chksite->row()->domain_url));
            }else{
                echo json_encode(array("status" => false, "message" => "Requesting URL does not match with API URL"));
            }
        } else {
            echo json_encode(array("status" => false, "message" => "Username or password is invalid"));
        }
    }
    public function add_shop(){
        $error_data = array();
        $jsondata = json_decode(file_get_contents("php://input"));
        $name = trim($jsondata->shop_name);
        $url = strtolower(trim($jsondata->shop_url));
        $slug = base64_encode($name);
        $name = !empty($name) ? $name : $error_data["shop_name"] = $this->lang->line("project_name_blank");
        $url = !empty($url) ? strtolower($url) : $error_data["shop_url"] = $this->lang->line("project_url_blank");
        $added_date = strtotime(date("Y-m-d"));
        $date = date("Y-m-d");
        $folder_name = str_replace("/","",$slug)."_".time();
        if(count($error_data) > 0){
            echo json_encode(array("status" => false,"errors" => $error_data));exit;
        }else{
                mkdir(FCPATH."projects/" . $folder_name, 0777);
                mkdir(FCPATH."projects/" . $folder_name . "/ftp_server", 0777);
                mkdir(FCPATH."projects/" . $folder_name . "/db_server", 0777);
                $data = array(
                                "project_name" => $this->encryption->encrypt($name),
                                "slug" => $slug,
                                "added_date" => $date,
                                "folder_name" => $this->encryption->encrypt($folder_name),
                                "datetimestamp" => $added_date,
                                "client_id" => $this->user_data->client_id,
                                "url" => $this->encryption->encrypt($url),
                                "project_type" => 'shopware'
                            );
                $this->db->insert("project", $data);
                $project_id = $this->db->insert_id(); 
                echo json_encode(array("status" => true,"msg" => $this->lang->line("shop_added_msg"),"project_data" => array("project_id" => $project_id)));exit;
        }
    }
    public function add_shop_project(){
        $error_data = array();
        $jsondata = json_decode(file_get_contents("php://input"));
		
	
        $project_id = $jsondata->project_id;
        $protocol_type = trim($jsondata->protocol_type);
        $hostname = !empty(trim(base64_decode($jsondata->hostname))) ? trim(base64_decode($jsondata->hostname)) :$error_data["hostname"] = $this->lang->line("hostname_blank");
        $username = !empty(trim(base64_decode($jsondata->username))) ? trim(base64_decode($jsondata->username)) :$error_data["username"] = $this->lang->line("username_blank");
        $password = !empty(trim(base64_decode($jsondata->password))) ? trim(base64_decode($jsondata->password)) :$error_data["password"] = $this->lang->line("password_blank");
        $port_no = !empty(trim($jsondata->port_no)) ? trim($jsondata->port_no) :$error_data["port_no"] = $this->lang->line("port_no_blank");
        $config['hostname'] = $hostname;
        $config['username'] = $username;
        $config['password'] = $password;
        $config['port'] = $port_no;
        $config['passive'] = TRUE;
        $config['debug'] = FALSE;
        $ftpstatus = 0;
        if($protocol_type == "ftp") {
            if (empty($port_no)) {
                $port_no = 21;
            }
            $callingftp = $this->ftp;
        }else if($protocol_type == "sftp") {
            if (empty($port_no)) {
                $port_no = 22;
            }
            $callingftp = $this->ftpbackup;
        }else{
            $port_no = $port_no;
        }

     /*   if($callingftp->connect($config)) {
            $ftpstatus = 1;
        } else {
            $config['passive'] = FALSE;
            if ($callingftp->connect($config)) {
                $ftpstatus = 1;
            } else {
                $ftpstatus = 0;
            }
        }
        */
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
        throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
        try {

            if($callingftp->connect($config)) {
                $ftpstatus = 1;
            } else {
				
				
                $config['passive'] = FALSE;
                if ($callingftp->connect($config)) {
                    $ftpstatus = 1;
                } else {
                    $error_data["ftp_error"] = "Unable to connec FTP using Active mode";
                }
            }    
        }catch(\Throwable $e){
            $error_data["ftp_error"] = "<p>".$e->getMessage()."</p>";
            $ftpstatus = 0;
        }



        if(count($error_data) > 0){
            echo json_encode(array("status" => false,"errors" => $error_data));exit;
        }else{
                $projectdata = $this->db->get_where("project",array("project_id" => $project_id))->row();

                $folder_path = base64_encode($this->encryption->decrypt($projectdata->project_name))."_".time()."_".$project_id;
                $projectpath = $this->encryption->decrypt($projectdata->folder_name);
                mkdir(FCPATH."projects/".$projectpath."/ftp_server/".$folder_path, 0777);
                if(is_dir(FCPATH."projects/".$projectpath."/ftp_server/".$folder_path)){
                        mkdir(FCPATH."projects/".$projectpath."/ftp_server/".$folder_path."/temp", 0777);
                        mkdir(FCPATH."projects/".$projectpath."/ftp_server/".$folder_path."/syncbackup", 0777);
                }
                $data = array(
                                "protocol_type" => $this->encryption->encrypt($protocol_type),
                                "hostname" => $this->encryption->encrypt($hostname),
                                "username" => $this->encryption->encrypt($username),
                                "port_no" => $this->encryption->encrypt($port_no),
                                "password" => $this->encryption->encrypt($password),
                                "project_id" =>$project_id,
                                "folder_path" => $folder_path,
                                "client_id" => $projectdata->client_id,
                                "caption" => $this->encryption->encrypt($this->encryption->decrypt($projectdata->project_name))
                            );
                $this->db->insert("ftp_server", $data);
                $ftp_id = $this->db->insert_id();
                echo json_encode(array("status" => true,"msg" => $this->lang->line("shop_added_msg"),"ftp_data" => array("ftp_id" => $ftp_id,"project_id" => $project_id)));exit;
        }
    }
    public function backupnow(){
        $jsondata = json_decode(file_get_contents("php://input"));
        $ftp_id   =  $jsondata->ftp_id;
        $getbackups = $this->db->query("select * from backupftp where ftp_id = ".$ftp_id." AND status = 'processing' OR ftp_id = ".$ftp_id." AND status = 'failed' order by ftp_id DESC")->result();
        if(count($getbackups) ==0){
                        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                        throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                    }, E_WARNING);
                    try {
                                $getftp = $this->db->get_where("ftp_server",array("ftp_id" => $ftp_id))->row();
                                $project_id = $getftp->project_id;
                                $getprojects = $this->db->get_where('project', array("status" => 'active', "project_id" => $project_id))->row();
                                $rtfolderremote = "/";
                                $rootfolder = FCPATH."projects/".$this->encryption->decrypt($getprojects->folder_name). "/ftp_server/".$getftp->folder_path."/syncbackup/";
                                $rootfolder = preg_replace('~/+~', '/', $rootfolder);
                                $rtfolderremote = preg_replace('~/+~', '/', $rtfolderremote);
                                $error_logfile = "errrlog" . time() . ".log";
                                $myfile = fopen(APPPATH . "logs/" . $error_logfile, "w");
                                $startdate = date("Y-m-d H:i:s");
                                $total_files_folders = 0;
                                $foldersdata = array();
                                $ddins = array(
                                                        "project_id" => $project_id,
                                                        "ftp_id" => $ftp_id,
                                                         "client_id" => $getprojects->client_id,
                                                         "startdate" => $startdate,
                                                         "localroot_folder" => $rootfolder,
                                                         "remoteroot_folder" => $rtfolderremote,
                                                         "error_logfile" => $error_logfile,
                                                    );
                                $this->db->insert("backupftp",$ddins);
                                $ftpbkp_id = $this->db->insert_id();
                                echo json_encode(array("status" => true, "message" => $this->lang->line("ftp_process_background_msg")));
            
                    }catch(\Throwable $e){
                           echo json_encode(array("status" => false, "message" => $e->getMessage()));
                           exit; 
                    }
                    restore_error_handler();
        }else{
           echo json_encode(array("status" => false, "message" => "Please wait till your previous backup is in processing................" ));
           exit; 
        }
    }
    public function backup_list(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $ftp_id   =  $jsondata->ftp_id;
            //$getftp_server = $this->db->get_where("ftp_server",array("ftp_id" => $ftp_id))->row();
            $getbackups = $this->db->query("select * from backupftp where ftp_id = ".$ftp_id." order by backup_id DESC")->result();
            $backup_array =array();
			if(count($getbackups) > 0){
                
                $cnt = 1;
                foreach($getbackups as $key) {
                        $dt = array();
                        $dt["sr_no"] = $cnt;
                        $dt["backup_id"] = $key->backup_id;
                        $dt["created"] = displayDate($key->startdate);
                        $dt["last_backup_size"] = $this->general->convert_size($key->total_size);
                        $dt["last_backup_size"] = $this->general->convert_size($key->total_size);
                        $dt["status"] = $key->status;
                        $dt["view_log_enabled"] = $key->folder_retrieve_flag == 1 ? 1 : 0;
                        $dt["download_backup_enabled"] = $key->status == 'success' ? 1 : 0;
                        $dt["restore_backup_enabled"] = $key->status == 'success' ? 1 : 0;
                        $backup_array[] = $dt;
                        $cnt++;    
                }
                echo json_encode(array("status" => true, "ftp_data" => $backup_array));
            }else{
                echo json_encode(array("status" => false, "message" => $this->lang->line("no_records_found")));
            exit;
            }    
    }
    public function view_backup_logs(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $backup_id   =  $jsondata->backup_id;
            $getbackup= $this->db->query("select * from backupftp where backup_id = ".$backup_id." ")->row();
            if(!empty($getbackup)){
                 $bkpdata = json_decode($getbackup->foldersdata);   
                 echo json_encode(array("status" => true, "log_data" => $bkpdata));
            }else{
                echo json_encode(array("status" => false, "message" => $this->lang->line("no_records_found")));
                exit;
            }
    }
    public function download_backup(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $this->load->helper('download');
            $backup_id   =  $jsondata->backup_id;
            $getbackup = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$backup_id." AND bftp.status = 'success' ")->result();
            if(count($getbackup) > 0){
                $file = FCPATH."projects/" . $this->encryption->decrypt($getbackup[0]->folder_name)."/ftp_server/".$getbackup[0]->folder_path."/".$this->encryption->decrypt($getbackup[0]->file_name);
                $data = file_get_contents($file);
                force_download($this->encryption->decrypt($getbackup[0]->file_name),$data);
            }else{
                echo json_encode(array("status" => false, "message" => 'Please wait backup is in processing................' ));
                exit;
            }
    }
    public function restorenow(){
                    $jsondata = json_decode(file_get_contents("php://input"));
                    $bkp_id   = $jsondata->backup_id;
                    $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$bkp_id."")->row();
                    $restore_folder = "restore_folder_".base64_encode($bkpdata->folder_path)."_".time();
                    $fileloc = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/".$this->encryption->decrypt($bkpdata->file_name);
                    $baserootpath = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/"."temp";
                    if(is_dir($baserootpath)){
                        mkdir($baserootpath."/".$restore_folder);
                        $baserootpath = $baserootpath."/".$restore_folder;
                    }
                    $getrestore_data = $this->db->query("select * from restore_ftp where status = 'processing' AND backup_id = ".$bkp_id." ")->num_rows();
                     set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                        throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                    }, E_WARNING);
                    try {
                        if($getrestore_data == 0){
                             if(file_exists($fileloc)){
                                           $insdata =  array("project_id" => $bkpdata->project_id , 
                                            "ftp_id" => $bkpdata->ftp_id , 
                                            "backup_id" => $bkpdata->backup_id , 
                                            "status" => "processing", 
                                            "total_size" => 0, 
                                            "cron_status" => 0,
                                            "zippath" => $fileloc,
                                            "baserootpath" => $baserootpath,
                                            "restore_folder" => $restore_folder,
                                             "startdate" => date("Y-m-d H:i:s")
                                        );
                                        $this->db->insert("restore_ftp",$insdata);
                                        $output = array("status" => true, "message" => $this->lang->line("ftp_process_restore_background_msg"));
                                        echo json_encode($output);
                                        exit;
                                }else{
                                             $output = array("status" => false, "message" => $this->lang->line("no_records_found"));
                                             echo json_encode($output);
                                             exit;
                                }
                            }else{
                                $output = array("status" => false, "message" => $this->lang->line("ftp_process_restore_msg"));
                                echo json_encode($output);
                                exit;
                            }    
                         
                 }catch(\Throwable $e) {
                        $output = array("status" => false, "message" => $e->getMessage());
                        echo json_encode($output);
                        exit;
                }
    }
    public function view_all_restore_process(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $ftp_id   =  $jsondata->ftp_id;
            $getrestores = $this->db->query("select * from restore_ftp where ftp_id = ".$ftp_id." order by restore_id DESC")->result();
            $cnt = 1;
            $backup_array = array();
            if(count($getrestores) > 0){
                    foreach ($getrestores as $key) {
                        $dt = array();
                        $dt["sr_no"] = $cnt;
                        $dt["restore_id"] = $key->restore_id;
                        $dt["created"] = displayDate($key->startdate);
                        $dt["status"] = $key->status;
                        $dt["view_log_enabled"] = $key->extract_flag == 1 ? 1 : 0;
                        $backup_array[] = $dt;
                        $cnt++;
                    }
                    echo json_encode(array("status" => true, "restore_data" => $backup_array));
            }else{
                 $output = array("status" => false, "message" => $this->lang->line("no_records_found"));
                 echo json_encode($output);
                 exit;
            }
    }
    public function view_restore_logs(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $restore_id   =  $jsondata->restore_id;
            $getrestore= $this->db->query("select * from restore_ftp where restore_id = ".$restore_id." ")->row();
            if(!empty($getrestore)){
                 $bkpdata = json_decode($getrestore->foldersdata);   
                 echo json_encode(array("status" => true, "log_data" => $bkpdata));
            }else{
                echo json_encode(array("status" => false, "message" => $this->lang->line("no_records_found")));
                exit;
            }
    }
    public function add_database_server(){
        $error_data = array();
        $jsondata = json_decode(file_get_contents("php://input"));
        $project_id = $jsondata->project_id;
        
        $hostname = !empty(trim(base64_decode($jsondata->hostname))) ? trim(base64_decode($jsondata->hostname)) :$error_data["hostname"] = $this->lang->line("hostname_blank");
        $username = !empty(trim(base64_decode($jsondata->username))) ? trim(base64_decode($jsondata->username)) :$error_data["username"] = $this->lang->line("username_blank");
        $password = !empty(trim(base64_decode($jsondata->password))) ? trim(base64_decode($jsondata->password)) :$error_data["password"] = $this->lang->line("password_blank");
        $port_no = trim($jsondata->port_no);
        $projectdata = $this->db->get_where("project",array("project_id" => $project_id))->row();
        $caption = trim($this->encryption->decrypt($projectdata->project_name));
        $sqlcheck = 0;
            set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
            try {
                if($port_no != ""){
                    $conn = mysqli_connect($hostname, $username, $password,$port_no);
                }else{
                    $conn = mysqli_connect($hostname, $username, $password);
                }
                if ($conn) {
                    $sqlcheck = 1;
                }
            }
            catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                $error_data["db_error"] =$e->getMessage();  
            }
            restore_error_handler();
        if(count($error_data) == 0){
            $projectdata = $this->db->get_where("project",array("project_id" => $project_id))->row();
            $folder_path = base64_encode($caption)."_".time()."_".$project_id;
            $projectpath = $this->encryption->decrypt($projectdata->folder_name);
            mkdir("./projects/".$projectpath."/db_server/".$folder_path, 0777);
            if(is_dir("./projects/".$projectpath."/db_server/".$folder_path)){
                mkdir("./projects/".$projectpath."/db_server/".$folder_path."/db", 0777);
                mkdir("./projects/".$projectpath."/db_server/".$folder_path."/dbrestore", 0777);
                mkdir("./projects/".$projectpath."/db_server/".$folder_path."/dbcheck", 0777);
            }
            $data = array(
                            "mhostname" => $this->encryption->encrypt($hostname), 
                            "musername" => $this->encryption->encrypt($username),
                            "mpassword" => $this->encryption->encrypt($password),
                            "project_id" =>$project_id,
                            "folder_path" => $folder_path,
                            "added_date" => date("Y-m-d"),
                            "caption" => $this->encryption->encrypt($caption),
                            "client_id" => $projectdata->client_id
                        );
            $data["port_no"] = $port_no !="" ? $port_no : 0;
            if ($this->db->insert("mysql_server", $data)) {
                $db_id = $this->db->insert_id();
                $output = array("status" => true, "message" => $this->lang->line("sql_setup_msg"),"db_id" => $db_id);
            } else {
                $output = array("status" => false, "message" => $this->lang->line("something_wrong"));exit;
            }
        }else{
            echo json_encode(array("status" => false,"errors" => $error_data));exit;
        }
        echo json_encode($output);
    }
    public function backupdb_now(){
        $jsondata = json_decode(file_get_contents("php://input"));
        $db_id   = $jsondata->db_id;
        $db_data = $this->db->get_where("mysql_server",array("mysql_id" => $db_id))->row();
            set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
            try {
                if($db_data->port_no > 0){
                    $conn = mysqli_connect($this->encryption->decrypt($db_data->mhostname), $this->encryption->decrypt($db_data->musername), $this->encryption->decrypt($db_data->mpassword),$db_data->port_no);
                }else{
                    $conn = mysqli_connect($this->encryption->decrypt($db_data->mhostname), $this->encryption->decrypt($db_data->musername), $this->encryption->decrypt($db_data->mpassword));
                }
                if($conn) {
                    $sql="SHOW DATABASES";  
                    $link = mysqli_query($conn,$sql);
                    $foldersdata = array();
                    $total_size = 0;
                    $total_dbs = 0;
                    while($row = mysqli_fetch_row($link)){
                        if (($row[0]!="information_schema") && ($row[0]!="mysql")) {
                            $dbname = $row[0];
                            $sqldb = "SELECT TABLE_NAME AS `Table`,ROUND((DATA_LENGTH + INDEX_LENGTH)) AS Sizes FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dbname."' ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC";
                            $link = mysqli_query($conn,$sqldb);
                            $ftchdata = mysqli_fetch_array($link);
                            $file_name = base64_encode($dbname).time()."_".$db_data->mysql_id;
                            if($ftchdata["Sizes"] != null){
                                $total_size +=  $ftchdata["Sizes"];
                                $total_dbs++;
                                array_push($foldersdata, array("status" => "processing", "db_name" => $dbname, "size" => $ftchdata["Sizes"],"file_name" => $file_name,"logs" => "","startdate" => date("Y-m-d H:i:s"),"enddate" => ""));
                            }
                        }
                    }
                    if(count($foldersdata) > 0){
                       $dbbdata =   array(
                                        "client_id" =>  $db_data->client_id,
                                        "project_id" => $db_data->project_id,
                                        "startdate" => date("Y-m-d H:i:s"),
                                        "db_id" => $db_data->mysql_id,
                                        "foldersdata" => json_encode($foldersdata),
                                        "total_size" => $total_size,
                                        "total_database" => $total_dbs,
                                        "completed_database" => 0,
                                    );

                       if($this->db->insert("backupsql",$dbbdata)){
                            $output = array("status" => true, "message" => $this->lang->line("db_process_background_msg"));
                       }else{
                         $output = array("status" => false, "message" => $this->lang->line("something_wrong"));
                       }
                    }else{
                        $output = array("status" => false, "message" => $this->lang->line("no_db_avail"));
                    }
                    echo json_encode($output);   
                }
            }
            catch(\Throwable $e) {
                $output = array("status" => false, "message" => $e->getMessage());
                echo json_encode($output);
                exit;
            }
            restore_error_handler();
    }
    public function backupdb_list(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $db_id   =  $jsondata->db_id;
            $getdb_server = $this->db->get_where("mysql_server",array("mysql_id" => $db_id))->row();
            $getbackups = $this->db->query("select * from backupsql where db_id = ".$db_id." order by backup_id DESC")->result();
            if(count($getbackups) > 0){
                $backup_array =array();
                $cnt = 1;
                foreach($getbackups as $key) {
                        $dt = array();
                        $dt["sr_no"] = $cnt;
                        $dt["backup_id"] = $key->backup_id;
                        $dt["created"] = displayDate($key->startdate);
                        $dt["last_backup_size"] = $this->general->convert_size($key->total_size);
                        $dt["status"] = $key->status;
                        $dt["view_log_enabled"] = 1;
                        $dt["download_backup_enabled"] = $key->status == 'success' ? 1 : 0;
                        $dt["restore_backup_enabled"] = $key->status == 'success' ? 1 : 0;
                        $backup_array[] = $dt;
                        $cnt++;    
                }
                echo json_encode(array("status" => true, "ftp_data" => $backup_array));
            }else{
                echo json_encode(array("status" => false, "message" => $this->lang->line("no_records_found")));
            exit;
            }    
    }
    public function view_backupdb_logs(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $backup_id   =  $jsondata->backup_id;
            $getbackup= $this->db->query("select * from backupsql where backup_id = ".$backup_id." ")->row();
            if(!empty($getbackup)){
                 $bkpdata = json_decode($getbackup->foldersdata);   
                 echo json_encode(array("status" => true, "log_data" => $bkpdata));
            }else{
                echo json_encode(array("status" => false, "message" => $this->lang->line("no_records_found")));
                exit;
            }
    }
    public function restoredb_list(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $backup_id   =  $jsondata->backup_id;
            $bkpdata = $this->db->query("select bdbs.*,ms.mhostname,ms.mpassword,ms.musername,ms.port_no,ms.folder_path,ms.status as dbstatus,p.folder_name,ms.caption from backupsql bdbs INNER JOIN mysql_server ms on bdbs.db_id =ms.mysql_id INNER JOIN project p ON bdbs.project_id = p.project_id WHERE bdbs.backup_id = ".$backup_id." ")->row();
            $db_data = json_decode($bkpdata->foldersdata);
            $alldb = array();
            $cnt = 0;
            if(count($db_data) > 0){
                foreach($db_data as $key){
                    $restorefile = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/db_server/".$bkpdata->folder_path."/".$key->file_name.".sql";
                    $sz = filesize($restorefile);
                    $getrstdata = $this->db->get_where("restore_db",array("backup_id" => $backup_id,"ind_id" => $cnt))->num_rows() > 0 ? 1 : 0 ;
                    $ss = array(
                                    "index_id" => $cnt,
                                    "db_name"  => $key->db_name,
                                    "size"     => $this->general->convert_size($sz),
                                    "status"   => $key->status,
                                    "backup_date" =>  $key->startdate,
                                    "backup_id" => $backup_id,
                                    "project_id" => $bkpdata->project_id,
                                    "db_id"     => $bkpdata->db_id,
                                    "view_log_enabled" => $getrstdata
                                ); 
                      array_push($alldb, $ss);
                      $cnt++;
                }
                if(count($alldb) > 0){
                    echo json_encode(array("status" => true,"db_data" => $alldb));
                    exit;
                }else{
                    echo json_encode(array("status" => false,"message" => $this->lang->line("no_records_found")));
                    exit;
                }
            }else{
                echo json_encode(array("status" => false, "message" => $this->lang->line("no_records_found")));
                exit;
            }
    }
    public function restoredb_now(){
            $jsondata = json_decode(file_get_contents("php://input"));
            $backup_id   =  $jsondata->backup_id;
            $index_id   =  $jsondata->index_id;
            $checkdb_process = $this->db->get_where("restore_db",array("backup_id" => $backup_id,"ind_id" => $index_id,"status" => "processing"))->num_rows();
            if($checkdb_process == 0){
                $bkpdata = $this->db->query("select bdbs.*,ms.mhostname,ms.mpassword,ms.musername,ms.port_no,ms.folder_path,ms.status as dbstatus,p.folder_name,ms.caption from backupsql bdbs INNER JOIN mysql_server ms on bdbs.db_id =ms.mysql_id INNER JOIN project p ON bdbs.project_id = p.project_id WHERE bdbs.backup_id = " . $backup_id . "")->row();
                $dbdt =json_decode($bkpdata->foldersdata);
                $db_name =$dbdt[$index_id]->db_name;
                $dbhost = $this->encryption->decrypt($bkpdata->mhostname);
                $dbuser = $this->encryption->decrypt($bkpdata->musername);
                $dbpass = $this->encryption->decrypt($bkpdata->mpassword);
                $dbname = $db_name;
                $restorefile = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/db_server/".$bkpdata->folder_path."/".$dbdt[$index_id]->file_name.".sql";
                $conn =new mysqli($dbhost, $dbuser, $dbpass ,$dbname);
                $query = '';
                $sqlScript = file($restorefile);
                $startdate = date("Y-m-d H:i:s");
                $cnt = 0;
                foreach($sqlScript as $line){
                    $startWith = substr(trim($line), 0 ,2);
                    $endWith = substr(trim($line), -1 ,1);
                    if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                        continue;
                    }   
                    $query = $query . $line;
                    if ($endWith == ';') {
                        $queryArr[] = array("table_id" =>$cnt,"startdate" => date("Y-m-d H:i:s"),"status" => "processing","enddate" => "");
                        $query= ''; 
                        $cnt++;    
                    }
                    
                }
                $querycount = count($queryArr);

                $data = array("backup_id" => $backup_id,"startdate" => $startdate,"ind_id" => $index_id, "db_name" => $db_name,"total_table" => $querycount,"file_path" =>$restorefile,"tables_data" => json_encode($queryArr));
                if($this->db->insert("restore_db",$data)){
                   $restore_id =  $this->db->insert_id();
                     echo json_encode(array("status" => true, "message" => "restore for this database is process please check log for the status of restore process","restore_id" => $restore_id));
                }else{
                     echo json_encode(array("status" => false, "message" => $this->lang->line("something_wrong")));
                exit;
                }
            }else{
                 echo json_encode(array("status" => false, "message" => "Restore for this database is already in process"));
                exit;
            }
    }
    public function view_restore_db_log(){
             $jsondata = json_decode(file_get_contents("php://input"));
             $restore_id   =  $jsondata->restore_id;
             $index_id   =  $jsondata->index_id;
             $getbackups = $this->db->query("select * from restore_db where restore_id = ".$restore_id." AND ind_id = ".$index_id." ")->row();
             $ss = json_decode($getbackups->tables_data);
             $getbackups->tables_data = $ss;
             if(!empty($getbackups)){
                echo json_encode(array("status" => true, "log_data" => $getbackups));
             }else{
                echo json_encode(array("status" => false,"message" => $this->lang->line("no_records_found")));
                    exit;
             }
    }
}
