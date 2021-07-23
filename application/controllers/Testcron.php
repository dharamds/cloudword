<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_error', 1);
class Testcron extends CI_Controller {
	
	private $ftpconnect;
	
    function __construct() {
        parent::__construct();
				
    }
    //Background process running code
    public function run_processing_projects() {
        $getprojects = $this->db->query("select * from backupftp where  cron_status = 0 AND status IN('processing','failed')")->result();
        foreach ($getprojects as $pro) {
             
                if($pro->folder_retrieve_flag == 1){
                    $cmd = "php " . FCPATH . "index.php cron execute_background_requests " . $pro->backup_id;
                    exec($cmd."  > /dev/null 2>/dev/null &");
                }else{
                    $this->folder_retrieve($pro->backup_id);
                    $cmd = "php " . FCPATH . "index.php cron execute_background_requests " . $pro->backup_id;
                    exec($cmd."  > /dev/null 2>/dev/null &");
                }
            
        }
    }
    
    public function get_running_process($prefix = 'execute_background_requests'){   
         $ret =  shell_exec("ps aux | grep php | grep ".$prefix);
         $retData = explode('www-data' ,$ret);
         $retDataNew = explode('root' ,$retData[0]);
        
		//print_r($ret); exit;
		
         foreach($retDataNew as $key => $item):
            $index = strpos($item, $prefix) + strlen($prefix);
            $number_text = substr($item, $index);
            $process_id = explode(' ', trim($number_text));
            $result[] = $process_id[0];
         endforeach;
         return (array_unique(array_filter($result)));
    }
	
    public function runallcronstuck(){
        $getftp = $this->get_running_process("execute_background_requests");
        $getftpbkp = $this->db->query("select backup_id from backupftp where status = 'processing' and cron_status = 1 ")->result();
		print_r($getftp); 
        if(sizeof($getftpbkp) > 0){
			$cnt = 0;
            foreach($getftpbkp as $ft) {
               if(!in_array($ft->backup_id,$getftp)){
                   // $this->db->where("backup_id",$ft->backup_id)->update("backupftp",["cron_status" => 0]);
					
							$cmd = "php " . FCPATH . "index.php cron execute_background_requests ".$ft->backup_id;
                            exec($cmd." > /dev/null 2>/dev/null &");
               } 
			   $cnt++;
            }
        }

        $getftprestore = $this->get_running_process("execute_restore_ftp_process");
        $getftpres = $this->db->query("select restore_id from restore_ftp where status = 'processing' and cron_status = 1 ")->result(); 
		 print_r($getftprestore); 
        if(sizeof($getftpres) > 0){
			$cnt=0;
            foreach($getftpres as $fr) {
				
               if(!in_array($fr->restore_id,$getftprestore)){
                    //$this->db->where("restore_id",$fr->restore_id)->update("restore_ftp",["cron_status" => 0]);
					//print_r($fr->restore_id);
					$cmd = "php " . FCPATH . "index.php cron execute_restore_ftp_process ".$fr->restore_id;
                  exec($cmd." > /dev/null 2>/dev/null &");
					
               } 
			   $cnt++;
            }
        }
		
        $getdb = $this->get_running_process("execute_background_db"); 
        $getdbbkp = $this->db->query("select backup_id from backupsql where status = 'processing' and cron_status = 1 ")->result();    
        //print_r($getdb); exit;
		
		if(sizeof($getdbbkp) > 0){
            foreach($getdbbkp as $dbb) {
               if(!in_array($dbb->backup_id,$getdb)){
				   
				   
				    $cmd = "php " . FCPATH . "index.php cron execute_background_db " . $dbb->backup_id . " ";
					exec($cmd." > /dev/null 2>/dev/null &");
				   
                    //$this->db->where("backup_id",$dbb->backup_id)->update("backupsql",["cron_status" => 0]);
               } 
            }
        }
		
		$getdb = $this->get_running_process("execute_restore_db_process"); 
		
		
		
        $getdbbkp = $this->db->query("select restore_id from restore_db where status = 'processing' and cron_status = 1 ")->result();   

	//print_r($getdbbkp); exit;	
        if(sizeof($getdbbkp) > 0){
            foreach($getdbbkp as $dbb) {
               if(!in_array($dbb->restore_id,$getdb)){
				   $cmd = "php " . FCPATH . "index.php cron execute_restore_db_process ".$dbb->restore_id;
                    exec($cmd." > /dev/null 2>/dev/null &");
                    //$this->db->where("restore_id",$dbb->restore_id)->update("restore_db",["cron_status" => 0]);
               } 
            }
        }
        
        
        
    }
	
    public function execute_background_requests($backup_id) {
        $getftp = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = " . $backup_id . "");
		
		 
            if($getftp->num_rows() > 0){
				
                $getftp = $getftp->row();
				
				
                $fdata  = json_decode($getftp->foldersdata);
                $cntt   = 0;
			
	// make ftp connection	
		if($getftp->set_connect == 1){
			
			$this->load->library("ftpbackup");
            $folderdata = array();
            $config['hostname'] = $this->encryption->decrypt($getftp->hostname);
            $config['username'] = $this->encryption->decrypt($getftp->username);
            $config['password'] = $this->encryption->decrypt($getftp->password);
            $config['port'] = (int)$this->encryption->decrypt($getftp->port_no);
            $config['passive'] = TRUE;
            $config['debug'] = FALSE;
			
			
			
            if((int)$this->encryption->decrypt($getftp->port_no) == 22) {
                $ftpcalling = $this->ftpbackup;
            } else {
                $ftpcalling = $this->ftp;
            }
			
            if($ftpcalling->connect($config)){
				  $fdatadata = $ftpcalling->raw_files("/");
				
                if(count($fdatadata) == 0) {
                    $config['passive'] = FALSE;
                    if($ftpcalling->connect($config)){
                        $this->ftpconnect = $ftpcalling;
                    }else{
                        $conn1 = 0;
                    }
                }else{
					 
					$this->ftpconnect = $ftpcalling;
                    $conn1 = 1;
                }
			}
		}
				//////////////////////////////////////////////////////////
				

                $projectdata = $getftp;
               $this->db->where("backup_id",$backup_id)->update("backupftp",["cron_status" => 1]);
              
                    foreach($fdata as $fset){
                        
						if($fset->status == "processing" || $fset->status == "failed"){
                            $cmd = "php " . FCPATH . "index.php cron ftploopdataback ".$backup_id ." ".$cntt."";
                            //exec($cmd." > /dev/null 2>/dev/null &");
                           $this->ftploopdataback($backup_id,$cntt);
                        }
						
                        $cntt++;
                    }
                
            }
    }

    public function add_schedule_backup_ftps(){
                $getftps = $this->db->query("select * from ftp_server fs where scheduling_flag = 1 AND scheduling_add_flag = 0")->result();
                $curdate = strtotime(date("Y-m-d H:i"));
                foreach ($getftps as $fsdata) {
                    $getprojects = $this->db->get_where('project', array("status" => 'active', "project_id" => $fsdata->project_id))->row();
                    $chkdata  = 0;
                    if($fsdata->scheduling_type == "daily" && $fsdata->scheduling_time !=""){
                        $allotedtime = strtotime(date("Y-m-d ".$fsdata->scheduling_time));
                        $testdate = $curdate - $allotedtime;
                        if($testdate >= 0 ){
                            $chkdata = 1; 
                            
                        } 
                        
                    }else if($fsdata->scheduling_type == "weekly" && $fsdata->scheduling_time !=""){
                        $today = strtolower(date("D"));
                        if(strtolower($fsdata->scheduling_day) == $today){
                           $allotedtime = strtotime(date("Y-m-d ".$fsdata->scheduling_time));
                            $testdate = $curdate - $allotedtime;
                            if($testdate >= 0 ){
                                $chkdata = 1; 
                                
                            } 
                        }  
                    }else if($fsdata->scheduling_type == "monthly" && $fsdata->scheduling_time !=""){
                        $currentweekdates = array(date("Y-m-01"),date("Y-m-02"),date("Y-m-03"),date("Y-m-04"),date("Y-m-05"),date("Y-m-06"),date("Y-m-07"));
                        $today = date("Y-m-d");
                        if(in_array($today, $currentweekdates)){
                            $today = strtolower(date("D"));
                            if(strtolower($fsdata->scheduling_day) == $today){
                                    $allotedtime = strtotime(date("Y-m-d ".$fsdata->scheduling_time));
                                    $testdate = $curdate - $allotedtime;
                                    if($testdate >= 0 ){
                                        $chkdata = 1; 
                                        
                                    }  
                            }
                        }
                    }

                    if($chkdata == 1){
                        $startdate = date("Y-m-d H:i:s");
                        $rtfolderremote = "/";    
                        $rootfolder = FCPATH."projects/".$this->encryption->decrypt($getprojects->folder_name). "/ftp_server/".$fsdata->folder_path."/syncbackup/";
                        $rootfolder = preg_replace('~/+~', '/', $rootfolder);
                        $rtfolderremote = preg_replace('~/+~', '/', $rtfolderremote);
                        $error_logfile = "errrlog" . time().rand(000000000,999999999).".log"; 
                        $myfile = fopen(APPPATH . "logs/" . $error_logfile, "a+");
                        $ddins = array(
                                        "project_id" => $fsdata->project_id,
                                        "ftp_id" => $fsdata->ftp_id,
                                         "client_id" => $fsdata->client_id,
                                         "startdate" => $startdate,
                                         "localroot_folder" => $rootfolder,
                                         "remoteroot_folder" => $rtfolderremote,
                                         "error_logfile" => $error_logfile,
                                    );
                        if($this->db->insert("backupftp",$ddins)){
                        //$ftpbkp_id = $this->db->insert_id();
                        $this->db->where("ftp_id",$fsdata->ftp_id)->update("ftp_server",["scheduling_add_flag" => 1]);
						}
                    }
                }
    }

    public function run_all_schedule_queries(){
            $this->db->update("ftp_server",["scheduling_add_flag" => 0]);
            $this->db->update("mysql_server",["scheduling_add_flag" => 0]); 
    }


