<?php
ini_set('max_execution_time', -1);
ini_set('memory_limit', '50G');
class Project extends MX_Controller {
    function __construct() {
        parent::__construct();
        if ($this->session->userdata("role_type") != "admin") {
            redirect(base_url() . "admin/login");
        }
		ini_set('display_errors', 'Off');
    }
    public function index(){
    	$user_id = $this->session->userdata("user_id");
        $prodata = $this->db->query("select p.* from project p order by p.project_id DESC");
        $data["project_count"] = $prodata->num_rows();
        $data["page"] = "projects";
        $data["projects"] = $this->get_decrypt_project_object($prodata->result());
       
        $this->load->view("admin/project/project", $data);
    }
    public function get_decrypt_project_object($data) {
            foreach($data as $key => $value) {
                   
                 if ($data[$key]->project_name != '') {
                    $data[$key]->project_name = $this->encryption->decrypt($value->project_name);
                }
                if ($data[$key]->slug != '') {
                    $data[$key]->slug = $this->encryption->decrypt($value->slug);
                }
                if ($data[$key]->folder_name != '') {
                    $data[$key]->folder_name = $this->encryption->decrypt($value->folder_name);
                }
                if ($data[$key]->url != '') {
                    $data[$key]->url = $this->encryption->decrypt($value->url);
                }    
            }
        return $data;
    }
    function create() {
        $data["page"] = "projects";
        $this->load->view("admin/project/create", $data);
    }
    public function addhttp($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
    }
    function save() {
        $user_id = $this->session->userdata("user_id");
        $error_data = array();
        $name = trim($this->input->post("project_name"));
        $url = strtolower($this->input->post("url"));
        $slug = base64_encode($name);
        $name = !empty($name) ? $name : $error_data["project_name_msg"] = $this->lang->line("project_name_blank");
        $url = !empty($url) ? strtolower($url) : $error_data["project_url_msg"] = $this->lang->line("project_url_blank");
        if(!empty($url)) {
            
            $header_size =$this->checksystemresponse($url);
            $errorcodes = array(0);
            if(in_array($header_size["http_code"], $errorcodes)) {
                $error_data["project_url_msg"] = $this->lang->line("Webpage not reachable");
            }else{
                $url = $url;
            }
        }
        $url = $this->addhttp($url);
        $added_date = strtotime(date("Y-m-d"));
        $date = date("Y-m-d");
        $folder_name = str_replace("/","",$slug)."_".time();
        if(count($error_data) > 0) {
            echo json_encode(array("status" => "failed", "error_data" => $error_data));
        }else{
            mkdir("./projects/" . $folder_name, 0777);
            mkdir("./projects/" . $folder_name . "/ftp_server", 0777);
            mkdir("./projects/" . $folder_name . "/db_server", 0777);
            $data = array("project_name" => $this->encryption->encrypt($name), "slug" => $slug, "added_date" => $date, "folder_name" => $this->encryption->encrypt($folder_name), "datetimestamp" => $added_date, "client_id" => $user_id, "url" => $this->encryption->encrypt($url));$this->db->insert("project", $data);
            $project_id = $this->db->insert_id();
            echo json_encode(array("status" => "success", "msg" => $this->lang->line("project_add_msg")));
        }
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
                                        if($header_size["http_code"] == 0){
                                            $url_data = parse_url($url);
                                            if($url_data["scheme"] === "http://"){
                                                $ss = "https://".$url_data["host"];
                                                return $this->checksystemresponse($ss);
                                            }else if(strpos($url_data["host"], 'www.') !== false){
                                                
                                               $ss = "https://".str_replace("www.", "", $url_data["host"]);
                                               $url_data = parse_url($ss); 
                                               return $this->checksystemresponse($ss);
                                            }
                                        }
                                    }
                                    return $header_size;
                                }
    public function update() {
        $project_id = $this->input->post("proj_id");
        $error_data = array();
        $project_name = !empty($this->input->post("proj_name")) ? $this->input->post("proj_name") : $error_data["proj_name_msg"] = $this->lang->line("project_name_blank");
        $url = !empty($this->input->post("proj_url")) ? strtolower($this->input->post("proj_url")) : $error_data["proj_url_msg"] = $this->lang->line("project_url_blank");
        if (!empty($url)) {
            $header_size =$this->checksystemresponse($url);
            $errorcodes = array(0);
            if (in_array($header_size["http_code"], $errorcodes)) {
                $error_data["proj_url_msg"] = $this->lang->line("Webpage not reachable");
            }
        }
        if (count($error_data) > 0) {
            echo json_encode(array("status" => "failed", "error_data" => $error_data));
        } else {
            $url = $this->addhttp($url);
            $data = array("project_name" => $this->encryption->encrypt($project_name), "url" => $this->encryption->encrypt($url));
            $this->db->where("project_id", $project_id);
            if ($this->db->update("project", $data)) {
                echo json_encode(array("status" => "success", "msg" => $this->lang->line("project_update_msg")));
            } else {
                echo json_encode(array("status" => "failed", "msg" => $this->lang->line("something_wrong")));
            }
        }
    }
    public function deleteproject() {
        $password = trim($this->input->post("passwordverify"));
        $user_id = $this->session->userdata("user_id");
        if($password != ""){
            if($password == "delete"){
                $project_id = $this->input->post("delproj_id");
                $getdeletecron = $this->db->get_where("delete_project_cron",array("project_id" => $project_id))->num_rows();
                if($getdeletecron == 0){
                    $getprojects =  $this->db->get_where("project",array("project_id" => $project_id))->result();
                    $path = FCPATH."projects/".$this->encryption->decrypt($getprojects[0]->folder_name);
                    $this->db->insert("delete_project_cron",["project_id" => $project_id,"folderpath" => $path,"client_id" =>$user_id,"project_data" => json_encode($getprojects)]);
                    $deleteftpbkp = $this->db->where("project_id", $project_id)->delete("backupftp");
                    $deletesqlbkp = $this->db->where("project_id", $project_id)->delete("backupsql");
                    $deleteftpserver = $this->db->where("project_id", $project_id)->delete("mysql_server");
                    $deletesqlbkp = $this->db->where("project_id", $project_id)->delete("ftp_server");
                    $deletesqlbkp = $this->db->where("project_id", $project_id)->delete("restore_ftp");
                    $deletesqlbkp = $this->db->where("project_id", $project_id)->delete("project");
                        echo json_encode(array("status" => "success", "msg" => $this->lang->line("project_data_delete")));
                }else{
                     echo json_encode(array("status" => "failed", "msg" => $this->lang->line("delete_proj_already_in_process")));
                }   
            } else {
                echo json_encode(array("status" => "failed", "msg" => $this->lang->line("please_enter_proper_keyword")));
            }
        } else {
            echo json_encode(array("status" => "failed", "msg" => $this->lang->line("please_enter_keyword")));
        }
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
    public function manage_backup($project_id = NULL){
		if(!is_numeric($project_id)){
        $project_id = base64_decode($project_id);
		}
    		$prodata = $this->db->query("select p.* from project p where p.project_id = ".$project_id." ")->row();
    		$data["ftp_servers"] = $this->db->order_by("ftp_id","DESC")->get_where("ftp_server",array("project_id" => $project_id))->result();
    		$data["db_servers"] = $this->db->order_by("mysql_id","DESC")->get_where("mysql_server",array("project_id" => $project_id))->result(); 
	        $data["project_data"] = $prodata;
	        $data["page"] = "projects";
	        $this->load->view("admin/project/manage_backup", $data);
    }
    public function add_ftp($project_id=NULL){
    	if($project_id != NULL){
    		$prodata = $this->db->query("select p.* from project p where p.project_id = ".$project_id." ")->row();
    		$data["project_data"] = $prodata;
	    	$data["project_id"] = $project_id;
	    	$data["fun"] = "ftp";
	    	$data["page"] = "projects";
		    $this->load->view("admin/project/add_ftp_db", $data);
	    }
    }
    public function add_db($project_id=NULL){
    	if($project_id != NULL){
    		$prodata = $this->db->query("select p.* from project p where p.project_id = ".$project_id." ")->row();
    		$data["project_data"] = $prodata;
	    	$data["project_id"] = $project_id;
	    	$data["fun"] = "db";
	    	$data["page"] = "projects";
		    $this->load->view("admin/project/add_ftp_db", $data);	
		}
    }
     public function save_ftp() {
		  $config['upload_path']   = FCPATH.'key_files/'; 
         $config['allowed_types'] = '*';   
         $this->load->library('upload', $config);
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
            try {
        $ftp_id = $this->input->post("ftp_id");
        $protocol_type = trim($this->input->post("protocol_type"));
        $hostname = trim($this->input->post("hostname"));
        $username = trim($this->input->post("username"));
        $password = trim($this->input->post("password"));
        $port_no = trim($this->input->post("port_no"));
        $caption = trim($this->input->post("caption"));
        $project_id = trim($this->input->post("project_id"));
		$key_filepath = '';
		if ( ! $this->upload->do_upload('rsa_file')) {
            $error = array('error' => $this->upload->display_errors()); 
           
         }else{
			 $key_filepath = $this->upload->data('full_path');
		 }
		 	if($key_filepath){
			include(APPPATH.'third_party/phpseclib/Crypt/RSA.php');
					 
					 $rsa = new Crypt_RSA();
					$rsa->loadKey(file_get_contents($key_filepath));

					$ssh = new Net_SSH2($hostname);
					if (!$ssh->login($username, $password, $rsa)) {
						$ftpstatus = 0;
					}else{
						$ftpstatus = 1;
					}
					 
			}else{
        if($protocol_type == "ftp") {
            if (empty($port_no)) {
                $port_no = 21;
            }
            $callingftp = $this->ftp;
        }elseif($protocol_type == "sftp") {
            if (empty($port_no)) {
                $port_no = 22;
            }
            $callingftp = $this->ftpbackup;
        }
        $config['hostname'] = $hostname;
        $config['username'] = $username;
        $config['password'] = $password;
        $config['port'] = $port_no;
        $config['passive'] = TRUE;
        $config['debug'] = FALSE;
        $ftpstatus = 0;



        if ($callingftp->connect($config)) {
            $ftpstatus = 1;
        } else {
            $config['passive'] = FALSE;
            if ($callingftp->connect($config)) {
                $ftpstatus = 1;
            } else {
                $ftpstatus = 0;
            }
        }
	}		
        if($ftpstatus == 1){
        	$projectdata = $this->db->get_where("project",array("project_id" => $project_id))->row();
        	$folder_path = base64_encode($caption)."_".time()."_".$project_id;
        	$projectpath = $this->encryption->decrypt($projectdata->folder_name);
        	mkdir("./projects/".$projectpath."/ftp_server/".$folder_path, 0777);
        	if(is_dir("./projects/".$projectpath."/ftp_server/".$folder_path)){
        			mkdir("./projects/".$projectpath."/ftp_server/".$folder_path."/temp", 0777);
        			mkdir("./projects/".$projectpath."/ftp_server/".$folder_path."/syncbackup", 0777);
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
            				"caption" => $this->encryption->encrypt($caption),
							"key_filepath" => $key_filepath,
							"root_path" => ($this->input->post("remote_folder")) ? trim($this->input->post("remote_folder")) : '/',
							"exclude_dir" => ($this->input->post("exclude_dir")) ? trim($this->input->post("exclude_dir")) : '',
                            "added_date" => date("Y-m-d H:i:s")

            			);
            if ($this->db->insert("ftp_server", $data)) {
                $output = array("status" => "success", "msg" => $this->lang->line("ftp_credentials_added_msg"));
            } else {
                $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
            }
        } else {
            $output = array("status" => "failed", "msg" => $this->lang->line("cred_wrong"));
        }

        echo json_encode($output);
        }
            catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
            }

    }
	 public function save_db() {
        $hostname = trim($this->input->post("hostname"));
        $username = trim($this->input->post("username"));
        $password = trim($this->input->post("password"));
        $port_no = trim($this->input->post("port_no"));
        $caption = trim($this->input->post("caption"));
        $project_id = trim($this->input->post("project_id"));
		ini_set('display_errors', 'Off');
        $sqlcheck = 0;
          /*  set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING); )*/
			
            try {
            	if($port_no != ""){
            		$conn = mysqli_connect($hostname, $username, $password, '', $port_no);
            	}else{
            		$conn = mysqli_connect($hostname, $username, $password);
            	}
                
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
        if($sqlcheck == 1){
			// Turn off error reporting

        	$projectdata = $this->db->get_where("project",array("project_id" => $project_id))->row();
        	$folder_path = base64_encode($caption)."_".time()."_".$project_id;
        	$projectpath = $this->encryption->decrypt($projectdata->folder_name);
			
        	mkdir(FCPATH."/projects/".$projectpath."/db_server/".$folder_path, 0777, true);

        	if(is_dir(FCPATH."/projects/".$projectpath."/db_server/".$folder_path)){
        		mkdir(FCPATH."/projects/".$projectpath."/db_server/".$folder_path."/db", 0777);
            	mkdir(FCPATH."/projects/".$projectpath."/db_server/".$folder_path."/dbrestore", 0777);
            	mkdir(FCPATH."/projects/".$projectpath."/db_server/".$folder_path."/dbcheck", 0777);
        	
            $data = array(
            				"mhostname" => $this->encryption->encrypt($hostname), 
            				"musername" => $this->encryption->encrypt($username),
            				"mpassword" => $this->encryption->encrypt($password),
            				"project_id" =>$project_id,
            				"folder_path" => $folder_path,
            				"added_date" => date("Y-m-d"),
            				"caption" => $this->encryption->encrypt($caption),
            				"client_id" => $projectdata->client_id,
                            "added_date" => date("Y-m-d H:i:s")
            			);
            $data["port_no"] = $port_no !="" ? $port_no : 0;

				if ($this->db->insert("mysql_server", $data)) {
					$output = array("status" => "success", "msg" => $this->lang->line("sql_setup_msg"));
				} else {
					$output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
				}
			}
        }else{
            $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
        }
        echo json_encode($output);
    }
    public function backup_ftp($ftp_id=NULL){
            if($ftp_id != NULL){
                $data["ftp_server_details"] = $this->db->query("select ftp.* from ftp_server ftp where ftp.ftp_id = ".$ftp_id." ")->row();
                $ftpdata = $this->db->query("select ftp.* from backupftp ftp where ftp.ftp_id = ".$ftp_id." order by ftp.backup_id DESC")->result();

                $data["ftp_backup_list"] = $ftpdata;
                $data["ftp_id"] = $ftp_id;

                $data["page"] = "projects";
                $this->load->view("admin/project/backup_ftp", $data);   
            }
    }
    public function getftp_ajax(){
        
        $postData = $this->input->post();
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $ftp_id = $postData["ftp_id"];
        $records = $this->db->query("select ftp.* from backupftp ftp where ftp.ftp_id = ".$ftp_id." order by ftp.backup_id DESC")->result();
        $totalRecords = count($records);
        //$records = $this->db->get('client')->result();
        $totalRecordwithFilter = $totalRecords;
         $this->db->select('*');
         $this->db->where("ftp_id",$ftp_id);
         $this->db->order_by('backup_id', 'DESC');
         $this->db->limit($rowperpage, $start);
         $records = $this->db->get('backupftp')->result();
         $data = array();
         $cnt = 1;
         foreach($records as $record ){
                                   $action =     '<a  href="javascript:" class="btn btn-primary" onclick="viewlogs('.$record->backup_id.')">'.$this->lang->line("view_logs").'</a>';
                                       if($record->status == "success"){
                                          
                                       $action .= '<a  href="'.base_url().'admin/backup/downloadftp/'.$record->backup_id.'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="'.$this->lang->line("download").'">'.$this->lang->line("download").'</a><a style="margin:5px;min-width: 40px;" href="javascript:" class="btn btn-primary waves-effect waves-light" onclick="restorebkptest('.$record->backup_id.')" data-toggle="tooltip" data-placement="top" title="'.$this->lang->line("restore_bkp").'">'.$this->lang->line("restore_bkp").'</a><a style="margin:5px;min-width: 40px;" href="'.base_url().'admin/project/restore_to_server/'.$record->backup_id.'" class="btn btn-primary waves-effect waves-light"  data-toggle="tooltip" data-placement="top" title="'.$this->lang->line("restore_bkp_to_other").'"> '.$this->lang->line("restore_bkp_to_other").'</a>';
                                       
                                          }
                                        $st =  $record->status == 'processing' ? 'warning' : 'success';
            $status = '<span class="badge badge-'.$st.'">'.$this->lang->line($record->status).'</span>';
            $data[] = array( 
                "sr_no" => $cnt,
                "created" =>displayDate($record->startdate),
                "last_backup_size" => $this->general->convert_size($record->total_size),
                "status" => $status,
                "action" => $action
            ); 
            $cnt++;
        }
         ## Response
         $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
         );
         echo json_encode($response);
    }
    public function getdb_ajax(){
        
        $postData = $this->input->post();
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $db_id = $postData["db_id"];
        $records = $this->db->query("select * from backupsql where db_id = ".$db_id." order by backup_id DESC")->result();
        $totalRecords = count($records);
        //$records = $this->db->get('client')->result();
        $totalRecordwithFilter = $totalRecords;
         $this->db->select('*');
         $this->db->where("db_id",$db_id);
         $this->db->order_by('backup_id', 'DESC');
         $this->db->limit($rowperpage, $start);
         $records = $this->db->get('backupsql')->result();
         $data = array();
         $cnt = 1;
         foreach($records as $record ){
                                   $action ='<a  href="javascript:" class="btn btn-primary" onclick="viewlogs('.$record->backup_id.')">'.$this->lang->line("view_logs").'</a>';
                                  if($record->status == "success"){
                                    $action .= '<a href="'.base_url().'admin/backup/restore_db/'.$record->backup_id.'" class="btn btn btn-primary">'.$this->lang->line("download").'</a><a href="'.base_url().'admin/backup/restore_db/'.$record->backup_id.'" class="btn btn btn-info">'.$this->lang->line("restore_bkp").'</a>';
                                  }
            $st =  $record->status == 'processing' ? 'warning' : 'success';
            $status = '<span class="badge badge-'.$st.'">'.$this->lang->line($record->status).'</span>';
            $data[] = array( 
                "sr_no" => $cnt,
                "last_backup_time" =>displayDate($record->startdate),
                "last_backup_size" => $this->general->convert_size($record->total_size),
                "status" => $status,
                "action" => $action
            ); 
            $cnt++;
        }
         ## Response
         $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
         );
         echo json_encode($response);
    }
    public function backup_db($db_id = NULL){
            if($db_id != NULL){
                $data["db_server_details"] = $this->db->query("select dbs.* from mysql_server dbs where dbs.mysql_id = ".$db_id." ")->row();
                $dbdata = $this->db->query("select db.* from backupsql db where db.db_id = ".$db_id." order by db.backup_id DESC")->result();
                $data["db_backup_list"] = $dbdata;
                 $data["db_id"] = $db_id;
                $data["page"] = "projects";
                $this->load->view("admin/project/backup_db", $data);   
            }
    }
    public function putdbcron(){
        $db_id   = $this->input->post("db_id");
        $type   = $this->input->post("type");
        $schedule_date = $type == "schedule" ? $this->input->post("schedule_date") :"" ;
        $schedule_check = $type == "schedule" ? 1 :0;
        $db_data = $this->db->get_where("mysql_server",array("mysql_id" => $db_id))->row();
            set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
            try {
                if($db_data->port_no > 0){
                    $conn = mysqli_connect($this->encryption->decrypt($db_data->mhostname), $this->encryption->decrypt($db_data->musername), $this->encryption->decrypt($db_data->mpassword),'', $db_data->port_no);
                }else{
                    $conn = mysqli_connect($this->encryption->decrypt($db_data->mhostname), $this->encryption->decrypt($db_data->musername), $this->encryption->decrypt($db_data->mpassword));
                }
                if ($conn) {
                    $sql="SHOW DATABASES";  
                    $link = mysqli_query($conn,$sql);
                    $foldersdata = array();
                    $total_size = 0;
                    $total_dbs = 0;
					
                    while($row = mysqli_fetch_row($link)){
						
						
						$nodb_array = array("information_schema", "mysql", "innodb", "performance_schema", "sys", "tmp");
						
                        if (!in_array($row[0], $nodb_array)) {
														
                            $dbname = $row[0];
							
                            $sqldb = "SELECT TABLE_NAME AS `Table`,ROUND((DATA_LENGTH + INDEX_LENGTH)) AS Sizes FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dbname."' ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC";
                            $link2 = mysqli_query($conn,$sqldb);
                            $ftchdata = mysqli_fetch_array($link2);
                                                        
                            $file_name = base64_encode($dbname).time()."_".$db_data->mysql_id;
                            if($ftchdata["Sizes"] != null){
                                $total_size +=  $ftchdata["Sizes"];
                                $total_dbs++;
                                array_push($foldersdata, array("status" => "processing", "db_name" => $dbname, "size" => $ftchdata["Sizes"],"file_name" => $file_name,"logs" => ""));
                            }

                            
                        }
                    }
					//print_r($foldersdata); exit;
                    if(count($foldersdata) > 0){
                       $dbbdata =   array(
                                        "client_id" =>  $db_data->client_id,
                                        "project_id" => $db_data->project_id,
                                        "startdate" => date("Y-m-d H:i:s"),
                                        "db_id" => $db_data->mysql_id,
                                        "foldersdata" => json_encode($foldersdata),
                                        "total_size" => 0,
                                        "total_database" => $total_dbs,
                                        "completed_database" => 0,
                                        "schedule_date" => $schedule_date,
                                        "schedule_check" => $schedule_check,
                                    );

                       if($this->db->insert("backupsql",$dbbdata)){
                            $output = array("status" => "success", "msg" => $this->lang->line("db_process_background_msg"));
                       }else{
                         $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
                       }
                    }else{
                        $output = array("status" => "failed", "msg" => $this->lang->line("no_db_avail"));
                    }
                    echo json_encode($output);   
                }
            }
            catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
            }
            restore_error_handler();
    }
    public function putftpcron(){
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
            try {
        $type   = $this->input->post("type");    
        $ftp_id   = $this->input->post("ftp_id");
        $getftp = $this->db->get_where("ftp_server",array("ftp_id" => $ftp_id))->row();
		
		$total_remote_data = $this->db->order_by('backup_id', 'DESC')->get_where("backupftp",array("ftp_id" => $ftp_id))->row();$total_remote_size = ($total_remote_data->total_remote_size > 0) ? $total_remote_data->total_remote_size : $total_remote_data->total_size;
        $project_id = $getftp->project_id;
        $getprojects = $this->db->get_where('project', array("status" => 'active', "project_id" => $project_id))->row();
        $config['hostname'] = $this->encryption->decrypt($getftp->hostname);
        $config['username'] = $this->encryption->decrypt($getftp->username);
        $config['password'] = $this->encryption->decrypt($getftp->password);
        $config['port'] 	= $this->encryption->decrypt($getftp->port_no);
        $config['passive'] 	= TRUE;
        $config['debug'] 	= FALSE;
        $rtfolderremote 	= "/";
        $rootfolder 		= FCPATH."projects/".$this->encryption->decrypt($getprojects->folder_name). "/ftp_server/".$getftp->folder_path."/syncbackup/";
        $rootfolder 		= preg_replace('~/+~', '/', $rootfolder);
        $rtfolderremote 	= preg_replace('~/+~', '/', $rtfolderremote);
        $error_logfile 		= "errrlog" . time() . ".log";
        $myfile 			= fopen(APPPATH . "logs/" . $error_logfile, "w");
        $startdate 			= date("Y-m-d H:i:s");
        $total_files_folders = 0;
        $foldersdata 		= array();
        $ddins = array(
                                "project_id" => $project_id,
                                "ftp_id" => $ftp_id,
                                 "client_id" => $getprojects->client_id,
                                 "startdate" => $startdate,
                                 "localroot_folder" => $rootfolder,
                                 "remoteroot_folder" => $rtfolderremote,
                                 "error_logfile" => $error_logfile,
								 "total_remote_size" => ($total_remote_size) ? $total_remote_size : 0
                            );

                $this->db->insert("backupftp",$ddins);
                $ftpbkp_id = $this->db->insert_id();
                echo json_encode(array("status" => "success", "msg" => $this->lang->line("ftp_process_background_msg")));
            
        }catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
        }
            restore_error_handler();
    }
    public function deleteftp(){
             set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
            try {
                     $ftp_id   = $this->input->post("ftp_id");
                     $getftp = $this->db->get_where("ftp_server",array("ftp_id" => $ftp_id))->row();
                     $getprojects = $this->db->get_where("project",array("project_id" => $getftp->project_id))->row();
                     $rootfolder = FCPATH."projects/".$this->encryption->decrypt($getprojects->folder_name). "/ftp_server/".$getftp->folder_path;
                        $this->db->where("ftp_id",$getftp->ftp_id)->delete("backupftp");
                        $this->db->where("ftp_id",$getftp->ftp_id)->delete("ftp_server");
                        $data = array(
                                        "project_id" =>$getftp->project_id,
                                        "client_id" =>$getftp->client_id,
                                        "ftp_id" =>$ftp_id,
                                        "folderpath" =>  $rootfolder,
                                        "ftp_data" => json_encode($getftp)   
                                    );
                        $this->db->insert("delete_ftp_cron",$data);
                        $output = array("status" => "success", "msg" => $this->lang->line("ftp_server_delete"));
                        echo json_encode($output);
                     exit;
                }catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
            }
    }
    public function deletedb(){
            set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
            try {
                     $db_id   = $this->input->post("db_id");
                     $getdb = $this->db->get_where("mysql_server",array("mysql_id" => $db_id))->row();
                     $getprojects = $this->db->get_where("project",array("project_id" => $getdb->project_id))->row();
                     $rootfolder = FCPATH."projects/".$this->encryption->decrypt($getprojects->folder_name). "/db_server/".$getdb->folder_path;
                        $this->db->where("db_id",$getdb->mysql_id)->delete("backupsql");
                        $this->db->where("mysql_id",$getdb->mysql_id)->delete("mysql_server");
                        $data = array(
                                        "project_id" =>$getdb->project_id,
                                        "client_id" =>$getdb->client_id,
                                        "db_id" =>$db_id,
                                        "folderpath" =>  $rootfolder,
                                        "db_data" => json_encode($getdb)   
                                    );
                        $this->db->insert("delete_db_cron",$data);
                        $output = array("status" => "success", "msg" => $this->lang->line("db_server_delete"));
                      echo json_encode($output);
                      exit;
                }catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
            }
    }


    public function edit_ftp($ftp_id = null)
    {
        if ($ftp_id == null) {
            return false;
        }

        $ftp_id = base64_decode($ftp_id);
        $data_row = $this->db->where("ftp_id",$ftp_id)->get('ftp_server')->row();
        $data["project_data"] = $this->db->where("project_id",$data_row->project_id)->get('project')->row();
        $data["ftp_data"] = $data_row;
        $data["page"] = "project";
        $this->load->view("admin/project/edit_ftp", $data);
    }

    public function edit_db($db_id = null)
    {
        if ($db_id == null) {
            return false;
        }

        $db_id = base64_decode($db_id);
        $data_row = $this->db->get_where('mysql_server', ["mysql_id"=>$db_id] )->row();

     

        $data["project_data"] = $this->db->where("project_id",$data_row->project_id)->get('project')->row();
        $data["db_data"] = $data_row;
        $data["page"] = "project";
        $this->load->view("admin/project/edit_db", $data);
    }

    public function update_ftp()
    {
        $config['upload_path']   = FCPATH.'key_files/'; 
         $config['allowed_types'] = '*';   
         $this->load->library('upload', $config);
		 
        if (!$this->input->post()) {
            echo json_encode(["status" => "failed", "msg" => $this->lang->line("something_wrong")]);die;
        }

        try {
			$key_filepath = '';
			$ftp_id = $this->input->post("ftp_id");

            $ftp_data = $this->db->get_where("ftp_server", ['ftp_id' => $ftp_id])->row();
			
			 if($ftp_data->key_filepath){
					$key_filepath = $ftp_data->key_filepath;
			   }
			if ( ! $this->upload->do_upload('rsa_file')) {
            $error = array('error' => $this->upload->display_errors()); 
			  
			 }else{
					$key_filepath = $this->upload->data('full_path');
					
					$info = pathinfo($key_filepath);
					if ($info["extension"] == "ppk") {
					$key_new_filepath = str_replace("ppk", "pem", $key_filepath);
					$cmd = "puttygen ".$key_filepath." -O private-openssh -o".$key_new_filepath;				
						exec($cmd);	
						$key_filepath = $key_new_filepath;
					}				
			 }
		 
		 
            

            $protocol_type = trim($this->input->post("protocol_type"));
            $hostname = trim($this->input->post("hostname"));
            $username = trim($this->input->post("username"));
            $password = !empty(trim($this->input->post("password"))) ? trim($this->input->post("password")) : $this->encryption->decrypt($ftp_data->password);

            $port_no = trim($this->input->post("port_no"));
            $caption = trim($this->input->post("caption"));
            $project_id = trim($this->input->post("project_id"));

			if($key_filepath){
			include(APPPATH.'third_party/phpseclib/Crypt/RSA.php');
					 
					 $rsa = new Crypt_RSA();
					$rsa->loadKey(file_get_contents($key_filepath));

					$ssh = new Net_SSH2($hostname);
					if (!$ssh->login($username, $password, $rsa)) {
						$ftpstatus = 0;
					}else{
						$ftpstatus = 1;
					}
					 
			}else{
            if($protocol_type == "ftp") {
                if (empty($port_no)) {
                    $port_no = 21;
                }
                $callingftp = $this->ftp;
            }elseif($protocol_type == "sftp") {
                if (empty($port_no)) {
                    $port_no = 22;
                }
                $callingftp = $this->ftpbackup;
            }

            $config['hostname'] = $hostname;
            $config['username'] = $username;
            $config['password'] = $password;
            $config['port'] = $port_no;
            $config['passive'] = TRUE;
            $config['debug'] = FALSE;
            $ftpstatus = 0;
           // print_r($config); exit;
            if ($callingftp->connect($config)) {
                $ftpstatus = 1;
            } else {
                $config['passive'] = FALSE;
                if ($callingftp->connect($config)) {
                    $ftpstatus = 1;
                } else {
                    $ftpstatus = 0;
                }
            }

			}
            if($ftpstatus == 1){

                

                $data = array(
                                "protocol_type" => $this->encryption->encrypt($protocol_type),
                                "hostname" => $this->encryption->encrypt($hostname),
                                "username" => $this->encryption->encrypt($username),
                                "port_no" => $this->encryption->encrypt($port_no),
                                "caption" => $this->encryption->encrypt($caption),
                                "password" => $this->encryption->encrypt($password),
								"key_filepath" => $key_filepath,
								"root_path" => ($this->input->post("remote_folder")) ? trim($this->input->post("remote_folder")) : '/',
								"exclude_dir" => ($this->input->post("exclude_dir")) ? trim($this->input->post("exclude_dir")) : ''
                            );

                //Update Query
                $this->db->where(["ftp_id"=> $ftp_id, 'project_id' => $project_id]);

                if ($this->db->update("ftp_server", $data)) {
                    $output = array("status" => "success", "msg" => $this->lang->line("ftp_credentials_update_msg"));
                } else {
                    $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
                }

            } else {
                $output = array("status" => "failed", "msg" => $this->lang->line("cred_wrong"));
            }

            echo json_encode($output);
        }
        catch(\Throwable $e) {
            $output = array("status" => "failed", "msg" => $e->getMessage());
            echo json_encode($output);
        }

        exit;
    }


    public function update_db()
    {

        if (!$this->input->post()) {
            echo json_encode(["status" => "failed", "msg" => $this->lang->line("something_wrong")]);die;
        }

        $mysql_id = trim($this->input->post("mysql_id"));
        $project_id = trim($this->input->post("project_id"));

        $mysql_data = $this->db->get_where('mysql_server', ['mysql_id' => $mysql_id])->row();

        $hostname = trim($this->input->post("hostname"));
        $username = trim($this->input->post("username"));
        $password = !empty(trim($this->input->post("password"))) ? trim($this->input->post("password")) : $this->encryption->decrypt($mysql_data->mpassword);
        $port_no = trim($this->input->post("port_no"));
        $caption = trim($this->input->post("caption"));
        
        $sqlcheck = 0;

        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
        }, E_WARNING);

        try {
            if( !empty($port_no) ){
                $conn = mysqli_connect($hostname, $username, $password, '', $port_no);
            }else{
                $conn = mysqli_connect($hostname, $username, $password);
            }
            
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
        if($sqlcheck == 1){    
            $data = array(
                            "mhostname" => $this->encryption->encrypt($hostname), 
                            "musername" => $this->encryption->encrypt($username),
                            "mpassword" => $this->encryption->encrypt($password),
                            "caption" => $this->encryption->encrypt($caption),
                            "port_no" => $port_no != "" ? $port_no : 0,
                    );
            $this->db->where(["mysql_id"=> $mysql_id, 'project_id' => $project_id]);
            if($this->db->update("mysql_server", $data)) {
                $output = array("status" => "success", "msg" => $this->lang->line("db_update_success"));
            }else{
                $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
            }
        }else{
            $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
        }
        echo json_encode($output);
    }
    public function putftprestorecron(){
                    $bkp_id   = $this->input->post("bkp_id");
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
                                             "startdate" => date("Y-m-d H:i:s"),
											 "total_remote_size" => $bkpdata->total_remote_size
                                        );
										

                                        $this->db->insert("restore_ftp",$insdata);
                                             $output = array("status" => "success", "msg" => $this->lang->line("ftp_process_restore_background_msg"));
                                        
                                }else{
                                             $output = array("status" => "failed", "msg" => $this->lang->line("no_records_found"));
                                }
                            }else{
                                $output = array("status" => "failed", "msg" => $this->lang->line("ftp_process_restore_msg"));
                            }    
                         echo json_encode($output);
                 }catch(\Throwable $e) {
                        $output = array("status" => "failed", "msg" => $e->getMessage());
                        echo json_encode($output);
                        exit;
                    }
                }
                public function putcronalivesystem(){
                        $project_id     = $this->input->post("project_id");
                        $current_date   = date("Y-m-d");
                        $getalivesystem = $this->db->get_where("alive_system",array("added_date" => $current_date,"project_id" => $project_id));
                        $getprojects = $this->db->get_where("project",array("project_id" => $project_id))->row();
                        if($getalivesystem->num_rows() == 0 ){
                                $data = array(
                                                "client_id" => $getprojects->client_id,
                                                "project_id" => $project_id,
                                                "added_date" => $current_date,
                                                "status" => "processing"
                                            );
                                if($this->db->insert("alive_system",$data)){
                                        $output = array("status" => "success", "msg" => $this->lang->line("request_sent_capture_web"));
                                }else{
                                    $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
                                }
                        }else{
                                if($getalivesystem->row()->status == "success"){
                                    $output = array("status" => "redirect","alive_id" =>$getalivesystem->alive_id);
                                }else{
                                    $output = array("status" => "failed", "msg" => $this->lang->line("we_capture_web"));
                                }
                        }   
                        echo json_encode($output);
                    }

                 public function backuplogs(){
                    $backup_id     = $this->input->post("backup_id");
                    $data          = $this->db->get_where("backupftp",array("backup_id" => $backup_id));
                    if($data->num_rows() > 0){
                        $dt = $data->row();
                     
                            $output = array("status" => $dt->status, "total_size" => $dt->total_size, "remote_total_size" => $dt->total_remote_size, "mail_sent" => $dt->mail_sent , "mail_zip_sent" => $dt->mail_zip_sent );
                       
                    echo json_encode($output);
				}
                }
                public function backupdblogs(){
                    $backup_id     = $this->input->post("backup_id");
                    $data          = $this->db->get_where("backupsql",array("backup_id" => $backup_id));
                    if($data->num_rows() > 0){
						$dbData = $data->row();                      
                            $output = array("status" => $dbData->status, "data" => json_decode($dbData->foldersdata), "total_table" =>$dbData->total_table,"completed_table" =>$dbData->completed_table );
                    }else{
                         $output = array("status" => "failed", "msg" => $this->lang->line("no_records_found"));
                    }
                    echo json_encode($output);
                }
				
                public function restorelogs(){
                    $restore_id     = $this->input->post("restore_id");
                    $data          = $this->db->get_where("restore_ftp",array("restore_id" => $restore_id));
					
					if($data->num_rows() > 0){
					$data = $data->row();
                    $output = array("status" => $data->status, "total_size" => $data->total_size, "total_remote_size" => $data->total_remote_size, "data" => json_decode($data->foldersdata));
					}else{
					$output = array("status" => "failed", "msg" => $this->lang->line("no_records_found"));
					}
								
                    echo json_encode($output);
                }
                public function restore_to_server($backup_id=NULL){

                    $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,fs.caption,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$backup_id."")->row();
                    $data["backup_data"]=$bkpdata;
                    $data["page"] = "project";
                    $this->load->view("admin/project/restore_to_remote_process", $data);
                }
                public function fetch_remote_server_data(){
                                        $error_data = array();
                                        $protocol_type = !empty(trim($this->input->post("protocol_type"))) ? trim($this->input->post("protocol_type")) : $error_data["protocol_type_msg"] = $this->lang->line("protocol_type_blank");
                                        $hostname = !empty(trim($this->input->post("hostname"))) ? trim($this->input->post("hostname")) : $error_data["hostname_msg"] = $this->lang->line("hostname_blank");
                                        $username = !empty(trim($this->input->post("username"))) ? trim($this->input->post("username")) : $error_data["username_msg"] = $this->lang->line("username_blank");
                                        $password = !empty(trim($this->input->post("password"))) ? trim($this->input->post("password")) : $error_data["password_msg"] = $this->lang->line("password_blank");
                                        $port_no = !empty(trim($this->input->post("port_no"))) ? trim($this->input->post("port_no")) : $error_data["port_no_msg"] = $this->lang->line("port_no_blank");
                                        $caption = !empty(trim($this->input->post("caption"))) ? trim($this->input->post("caption")) : $error_data["caption_msg"] = $this->lang->line("caption_blank");

                                        $project_id = trim($this->input->post("project_id"));
                                        if($protocol_type == "ftp") {
                                            if (empty($port_no)) {
                                                $port_no = 21;
                                            }
                                            $callingftp = $this->ftp;
                                        }elseif($protocol_type == "sftp") {
                                            if (empty($port_no)) {
                                                $port_no = 22;
                                            }
                                            $callingftp = $this->ftpbackup;
                                        }
                                        $config['hostname'] = $hostname;
                                        $config['username'] = $username;
                                        $config['password'] = $password;
                                        $config['port'] 	= $port_no;
                                        $config['passive'] 	= TRUE;
                                        $config['debug'] 	= FALSE;
                                        $ftpstatus = 0;
										
										
                                        if ($callingftp->connect($config)) {
                                            $ftpstatus = 1;
                                        }else{
                                            $config['passive'] = FALSE;
                                            if ($callingftp->connect($config)) {
                                                $ftpstatus = 1;
                                            } else {
                                                $ftpstatus = 0;
                                            }
                                        }
										
                                        if($ftpstatus == 1 && count($error_data) == 0){
                                               $fdatadata = $callingftp->raw_files("/");
                                               $folderdata = array();
                                               $cnt = 1;
                                                array_push($folderdata, ["count" => $cnt,"folder_name" =>"/"]);
                                               if(count($fdatadata) > 0){
                                                    foreach ($fdatadata as $fkey){
                                                        if($fkey["filename"] == '.' || $fkey["filename"] == '..') {
                                                            continue;
                                                        }else{
                                                            if($fkey["type"] == 2){
                                                                array_push($folderdata, ["count" => $cnt,"folder_name" =>"/".$fkey["filename"]."/"]);
                                                                $cnt++;
                                                            }
                                                        }
                                                    }
                                                }
                                            $output = array("status" => "success","folderdata" => $folderdata);
                                        }else{
                                            $error_data["ftp_error_msg"] = $this->lang->line("cred_wrong");
                                            $output = array("status" => "failed", "error_data" => $error_data);
                                        }
                                        echo json_encode($output);
                                    }
                                    public function putftprestorecron_server(){
                                        $bkp_id   = $this->input->post("backup_id");
                                        $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$bkp_id."")->row();
                                        $protocol_type = $this->input->post("protocol_type");
                                        $hostname = $this->input->post("hostname");
                                        $username = $this->input->post("username");
                                        $password = $this->input->post("password");
                                        $port_no = $this->input->post("port_no");
                                        $caption = $this->input->post("caption");
                                        $remote_path = $this->input->post("remote_path");
                                        $remote_cred = array(
                                                                "protocol_type" => $protocol_type, 
                                                                "hostname" => base64_encode($hostname), 
                                                                "username" => base64_encode($username), 
                                                                "password" => base64_encode($password), 
                                                                "port_no" => $port_no, 
                                                                "caption" => $caption
                                                            );

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
                                        try{
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
                                                                 "startdate" => date("Y-m-d H:i:s"),
                                                                 "remote_data" => json_encode($remote_cred),
                                                                 "restore_type" => "remote",
                                                                 "remote_path" => $remote_path
                                                            );
                                                            $this->db->insert("restore_ftp",$insdata);
                                                                 $output = array("status" => "success", "msg" => $this->lang->line("ftp_process_restore_background_msg"));
                                                            
                                                    }else{
                                                                 $output = array("status" => "failed", "msg" => $this->lang->line("no_records_found"));
                                                    }  
                                             echo json_encode($output);
                                     }catch(\Throwable $e) {
                                            $output = array("status" => "failed", "msg" => $e->getMessage());
                                            echo json_encode($output);
                                            exit;
                                        }
                                    }
                                    public function putdbrestorecron(){
                                        $bkp_id   = $this->input->post("backup_id");
                                        $db_name   = $this->input->post("db_name");
                                        $bkpdata = $this->db->query("select bdbs.*,ms.mhostname,ms.mpassword,ms.musername,ms.port_no,ms.folder_path,ms.status as dbstatus,p.folder_name,ms.caption from backupsql bdbs INNER JOIN mysql_server ms on bdbs.db_id =ms.mysql_id INNER JOIN project p ON bdbs.project_id = p.project_id WHERE bdbs.backup_id = " . $bkp_id . "")->row();
                                        $db_data = json_decode($bkpdata->foldersdata);
                                        $ind_id = 0;
                                        $insert_id = 0;
                                        foreach($db_data as $k) {
                                            if($db_name == $k->db_name){
                                                $startdate = date("Y-m-d H:i:s");
                                                $filepath  = FCPATH."projects/" . $this->encryption->decrypt($bkpdata->folder_name) . "/db_server/" . $bkpdata->folder_path."/dbcheck/".$k->file_name;
                                                $srr = array("backup_id" => $bkp_id,"ind_id" => $ind_id,"db_name" =>$k->db_name,"startdate" =>  $startdate,"file_path" =>$filepath);
                                                $this->db->insert("restore_db",$srr);
                                                $insert_id = $this->db->insert_id();
                                            }
                                            $ind_id++;
                                        }
                                        if($insert_id != 0){
                                             $output = array("status" => "success", "msg" => "Successfully restored Backed up DB data");
                                        }else{
                                             $output = array("status" => "success", "msg" => $this->lang->line("something_wrong"));
                                        }

                                        echo json_encode($output);
                                    }


                                    public function update_daily_status(){
                                        $ftp_id = $this->input->post("ftp_id_schedule");
                                        $cntstatus = $this->input->post("cntstatus");
                                        $error_data = array();
                                        
                                        if($cntstatus == 1){
                                            $scheduling_type = !empty($this->input->post("scheduling_type")) ? $this->input->post("scheduling_type") : $error_data["scheduling_type"] = $this->lang->line("Please choose Scheduling Type");
                                            $scheduling_day = $this->input->post("scheduling_day");
                                            $scheduletime = !empty($this->input->post("scheduletime")) ? $this->input->post("scheduletime") : $error_data["scheduletime"] = $this->lang->line("Please select Scheduling time");

                                            if(!array_key_exists("scheduling_type",$error_data)){
                                                    if($scheduling_type != "daily"){
                                                        $scheduling_day = $this->input->post("scheduling_day") ? $this->input->post("scheduling_day") : $error_data["scheduling_day"] = $this->lang->line("Please select Scheduling day");
                                                    }else{
                                                        $scheduling_day = "";
                                                    }
                                            }
                                        }
                                       
                                        if(count($error_data) > 0 && $cntstatus == 1){
                                            $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"),"error_data" => $error_data);
                                        }else{
                                            $this->db->where("ftp_id",$ftp_id);
                                            if($cntstatus == 0){
                                                $up = array("scheduling_flag" => $cntstatus);
                                                $txt = "enabled";
                                            }else{
                                                $up = array("scheduling_flag" => $cntstatus,"scheduling_time" => $scheduletime, "scheduling_type" => $scheduling_type,"scheduling_day" => $scheduling_day);
                                                $txt = "disabled";
                                            } 


                                            if($this->db->update("ftp_server",$up)){
                                                $output = array("status" => "success", "msg" => $this->lang->line("Backup setting successfully")." ".$this->lang->line($txt));
                                            }else{
                                                $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
                                            }
                                        }
                                        echo  json_encode($output);
                                    }
                                    public function update_daily_status_db(){
                                        $mysql_id = $this->input->post("db_id_schedule");
                                        $cntstatus = $this->input->post("cntstatusdb");
                                        $error_data = array();
                                        if($cntstatus == 1){
                                            $scheduling_type = !empty($this->input->post("scheduling_type_db")) ? $this->input->post("scheduling_type_db") : $error_data["scheduling_type"] = $this->lang->line("Please choose Scheduling Type");
                                            $scheduling_day = $this->input->post("scheduling_day_db");
                                            $scheduletime = !empty($this->input->post("scheduletime")) ? $this->input->post("scheduletime") : $error_data["scheduletime"] = $this->lang->line("Please select Scheduling time");

                                            if(!array_key_exists("scheduling_type",$error_data)){
                                                    if($scheduling_type != "daily"){
                                                        $scheduling_day = $this->input->post("scheduling_day_db") ? $this->input->post("scheduling_day_db") : $error_data["scheduling_day"] = $this->lang->line("Please select Scheduling day");
                                                    }else{
                                                        $scheduling_day = "";
                                                    }
                                            }
                                        }
                                        
                                        if(count($error_data) > 0 && $cntstatus == 1){
                                            $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"),"error_data" => $error_data);
                                        }else{
                                            $this->db->where("mysql_id",$mysql_id);
                                            if($cntstatus == 0){
                                                $up = array("scheduling_flag" => $cntstatus);
                                                $txt = "enabled";
                                            }else{
                                                $up = array("scheduling_flag" => $cntstatus,"scheduling_time" => $scheduletime, "scheduling_type" => $scheduling_type,"scheduling_day" => $scheduling_day);
                                                 $txt = "disabled";
                                            } 
                                            if($this->db->update("mysql_server",$up)){
                                                $output = array("status" => "success", "msg" => $this->lang->line("Backup setting successfully")." ".$this->lang->line($txt));
                                            }else{
                                                $output = array("status" => "failed", "msg" => $this->lang->line("something_wrong"));
                                            }
                                        }
                                        echo  json_encode($output);

                                    }





            }