<?php
class Projects extends MX_Controller {
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
        $prodata = $this->db->query("select p.*,p.url as projecturl,ms.mysql_id,ms.mhostname,ms.musername,ms.mpassword,ms.mdatabase_name,fs.ftp_id,fs.protocol_type,fs.username,fs.password,fs.hostname,fs.url,fs.root_path,fs.auto_backup_hours,fs.port_no,ms.auto_backup_hours as mauto_backup_hours from project p inner join ftp_server fs on fs.project_id = p.project_id inner join mysql_server ms on ms.project_id = p.project_id where p.client_id = " . $user_id . "");
        $data["project_count"] = $prodata->num_rows();
        $data["page"] = "projects";
        $data["projects"] = $prodata->result();
        //to decrypt data
        if (!empty($data["projects"])) {
            foreach ($data["projects"] as $ky => $val) {
                //ftp details
                if ($val->url != '') {
                    $data["projects"][$ky]->url = $this->encryption->decrypt($val->url);
                }
                if ($val->protocol_type != '') {
                    $data["projects"][$ky]->protocol_type = $this->encryption->decrypt($val->protocol_type);
                }
                if ($val->username != '') {
                    $data["projects"][$ky]->username = $this->encryption->decrypt($val->username);
                }
                if ($val->password != '') {
                    $data["projects"][$ky]->password = $this->encryption->decrypt($val->password);
                }
                if ($val->hostname != '') {
                    $data["projects"][$ky]->hostname = $this->encryption->decrypt($val->hostname);
                }
                if ($val->port_no != '') {
                    $data["projects"][$ky]->port_no = $this->encryption->decrypt($val->port_no);
                }
                if ($val->root_path != '') {
                    $data["projects"][$ky]->root_path = $this->encryption->decrypt($val->root_path);
                }
                //db details
                if ($val->mdatabase_name != '') {
                    $data["projects"][$ky]->mdatabase_name = $this->encryption->decrypt($val->mdatabase_name);
                }
                if ($val->mhostname != '') {
                    $data["projects"][$ky]->mhostname = $this->encryption->decrypt($val->mhostname);
                }
                if ($val->musername != '') {
                    $data["projects"][$ky]->musername = $this->encryption->decrypt($val->musername);
                }
                if ($val->mpassword != '') {
                    $data["projects"][$ky]->mpassword = $this->encryption->decrypt($val->mpassword);
                }
                //project data
                if ($val->project_name != '') {
                    $data["projects"][$ky]->project_name = $this->encryption->decrypt($val->project_name);
                }
                if ($val->slug != '') {
                    $data["projects"][$ky]->slug = $this->encryption->decrypt($val->slug);
                }
                if ($val->folder_name != '') {
                    $data["projects"][$ky]->folder_name = $this->encryption->decrypt($val->folder_name);
                }
                if ($val->projecturl != '') {
                    $data["projects"][$ky]->projecturl = $this->encryption->decrypt($val->projecturl);
                }
            }
        }
        //echo '<pre>';
        //print_r($data["projects"]);
        //exit;
        $this->load->view("client/projects", $data);
    }
    function create() {
        $data["page"] = "users";
        $this->load->view("client/project/create", $data);
    }
    function save() {
        $user_id = $this->session->userdata("user_id");
        $error_data = array();
        $name = $this->input->post("project_name");
        $url = strtolower($this->input->post("url"));
        $slug = base64_encode($name);
        $name = !empty($this->input->post("project_name"))  ? $this->input->post("project_name")  : $error_data["project_name_msg"]  = $this->lang->line("project_name_blank");
        $url = !empty($this->input->post("url"))  ? strtolower($this->input->post("url"))  : $error_data["project_url_msg"]  = $this->lang->line("project_url_blank");
      
            if(!empty($url)){
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                $response = curl_exec($ch);
                $header_size = curl_getinfo($ch);
                $errorcodes  = array(0); 
                if(in_array($header_size["http_code"], $errorcodes)){
                   
                    $error_data["project_url_msg"] = $this->lang->line("Webpage not reachable");  
                }
            }
            
        $added_date = strtotime(date("Y-m-d"));
        $date = date("Y-m-d");
        $folder_name = str_replace("/","",$slug)."_".time();
        if(count($error_data) > 0){
            echo json_encode(array("status" => "failed", "error_data" => $error_data));
        }else{
            mkdir("./projects/" . $folder_name);
            mkdir("./projects/" . $folder_name . "/ftp_server");
            mkdir("./projects/" . $folder_name . "/ftp_server/temp");
            mkdir("./projects/" . $folder_name . "/ftp_server/syncbackup");
            mkdir("./projects/" . $folder_name . "/mysql_server");
            mkdir("./projects/" . $folder_name . "/mysql_server/db");
            mkdir("./projects/" . $folder_name . "/mysql_server/dbrestore");
            mkdir("./projects/" . $folder_name . "/mysql_server/dbcheck");
            $data = array("project_name" => $this->encryption->encrypt($name), "slug" => $this->encryption->encrypt($slug), "added_date" => $date, "folder_name" => $this->encryption->encrypt($folder_name), "datetimestamp" => $added_date, "client_id" => $user_id, "url" => $this->encryption->encrypt($url));
            $this->db->insert("project", $data);
            $project_id = $this->db->insert_id();
            $this->db->insert("ftp_server", ['client_id' => $user_id, "project_id" => $project_id]);
            $this->db->insert("mysql_server", ['client_id' => $user_id, "project_id" => $project_id]);
            echo json_encode(array("status" => "success", "msg" => $this->lang->line("project_add_msg")));
        }
    }
    public function update() {
        $project_id = $this->input->post("proj_id");
        $error_data = array();
        $project_name = !empty($this->input->post("proj_name"))  ? $this->input->post("proj_name")  : $error_data["proj_name_msg"]  = $this->lang->line("project_name_blank");
        $url = !empty($this->input->post("proj_url"))  ? strtolower($this->input->post("proj_url"))  : $error_data["proj_url_msg"]  = $this->lang->line("project_url_blank");
            if(!empty($url)){
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                $response = curl_exec($ch);
                $header_size = curl_getinfo($ch);
                $errorcodes  = array(0); 
                if(in_array($header_size["http_code"], $errorcodes)){
                   
                    $error_data["proj_url_msg"] = $this->lang->line("Webpage not reachable");  
                }
            }
        if (count($error_data) > 0){
                echo json_encode(array("status" => "failed", "error_data" => $error_data));
            }else{
            $data = array("project_name" => $this->encryption->encrypt($project_name),"url" => $this->encryption->encrypt($url));
            $this->db->where("project_id", $project_id);
            if ($this->db->update("project", $data)) {
                echo json_encode(array("status" => "success", "msg" => $this->lang->line("project_update_msg")));
            } else {
                echo json_encode(array("status" => "failed", "msg" => $this->lang->line("something_wrong")));
            }
        }
    }
    public function set_ftp() {
        $ftp_id = $this->input->post("ftp_id");
        $protocol_type = trim($this->input->post("protocol_type"));
        $hostname = trim($this->input->post("hostname"));
        $username = trim($this->input->post("username"));
        $password = trim($this->input->post("password"));
        $domain_url = trim($this->input->post("domain_url"));
        $root_path = trim($this->input->post("root_path"));
        $port_no = trim($this->input->post("port_no"));
        $projectdata = $this->db->get_where("ftp_server", array("ftp_id" => $ftp_id))->row();
         

        if($password == ''){
            $password = $this->encryption->decrypt($projectdata->password);
        }   


        if ($protocol_type == "ftp") {
            if(empty($port_no)){$port_no = 21;}
        } else if ($protocol_type == "sftp") {
           if(empty($port_no)){$port_no = 22;}
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
            $this->db->where("project_id", $projectdata->project_id);
            $this->db->update("project", ["ftp_status" => 1]);
            $data = array(
                "protocol_type" => $this->encryption->encrypt($protocol_type), 
                "hostname" => $this->encryption->encrypt($hostname), 
                "username" => $this->encryption->encrypt($username), 
                //"password" => $this->encryption->encrypt($password), 
                "port_no" => $this->encryption->encrypt($port_no), 
                "url" => $this->encryption->encrypt($domain_url), 
                "root_path" => $this->encryption->encrypt($root_path)
            );


            $data['password'] = $this->encryption->encrypt($password);

            $this->db->where("ftp_id", $ftp_id);
            if ($this->db->update("ftp_server", $data)) {
                $output = array("status" => "success", "msg" => $this->lang->line("ftp_update_success_msg"));
            } else {
                $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
            }
        } else {
            $output = array("status" => "failed", "msg" => $this->lang->line("cred_wrong"));
        }
        echo json_encode($output);
    }
    public function set_mysql() {
        $mysql_id = $this->input->post("mysql_id");
        $mdatabase_name = trim($this->input->post("mdatabase_name"));
        $mhostname = trim($this->input->post("mhostname"));
        $musername = trim($this->input->post("musername"));
        $mpassword = trim($this->input->post("mpassword"));
        $mautobkphrs = trim($this->input->post("mautobkphrs"));
        $projectdata = $this->db->get_where("mysql_server", array("mysql_id" => $mysql_id))->row();

        if($mpassword == ''){
            $mpassword = $this->encryption->decrypt($projectdata->mpassword);
        }


        
        $sqlcheck = 0;
        if ($mhostname == "localhost" || $mhostname == "127.0.0.1" || $mhostname == "") {
            $getproj = $this->db->get_where("project", array("project_id" => $projectdata->project_id))->row();
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
            $ftpdata = $this->db->get_where("ftp_server", array("project_id" => $getproj->project_id))->row();
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
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
            }
            restore_error_handler();
        }
        if ($sqlcheck == 1) {
            $this->db->where("project_id", $projectdata->project_id);
            $this->db->update("project", ["sql_status" => 1]);
            $data = array("mdatabase_name" => $this->encryption->encrypt($mdatabase_name), "mhostname" => $this->encryption->encrypt($mhostname), "musername" => $this->encryption->encrypt($musername), "mpassword" => $this->encryption->encrypt($mpassword), "auto_backup_hours" => $mautobkphrs);
            $this->db->where("mysql_id", $mysql_id);
            if ($this->db->update("mysql_server", $data)) {
                $output = array("status" => "success", "msg" => $this->lang->line("sql_setup_msg"));
            } else {
                $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
            }
        } else {
            if ($sqlcheck == 3) {
                $msg = $this->lang->line("ftp_root_wrong");
            } else if ($sqlcheck == 2) {
                $msg = $this->lang->line("ftp_setup_not_done");
            } else {
                $msg = $this->lang->line("sql_cred_wrong");
            }
            $output = array("status" => "failed", "msg" => $msg);
        }
        echo json_encode($output);
    }
    public function listftp($project_id, $type = "/") {
        if ($type != '/') {
            $type = str_replace("//", "/", base64_decode($type));
        }
        $project_id = base64_decode($project_id);
        $prodata = $this->db->query("select p.*,ms.mysql_id,ms.mhostname,ms.musername,ms.mpassword,ms.mdatabase_name,fs.ftp_id,fs.protocol_type,fs.username,fs.password,fs.hostname,fs.port_no from project p inner join ftp_server fs on fs.project_id = p.project_id inner join mysql_server ms on ms.project_id = p.project_id where p.project_id = " . $project_id . "")->row();
        $config['hostname'] = $this->encryption->decrypt($prodata->hostname);
        $config['username'] = $this->encryption->decrypt($prodata->username);
        $config['password'] = $this->encryption->decrypt($prodata->password);
        $config['port'] = $this->encryption->decrypt($prodata->port_no);
        $config['passive'] = TRUE;
        $config['debug'] = TRUE;
        if ($prodata->hostname != "") {
            if ($this->ftp->connect($config)) {
                $data["projectss"] = $prodata;
                $list = $this->ftp->list_files($type);
                if (!is_array($list)) {
                    $config['passive'] = FALSE;
                    $this->ftp->connect($config);
                    $list = $this->ftp->list_files($type);
                }
                $data["list"] = $list;
                if (count($data["list"]) > 0) {
                    $data["root_folder"] = $type;
                    $data["error"] = "";
                    $data["page"] = "projects";
                    $this->load->view("client/project/backupftp", $data);
                } else {
                    $data["root_folder"] = $type;
                    $data["page"] = "projects";
                    $data["error"] = $this->lang->line("no_data_in_cred");
                    $this->load->view("client/project/backupftp", $data);
                }
            } else {
                echo "hg";
            }
        } else {
            redirect(base_url() . "client/projects");
        }
    }
    public function bulkftp() {
        $user_id = $this->session->userdata("user_id");
        $project_id = base64_decode($this->input->post("project_id"));
        $rtfolderremote = base64_decode($this->input->post("rootfolder"));
        $projectdata = $this->db->get_where('project', array("project_id" => $project_id))->row();
        $projectftp = $this->db->get_where('ftp_server', array("project_id" => $project_id))->row();
        $data = $this->input->post("folderid");
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
                    $to = $this->session->userdata("email");
                    $checkerror = 1;
                }
            } else {
                $msg = $this->lang->line("Do not have Sufficient FTP Storage Space");
                $subject = $this->lang->line("FTP Backup Process");
                $to = $this->session->userdata("email");
                $checkerror = 1;
            }
        } else {
            $msg = $this->lang->line("FTP credentials provided is wrong");
            $subject = $this->lang->line("FTP Backup Process");
            $to = $this->session->userdata("email");
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
        echo json_encode($msg_data);
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

    public function testzip() {
        $this->load->library("zipp");
        $baserootpath = str_replace('application\\', "", APPPATH) . "projects\stadtwerke_1593727200\\ftp_server\syncbackup";
        $zipPath = str_replace('application\\', "", APPPATH) . "projects\stadtwerke_1593727200\\ftp_server\\" . "syncbackup" . time() . ".zip";
        $this->zipp->create($baserootpath, $zipPath);
    }
    public function testunzip() {
        $zipPath = str_replace('application\\', "", APPPATH) . "projects\stadtwerke_1593727200\\ftp_server\\" . "syncbackup1594291304.zip";
        $baserootpath = str_replace('application\\', "", APPPATH) . "projects\stadtwerke_1593727200\\ftp_server\\temp";
        $zip_obj = new ZipArchive;
        if ($zip_obj->open($zipPath) === TRUE) {
            $zip_obj->extractTo($baserootpath);
            echo "Zip exists and successfully extracted";
        } else {
            echo "This zip file does not exists";
        }
    }







    public function startbkp() {

       
        $project_id     = $this->input->post("proid");;
        $getserver      = $this->db->get_where("mysql_server", array("project_id" => $project_id))->row();
        $getftpserver   = $this->db->get_where("ftp_server", array("project_id" => $project_id))->row();
        $getproject     = $this->general->get_decrypt_project_data_by_id($project_id);
        $user_id        = $getproject->client_id;
        $getusrdd       = $this->db->get_where('client', array("client_id" => $user_id))->row();

        if($getusrdd->role_id == 1){
            $checkdbstorage["status"] = true; 
        }else{
            $checkdbstorage = $this->general->checkclientdb_storage($user_id);
        }
        

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

            $dbhost = $getserver->mhostname;
            $dbuser = $getserver->musername;
            $dbpass = $getserver->mpassword;
            $dbname = $getserver->mdatabase_name;
            $tables = '*';
            $file_name = $getproject->slug . "_" . time() . ".sql";
            $path = "./projects/" . $getproject->folder_name . "/mysql_server/" . $file_name;
            $returndata = $this->connecttodbandgettablecount($dbhost, $dbuser, $dbpass, $dbname, $tables, $path, $project_id, $user_id, $file_name);



        } else {
            $msg = $this->lang->line("Do not have Sufficient DB Storage Space");
            $subject = $this->lang->line("Auto DB Backup Process");
            $to = $this->session->userdata("email");
            $checkerror = 1;
            $this->send_mail($msg, $subject, $to);

            $returndata = ["user_id" => $user_id, "project_id" => $project_id, "error_title" => $subject, "descriptions" => $msg];
            $returndata["status"] = "failed";
            $returndata["msg"] = $msg;
        }    



        echo json_encode($returndata);
        exit;


    }








    public function connecttodbandgettablecount($host, $user, $pass, $dbname, $tables = '*', $path, $project_id, $user_id, $file_name) {
        

        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
        }, E_WARNING);

       try {

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

                $tablescount = count($tables);

                return array("status"       => "success", 
                             "msg"          => "Table fetched Successfully",
                             "path"         => $path,
                             "file_name"    => $file_name,
                             "tables"       => $tables,
                             "tablescount"  => $tablescount
                        );
            }


        

        } catch(\Throwable $e) {
            return array("status" => "failed", "msg" => $e->getMessage());
            //echo json_encode($output);
            //exit;
        }

        restore_error_handler();

    }










    public function backuptableonebyone() {

        $project_id     = $this->input->post("proid");;
        $getserver      = $this->db->get_where("mysql_server", array("project_id" => $project_id))->row();
        $getftpserver   = $this->db->get_where("ftp_server", array("project_id" => $project_id))->row();
        $getproject     = $this->general->get_decrypt_project_data_by_id($project_id);
        $user_id        = $getproject->client_id;
        $getusrdd       = $this->db->get_where('client', array("client_id" => $user_id))->row();

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


        // $host   = 'localhost';
        // $user   = 'root';
        // $pass   = '';
        // $dbname = 'retterdbdev';

        $host = $getserver->mhostname;
        $user = $getserver->musername;
        $pass = $getserver->mpassword;
        $dbname = $getserver->mdatabase_name;

        $tname      = $this->input->post("table");
        $tables     = $this->input->post("table");
        $filepath   = $this->input->post("filepath");
        $file_name   = $this->input->post("file_name");
        $tKey       = $this->input->post("tKey");
        $tablescount     = $this->input->post("tablescount");


        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
        }, E_WARNING);

       try {

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


                //print_r($tables);
                //exit;


                $return = '';
                if( $tKey == 1 ){
                    $return = 'set global max_allowed_packet=268435456;';    
                    mysqli_query($link, 'set global max_allowed_packet=268435456;');
                }
                $return.= "\n\n";
                foreach ($tables as $table) {

                    //echo 'SELECT * FROM ' . $table;
                    //echo '<br>';


                    $result = mysqli_query($link, 'SELECT * FROM ' . '`' . $table .'`');
                    $num_fields = mysqli_num_fields($result);
                    $num_rows = mysqli_num_rows($result);
                    $return.= 'DROP TABLE IF EXISTS ' . '`' . $table .'`' . ';';
                    $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE ' . '`' . $table .'`'));
                    $return.= "\n\n" . $row2[1] . ";\n\n";
                    $counter = 1;
                    //Over tables
                    for ($i = 0;$i < $num_fields;$i++) { //Over rows
                        while ($row = mysqli_fetch_row($result)) {
                            if ($counter == 1) {
                                $return.= 'INSERT INTO ' . '`' . $table .'`' . ' VALUES(';
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
                $fileName = $filepath;
                $handle = fopen($fileName, 'a+');
                fwrite($handle, $return);
                if (file_exists($fileName)) {

                    if( $tKey == $tablescount ){
                        $insdata = array("project_id" => $project_id, "client_id" => $user_id, "file_name" =>  $this->encryption->encrypt($file_name), "timestamp_date" => time(), "startdate" => $startdate, "enddate" => date("Y-m-d H:i:s"));
                        $this->db->insert("backupsql", $insdata);
                    }

                    $output = array("status" => "success", "msg" => $tname.' '.$this->lang->line("Table backup done successfully") );
                    echo json_encode($output);
                    exit;
                } else {
                    $output = array("status" => "failed", "msg" => $tname.' '.$this->lang->line("Table backup not done") );
                    echo json_encode($output);
                    exit;
                }

            }


        

        } catch(\Throwable $e) {
            $output = array("status" => "failed", "msg" => $e->getMessage());
            echo json_encode($output);
            exit;
        }

        restore_error_handler();




    }
















    public function backupdb($project_id) {
        $user_id = $this->session->userdata("user_id");
        $project_id = base64_decode($project_id);
        $getserver = $this->db->get_where("mysql_server", array("project_id" => $project_id))->row();
        $getftpserver = $this->db->get_where("ftp_server", array("project_id" => $project_id))->row();
        $getproject = $this->general->get_decrypt_project_data_by_id($project_id);
        $checkdbstorage = $this->general->checkclientdb_storage($user_id);

        // echo '<pre>';
        // print_r($checkdbstorage);
        // exit;


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
        if ($checkdbstorage["status"] == true) {
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
                        if ($checkdbstorage["storage_avail"] >= $this->ftp->sizes($dbfil)) {
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
                    $insdata = array("project_id" => $project_id, "client_id" => $user_id, "file_name" => $this->encryption->encrypt($file_name), "timestamp_date" => time(), "startdate" => $startdate, "enddate" => date("Y-m-d H:i:s"));
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
        echo json_encode($msg_data);
    }
    public function backup_tables($host, $user, $pass, $dbname, $tables = '*', $path,$storage_avalable, $project_id, $user_id) {
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
    public function deleteproject() {
        $password = $this->input->post("passwordverify");
        $user_id = $this->session->userdata("user_id");
        $get_user = $this->db->get_where("client", array("client_id" => $user_id))->row();
        if ($password != "") {
            $dbpass = base64_decode($get_user->pass_text);
            if ($dbpass == $password) {
                $project_id = $this->input->post("delproj_id");
                //$getproject  = $this->db->get_where("project",array("project_id" => $project_id))->row();
                $getproject = $this->general->get_decrypt_project_data_by_id($project_id);
                $path = "./projects/" . $getproject->folder_name;
                $remddr = $this->delTree($path);
                $deleteftpbkp = $this->db->where("project_id", $project_id)->delete("backupftp");
                $deletesqlbkp = $this->db->where("project_id", $project_id)->delete("backupsql");
                $deleteftpserver = $this->db->where("project_id", $project_id)->delete("mysql_server");
                $deletesqlbkp = $this->db->where("project_id", $project_id)->delete("ftp_server");
                $deletesqlbkp = $this->db->where("project_id", $project_id)->delete("project");
                if ($remddr) {
                    echo json_encode(array("status" => "success", "msg" => $this->lang->line("project_data_delete")));
                } else {
                    echo json_encode(array("status" => "failed", "msg" => $this->lang->line("something_wrong")));
                }
            } else {
                echo json_encode(array("status" => "failed", "msg" => $this->lang->line("password_wrong")));
            }
        } else {
            echo json_encode(array("status" => "failed", "msg" => $this->lang->line("password_blank")));
        }
    }
    function delTree($dir) {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
        return true;
    }
    public function checksystem($project_id) {
        $project_id = base64_decode($project_id);
        //$getproject = $this->db->get_where("project",array("project_id" => $project_id))->row();
        $getproject = $this->general->get_decrypt_project_data_by_id($project_id);
        $url = '';
        $header_size = array();
        if (!empty($getproject)) {
            $url = $getproject->url;
        }
        if ($url != '') {
            //$url = 'https://www.google.com';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch);
        }
        echo json_encode($header_size);
        exit;
    }
    public function get_decrypt_project_data_by_id($project_id) {
        $projectdata = array();
        if ($project_id != '') {
            $projectdata = $this->db->get_where("project", array("project_id" => $project_id))->row();
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
        }
        return $projectdata;
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




    //new code
    public function backupftp() {
        $project_id = $this->input->post("project_id");
        $rtfolderremote = "/";
        $projectdata = $this->db->get_where('project', array("project_id" => $project_id))->row();
        $user_id = $projectdata->client_id;
        $getusrdd = $this->db->get_where('client', array("client_id" => $user_id))->row();
        $projectftp = $this->db->get_where('ftp_server', array("project_id" => $project_id))->row();
        $data = $this->input->post("folderid");
        $error_logfile = "errrlog" . time() . ".log";
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
        $config['hostname'] = $projectftp->hostname;
        $config['username'] = $projectftp->username;
        $config['password'] = $projectftp->password;
        $config['port'] = $projectftp->port_no;
        $config['passive'] = TRUE;
        $config['debug'] = TRUE;
        $folderdata = array();
        $checkerror = 0;
        $myfile = fopen(APPPATH . "logs/" . $error_logfile, "w");
        $startdate = date("Y-m-d:H:i:s");
        if ($projectftp->port_no == 22) {
            $ftpcalling = $this->ftpbackup;
        } else {
            $ftpcalling = $this->ftp;
        }
        if ($ftpcalling->connect($config)) {
            $fdatadata = $ftpcalling->raw_files($rtfolderremote);
            $rootfolder = "./projects/" . $projectdata->folder_name . "/ftp_server/syncbackup/";
            if (!is_array($fdatadata)) {
                $config['passive'] = FALSE;
                $ftpcalling->connect($config);
                $fdatadata = $ftpcalling->raw_files($rtfolderremote);
            }
            if (array_key_exists("public_html", $fdatadata)) {
                $rtfolderremote = "/public_html/";
                $rootfolder = $rootfolder . "public_html/";
                if (!is_dir($rootfolder)) {
                    mkdir($rootfolder);
                }
                $fdatadata = $ftpcalling->raw_files($rtfolderremote);
            }
            $rootfolder = preg_replace('~/+~', '/', $rootfolder);
            $rtfolderremote = preg_replace('~/+~', '/', $rtfolderremote);
            if (!empty($fdatadata)) {
                $startdate = date("Y-m-d H:i:s");
                $total_files_folders = 0;
                $foldersdata = array();
                $fddata = array();
                foreach ($fdatadata as $key) {
                    if ($key["filename"] == "." || $key["filename"] == "..") {
                        continue;
                    } else {
                        array_push($foldersdata, array("status" => "processing", "type" => $key["type"], "filename" => $key["filename"], "size" => $key["size"]));
                        array_push($fddata, $key["filename"]);
                        $total_files_folders++;
                    }
                }
                $this->db->insert("backupftp", ["project_id" => $project_id, "client_id" => $user_id, "startdate" => $startdate, "total_files_folders" => $total_files_folders, "localroot_folder" => $rootfolder, "remoteroot_folder" => $rtfolderremote, "foldersdata" => json_encode($foldersdata), "error_logfile" => $error_logfile]);
                $ftpbkp_id = $this->db->insert_id();
                echo json_encode(array("status" => "success", "data" => $fddata, "localrootfolder" => $rootfolder, "remoterootfolder" => $rtfolderremote, "startdate" => $startdate, "error_logfile" => $error_logfile, "ftpbkp_id" => $ftpbkp_id));
            } else {
                echo json_encode(array("status" => "failed", "msg" => "No Data available in FTP Data"));
            }
        } else {
            echo json_encode(array("status" => "failed", "msg" => "Failed to connect Please check credentials"));
        }
    }
    public function ftploopdata($callingdata = array(), $project_id = NULL, $indexid = NULL, $error_logfile = '', $ftpbkp_id = NULL, $totalsize = 0) {
        if (count($callingdata) == 0) {
            $localfolder = $this->input->post("localfolder");
            $remotefolder = $this->input->post("remotefolder");
            $fileorfolder = $this->input->post("fileorfolder");
            $project_id = $this->input->post("project_id");
            $error_logfile = $this->input->post("error_logfile");
            $ftpbkp_id = $this->input->post("ftpbkp_id");
            $indexid = $this->input->post("indexid");
            $totalsize = 0;
            if (strpos($fileorfolder, ".") === false) {
                $remotefolder = preg_replace('~/+~', '/', $remotefolder . $fileorfolder . "/");
                $localfolder = preg_replace('~/+~', '/', $localfolder . $fileorfolder . "/");
                array_push($callingdata, array("localrootfolder" => $localfolder, "remoterootfolder" => $remotefolder));
                if (!is_dir(preg_replace('~/+~', '/', $localfolder))) {
                    mkdir(preg_replace('~/+~', '/', $localfolder));
                }
            } else {
                $remotefolder = preg_replace('~/+~', '/', $remotefolder . $fileorfolder);
                $localfolder = preg_replace('~/+~', '/', $localfolder . $fileorfolder);
            }
        } else {
            $localfolder = "";
            $remotefolder = "";
            $fileorfolder = '';
            $project_id = $project_id;
            $indexid = $indexid;
        }
        $getftpdata = $this->db->get_where("backupftp", array("status" => "processing", "backup_id" => $ftpbkp_id))->num_rows();
        if ($getftpdata > 0) {
            $ff = fopen(APPPATH . "logs/" . $error_logfile, "a+");
            $folderdata = array();
            $getftp = $this->db->get_where("ftp_server", array("project_id" => $project_id))->row();
            $config['hostname'] = $this->encryption->decrypt($getftp->hostname);
            $config['username'] = $this->encryption->decrypt($getftp->username);
            $config['password'] = $this->encryption->decrypt($getftp->password);
            $config['port'] = $this->encryption->decrypt($getftp->port_no);
            $config['passive'] = TRUE;
            $config['debug'] = FALSE;
            if ($this->encryption->decrypt($getftp->port_no) == 22) {
                $ftpcalling = $this->ftpbackup;
            } else {
                $ftpcalling = $this->ftp;
            }
            if ($ftpcalling->connect($config)) {
                $fdatadata = $ftpcalling->list_files("/");
                if (!is_array($fdatadata)) {
                    $config['passive'] = FALSE;
                    $ftpcalling->connect($config);
                }
                if (strpos($fileorfolder, ".") !== false) {
                    $ftpcalling->download($remotefolder, $localfolder, "auto");
                    $ge = $this->db->get_where("backupftp", array("backup_id" => $ftpbkp_id))->row();
                    $chksz = json_decode($ge->foldersdata);
                    $totalsize += $chksz[$indexid]->size;
                } else {
                    foreach ($callingdata as $key => $value) {
                        $fdatadata = $ftpcalling->raw_files($callingdata[$key]["remoterootfolder"]);
                        if (!empty($fdatadata)) {
                            foreach ($fdatadata as $fkey) {
                                $lval = explode("/", $fkey["filename"]);
                                $lval = $lval[count($lval) - 1];
                                if ($fkey["filename"] == '.' || $fkey["filename"] == '..') {
                                    continue;
                                } else {
                                    if ($fkey["type"] == 1) {
                                        if ($ftpcalling->download($callingdata[$key]["remoterootfolder"] . $fkey["filename"], $callingdata[$key]["localrootfolder"] . $fkey["filename"], 'auto')) {
                                            $totalsize += $fkey["size"];
                                        } else {
                                            $msg = '<p> Unable to download file please check specified path : ' . $callingdata[$key]["remoterootfolder"] . $fkey["filename"] . '</p>';
                                            fwrite($ff, $msg);
                                        }
                                    } else {
                                        $rf = preg_replace('~/+~', '/', $callingdata[$key]["remoterootfolder"] . $fkey["filename"] . "/");
                                        $lf = preg_replace('~/+~', '/', $callingdata[$key]["localrootfolder"] . $fkey["filename"] . "/");
                                        if (!is_dir(preg_replace('~/+~', '/', $callingdata[$key]["localrootfolder"] . $fkey["filename"]))) {
                                            mkdir(preg_replace('~/+~', '/', $callingdata[$key]["localrootfolder"] . $fkey["filename"]));
                                        }
                                        array_push($folderdata, array("localrootfolder" => $lf, "remoterootfolder" => $rf));
                                    }
                                }
                            }
                        }
                    }
                }
                if (count($folderdata) > 0) {
                    $this->ftploopdata($folderdata, $project_id, $indexid, $error_logfile, $ftpbkp_id, $totalsize);
                } else {
                    $getftp = $this->db->get_where("backupftp", array("backup_id" => $ftpbkp_id))->row();
                    $ffff = json_decode($getftp->foldersdata);
                    foreach ($ffff as $dss) {
                        $ffff[$indexid]->status = "success";
                    }
                    $folderssdaa = json_encode($ffff);
                    $cmt = $getftp->completed_files_folders + 1;
                    $total_size = $getftp->total_size + $totalsize;
                    if ($getftp->total_files_folders == $cmt) {
                        $this->db->where("backup_id", $ftpbkp_id);
                        $this->db->update("backupftp", ["completed_files_folders" => $cmt, "total_size" => $total_size, "foldersdata" => $folderssdaa]);
                        $this->zipproject($project_id, $error_logfile, $total_size, $ftpbkp_id, $indexid);
                    } else {
                        $this->db->where("backup_id", $ftpbkp_id);
                        $this->db->update("backupftp", ["completed_files_folders" => $cmt, "total_size" => $total_size, "foldersdata" => $folderssdaa]);
                        echo json_encode(array("status" => "success", "msg" => $this->lang->line("backup_success_taken"), "indexid" => $indexid));
                    }
                }
            } else {
                echo json_encode(array("status" => "failed", "msg" => "Unable to connect ftp"));
            }
        } else {
            echo json_encode(array("status" => "success", "msg" => $this->lang->line("backup_success_taken"), "indexid" => $indexid));
        }
    }
    public function zipproject($project_id, $error_logfile, $total_size, $ftpbkp_id, $indexid) {
        $this->load->library("zipp");
        $project_id = $project_id;
        $error_logfile = $error_logfile;
        $total_size = $total_size;
        $getdd = $this->get_decrypt_project_data_by_id($project_id);
        $currtimestamp = time();
        $file_name = "syncftpbackup_" . $currtimestamp . "_" . base64_encode($project_id) . ".zip";
        $baserootpath = str_replace('application\\', "", APPPATH) . "projects\\" . $getdd->folder_name . "\\ftp_server\syncbackup";
        $zipPath = str_replace('application\\', "", APPPATH) . "projects\\" . $getdd->folder_name . "\\ftp_server\\" . $file_name;
        $this->zipp->create($baserootpath, $zipPath);
        $bkpdata = array("timestamp_date" => $currtimestamp, "file_name" => $this->encryption->encrypt($file_name), "enddate" => date("Y-m-d H:i:s"), "error_logfile" => $error_logfile, "status" => "success");
        $this->db->where("backup_id", $ftpbkp_id);
        $this->db->update("backupftp", $bkpdata);
        $remddr = $this->delTree("./projects/" . $getdd->folder_name . "/ftp_server/syncbackup/");
        mkdir("./projects/" . $getdd->folder_name . "/ftp_server/syncbackup/");
        //mkdir("./projects/" . $getdd->folder_name . "/ftp_server/syncbackup/testinggre");
        echo json_encode(array("status" => "success", "msg" => $this->lang->line("backup_success_taken"), "indexid" => $indexid));
        return TRUE;
    }
    public function logfile() {
        $error_logfile = $this->input->post("error_logfile");
        $fget = @file_get_contents(APPPATH . "logs/" . $error_logfile);
        if (!empty($fget)) {
            echo json_encode(array("status" => "success", "data" => $fget));
        } else {
            echo json_encode(array("status" => "failed"));
        }
    }
    public function testftp() {
        $config['hostname'] = 'w0148643.kasserver.com';
        $config['username'] = 'f01387ac';
        $config['password'] = 'FvZsVC6f9yksVGN5';
        $config['port'] = 21;
        $config['passive'] = FALSE;
        $config['debug'] = FALSE;
        if ($this->ftp->connect($config)) {
            try {
                $this->ftp->download("/indexs.php", "./projects/indexs.php", "auto");
            }
            catch(Exception $e) {
                echo 'Message: ' . $e->getMessage();
            }
        }
    }
    public function putreqcron() {
        $project_id = $this->input->post("project_id");
        $getprojects = $this->db->get_where('project', array("status" => 'active', "project_id" => $project_id))->row();
        $getftp = $this->db->get_where("ftp_server", array("project_id" => $project_id))->row();
        $config['hostname'] = $this->encryption->decrypt($getftp->hostname);
        $config['username'] = $this->encryption->decrypt($getftp->username);
        $config['password'] = $this->encryption->decrypt($getftp->password);
        $config['port'] = $this->encryption->decrypt($getftp->port_no);
        $config['passive'] = TRUE;
        $config['debug'] = FALSE;
        $rtfolderremote = "/";
        $rootfolder = "./projects/" . $this->encryption->decrypt($getprojects->folder_name) . "/ftp_server/syncbackup/";
        $rootfolder = preg_replace('~/+~', '/', $rootfolder);
        $rtfolderremote = preg_replace('~/+~', '/', $rtfolderremote);
        $error_logfile = "errrlog" . time() . ".log";
        $myfile = fopen(APPPATH . "logs/" . $error_logfile, "w");
        if ($this->encryption->decrypt($getftp->port_no) == 22) {
            $ftpcalling = $this->ftpbackup;
        } else {
            $ftpcalling = $this->ftp;
        }
        if ($ftpcalling->connect($config)) {
            $fdatadata = $ftpcalling->raw_files($rtfolderremote);
            if (!is_array($fdatadata)) {
                $config['passive'] = FALSE;
                $ftpcalling->connect($config);
                $fdatadata = $ftpcalling->raw_files($rtfolderremote);
            }
            if (array_key_exists("public_html", $fdatadata)) {
                $rtfolderremote = $rtfolderremote . "public_html/";
                $rootfolder = $rootfolder . "public_html/";
                if (!is_dir($rootfolder)) {
                    mkdir($rootfolder);
                }
                $fdatadata = $ftpcalling->raw_files($rtfolderremote);
            }
            if (count($fdatadata) > 0) {
                $startdate = date("Y-m-d H:i:s");
                $total_files_folders = 0;
                $foldersdata = array();
                foreach ($fdatadata as $key) {
                    if ($key["filename"] == "." || $key["filename"] == "..") {
                        continue;
                    } else {
                        array_push($foldersdata, array("status" => "processing", "type" => $key["type"], "filename" => $key["filename"], "size" => $key["size"]));
                        $total_files_folders++;
                    }
                }
                $this->db->insert("backupftp", ["project_id" => $project_id, "client_id" => $getprojects->client_id, "startdate" => $startdate, "total_files_folders" => $total_files_folders, "localroot_folder" => $rootfolder, "remoteroot_folder" => $rtfolderremote, "foldersdata" => json_encode($foldersdata), "error_logfile" => $error_logfile]);
                $ftpbkp_id = $this->db->insert_id();
                echo json_encode(array("status" => "success", "msg" => "Request to take backup submitted successfully please check latest FTP backup status on URL : <a href='" . base_url() . "client/backup'>Backup List</a>"));
            } else {
                echo json_encode(array("status" => "failed", "msg" => "No Data available FTP the credentials you have provided"));
            }
        } else {
            echo json_encode(array("status" => "failed", "msg" => "Failed to connect Please check FTP credentials"));
        }
    }
}