    public function run_daily_backups_ftps(){
            $getprojects = $this->db->query("select * from backupftp where cron_status = 0 AND daily_backup_check = 1 AND status IN('processing','failed')")->result();
            foreach($getprojects as $pro) {
               //if($pro->backup_id == 86){
                    if($pro->folder_retrieve_flag == 1){
                        $cmd = "php " . FCPATH . "index.php cron execute_background_requests " . $pro->backup_id . " ";
                        exec($cmd."  > /dev/null 2>/dev/null &");
                    }else{
                        $this->folder_retrieve($pro->backup_id);
                        //$this->execute_background_requests($pro->backup_id);
                        $cmd = "php " . FCPATH . "index.php cron execute_background_requests " . $pro->backup_id . " ";
                        exec($cmd."  > /dev/null 2>/dev/null &");
                    }
                //}
            }
        }
    public function folder_retrieve($backup_id){
            $getftp = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$backup_id . "")->row();
            $config['hostname'] = $this->encryption->decrypt($getftp->hostname);
            $config['username'] = $this->encryption->decrypt($getftp->username);
            $config['password'] = $this->encryption->decrypt($getftp->password);
            $config['port'] = $this->encryption->decrypt($getftp->port_no);
            $config['passive'] = TRUE;
            $config['debug'] = FALSE;
            $rtfolderremote = $getftp->remoteroot_folder;
            $rootfolder = $getftp->localroot_folder;
            $ff = fopen(APPPATH . "logs/" . $getftp->error_logfile, "a+");
            if ($this->encryption->decrypt($getftp->port_no) == 22) {
                $ftpcalling = $this->ftpbackup;
            }else{
                $ftpcalling = $this->ftp;
            }
            if($ftpcalling->connect($config)){
               $fdatadata = $ftpcalling->raw_files($rtfolderremote);
                if(count($fdatadata) == 0){
                         $config['passive']  = FALSE;
                         if($ftpcalling->connect($config)){
                                $conn1 = 1;
                         }else{
                            $conn1 = 0;
                         }
                }else{
                        $conn1 = 1;
                }
                $fdatadata = $ftpcalling->raw_files($rtfolderremote);
                if($conn1){
                    if (array_key_exists("public_html", $fdatadata)) {
                        $rtfolderremote = $getftp->remoteroot_folder . "public_html/";
                        $rootfolder = $getftp->localroot_folder . "public_html";
                        if (!is_dir($rootfolder)){
                            mkdir($rootfolder, 0777);
                        }
                        $rootfolder = $rootfolder."/";
                        $fdatadata = $ftpcalling->raw_files($rtfolderremote);
                    }
                    if (count($fdatadata) > 0){
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
                        $dataaa = array(
                                        "foldersdata" => json_encode($foldersdata),
                                        "folder_retrieve_flag" => 1,
                                        "total_files_folders" => $total_files_folders,
                                    );
                        $this->db->where("backup_id",$backup_id);
                        $this->db->update("backupftp",$dataaa);
                    }
                }else{
                    $msg = '<p>Unable to connect ftp on '.date("d/m/Y H:i:s").'</p>';
                    fwrite($ff, $msg);
                    $up = array();
                    if($getftp->error_mail_flag == 0){
                        $getemail = $this->db->get_where("client",array("client_id" => $getftp->client_id))->row();
                        $error_data = array("error_msg" => $msg);
                        //sendMail($getemail->email, 'ERROR_DATA', $error_data);  
                        sendMail('dharamendra@datalogysftware.com', 'ERROR_DATA', $error_data); 
                        $up["error_mail_flag"] = 1;
                    }
                    $up["cron_status"] = 0;
                    $this->db->where("backup_id", $backup_id);
                    $this->db->update("backupftp", $up);
                }
            }else{
                    $msg = '<p>Unable to connect ftp on '.date("d/m/Y H:i:s").'</p>';
                    fwrite($ff, $msg);
                    $up = array();
                    if($getftp->error_mail_flag == 0){
                        $getemail = $this->db->get_where("client",array("client_id" => $getftp->client_id))->row();
                        $error_data = array("error_msg" => $msg);
                       // sendMail($getemail->email, 'ERROR_DATA', $error_data);  
                       sendMail('dharamendra@datalogysftware.com', 'ERROR_DATA', $error_data); 
                        $up["error_mail_flag"] = 1;
                    }
                    $up["cron_status"] = 0;
                    $this->db->where("backup_id", $backup_id);
                    $this->db->update("backupftp", $up);
                    
                    
            }
    }
	
	
    public function ftploopdataback($backup_id,$indexid,$callingdata=array(),$totalsize = 0,$projectdata = '', $connect = NULL) {
		//print_r($backup_id); exit;
		
            if(empty($projectdata)){
                $projectdata = $this->db->query("select bftp.*,p.project_name,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$backup_id."")->row();
				
                $localfolder = $projectdata->localroot_folder;
                $remotefolder = $projectdata->remoteroot_folder;
                $jsondata = json_decode($projectdata->foldersdata);
                $fileorfolder = $jsondata[$indexid]->filename;
                $filesize = $jsondata[$indexid]->size;
                $filetype =  $jsondata[$indexid]->type;
                $project_id = $projectdata->project_id;
                $error_logfile = $projectdata->error_logfile;
                $ftpbkp_id = $projectdata->backup_id;
                $indexid = $indexid;
                $totalsize = 0;
				
				
                if ($filetype == 2) {
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
            }else{
                $localfolder = "";
                $remotefolder = "";
                $fileorfolder = '';
                $filetype = 0;
                $project_id = $projectdata->project_id;
                $error_logfile = $projectdata->error_logfile;
                $indexid = $indexid;
            }
            
            $ff = fopen(APPPATH . "logs/" . $error_logfile, "a+");
			
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
        throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
			
        try {
			
			$folderdata = array();
			
			if(isset($this->ftpconnect)){
				
				$ftpcalling = $this->ftpconnect;;
				
				$conn1 = 1;
			}else{
			$this->load->library("ftpbackup");
            
            $config['hostname'] = $this->encryption->decrypt($projectdata->hostname);
            $config['username'] = $this->encryption->decrypt($projectdata->username);
            $config['password'] = $this->encryption->decrypt($projectdata->password);
            $config['port'] = (int)$this->encryption->decrypt($projectdata->port_no);
            $config['passive'] = TRUE;
            $config['debug'] = FALSE;
			
            if((int)$this->encryption->decrypt($projectdata->port_no) == 22) {
                $ftpcalling = $this->ftpbackup;
            } else {
                $ftpcalling = $this->ftp;
            }
			
			 if($ftpcalling->connect($config)){
                $fdatadata = $ftpcalling->raw_files("/");
                if(count($fdatadata) == 0) {
                    $config['passive'] = FALSE;
                    if($ftpcalling->connect($config)){
                        $conn1 = 1;
                    }else{
                        $conn1 = 0;
                    }
                }else{
                    $conn1 = 1;
                }
			 }	
			
			}
            							

            if($ftpcalling){
               				
				
                if($conn1){
                    if($filetype == 1){
                        $ftpcalling->download($remotefolder, $localfolder, "auto");
                        $totalsize+= $filesize;
                    }else{
						
						
                        foreach($callingdata as $key) {
                            $fdatadata = $ftpcalling->raw_files($key["remoterootfolder"]);
										
							
                            if(count($fdatadata) > 0) {
														
								
                                foreach ($fdatadata as $fkey) {
                                    $lval = explode("/", $fkey["filename"]);
                                    $lval = $lval[count($lval) - 1];
                                    if($fkey["filename"] == '.' || $fkey["filename"] == '..') {
                                        continue;
                                    } else {
										
                                        if ($fkey["type"] == 1) {
											
											
                                            if(!file_exists($key["localrootfolder"] . $fkey["filename"])){
																								
                                                if ($ftpcalling->download($key["remoterootfolder"] . $fkey["filename"], $key["localrootfolder"] . $fkey["filename"])) {
                                                    $totalsize+= $fkey["size"];
													
													
                                                } else {
                                                    $msg = '<p> Unable to download file please check specified path : ' . $key["remoterootfolder"] . $fkey["filename"].'</p>';
                                                    fwrite($ff, $msg);
                                                }
                                            }else if(filesize($key["localrootfolder"] . $fkey["filename"]) !== $fkey["size"]){
												
                                                if ($ftpcalling->download($key["remoterootfolder"] . $fkey["filename"], $key["localrootfolder"] . $fkey["filename"])) {
                                                    $totalsize+= $fkey["size"];
                                                } else {
                                                    $msg = '<p> Unable to download file please check specified path : ' . $key["remoterootfolder"] . $fkey["filename"].'</p>';
                                                   
                                                    fwrite($ff, $msg);
                                                }
											
												
                                            }else{
												
											
												 $msg = '<p> Reconnect FTP</p>';
												 
                                                fwrite($ff, $msg);
                                                continue;
                                            }
                                        } else {
                                            $rf = preg_replace('~/+~', '/', $key["remoterootfolder"] . $fkey["filename"] . "/");
                                            $lf = preg_replace('~/+~', '/', $key["localrootfolder"] . $fkey["filename"] . "/");
                                            if (!is_dir(preg_replace('~/+~', '/', $key["localrootfolder"] . $fkey["filename"]))) {
                                                mkdir(preg_replace('~/+~', '/', $key["localrootfolder"] . $fkey["filename"]));   
                                            }
                                            array_push($folderdata, array("localrootfolder" => $lf, "remoterootfolder" => $rf));
                                        }
                                    }
                                }
                            } else {
                                $msg = '<p> No Data available in : ' . $key["remoterootfolder"] . '</p>';
                                fwrite($ff, $msg);
                                continue;
                            }
                        }
                    }
		
                    if(count($folderdata) > 0) {
                        $this->ftploopdataback($backup_id,$indexid,$folderdata,$totalsize,$projectdata);
                    } else {
                        $getftp = $this->db->get_where("backupftp", array("backup_id" => $projectdata->backup_id))->row();
                        $ffff = json_decode($getftp->foldersdata);
                         $ffff[$indexid]->status = "success";
                         $cmplcnt = 0;
                        foreach ($ffff as $dss) {
                                if($dss->status == "success"){
                                    $cmplcnt++;
                                }
                        }
                        $folderssdaa = json_encode($ffff);
                        $cmt = $cmplcnt;
                        $total_size = (int)$getftp->total_size + $totalsize;
                        if ($getftp->total_files_folders == $cmt) {
                            $this->db->where("backup_id", $projectdata->backup_id);
                            $this->db->update("backupftp", ["completed_files_folders" => $cmt, "total_size" => $total_size, "foldersdata" => $folderssdaa]);
							
							
                            $this->zipprojectss($projectdata, $total_size, $indexid);
                        } else {
                            $this->db->where("backup_id", $projectdata->backup_id);
                            $this->db->update("backupftp", ["completed_files_folders" => $cmt, "total_size" => $total_size, "foldersdata" => $folderssdaa]);
                        }
                    }
                }else{
                    $msg = '<p>Unable to connect ftp </p>';
                    fwrite($ff, $msg);
                    $up = array();
                    if($projectdata->error_mail_flag == 0){
                        $getemail = $this->db->get_where("client",array("client_id" => $projectdata->client_id))->row();
                        $error_data = array("error_msg" => "<p>".$msg."</p>");
                       // sendMail($getemail->email, 'ERROR_DATA', $error_data);  
                        sendMail('dharamendra@datalogysftware.com', 'ERROR_DATA', $error_data); 
                        $up["error_mail_flag"] = 1;
                    }
                   
                    $up["status"] ="processing";
                    $up["cron_status"] =0;
                    $getftp = $this->db->get_where("backupftp", array("backup_id" => $backup_id))->row();
                    $ffff = json_decode($getftp->foldersdata);
                    $ffff[$indexid]->status = "processing";
                     $up["foldersdata"] = json_encode($ffff);
                    $this->db->where("backup_id", $projectdata->backup_id);
                    $this->db->update("backupftp", $up);         
                   
                }
                } else {
                    $msg = '<p>Unable to connect ftp </p>';
                    fwrite($ff, $msg);
                    $up = array();
                    if($projectdata->error_mail_flag == 0){
                        $getemail = $this->db->get_where("client",array("client_id" => $projectdata->client_id))->row();
                        $error_data = array("error_msg" => "<p>".$msg."</p>");
                       // sendMail($getemail->email, 'ERROR_DATA', $error_data);  
                        sendMail('dharamendra@datalogysftware.com', 'ERROR_DATA', $error_data); 
                        $up["error_mail_flag"] = 1;
                    }
                    
                    $up["status"] ="processing";
                    $up["cron_status"] =0;
                    $getftp = $this->db->get_where("backupftp", array("backup_id" => $backup_id))->row();
                    $ffff = json_decode($getftp->foldersdata);
                    $ffff[$indexid]->status = "processing";
                    $up["foldersdata"] = json_encode($ffff);
                    $this->db->where("backup_id", $projectdata->backup_id);
                    $this->db->update("backupftp", $up);         
                }
        }catch(\Throwable $e){
                    $msg = $e->getMessage();
                    fwrite($ff, $msg);
					//sleep(30);
                    $up = array();
                    if($projectdata->error_mail_flag == 0){
                        $getemail = $this->db->get_where("client",array("client_id" => $projectdata->client_id))->row();
                        $error_data = array("error_msg" => "<p>".$msg."</p>");
                       // sendMail($getemail->email, 'ERROR_DATA', $error_data);  
                        sendMail('dharamendra@datalogysftware.com', 'ERROR_DATA', $error_data); 
                        $up["error_mail_flag"] = 1;
                    }
                    $up["status"] ="processing";
                    $up["cron_status"] =0;
                    $getftp = $this->db->get_where("backupftp", array("backup_id" => $backup_id))->row();
                    $ffff = json_decode($getftp->foldersdata);
                    $ffff[$indexid]->status = "processing";
                    $up["foldersdata"] = json_encode($ffff);
					$up["set_connect"] = 1;
					 	
                    $this->db->where("backup_id", $projectdata->backup_id);
                    $this->db->update("backupftp", $up);         
        }
        restore_error_handler();
    }
	
	
	/// test cron



	
    public function zipprojectss($projectdata, $total_size, $indexid){
			
		
       $ff = fopen(APPPATH . "logs/" . $projectdata->error_logfile, "a+");
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
        }, E_WARNING);
        try {
        //$this->load->library("zipp");    
        $startdate = $projectdata->startdate;
        $project_id = $projectdata->project_id;
        $error_logfile = $projectdata->error_logfile;
        //$total_size = 265;
        $projectfolder = $this->encryption->decrypt($projectdata->folder_name);
        $ftppath = $projectdata->folder_path;
        $currtimestamp = time();
        $file_name = "syncftpbackup_" . $currtimestamp . "_" . base64_encode($project_id) . ".zip";
        $baserootpath = FCPATH. "projects/". $projectfolder .'/ftp_server/'.$ftppath."/syncbackup";
        $zipPath = FCPATH. "projects/" . $projectfolder . "/ftp_server/" . $ftppath . "/" . $file_name;
		$syncbackuppath = FCPATH. "projects/". $projectfolder .'/ftp_server/'.$ftppath;
        //$checkzip = $this->zipp->create($baserootpath,$zipPath);
			
			// send email before zip
			$getbkftp = $this->db->get_where("backupftp", array("backup_id" => $projectdata->backup_id))->row();
			
			
			
			if($getbkftp->mail_sent == 0){
				
				if($this->send_ftp_backup_email($projectdata, $total_size, 'Backup FTP Report:', 1)){
					
				$this->db->where("backup_id", $projectdata->backup_id);
				$this->db->update("backupftp", ["mail_sent" => 1]);
				}
				
			}
			$cmd = 'cd '.$syncbackuppath.' && zip -r '.$zipPath.' syncbackup';
			//
			
			//$cmd = 'zip -r '.$zipPath.' '.$baserootpath;
				if(shell_exec($cmd)){
					$checkzip = true;
				}	
				
            if($checkzip) {
				//blank local root path
				
                $fsize = filesize($zipPath);
                $enddate = date("Y-m-d H:i:s");
                $bkpdata = array("timestamp_date" => $currtimestamp, "file_name" => $this->encryption->encrypt($file_name), "enddate" =>$enddate , "error_logfile" => $error_logfile, "total_size" => $fsize, "status" => "success");

			   // send email before zip
					$getbkftp = $this->db->get_where("backupftp", array("backup_id" => $projectdata->backup_id))->row();
					if($getbkftp->mail_zip_sent == 0){
						if($this->send_ftp_backup_email($projectdata, $fsize, 'Backup FTP with zip creation:', 0)){
							$bkpdata['mail_zip_sent'] = 1;
                        }
					}
					
					$this->db->where("backup_id", $projectdata->backup_id);
					$this->db->update("backupftp", $bkpdata);
				// delete unzip files	
				exec("rm -rf ".$baserootpath."/* > /dev/null 2>/dev/null &");  
            }
			exit;
        }
        catch(\Throwable $e) {
            $msg = $e->getMessage();
            fwrite($ff, $msg);
            exit;
        }
        restore_error_handler();
    } 
	
	public function send_ftp_backup_email($projectdata, $fsize, $subject, $type = 0){
			  
                 $getproject_setting =  $this->db->get_where("project_setting",array("client_id" => $projectdata->client_id))->row_array();
				 
                if($getproject_setting){
						$getclient =  $this->db->get_where("client",array("client_id" => $projectdata->client_id))->row();
                        $first_date   = new DateTime($projectdata->startdate);
                        $second_date  = new DateTime($enddate);
                        $interval = $first_date->diff($second_date);
                        $nt = $interval->format("%H hours %I minutes and %S seconds");
						if($type == 1){
						$email_head = "The backup for ".$this->encryption->decrypt($projectdata->project_name)." has been completed";
						$wzip_text = "Zip file creation is under process. We will send email once it will complete.<br>";
						$Processed = "Processed (FTP) :";
						
						}else{
						$email_head = "The backup and zip creation for ".$this->encryption->decrypt($projectdata->project_name)." has been completed";
						$Processed = "Backup (Zip) :";
						}
						
						
                        $content = array(
                                            "backup_email" => "Backup Email",
                                            "backup_email_msg" => $email_head,
                                            "startdate" => displayDate($projectdata->startdate,true),
                                            "Elapsed" => "Elapsed:",
                                            "elapsed_duration" => $nt,
                                            "processed_ftp" => $Processed,
                                            "processed_ftp_size" => $this->general->convert_size($fsize),
                                            "display_style_error" => "none",
                                            "display_style_success" => "show",
                                            "backup_detail_msg" =>   $wzip_text." <br>You can find more information about the reported errors by opening Cloud Service World. Go to your Project profile, click your backups, check select Logs. In the Warnings tab you will find detailed information",
                                            "thank_you" => "Thank you ! ,",
                                            "cpright" => $this->lang->line("cpright"),
                                            "website_url" => base_url(),
                                            "logo_url"  => base_url("public/public/front/img/frontend-logo.png"),
                                            "date" => "Date:"
                                        );

                    $geterro    = @file_get_contents(APPPATH."logs/".$error_logfile); 
                    preg_match_all('/<p>(.*?)<\/p>/s', $gettag, $matches);
                    $geterrors = '';
                    if(count(($matches[1])) > 0){
                      foreach($matches[1] as $a => $value) {
                            $geterrors .= "<p>";
                            $geterrors .= $value;
                            $geterrors .= "</p>";
                        }  
                    }
                    if($getproject_setting["notify_id"] == "success"){
                        $content["display_style_error"] = "none";
                        $content["display_style_success"] = "show";
                    }else if($getproject_setting["notify_id"] == "error"){
                        $content["ftp_errors"] = "Incompleted entries (FTP) & FTP Errors:";
                        $content["ftp_errors_content"]= !empty($geterrors) ? $geterrors : 'NA';
                        $content["display_style_error"] = "show";
                        $content["display_style_success"] = "none";

                    }else if($getproject_setting["notify_id"] == "both"){
                        $content["ftp_errors"] = "Incompleted entries (FTP) & FTP Errors:";
                        $content["ftp_errors_content"]= !empty($geterrors) ? $geterrors : 'NA';
                        $content["display_style_error"] = "show";
                        $content["display_style_success"] = "show";
                    }else{
                        $content = array();
                    }
                    if(count($content) > 0){
                        $subject = "Backup FTP Report: ";
                        $getfilecontent = "ftp_backup_template.html";
                        $stsetting    = $this->db->query("select name_value from site_setting ss where ss.setting_id IN(7,8)")->result(); 
                        $content["down_content"] = "This e-mail is generated from your Cloud Service World. You are receiving this email because you have a account with Cloud Service World and you've set it to be announced when a backup completes. If you think this e-mail is SPAM, please do report to us and we will take immediate action on this. Report spam to ".$stsetting[0]->name_value;
                        $msg   =$this->replace_content($content,$getfilecontent); 
                        $this->email->set_newline("\r\n");
                        $this->email->from($stsetting[0]->name_value, $stsetting[1]->name_value);
                        if($getproject_setting["send_to_mails"] != ""){
                            $this->email->to('dharamendra@datalogysoftware.com');
                            //$this->email->to($getclient->email.",".$getproject_setting["send_to_mails"]);
                        }else{
                            $this->email->to('dharamendra@datalogysoftware.com');
                            //$this->email->to($getclient->email);
                        }
                        $this->email->subject($subject);
                        $this->email->message($msg);
						return $this->email->send();
					}	
					
				}else{
					return false;
				}
		
	}
	

    public function replace_content($replace_data,$file_path){
            $cnt = @file_get_contents(FCPATH."public/mailtemplates/".$file_path);
            $content = $cnt;
            foreach($replace_data as $key => $value) {
                $content = str_replace("{".$key."}", $value, $content);
            }
            return $content;
    }

    public function testzip(){
        $ff = fopen("testsss.log","w+");
        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
        }, E_WARNING);
        try { 
            $this->load->library("zipp"); 
            $baserootpath = APPPATH.'cache';
            $file_name = "test".time().".zip";
            $zipPath = FCPATH. "uploads/". $file_name;
            echo $zipPath."<br>".$baserootpath;
            ///$checkzip = $this->zipp->create($baserootpath,$zipPath);
            //$cmd = "zip -r " .$zipPath.' -j '. $baserootpath.'/*';
            $cmd = "cd ". $baserootpath." && zip -r " .$zipPath." ";
            exec($cmd." > /dev/null 2>/dev/null &");
        }
        catch(\Throwable $e) {
            $msg = $e->getMessage();
            fwrite($ff, $msg);
            exit;
        }
    } 


        //newcron for Database Backup Background Process
        public function run_processing_db() {
            $getbackups = $this->db->query("select * from backupsql where cron_status = 0 AND daily_backup_check = 0 AND status IN('processing','failed')")->result();
            foreach ($getbackups as $bkp) {
                $cmd = "php " . FCPATH . "index.php cron execute_background_db " . $bkp->backup_id . " ";
                exec($cmd." > /dev/null 2>/dev/null &");
            }
        }
        public function add_daily_backups_dbs(){
            $db_data = $this->db->query("select * from mysql_server where daily_backup_flag = 1 AND daily_backup_add_flag = 0")->result();
            $date2  = date('Y-m-d H:i');
            $curdate = date('Y-m-d');
            $date22=date_create($date2);
            $date2 = strtotime(date_format($date22,"Y-m-d H:i"));
            foreach($db_data as $dbs){
                $date11=date_create($curdate." ".$dbs->daily_backup_time);
                $date1 = strtotime(date_format($date11,"Y-m-d H:i"));
                $backup_time = date('H:i');
                if($date1 > $date2 && $dbs->daily_backup_time != ""){
                    
              }    
            }
        }

        public function add_schedule_backup_dbs(){
                $db_data = $this->db->query("select * from mysql_server where scheduling_flag = 1 AND scheduling_add_flag = 0")->result();
				
				
				
                    $curdate  = strtotime(date('Y-m-d H:i'));
               // print_r($db_data); exit;   
                foreach ($db_data as $dbs) {
                    $getprojects = $this->db->get_where('project', array("status" => 'active', "project_id" => $dbs->project_id))->row();
                    $datechk = 0;
                    if($dbs->scheduling_type == "daily" && $dbs->scheduling_time != ""){
                        
                        $allotedtime = strtotime(date("Y-m-d ".$dbs->scheduling_time));
                        $testdate = $curdate - $allotedtime;
                        if($testdate >= 0 ){
                            $datechk = 1; 
                            
                        }   
                    }else if($dbs->scheduling_type == "weekly" && $dbs->scheduling_time != ""){
                        $today = strtolower(date("D"));
                        if(strtolower($dbs->scheduling_day) == $today){
                           $allotedtime = strtotime(date("Y-m-d ".$dbs->scheduling_time));
                            $testdate = $curdate - $allotedtime;
                            if($testdate >= 0 ){
                                $datechk = 1; 
                                
                            }   
                                
                        }  
                    }else if($dbs->scheduling_type == "monthly" && $dbs->scheduling_time != ""){
						
						
                        $currentweekdates = array(date("Y-m-01"),date("Y-m-02"),date("Y-m-03"),date("Y-m-04"),date("Y-m-05"),date("Y-m-06"),date("Y-m-07"));
                        $today = date("Y-m-d");
					
						
                        if(in_array($today, $currentweekdates)){
                            $today = strtolower(date("D"));
							
                            if(strtolower($dbs->scheduling_day) == $today){
                                 $allotedtime = strtotime(date("Y-m-d ".$dbs->scheduling_time));
                                    $testdate = $curdate - $allotedtime;
                                    if($testdate >= 0 ){
                                        $datechk = 1; 
                                        
                                    }  
                                    
                            }
                        }
                    }
					
						
					 
                    if($datechk == 1){

                        if($dbs->port_no > 0){
                        $conn = mysqli_connect($this->encryption->decrypt($dbs->mhostname), $this->encryption->decrypt($dbs->musername), $this->encryption->decrypt($dbs->mpassword), '', $dbs->port_no);
                        }else{
                            $conn = mysqli_connect($this->encryption->decrypt($dbs->mhostname), $this->encryption->decrypt($dbs->musername), $this->encryption->decrypt($dbs->mpassword));
                        }
						
						
						
                    if($conn){

                        $sql="SHOW DATABASES";  
                        $link = mysqli_query($conn,$sql);
                        $foldersdata = array();
                        $total_size = 0;
                        $total_dbs = 0;
						$nodb_array = array("information_schema", "mysql", "innodb", "performance_schema", "sys", "tmp");
						
                        while($row = mysqli_fetch_row($link)){
							//print_r($row);
                            if (!in_array($row[0], $nodb_array)) {
                                $dbname = $row[0];
                                $sqldb = "SELECT TABLE_NAME AS `Table`,ROUND((DATA_LENGTH + INDEX_LENGTH)) AS Sizes FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$dbname."' ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC";
                                $link2 = mysqli_query($conn,$sqldb);
                                $ftchdata = mysqli_fetch_array($link2);
                                $file_name = base64_encode($dbname).time()."_".$dbs->mysql_id;
                                if($ftchdata["Sizes"] != null){
                                    $total_size +=  $ftchdata["Sizes"];
                                    $total_dbs++;
                                    array_push($foldersdata,array("status" => "processing", "db_name" => $dbname, "size" => $ftchdata["Sizes"],"file_name" => $file_name,"logs" => ""));
                                }   
                            }
                        }
						
						
                        if(count($foldersdata) > 0){
                           
                           $dbbdata = array(
                                            "client_id" =>  $dbs->client_id,
                                            "project_id" => $dbs->project_id,
                                            "startdate" => date("Y-m-d H:i:s"),
                                            "db_id" => $dbs->mysql_id,
                                            "foldersdata" => json_encode($foldersdata),
                                            "total_size" => $total_size,
                                            "total_database" => $total_dbs,
                                            "completed_database" => 0,
                                            "daily_backup_check" => 0
                                        );
										
																			
                          
						  if($this->db->insert("backupsql",$dbbdata)){
							$this->db->where("mysql_id",$dbs->mysql_id)->update("mysql_server",["scheduling_add_flag" => 1]);  
						  }
                          
                        }
                    }


                    }
                }
				
				
    }








        public function run_daily_backups_dbs() {
            $getbackups = $this->db->query("select * from backupsql where cron_status = 0 AND  daily_backup_check = 1 AND status IN('processing','failed')")->result();
            foreach ($getbackups as $bkp) {
                $cmd = "php " . FCPATH . "index.php cron execute_background_db " . $bkp->backup_id . " ";
                exec($cmd." > /dev/null 2>/dev/null &");
            }
        }



        public function execute_background_db($backup_id) {
            $this->db->where("backup_id",$backup_id)->update("backupsql",["cron_status" => 1, 'status' => 'processing']);
            $getbackups = $this->db->get_where('backupsql', array("backup_id" => $backup_id))->row();
            $getdbservr = $this->db->get_where('mysql_server', array("mysql_id" => $getbackups->db_id))->row();
            $getdbproject = $this->db->get_where('project', array("project_id" => $getdbservr->project_id))->row();
            $errorlog = array();
            // set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            //     throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            // }, E_WARNING);
            // try {
                $folderdata = json_decode($getbackups->foldersdata);
                
				
				//print_r($folderdata); exit;
				$indexid = 0;
				$coundDB = count($folderdata);
                foreach ($folderdata as $db) {
					
					
                    $dbhost = $this->encryption->decrypt($getdbservr->mhostname);
                    $dbuser = $this->encryption->decrypt($getdbservr->musername);
                    $dbpass = $this->encryption->decrypt($getdbservr->mpassword);
                    $dbname = $db->db_name;
					
					//if($dbname == 'shopware')
					//{
						
					
					
                    $tables = '*';
                    $file_name = $db->file_name . ".zip";
                    $path = "./projects/" . $this->encryption->decrypt($getdbproject->folder_name) . "/db_server/" . $getdbservr->folder_path;
					
					
                    if ($db->status == "failed" || $db->status == "processing") {
						
                        $returndata = $this->backup_tables($dbhost, $dbuser, $dbpass, $dbname, $tables, $path, $file_name, $getdbservr->project_id, $getdbproject->client_id, $errorlog);
                       
					   //print_r($returndata);
					   
					   if ($returndata["status"] == "success") {
						   
                            $folderdata[$indexid]->status = "success";
                            $folderdata[$indexid]->logs = $returndata["error_log"];
							 						
                           
							
                            $getba = $this->db->get_where('backupsql', array("backup_id" => $backup_id))->row();
                            $cmpdb = $getba->completed_database + 1;
                            $this->db->where("backup_id", $backup_id);
							
														
                            $this->db->update("backupsql", ["foldersdata" => json_encode($folderdata), "completed_database" => $cmpdb,"status" =>"success"]);
							
      
							if($indexid == ($coundDB - 1))
							{
								$getproject_setting =  $this->db->get_where("project_setting",array("client_id" => $getbackups->client_id))->row_array();
							
							
                            if(sizeof($getproject_setting) > 0){
                                            $getclient =  $this->db->get_where("client",array("client_id" => $getbackups->client_id))->row();
                                            $first_date   = new DateTime($getbackups->startdate);
                                            $second_date  = new DateTime($getba->enddate);
                                            $interval = $first_date->diff($second_date);
                                            $nt = $interval->format("%H hours %I minutes and %S seconds");
                                            $complsize = 0;
                                            if(count($folderdata) > 0){
                                                foreach ($folderdata as $l) {
                                                    $complsize += $l->size;
                                                }
                                            }
                                            $content = array(
                                                                "backup_email" => "Backup Email",
                                                                "backup_email_msg" => "The Database backup for ".$this->encryption->decrypt($getdbproject->project_name)." has been completed",
                                                                "startdate" => displayDate($getbackups->startdate,true),
                                                                "Elapsed" => "Elapsed:",
                                                                "elapsed_duration" => $nt,
                                                                "sql" => "SQL:",
                                                                "total_sql_table" => "Total SQL tables downloaded :".$getba->total_database."(".$this->general->convert_size($complsize).")",
                                                                "display_style_error" => "none",
                                                                "display_style_success" => "show",
                                                                "backup_detail_msg" =>   "You can find more information about the reported errors by opening Cloud Service World. Go to your Project profile, click your backups, check select Logs. In the Warnings tab you will find detailed information",
                                                                "thank_you" => "Thank you ! ,",
                                                                "cpright" => $this->lang->line("cpright"),
                                                                "website_url" => base_url(),
                                                                "logo_url"  => base_url("public/public/front/img/frontend-logo.png"),
                                                                "date" => "Date:",
                                                                "check_image" =>  base_url("public/public/assets/img/check.png"),
                                                            );
                                        $errorlogs = '';
                                        $ss = json_decode($getbackups->error_msg);
                                        if($ss !=""){
                                            foreach ($getbackups->error_msg as $key => $value) {
                                                   $errorlogs .= "<p>".$value."</p>"; 
                                            }
                                        }else{
                                            $errorlogs = "NA";
                                        }

                                        $geterrors    = $errorlogs; 
                                        if($getproject_setting["notify_id"] == "success"){
                                            $content["display_style_error"] = "none";
                                            $content["display_style_success"] = "show";
                                        }else if($getproject_setting["notify_id"] == "error"){
                                            $content["ftp_errors"] = "Incompleted entries (DB) & DB Errors:";
                                            $content["ftp_errors_content"]= !empty($geterrors) ? $geterrors : 'NA';
                                            $content["display_style_error"] = "show";
                                            $content["display_style_success"] = "none";

                                        }else if($getproject_setting["notify_id"] == "both"){
                                            $content["ftp_errors"] = "Incompleted entries (DB) & DB Errors:";
                                            $content["ftp_errors_content"]= !empty($geterrors) ? $geterrors : 'NA';
                                            $content["display_style_error"] = "show";
                                            $content["display_style_success"] = "show";
                                        }else{
                                            $content = array();
                                        }
                                        if(sizeof($content) > 0){
                                            $subject = "Backup Database Report: ";
                                            $getfilecontent = "db_backup_template.html";
                                            
                                            $stsetting    = $this->db->query("select name_value from site_setting ss where ss.setting_id IN(7,8)")->result(); 
                                            $content["down_content"] = "This e-mail is generated from your Cloud Service World. You are receiving this email because you have a account with Cloud Service World and you've set it to be announced when a backup completes. If you think this e-mail is SPAM, please do report to us and we will take immediate action on this. Report spam to ".$stsetting[0]->name_value;
                                            $msg   =$this->replace_content($content,$getfilecontent); 
                                            //$CI->load->library('email');
                                            $this->email->set_newline("\r\n");
                                            $this->email->from($stsetting[0]->name_value, $stsetting[1]->name_value);

                                             if($getproject_setting["send_to_mails"] != ""){
                                                    $this->email->to($getclient->email.",".$getproject_setting["send_to_mails"]);
                                                }else{
                                                    $this->email->to($getclient->email);
                                                }
                                            
                                            $this->email->subject($subject);
                                            $this->email->message($msg);
											
                                            if($this->email->send()){
                                                $this->db->where("backup_id", $getbackups->backup_id);
                                                $this->db->update("backupsql", ["mail_sent" => 1]);
                                            }else{
                                                 $this->db->where("backup_id", $getbackups->backup_id);
                                                 $this->db->update("backupsql", ["mail_sent" => 0]);           
                                            }
                                        }
                                }
							}
                        } else {
                            $folderdata[$indexid]->status = "processing";
                            $folderdata[$indexid]->logs = $returndata["error_log"];
                            $this->db->where("backup_id", $backup_id);
                            $this->db->update("backupsql", ["foldersdata" => json_encode($folderdata) ]);
							$this->db->reset();
                        }
						
						
                    }
					$indexid++;
					//}
                }
				
				
				
                $getb = $this->db->get_where('backupsql', array("backup_id" => $backup_id))->row();
                if ($getb->total_database == $getb->completed_database) {
                    $this->db->where("backup_id", $backup_id);
                    $this->db->update("backupsql", ["status" => "success", "enddate" => date("Y-m-d H:i:s") ]);
					
                }
            // }
            // catch(\Throwable $e) {
            //     $this->db->where("backup_id", $backup_id);
            //     $this->db->update("backupsql", ["error_status" => 1, "error_msg" => json_encode(array($e->getMessage())) ]);
            // }
            //restore_error_handler();

			exit('completed');
        }
		
		
        public function backup_tables($host, $user, $pass, $dbname, $tables = '*', $path, $file_name, $project_id, $user_id, $errorlog) {
			
            $getuser = $this->db->get_where("client", array("client_id" => $user_id))->row();
            $link = mysqli_connect($host, $user, $pass, $dbname);
            $startdate = date("Y-m-d H:i:s");
            set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);
            try {
                mysqli_query($link, 'set global max_allowed_packet=268435456;');
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
				$dbpath = $path.'/db/'.$dbname;
				if (!is_dir($dbpath)) {
					mkdir($dbpath);   
				}
				
				
				
				$tablecount = 0;
                foreach ($tables as $table) {
					//if($tablecount < 8){
                    set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                        throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                    }, E_WARNING);
                    try {
                        $result = mysqli_query($link, 'SELECT * FROM ' . $table);
                        $num_fields = mysqli_num_fields($result);
                        $num_rows = mysqli_num_rows($result);
                        $return = 'DROP TABLE IF EXISTS ' . $table . ';';
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
                    catch(\Throwable $e) {
                        $errorlog[] = $e->getMessage();
                    }
					$fileName = $dbpath.'/'.$table.'.sql';
                $handle = fopen($fileName, 'w+');
                fwrite($handle, $return);
				
				//}
					
					$tablecount++;
                }
				
				
				$sqlFilePath = FCPATH.$path.'/dbcheck/'.$file_name;
				$sqlFolderPath = FCPATH.$path;
				
				$cmd = "cd ". $sqlFolderPath." && zip -r " .$sqlFilePath." db";
				$res =  shell_exec($cmd);
				
				$cmd = "rm -rf ".$sqlFolderPath.'/db/*';
								exec($cmd." > /dev/null 2>/dev/null &");
								
						
						
                if (file_exists($sqlFilePath)) {
                    return array("status" => "success", "error_log" => $errorlog, 'db_name' => $dbname);
                } else {
                    return array("status" => "failed", "error_log" => $errorlog);
                }
            }
            catch(\Throwable $e) {
                $errorlog[] = $e->getMessage();
                return array("status" => "failed", "error_log" => $errorlog);
            }
        }
        public function testsize(){
                $size = 1099511627776;
                $units = explode(' ', 'B KB MB GB TB PB');
                $mod = 1024;
                for ($i = 0; $size > $mod; $i++) {
                    $size /= $mod;
                }
                $endIndex = strpos($size, ".")+3;
                echo substr( $size, 0, $endIndex).' '.$units[$i];

        }
        function convertFromBytes()
        {
            $bytes = 109951162777;
            $bytes /= 1024;
            if ($bytes >= 1024 * 1024) {
                $bytes /= 1024;
                echo number_format($bytes / 1024, 1) . ' GB';
            } elseif($bytes >= 1024 && $bytes < 1024 * 1024) {
                echo number_format($bytes / 1024, 1) . ' MB';
            } else {
                echo number_format($bytes, 1) . ' KB';
            }
        }
        public function testsftp(){

         
                $this->load->library("ftpbackup");
                $config['hostname'] = 'ssh.strato.de';
                $config['username'] = 'datalogy@future-call.de';
                $config['password'] = 'B4Spcs5s@9';
                $config['port'] = 22;
                $config['passive'] = FALSE;
                $config['debug'] = FALSE;
                if ($this->ftpbackup->connect($config)) {
                    set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                        throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                    }, E_WARNING);
                    try {
                        echo "<pre>";
                        print_r($this->ftpbackup->raw_files("/"));
                    }
                    catch(\Throwable $e) {
                        $errorlog[] = $e->getMessage();
                         print_r(array("status" => "failed", "error_log" => $errorlog));
                    }
                }
            
        }
        public function testftp(){

         
            $config['hostname'] = 'ftp.datalogysoftware.com';
            $config['username'] = 'csw1@datalogysoftware.com';
            $config['password'] = 'dharam444';
            $config['port'] = 21;
            $config['passive'] = FALSE;
            $config['debug'] = FALSE;
			
			
			
			
            if ($this->ftp->connect($config)) {
                set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                    throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                }, E_WARNING);
                try {
                    echo "<pre>";
                        print_r($this->ftp->raw_files("/"));
                }
                catch(\Throwable $e) {
                    $errorlog[] = $e->getMessage();
                     print_r(array("status" => "failed", "error_log" => $errorlog));
                }
            }
        
    }

    //run processing ftp restore process
    public function run_processing_ftp_restore() {
            $getbackups = $this->db->query("select * from restore_ftp where cron_status = 0 AND status IN('processing','failed')")->result();
			
            foreach($getbackups as $bkp) {
				
                    $this->db->where("restore_id",$bkp->restore_id)->update("restore_ftp",["cron_status" => 1]);

                    if($bkp->extract_flag == 1){
                        $cmd = "php " . FCPATH . "index.php cron execute_restore_ftp_process " . $bkp->restore_id;
                        exec($cmd." > /dev/null 2>/dev/null &");
                    }else{
                        $chkzip = $this->extracttofolder($bkp->restore_id);
                        if($chkzip){
                            $cmd = "php " . FCPATH . "index.php cron execute_restore_ftp_process " . $bkp->restore_id;
                            exec($cmd." > /dev/null 2>/dev/null &");
                        }
                    }
                   }
				   
        }

    public function extracttofolder($restore_id){
		
            $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from restore_ftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.restore_id = ".$restore_id."")->row();
			
					
            if(shell_exec("unzip -o ".$bkpdata->zippath." -d ".$bkpdata->baserootpath)){
				
					$filepath = "./projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/".$bkpdata->restore_folder."/syncbackup/";
			
            if(is_dir($filepath)){
                            $allFiles = scandir($filepath);
                                $foldersdata = array();
                                    foreach($allFiles as $key => $value){
                                            if($value == "." || $value == ".."){
                                                continue;
                                            }else{
                                                if(is_dir($filepath.$value)){
                                                    $size = 0;
                                                    $type = 2;
                                                }else{
                                                    $size = filesize($filepath.$value);
                                                    $type = 1;
                                                }
                                                array_push($foldersdata,array("filename" =>$value,"size" => $size ,"type" => $type,"status" => "processing"));
                                            }    
                                    }
                                    $foldercnt = count($foldersdata);
                                    $this->db->where("restore_id",$restore_id)->update("restore_ftp",["extract_flag " => 1,"foldersdata" => json_encode($foldersdata),"total_files_folders" => $foldercnt, 
                                        "completed_files_folders" => 0]);
                            return TRUE;
            }else{
                return FALSE;
            }
			}else{
				$this->db->where("restore_id",$restore_id)->update("restore_ftp",["extract_flag " => 0, "foldersdata" => '',"cron_status" => 0]);
			 return FALSE;							
			}
					
            
    }    
    public function execute_restore_ftp_process($restore_id){
        $restoredata = $this->db->get_where("restore_ftp",array("restore_id" => $restore_id))->row();
		
		$bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name,p.project_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$restoredata->backup_id." ")->row();
		
		
        if(!empty($restoredata)){
            $folderdata = json_decode($restoredata->foldersdata);
            $cnt = 0;
            if($restoredata->error_logfile ==""){
                 $error_logs = "restorelogs_".time().".log";
                 //$foo = fopen(FCPATH. "restorelogs/".$error_logs, "w+");
            }else{
                $error_logs = $restoredata->error_logfile;
            }
            $this->db->where("restore_id",$restore_id)->update("restore_ftp",["error_logfile" => $error_logs]);
				
			 $config['passive']  = TRUE;
             $config['debug']    = FALSE;
			 
			// make ftp connection	
			if($restoredata->restore_type == "remote"){
                 $remdata = json_decode($restoredata->remote_data);   
                 $port_no = $remdata->port_no;
                 $config['hostname'] = base64_decode($remdata->hostname);
                 $config['username'] = base64_decode($remdata->username);
                 $config['password'] = base64_decode($remdata->password);
                 $config['port']     = $port_no;
                 $remote_path = $restoredata->remote_path;
            }else{
             $port_no = $this->encryption->decrypt($bkpdata->port_no);  
             $config['hostname'] = $this->encryption->decrypt($bkpdata->hostname);
             $config['username'] = $this->encryption->decrypt($bkpdata->username);
             $config['password'] = $this->encryption->decrypt($bkpdata->password);
             $config['port']     = $this->encryption->decrypt($bkpdata->port_no);
             $remote_path = "/";
            }		
			
            if((int)$this->encryption->decrypt($bkpdata->port_no) == 22) {
                $ftpcalling = $this->ftpbackup;
            } else {
                $ftpcalling = $this->ftp;
            }
			//print_r($ftpcalling->connect($config));
		//exit;
			
			
            if($ftpcalling->connect($config)){
				  $fdatadata = $ftpcalling->raw_files("/");
				
                if(count($fdatadata) == 0) {
                    $config['passive'] = FALSE;
                    if($ftpcalling->connect($config)){
                        $this->ftpconnect = $ftpcalling;
                    }else{
                        $conn1 = 0;
                    }
                }else{
					 
					$this->ftpconnect = $ftpcalling;
                    $conn1 = 1;
                }
			}
			
		
            foreach($folderdata as $key){
                if($key->status == "processing" || $key->status == "failed"){
                  $cmd = "php " . FCPATH . "index.php cron ftprestoreloop ".$restore_id." ".$cnt." ";
                  //exec($cmd." > /dev/null 2>/dev/null &");
                  $this->ftprestoreloop($restore_id,$cnt);
                  }
                  $cnt++;       
            }
        }
    }
	
    public function ftprestoreloop($restore_id,$index_id){
        $restoredata = $this->db->get_where("restore_ftp",array("restore_id" => $restore_id))->row();
        $folderdata = json_decode($restoredata->foldersdata);  
        $remoteroot_folder =$folderdata[$index_id]->filename;
        $file_type =$folderdata[$index_id]->type;
        $foo = fopen(FCPATH . "restorelogs/".$restoredata->error_logfile, "a+");
	   
	   
		if($restoredata->restore_type == "remote"){
                 $remdata = json_decode($restoredata->remote_data);   
                 $remote_path = $restoredata->remote_path;
            }else{
             $remote_path = "/";
            }		
	   
        $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name,p.project_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$restoredata->backup_id." ")->row();
			
			if(isset($this->ftpconnect)){
				
				//print_r($ftpcalling); exit;
				$ftpcalling = $this->ftpconnect;;
				
				$conn1 = 1;
			}else{
		
		
            if($restoredata->restore_type == "remote"){
                 $remdata = json_decode($restoredata->remote_data);   
                 $port_no = $remdata->port_no;
                 $config['hostname'] = base64_decode($remdata->hostname);
                 $config['username'] = base64_decode($remdata->username);
                 $config['password'] = base64_decode($remdata->password);
                 $config['port']     = $port_no;
                 $remote_path = $restoredata->remote_path;
            }else{
              $port_no = $this->encryption->decrypt($bkpdata->port_no);  
             $config['hostname'] = $this->encryption->decrypt($bkpdata->hostname);
             $config['username'] = $this->encryption->decrypt($bkpdata->username);
             $config['password'] = $this->encryption->decrypt($bkpdata->password);
             $config['port']     = $this->encryption->decrypt($bkpdata->port_no);
             $remote_path = "/";
            }
				
			
             $config['passive']  = TRUE;
             $config['debug']    = FALSE;
			 
             if($port_no == 22){
                 $ftpcalling = $this->ftpbackup;
             }else{
                 $ftpcalling = $this->ftp;
             }
            if($ftpcalling->connect($config)){
                       $fdatadata = $ftpcalling->raw_files("/");
                     if(count($fdatadata) == 0){
                         $config['passive']  = FALSE;
                         if($ftpcalling->connect($config)){
                                $conn1 = 1;
                         }else{
                            $conn1 = 0;
                         }
                     }else{
                        $conn1 = 1;
                     }
			}	 
			}		
			
					if($ftpcalling){
						
                     if($conn1){

                     $dirpath  = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/".$restoredata->restore_folder;    
                     $filepath = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/".$restoredata->restore_folder."/syncbackup/".$remoteroot_folder;
                     if($file_type == 2) {
                            $filepath = $filepath."/";
                            $remoteroot_folder = $remote_path.$remoteroot_folder."/";
                           $upload = $ftpcalling->mirror($filepath,$remoteroot_folder);
                     }else{
                         $filepath = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/".$restoredata->restore_folder."/syncbackup/".$remoteroot_folder;
                          $upload = $ftpcalling->upload($filepath, $remoteroot_folder);

                     }
					 //print_r($upload); exit;
                         if($upload){
                           
                             $restoredat = $this->db->get_where("restore_ftp",array("restore_id" => $restore_id))->row();
                             $jsondata = json_decode($restoredat->foldersdata);
                              $jsondata[$index_id]->status = "success";
                             $cmnpcall = 0;
                             $errorcnt = 0;
                             foreach ($jsondata as $k) {
                                  if($k->status === "success"){
                                    $cmnpcall++;
                                  }else if($k->status === "failed" || $k->status === "processing"){
                                     $errorcnt++;   
                                  }
                              } 
                            if($errorcnt === 0){
                                $chk = "success";
                                //$cmdtoremove = exec("rmdir -r ".$dirpath);
                                $enddate = date("Y-m-d H:i:s");
                               
                                $getproject_setting =  $this->db->get_where("project_setting",array("client_id" => $bkpdata->client_id))->row_array();
                                if(sizeof($getproject_setting) > 0){
                                            $getclient =  $this->db->get_where("client",array("client_id" => $bkpdata->client_id))->row();
                                            $first_date   = new DateTime($restoredata->startdate);
                                            $second_date  = new DateTime($enddate);
                                            $interval = $first_date->diff($second_date);
                                            $nt = $interval->format("%H hours %I minutes and %S seconds");
                                            $complsize = 0;
                                            $fileloc = filesize(FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/".$this->encryption->decrypt($bkpdata->file_name));
                                            $content = array(
                                                                "backup_email" => "Restore FTP Email",
                                                                "backup_email_msg" => "The FTP Restore process for ".$this->encryption->decrypt($bkpdata->project_name)." has been completed",
                                                                "startdate" => displayDate($bkpdata->added_date,true),
                                                                "Elapsed" => "Elapsed:",
                                                                "elapsed_duration" => $nt,
                                                                "processed_ftp" => "Backup (Zip)",
                                                                "processed_ftp_size" => $this->general->convert_size($fileloc),
                                                                "display_style_error" => "none",
                                                                "display_style_success" => "show",
                                                                "backup_detail_msg" =>   "You can find more information about the reported errors by opening Cloud Service World. Go to your Project profile, click your Restore, check  Logs. In the Warnings tab you will find detailed information",
                                                                "thank_you" => "Thank you ! ,",
                                                                "cpright" => $this->lang->line("cpright"),
                                                                "website_url" => base_url(),
                                                                "logo_url"  => base_url("public/public/front/img/frontend-logo.png"),
                                                                "date" => "Date:",
                                                                "check_image" =>  base_url("public/public/assets/img/check.png"),
                                                            );
                                        $geterrors    = @file_get_contents(FCPATH."restorelogs/".$restoredat->error_logfile); 
                                        if($getproject_setting["notify_id"] == "success"){
                                            $content["display_style_error"] = "none";
                                            $content["display_style_success"] = "show";
                                        }else if($getproject_setting["notify_id"] == "error"){
                                            $content["ftp_errors"] = "Incompleted entries (FTP Restore) & FTP Restore Errors:";
                                            $content["ftp_errors_content"]= !empty($geterrors) ? $geterrors : 'NA';
                                            $content["display_style_error"] = "show";
                                            $content["display_style_success"] = "none";

                                        }else if($getproject_setting["notify_id"] == "both"){
                                            $content["ftp_errors"] = "Incompleted entries (FTP Restore) & FTP Restore Errors:";
                                            $content["ftp_errors_content"]= !empty($geterrors) ? $geterrors : 'NA';
                                            $content["display_style_error"] = "show";
                                            $content["display_style_success"] = "show";
                                        }else{
                                            $content = array();
                                        }
                                        if(sizeof($content) > 0){
                                            $subject = "Restore FTP Report: ";
                                            $getfilecontent = "ftp_restore_template.html";
                                            
                                            $stsetting    = $this->db->query("select name_value from site_setting ss where ss.setting_id IN(7,8)")->result(); 
                                            $content["down_content"] = "This e-mail is generated from your Cloud Service World. You are receiving this email because you have a account with Cloud Service World and you've set it to be announced when a backup completes. If you think this e-mail is SPAM, please do report to us and we will take immediate action on this. Report spam to ".$stsetting[0]->name_value;
                                            $msg   =$this->replace_content($content,$getfilecontent); 
                                            $this->email->set_newline("\r\n");
                                            $this->email->from($stsetting[0]->name_value, $stsetting[1]->name_value);
											//$email = $getclient->email;
                                            $email = "dharamendra@datalogysoftware.com";
                                             if($getproject_setting["send_to_mails"] != ""){
                                                   // $this->email->to($email.",".$getproject_setting["send_to_mails"]);
													$this->email->to($email);
                                                }else{
                                                    $this->email->to($email);
                                                }
                                            $this->email->subject($subject);
                                            $this->email->message($msg);
                                            $this->email->send();
											
                                        }
                                }
								
								
								 $cmd = "rm -rf ".$dirpath;
								exec($cmd." > /dev/null 2>/dev/null &");

                            }else{
                                $chk = "processing";
                                $enddate = '';
                            }    
                             
                             $this->db->where("restore_id",$restore_id)->update("restore_ftp",["foldersdata" => json_encode($jsondata),"completed_files_folders" => $cmnpcall,"status" => $chk,"enddate" => $enddate]);
                          }
                    }else{
                        $msg = "<p>".$this->lang->line("ftp_failed_connect")."</p>";
                        fwrite($foo, "");
                        fwrite($foo, $msg);
                        $folderdata[$index_id]->status = "processing";
                        $this->db->where("restore_id",$restore_id)->update("restore_ftp",["foldersdata" => json_encode($folderdata),"status" => "processing","cron_status" => 0,"error_log" => $msg]); 
                    }
                }else{
                    $msg = "<p>".$this->lang->line("ftp_failed_connect")."</p>";
                    fwrite($foo, $msg);
                        $folderdata[$index_id]->status = "processing";
                        $this->db->where("restore_id",$restore_id)->update("restore_ftp",["foldersdata" => json_encode($folderdata),"status" => "processing","cron_status" => 0,"error_log" => $msg]); 
                }
        
     }

     public function testcrn(){
        echo "testdd";
     }
     public function run_processing_alive_system(){
                $current_date   = date("Y-m-d");
                $getprocessinglist = $this->db->get_where("alive_system",["added_date" => $current_date,"status" => "processing"])->result();
                foreach ($getprocessinglist as $key) {
                        $cmd = "php " . FCPATH . "index.php cron checksss ".$key->alive_id;
                        exec($cmd);
                }
        }



                                public function checksss($alive_id){
                                    $gg = $this->db->query("select a.*,p.url from alive_system a inner join project p on a.project_id = p.project_id where a.alive_id = ".$alive_id."");
                                    if($gg->num_rows()){
                                        $projectdata = $gg->row();
                                        $url = $this->encryption->decrypt($projectdata->url);
                                        $checksys = $this->checksystemresponse($url);
                                        if($checksys["http_code"] > 0){
                                            $urldata = $this->follow_links($url);
                                            $data = json_encode($urldata);
                                        }else{
                                            $data = json_encode(array());
                                        }
                                        $this->db->where("alive_id",$alive_id)->update("alive_system",["json_data" => $data]);
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
                                public function get_details($url){
                                
                                $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
                                $context = stream_context_create($options);
                                $doc = new DOMDocument();
                                @$doc->loadHTML(@file_get_contents($url, false, $context));
                                $title = $doc->getElementsByTagName("title");
                                $title = $title->item(0)->nodeValue;
                                $description = "";
                                $keywords = "";
                                $metas = $doc->getElementsByTagName("meta");
                                for ($i = 0; $i < $metas->length; $i++) {
                                    $meta = $metas->item($i);
                                    if (strtolower($meta->getAttribute("name")) == "description")
                                        $description = $meta->getAttribute("content");
                                    if (strtolower($meta->getAttribute("name")) == "keywords")
                                        $keywords = $meta->getAttribute("content");
                                }
                                $imagesdata = $this->get_attributess($url,"images");
                                $scriptlinks = $this->get_attributess($url,"scripts");
                                $csslinks = $this->get_attributess($url,"csss");
                                $systemresponsedata = $this->checksystemresponse($url);
                                return array("title" => str_replace("\n", "", $title), "description" => str_replace("\n", "", $description),"keywords"=> str_replace("\n", "", $keywords),"url" => $url,"imagedata" => $imagesdata,"jsscripts" => $scriptlinks,"csslinks" =>$csslinks,"systemresponsedata"=>$systemresponsedata);

                            }
                            public function follow_links($url,$urldata=array()) {
                                $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
                                $context = stream_context_create($options);
                                $doc = new DOMDocument();
                                @$doc->loadHTML(@file_get_contents($url, false, $context));
                                $linklist = $doc->getElementsByTagName("a");
                                $imagelist = $doc->getElementsByTagName("img");
                                if(count($linklist) > 0){


                                foreach($linklist as $link) {
                                    $l =  $link->getAttribute("href");
                                    if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
                                    } else if (substr($l, 0, 2) == "//") {
                                        $l = parse_url($url)["scheme"].":".$l;
                                    } else if (substr($l, 0, 2) == "./") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
                                    } else if (substr($l, 0, 1) == "#") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
                                    } else if (substr($l, 0, 3) == "../") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
                                    } else if (substr($l, 0, 11) == "javascript:") {
                                        continue;
                                    } else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
                                    }
                                    if(!in_array($l, $this->already_crawled)){
                                            array_push($this->already_crawled, $l);
                                            $datatt = $this->get_details($l);
                                            array_push($this->crawling_data, $datatt);
                                            $this->follow_links($l);
                                    }
                                }
                                 return $this->crawling_data;
                             }else{
                                return array();
                             }
                            }
                            public function get_attributess($url,$type){
                                $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
                                $context = stream_context_create($options);
                                $doc = new DOMDocument();
                                @$doc->loadHTML(@file_get_contents($url, false, $context)); 
                                if($type == "images"){
                                    $tag = "img";
                                    $attr = "src";
                                }else if($type == "scripts"){
                                    $tag = "script";
                                    $attr = "src";
                                }else if($type == "csss"){
                                    $tag = "link";
                                    $attr = "href";
                                }
                                $linklist = $doc->getElementsByTagName($tag);
                                $imagearray = array();
                                foreach($linklist as $link) {
                                    $l =  $link->getAttribute($attr);
                                    if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
                                    } else if (substr($l, 0, 2) == "//") {
                                        $l = parse_url($url)["scheme"].":".$l;
                                    } else if (substr($l, 0, 2) == "./") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
                                    } else if (substr($l, 0, 1) == "#") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
                                    } else if (substr($l, 0, 3) == "../") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
                                    } else if (substr($l, 0, 11) == "javascript:") {
                                        continue;
                                    } else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
                                        $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
                                    }
                                array_push($imagearray,$l);
                                }
                                return $imagearray;
                            }
                            public function check_alive_system(){
                                $project_details = $this->db->order_by("project_id", "asc")->get_where("project",array("status" =>'active'))->result();
								
								
                                foreach ($project_details as $key ) {
                                    $getheader = $this->checksystemresponse($this->encryption->decrypt($key->url));
									
									
									
									
                                    if(count($getheader) >0){
                                        $gtproject = $this->db->get_where("alive_system",array("client_id" => $key->client_id,"project_id" =>$key->project_id));
                                        $user = $this->db->get_where("client",array("client_id" => $key->client_id))->row();
										
                                        if($getheader["http_code"] !== 200){
                                                $data["client_id"] = $key->client_id;
                                                $data["project_id"] = $key->project_id; 
                                                $data["error_code"] = $getheader["http_code"];
                                                $data["added_date"] = date("Y-m-d H:i:s");
												
                                                $data["mail_flag"] 		= 0;

                                                 if($gtproject->num_rows() > 0){
                                                    $dd = $gtproject->row();
                                                    $mail_flag_count = (int)$dd->mail_flag + 1; 
																									
													$timestamp1 = strtotime(date("Y-m-d"));
													$timestamp2 = strtotime($dd->added_date);
												
														$minute = abs($timestamp2 - time())/60;
													
													
                                                        if($minute > 2 && $dd->mail_flag == 0){
															//echo $minute;
															sendMail('dharamendra@datalogysoftware.com','ALIVE_SYSTEM_ERROR',["website" => $this->encryption->decrypt($key->url),'user_name' => $user->fname]);
															
															
														$foo = fopen(FCPATH . "tmp/webalive/".$this->encryption->decrypt($key->url).".log","a+");
														fwrite($foo, $this->encryption->decrypt($key->url). " Domain was down at. ".$dd->added_date);
															
                                                             $this->db->where("alive_id",$dd->alive_id)->update("alive_system",["error_code" => $getheader["http_code"],"mail_flag" => 1,"update_date" => date('Y-m-d H:i:h')]);
                                                        }else{
                                                            //sendMail($user->email,'ALIVE_SYSTEM_ERROR',["website" => $this->encryption->decrypt($key->url),'user_name' => $user->fname]);
																													
															 
															 $this->db->where("alive_id",$dd->alive_id)->update("alive_system",["error_code" => $getheader["http_code"],"update_date" => date('Y-m-d H:i:h')]);
                                                            continue;
                                                        }
                                                }else{
                                                        $this->db->insert("alive_system",$data);
                                                        //sendMail($user->email,'ALIVE_SYSTEM_ERROR',["website" => $this->encryption->decrypt($key->url),'user_name' => $user->fname]);
                                                }
                                                //print_r($data); exit;
                                        }else{
                                            if($gtproject->num_rows() > 0){
                                                $this->db->where("alive_id",$gtproject->row()->alive_id)->delete("alive_system");
                                            }
                                        }
                                        
                                    }
                                }
                                exit;
                            }
        public function system_on_reboot(){
            $this->db->where("status = 'processing' OR status = 'failed'")->update("backupftp",["cron_status" => 0]);
            $this->db->where("status = 'processing' OR status = 'failed'")->update("backupsql",["cron_status" => 0]);
            $this->db->where("status = 'processing' OR status = 'failed'")->update("restore_ftp",["cron_status" => 0]);
            $this->db->where("status = 'processing' OR status = 'failed'")->update("restore_db",["cron_status" => 0]);
			
        }
        public function deactivate_plans(){
            $NewDate=Date('Y-m-d');
            $all_active_subs  = $this->db->query("select * from subscription_details where expiry_date = '".$NewDate."' AND status = 'active'")->result();
            $ids = [];
           foreach ($all_active_subs as $val) {
                $ids[] = $val->sub_id;
           }
           if (count($ids) != 0) {
                $this->db->where_in("sub_id", $ids);
                $this->db->update("subscription_details",["status" => "deactive"]);
                foreach($all_active_subs as $active_subs) {
                    $plan_ = $this->db->get_where('plans', ['id'=> $active_subs->plan_id ])->row();
                    $get_user =$this->db->get_where("client",array("client_id" => $active_subs->user_id))->row();
                    $plan_icon = (isset($plan_->icon) && !empty($plan_->icon)) ? base_url('uploads/plan/'.$plan_->icon) : '';
                    $email_data = [
                        'user_name' => $get_user->fname." ".$get_user->lname,
                        'expiry_date' => $NewDate,
                        'plan_name' => isset($plan_->name) ? $plan_->name : '',
                        'plan_description' => isset($plan_->description) ? $plan_->description : '',
                        'plan_ftp_space_limit' => isset($plan_->ftp_space_limit) ? $plan_->ftp_space_limit.$plan_->ftp_unit : '',
                        'plan_db_space_limit' => isset($plan_->sql_space_limit) ? $plan_->sql_space_limit.$plan_->db_unit : '',
                        'plan_time_period' => isset($plan_->time_period) ? $plan_->time_period : '',
                        'plan_price_monthly' => isset($plan_->price) ? $plan_->price : '',
                        'plan_icon' => '<img src="'.$plan_icon.'" id="img_home" width="50" height="50">',
                    ];
                    sendMail($get_user->email, 'PLAN_EXPIRED_EMAIL', $email_data);
                    }   
                }
            }
            
            
// new code
            public function delete_project(){
                    $getprojectlist = $this->db->get_where("delete_project_cron",array("status" => "not_deleted"))->result();
                    foreach($getprojectlist as $key) {
                           $cmdtoremove = "rm -r ".$key->folderpath;
                            exec($cmdtoremove);
                            $this->db->where("delete_id",$key->delete_id)->update("delete_project_cron",array("status" => "deleted"));
                    }
            }
            public function delete_ftp(){
                    $getprojectlist = $this->db->get_where("delete_ftp_cron",array("status" => "not_deleted"))->result();
                    foreach($getprojectlist as $key) {
                           $cmdtoremove = "rm -r ".$key->folderpath;
                            exec($cmdtoremove);
                            $this->db->where("delete_id",$key->delete_id)->update("delete_ftp_cron",array("status" => "deleted"));
                    }
            }
            public function delete_db(){
                    $getprojectlist = $this->db->get_where("delete_db_cron",array("status" => "not_deleted"))->result();
                    foreach($getprojectlist as $key) {
                           $cmdtoremove = "rm -r ".$key->folderpath;
                            exec($cmdtoremove);
                            $this->db->where("delete_id",$key->delete_id)->update("delete_db_cron",array("status" => "deleted"));
                    }
            }

            // db restore
            public function run_processing_db_restore() {
                $getbackups = $this->db->query("select * from restore_db where cron_status = 0 AND status IN('processing','failed')")->result();



                foreach($getbackups as $bkp) {
                    $this->db->where("restore_id",$bkp->restore_id)->update("restore_db",["cron_status" => 1]);
                            $cmd = "php " . FCPATH . "index.php cron execute_restore_db_process ".$bkp->restore_id." ".$bkp->ind_id." ";
                            exec($cmd." > /dev/null 2>/dev/null &");
                   }
            }
			
			
            public function run_processing_db_restore_test() {
               
                $getbackups = $this->db->query("select * from restore_db where cron_status = 0 AND status IN('processing','failed')")->result();


                foreach($getbackups as $bkp) {
                    //$this->db->where("restore_id",$bkp->restore_id)->update("restore_db",["cron_status" => 1]);
                            $this->execute_restore_db_process($bkp->restore_id,$bkp->ind_id);
                            //$cmd = "php " . FCPATH . "index.php cron execute_restore_db_process ".$bkp->restore_id." ".$bkp->ind_id." ";
                            //exec($cmd." > /dev/null 2>/dev/null &");
                   }
            }
            public function execute_restore_db_process($restore_id,$index_id = NULL){
				
                    $getbackups = $this->db->query("select * from restore_db where restore_id = ".$restore_id."")->row();
                    $bkpdata = $this->db->query("select bdbs.*,ms.mhostname,ms.mpassword,ms.musername,ms.port_no,ms.folder_path,ms.status as dbstatus,p.folder_name,ms.caption from backupsql bdbs INNER JOIN mysql_server ms on bdbs.db_id =ms.mysql_id INNER JOIN project p ON bdbs.project_id = p.project_id WHERE bdbs.backup_id = " . $getbackups->backup_id . "")->row();
                //      set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                //         throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                //     }, E_WARNING);
                // try {
					
					
					 error_reporting(0);
					$foo = fopen(FCPATH . "restorelogs/".$getbackups->db_name.".log","a+");
					
					
                   
                    $dbhost = $this->encryption->decrypt($bkpdata->mhostname);
                    $dbuser = $this->encryption->decrypt($bkpdata->musername);
                    $dbpass = $this->encryption->decrypt($bkpdata->mpassword);
                    $port_no= $bkpdata->port_no;
                    $dbname = $getbackups->db_name;
                    $conn =new mysqli($dbhost, $dbuser, $dbpass ,$dbname);
                    $cnn = 0;
                    if($conn){
                        $cnn = 1;
                    }else{
                        $conn =new mysqli($dbhost, $dbuser, $dbpass ,$dbname,$port_no);
                        if($conn){
                            $cnn = 1;    
                        }else{
                            $cnn = 0;
                        }
                    }

                    if($cnn){
		$zip_path = $getbackups->file_path.'.zip';
//Unzip files
		$projectPath = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/db_server/".$bkpdata->folder_path."/";
		$unzipFilesPath = $projectPath.'dbrestore';
	
		if (!is_dir($unzipFilesPath)) {
			mkdir($unzipFilesPath);   
			}
		
		$cmd = "unzip -o ".$zip_path." -d ".$unzipFilesPath;				
		shell_exec($cmd);
	

					$restore_files = scandir($unzipFilesPath.'/db/'.$dbname, 0);
					
						$messagelog =[]; 	
					if($restore_files){
					foreach($restore_files as $file) {
						$sqlFile = $unzipFilesPath.'/db/'.$dbname."/$file";
					   if (!is_dir($sqlFile)) {					  
					   
						//print_r($sqlFile); 			
                        
                        $handle = fopen($sqlFile, "r");
                        if($handle) {
                            while (($line = fgets($handle)) !== false) {
                                $line = ltrim(rtrim($line));
                                if (strlen($line) > 1) { // avoid blank lines
                                    $lineIsComment = false;
                                    if (preg_match('/^\/\*/', $line)) {
                                        $multiLineComment = true;
                                        $lineIsComment = true;
                                    }
                                    if ($multiLineComment or preg_match('/^\/\//', $line)) {
                                        $lineIsComment = true;
                                    }
                                    if (!$lineIsComment) {
                                        $sql .= $line;
                                        if (preg_match('/;$/', $line)) {
                                            // execute query
											$data = explode('VALUES(', $sql);
											
											if($data[1]){
												
												$innerData = explode('),(', $data[1]);
												
												if(count($innerData) > 0){
													$inSql = $data[0];
													
													$sqlData = array_chunk($innerData, 50, true);
													
													$innerSql = array(); 
													foreach($sqlData as $item){
													
													$in2Sql = implode('), (', $item);
													
													$in2Sql = ltrim($in2Sql, '(');
														$innerSql[] = $inSql. " VALUES (".rtrim($in2Sql, ');').');';
														
													}
													
												}
												$data  = $innerSql;
												//print_r($data); exit;
												
											}
											
											foreach($data as $sql2){
												
												if(mysqli_query($conn, $sql2)) {
													
                                                if (preg_match('/^CREATE TABLE `([^`]+)`/i', $sql2, $tableName)) {
													
                                                    $messagelog[] = array("date" => date("d-m-Y H:i:s"),"msg" => "Table succesfully created: " . $tableName[1] ,"table_name" => $tableName[1],"status" => "success");
                                                }
                                                
												} else {
													$foo = fopen(FCPATH . "restorelogs/".$getbackups->db_name.".log","a+");
													fwrite($foo, $sql2."<br>".$sqlData);
													
													throw new Exception("ERROR: SQL execution error: " . mysqli_error($this->conn));
												}
												
											}
											
											$sql = '';
                                            
                                        }
										
										
                                    } else if (preg_match('/\*\/$/', $line)) {
                                        $multiLineComment = false;
                                    }
                                }
                            }
                            fclose($handle);
                        }
                       
					   
					
					   }
					}	
					
					
					}
					   
								$cmd = "rm -rf ".$unzipFilesPath.'/*';
								exec($cmd." > /dev/null 2>/dev/null &");
					   
                           $this->db->where("restore_id",$restore_id)->update("restore_db",["status" => "success","tables_data" => json_encode($messagelog)]); 
                       
                        
                    }else{
                        $msg = "<p> Database server credentials is wrong </p>";
                         $this->db->where("restore_id",$getbackups->restore_id);   
                         $this->db->update("restore_db",["error_msg" => $getbackups->error_msg.$msg]);
                    }



                // }catch(\Throwable $e) {
                //          $msg = "<p> ". $e->getMessage()."</p>";
                //          $this->db->where("restore_id",$getbackups->restore_id);   
                //          $this->db->update("restore_db",["error_msg" => $getbackups->error_msg.$msg]);
                // }
				
				exit('completed');
            }   


            public function testmail(){
                 $settings = $this->db->select('name_value')->where_in('slug',['mail_from_name', 'email', 'smtp_host', 'mail_protocol', 'smtp_port', 'smtp_user', 'smtp_pass'] )->get("site_setting")->result();
                 $from_name = $settings[1]->name_value;
                 $from_email = $settings[0]->name_value;
                 $message = 'Backups in der Cloud sind einfach, sicher und kosteneffizient. Anstelle eigener Hardware buchen Sie bei uns den Service und wir kümmern uns um die Sicherungen. Wir kümmern uns um das wichtigste, den Schutz und die Sicherung Ihrer Daten.';
                 $subject = "Testing UTF section";
                    $this->email->set_newline("\r\n");
                    $this->email->from($from_email, $from_name);
                    $this->email->to('kashish@datalogysoftware.com');
                    $this->email->subject($subject);
                    $this->email->message($message);
                    if($this->email->send()){
                        echo "t";
                    }else{
                        echo "f";            
                    }
            }
    }
