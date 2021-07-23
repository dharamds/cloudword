<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_error', 1);
class Cron extends CI_Controller {
	
	private $ftpconnect;
	
    function __construct() {
        parent::__construct();
				$this->load->dbutil();
			   $this->load->helper('url');
			   $this->load->helper('file');
			   //$this->load->helper('download');
			   $this->load->library('zip');
               $this->load->model('Connection');
    }
    //Background process running code
    public function run_processing_projects() {
        $getprojects = $this->db->query("select backup_id from backupftp where  cron_status = 0 AND status IN('processing','failed')")->result();
				
        foreach ($getprojects as $pro) {
             
                if($pro->folder_retrieve_flag == 1){
                    $cmd = "php " . FCPATH . "index.php cron execute_background_requests " . $pro->backup_id;
                    exec($cmd."  > /dev/null 2>/dev/null &");
                }else{
                    //$this->folder_retrieve($pro->backup_id);
					
					
					
                    $cmd = "php " . FCPATH . "index.php cron execute_background_requests " . $pro->backup_id;
                    exec($cmd."  > /dev/null 2>/dev/null &");
					$cmd = "php " . FCPATH . "index.php cron folder_retrieve " . $pro->backup_id;
                    exec($cmd."  > /dev/null 2>/dev/null &");
                }
            
        }
    }
    
	public function update_file_log($baserootpath, $backup_id){
		sleep(15);
		$baserootpath = base64_decode($baserootpath);
			
		$getebackupstatus = $this->db->get_where("backupftp",array("backup_id" => $backup_id))->row()->status;
		//echo $baserootpath; exit;
		
		if($getebackupstatus == 'processing'){
						$cmd = 'du -sb '.$baserootpath;
						$result =  exec($cmd);
						list($total_size, $path) = explode("/", $result);
						$total_size = (int)$total_size;	
						if($total_size > 0){
						$this->db->where("backup_id",$backup_id)->update("backupftp",["total_size" => $total_size]);
						$this->update_file_log(base64_encode($baserootpath), $backup_id);
						}else{
							exit;
						}
		}else{
			exit;
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
					//print_r($getftp); 
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

	print_r($getdb); 
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
        $getftp = $this->db->query("select bftp.*,fs.client_id,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.exclude_dir,fs.root_path,fs.key_filepath,fs.caption,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = " . $backup_id . "");
		
		
	
            if($getftp->num_rows() > 0){
				
                $getftp = $getftp->row();
				
				$project_id = $getftp->project_id;
				$error_logfile = $getftp->error_logfile;
				//$total_size = 265;
				$projectfolder = $this->encryption->decrypt($getftp->folder_name);
				$ftppath = $getftp->folder_path;
				$currtimestamp = time();
				$file_name = "syncftpbackup_" . $currtimestamp . "_" . base64_encode($project_id) . ".zip";
				$baserootpath = FCPATH. "projects/". $projectfolder .'/ftp_server/'.$ftppath."/syncbackup";
				$zipPath = FCPATH. "projects/" . $projectfolder . "/ftp_server/" . $ftppath . "/" . $file_name;
				$syncbackuppath = FCPATH. "projects/". $projectfolder .'/ftp_server/'.$ftppath;
				

                $hostname = $this->encryption->decrypt($getftp->hostname);
                $username = $this->encryption->decrypt($getftp->username);
                $password = $this->encryption->decrypt($getftp->password);
                $password = ($password) ? $password : 'test';
                $port = (int)$this->encryption->decrypt($getftp->port_no);
                $host = ($port == 22) ? 'sftp://'.$hostname : 'ftp://'.$hostname;
                
                $key_filepath =$getftp->key_filepath;
    
                if($this->Connection->check($hostname, $username, $password, $port, $key_filepath))
                    {
    							
				// calculating download file size
						$cmd = 'du -sb '.$baserootpath;
						$result =  exec($cmd);
						list($total_size, $path) = explode("/", $result);
						$total_size = (int)$total_size;				
						
						// Save log data
						$cmd = "php " . FCPATH . "index.php cron update_file_log " . base64_encode($baserootpath) .' '.$backup_id;
						exec($cmd."  > /dev/null 2>/dev/null &");
						
						
				//if($total_size !== (int)$getftp->total_remote_size){
			

			$key_fileData = ($key_filepath) ? '
set sftp:connect-program "ssh -v -a -x -i '.$key_filepath.'"' : '';
			$sftp_connection = ($key_filepath) ? '
set sftp:connect-program "ssh -a -x -T -c arcfour -o Compression=no"' : '';
			$exclude_dir = '';
			if($getftp->exclude_dir){
				$dires = explode(',',$getftp->exclude_dir);
				if(is_array($dires)){
					foreach($dires as $dir){
						$exclude_dir .= ' --exclude '.ltrim($dir,'/').'/';
					}
				}
			}	


			//$exclude_dir = ($getftp->exclude_dir) ? ' --exclude '.ltrim($getftp->exclude_dir,'/').'/' : '';
			$root_path = ($getftp->root_path) ? ' '.$getftp->root_path : ' /';
			
					
				$filetext = '#!/bin/bash
#!/bin/env bash
lftp -u '.$username.','.$password.' '.$host.':'.$port.'<<EOF
set xfer:clobber on'.$sftp_connection.'
set ssl:verify-certificate no
set sftp:auto-confirm yes'.$key_fileData.'
mirror -c --use-pget-n=8 -P 8'.$exclude_dir.$root_path.' '.$baserootpath.'
quit
EOF';
			
			$filePath = FCPATH.'cronjobs/'.$backup_id.'_b_.sh';
			if (write_file($filePath, $filetext, 'w'))
				{
						$this->db->where("backup_id",$backup_id)->update("backupftp",["cron_status" => 1]);
						$cmd = 'bash '.$filePath;
						exec($cmd);
				//unlink($filePath);		
				}
				//exit('ok'); exit;
						// calculating download file size
						$cmd = ' du -sb '.$baserootpath;
						$result =  exec($cmd);
						list($total_size, $path) = explode("/", $result);
						$total_size = (int)$total_size;	
	//}    
			if($getftp->mail_sent == 0){		
				// send email before zip
				if($this->send_ftp_backup_email($getftp, $total_size, 'Backup FTP Report:', 1)){
					
				$this->db->where("backup_id", $getftp->backup_id);
				$this->db->update("backupftp", ["total_size" => $total_size, "mail_sent" => 1]);
				}else{
					
						$this->db->where("backup_id", $getftp->backup_id);
						$this->db->update("backupftp", ["total_size" => $total_size]);						
				}				
			}
				// creating zip file
			$cmd = 'cd '.$syncbackuppath.' && zip -r '.$zipPath.' syncbackup';
			shell_exec($cmd);
			
				
                $fsize = filesize($zipPath);
                $enddate = date("Y-m-d H:i:s");
                $bkpdata = array("timestamp_date" => $currtimestamp, "file_name" => $this->encryption->encrypt($file_name), "enddate" =>$enddate , "error_logfile" => $error_logfile, "total_size" => $total_size, "status" => "success");

			   // send email after zip
					if($getftp->mail_zip_sent == 0){
						if($this->send_ftp_backup_email($getftp, $fsize, 'Backup FTP with zip creation:', 0)){
							$bkpdata['mail_zip_sent'] = 1;
							
						//Update ftp quota
						$used_ftp_storage = (int)$this->db->get_where("client_storage",array("user_id" => $getftp->client_id))->row()->used_ftp_storage + $fsize;
						$this->db->where("user_id", $getftp->client_id);
						$this->db->update("client_storage", ["used_ftp_storage" => $used_ftp_storage ]);
							
                        }
					}
					
					$this->db->where("backup_id", $getftp->backup_id);
					$this->db->update("backupftp", $bkpdata);
				// delete unzip files	
				exec("rm -rf ".$baserootpath."/* > /dev/null 2>/dev/null &");
                exit('200');   
        }else{
            $ff = fopen(APPPATH . "logs/" . $getftp->error_logfile, "a+");
            $caption = $this->encryption->decrypt($getftp->caption);
            $msg = '<p>Unable to connect Host:-<b>'.$hostname.'</b> for ('.$caption.') while taking backup on '.date("d/m/Y H:i:s").'</p>';
                    fwrite($ff, $msg);
                    $up = array();
                    if($getftp->error_mail_flag == 0){
                        $getemail = $this->db->get_where("client",array("client_id" => $getftp->client_id))->row();
                        $error_data = array("error_msg" => $msg);
                        //sendMail($getemail->email, 'ERROR_DATA', $error_data);  
                        sendMail('dharamds1104@gmail.com', 'ERROR_DATA', $error_data); 
                        $up["error_mail_flag"] = 1;
                    }
                    $up["cron_status"] = 0;
                    $this->db->where("backup_id", $backup_id);
                    $this->db->update("backupftp", $up);
                    exit('400');
        }   
    }	
    }

    
		
    public function folder_retrieve($backup_id){
            $getftp = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.key_filepath,fs.root_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$backup_id . "")->row();
            
			$hostname = $this->encryption->decrypt($getftp->hostname);
            $username = $this->encryption->decrypt($getftp->username);
            $password = $this->encryption->decrypt($getftp->password);
            $port 	  = $this->encryption->decrypt($getftp->port_no);
			$key_filepath = $getftp->key_filepath;
			$root_path = ($getftp->root_path) ? $getftp->root_path : '/';
            $config['passive'] = TRUE;
            $config['debug'] = FALSE;
            $rtfolderremote = $getftp->remoteroot_folder;
            $rootfolder = $getftp->localroot_folder;
            //$ff = fopen(APPPATH . "logs/" . $getftp->error_logfile, "a+");
			
			
			
			
				if($port == 22){
				if(isset($key_filepath)){
					$cmd  = 'lftp -u '.$username.','.$password.' -e "set ssl:verify-certificate no; set sftp:auto-confirm yes; set sftp:connect-program "ssh -a -x -i '.$key_filepath.'"; du -hb '.$root_path.'; quit;" sftp://'.$hostname.':'.$port;
					
				}else{
				$cmd  = 'lftp -u '.$username.','.$password.' -e "set ssl:verify-certificate no; set sftp:auto-confirm yes; du -hb '.$root_path.'; quit;" sftp://'.$hostname.':'.$port;	
				}	
				
				}else{
					$cmd  = 'lftp -u '.$username.','.$password.' -e "set ssl:verify-certificate no; set sftp:auto-confirm yes; du -hb '.$root_path.'; quit;" ftp://'.$hostname.':'.$port;
				}
				
				$cmdData = exec($cmd);
				
				if($cmdData){
					
					list($total_size, $path) = explode("/", $cmdData);
						$total_size = (int)$total_size;	
					$foldersdata = array();
                        $dataaa = array(
                                        "foldersdata" => json_encode($foldersdata),
                                        "folder_retrieve_flag" => 1,
                                        "total_remote_size" => $total_size,
                                    );
                        $this->db->where("backup_id",$backup_id);
                        $this->db->update("backupftp",$dataaa);
                    
                }else{
                    $msg = '<p>Unable to connect on '.date("d/m/Y H:i:s").'</p>';
                    fwrite($ff, $msg);
                    $up = array();
                    if($getftp->error_mail_flag == 0){
                        $getemail = $this->db->get_where("client",array("client_id" => $getftp->client_id))->row();
                        $error_data = array("error_msg" => $msg);
                        //sendMail($getemail->email, 'ERROR_DATA', $error_data);  
                        //sendMail('dharamendra@datalogysftware.com', 'ERROR_DATA', $error_data); 
                        $up["error_mail_flag"] = 1;
                    }
                    $up["cron_status"] = 0;
                    $this->db->where("backup_id", $backup_id);
                    $this->db->update("backupftp", $up);
                }
			exit('200');
    }
	
	
    
	

    
	
	public function send_ftp_backup_email($projectdata, $fsize, $subject, $type = 0){
			  
                 $getproject_setting =  $this->db->get_where("project_setting",array("client_id" => $projectdata->client_id))->row_array();
				 
                if($getproject_setting){
						$getclient =  $this->db->get_where("client",array("client_id" => $projectdata->client_id))->row();
                        $first_date   = new DateTime($projectdata->startdate);
                        $second_date  = new DateTime($enddate);
                        $interval = $first_date->diff($second_date);
                        $nt = $interval->format("%H ".$this->lang->line('hours')." %I ".$this->lang->line('minutes')." ".$this->lang->line('and')." %S ".$this->lang->line('seconds')."");
						if($type == 1){
						$email_head = $this->lang->line('backup_FTP_head').$this->encryption->decrypt($projectdata->project_name)." - ".$this->encryption->decrypt($projectdata->caption);
						$wzip_text = $this->lang->line('wzip_text')."<br>";
						$Processed = $this->lang->line("processed")." (FTP) :";
						
						}else{
						$email_head = $this->lang->line('backup_FTP_head').$this->encryption->decrypt($projectdata->project_name)." - ".$this->encryption->decrypt($projectdata->caption);
						$Processed = $this->lang->line("backup")." (Zip) :";
						}
						
						
                        $content = array(
                                            "backup_email" => $this->lang->line("backup_FTP_report"),
                                            "backup_email_msg" => $email_head,
                                            "startdate" => displayDate($projectdata->startdate,true),
                                            "Elapsed" => "Elapsed:",
                                            "elapsed_duration" => $nt,
                                            "processed_ftp" => $Processed,
                                            "processed_ftp_size" => $this->general->convert_size($fsize),
                                            "display_style_error" => "none",
                                            "display_style_success" => "show",
                                            "backup_detail_msg" =>   $wzip_text." <br>". $this->lang->line("backup_detail_msg"),
                                            "thank_you" => $this->lang->line('thank_you'),
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
                        $subject = $this->lang->line("backup_FTP_report");
                        $getfilecontent = "ftp_backup_template.html";
                        $stsetting    = $this->db->query("select name_value from site_setting ss where ss.setting_id IN(7,8)")->result(); 
                        $content["down_content"] = $this->lang->line("down_content").$stsetting[0]->name_value;
                        $msg   =$this->replace_content($content,$getfilecontent); 
                        $this->email->set_newline("\r\n");
                        $this->email->from($stsetting[0]->name_value, $stsetting[1]->name_value);
                        if($getproject_setting["send_to_mails"] != ""){
                            $this->email->to('dharamds1104@gmail.com');
                            //$this->email->to($getclient->email.",".$getproject_setting["send_to_mails"]);
                        }else{
                            $this->email->to('dharamds1104@gmail.com');
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
										
																			
                                       // print_r($dbs->mysql_id); exit; 
						  if($this->db->insert("backupsql",$dbbdata)){
							$this->db->where("mysql_id",$dbs->mysql_id)->update("mysql_server",["scheduling_add_flag" => 1]);  
						  }
                          
                        }
                    }


                    }
                    exit('200');
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
                    $file_name = $db->file_name. ".zip";
					
                    $path = "./projects/" . $this->encryption->decrypt($getdbproject->folder_name) . "/db_server/" . $getdbservr->folder_path;
					
					
                    if ($db->status == "failed" || $db->status == "processing") {
						
                        $returndata = $this->backup_tables($dbhost, $dbuser, $dbpass, $dbname, $tables, $path, $file_name, $backup_id, $getdbproject->client_id, $errorlog, $indexid, $folderdata);
                       
					   //print_r($returndata);
					   
					   if ($returndata["status"] == "success") {
						   
                            $folderdata[$indexid]->status = "success";
                            $folderdata[$indexid]->logs = $returndata["error_log"];
							 						
                           
							
                            $getba = $this->db->get_where('backupsql', array("backup_id" => $backup_id))->row();
                            $cmpdb = $getba->completed_database + 1;
                            							
							$complsize = filesize($path.'/dbcheck/'.$file_name);
							$folderdata[$indexid]->size = $complsize;
								//Update db quota
								$used_db_storage = (int)$this->db->get_where("client_storage",array("user_id" => $getbackups->client_id))->row()->used_db_storage + $complsize;
								$this->db->where("user_id", $getbackups->client_id);
								$this->db->update("client_storage", ["used_db_storage" => $used_db_storage ]);
						   
						   
							
      
							if($indexid == ($coundDB - 1))
							{
								$getproject_setting =  $this->db->get_where("project_setting",array("client_id" => $getbackups->client_id))->row_array();
								
								$last_total_size = $this->db->get_where('backupsql', array("backup_id" => $backup_id))->row()->total_size;
											$total_size = $last_total_size + $complsize;
								
							$this->db->where("backup_id", $backup_id);
							$this->db->update("backupsql", ["foldersdata" => json_encode($folderdata), "completed_database" => $cmpdb,"status" =>"success", "enddate" => date("Y-m-d H:i:s"), 'total_size' => $total_size]);
									
                            if(sizeof($getproject_setting) > 0){
                                            $getclient =  $this->db->get_where("client",array("client_id" => $getbackups->client_id))->row();
                                            $first_date   = new DateTime($getbackups->startdate);
                                            $second_date  = new DateTime($getba->enddate);
                                            $interval = $first_date->diff($second_date);
                                            $nt = $interval->format("%H ".$this->lang->line('hours')." %I ".$this->lang->line('minutes')." ".$this->lang->line('and')." %S ".$this->lang->line('seconds')."");
                                           
                                            $content = array(
                                                                "backup_email" => $this->lang->line('backup_DB_email'),
                                                                "backup_email_msg" => $this->lang->line('backup_email_msg').$this->encryption->decrypt($getdbproject->project_name)." - ".$this->encryption->decrypt($getdbservr->caption),
                                                                "startdate" => displayDate($getbackups->startdate,true),
                                                                "Elapsed" => "Elapsed:",
                                                                "elapsed_duration" => $nt,
                                                                "sql" => "SQL:",
                                                                "total_sql_table" => $this->lang->line('total_SQL_tables_downloaded').$getba->total_database."(".$this->general->convert_size($complsize).")",
                                                                "display_style_error" => "none",
                                                                "display_style_success" => "show",
                                                                "backup_detail_msg" =>   $this->lang->line('backup_detail_msg'),
                                                                "thank_you" =>  $this->lang->line('thank_you'),
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
                                            $subject = $this->lang->line('backup_DB_email');
                                            $getfilecontent = "db_backup_template.html";
                                            
                                            $stsetting    = $this->db->query("select name_value from site_setting ss where ss.setting_id IN(7,8)")->result(); 
                                            $content["down_content"] = $this->lang->line('down_content').$stsetting[0]->name_value;
                                            $msg   =$this->replace_content($content,$getfilecontent); 
                                            //$CI->load->library('email');
                                            $this->email->set_newline("\r\n");
                                            $this->email->from($stsetting[0]->name_value, $stsetting[1]->name_value);

                                             if($getproject_setting["send_to_mails"] != ""){
                                                    $this->email->to($getclient->email.",".$getproject_setting["send_to_mails"]);
                                                }else{
                                                    //$this->email->to($getclient->email);
													$this->email->to("dharamds1104@gmail.com");
                                                }
                                            
                                            $this->email->subject($subject);
                                            $this->email->message($msg);
											$last_total_size = $this->db->get_where('backupsql', array("backup_id" => $backup_id))->row()->total_size;
											$total_size = $last_total_size + $complsize;
											
                                            if($this->email->send()){
                                                $this->db->where("backup_id", $getbackups->backup_id);
                                                $this->db->update("backupsql", ["mail_sent" => 1]);
                                            }else{
                                                 $this->db->where("backup_id", $getbackups->backup_id);
                                                 $this->db->update("backupsql", ["mail_sent" => 0]);           
                                            }
                                        }
                                }
							}else{
								$last_total_size = $this->db->get_where('backupsql', array("backup_id" => $backup_id))->row()->total_size;
								$total_size = $last_total_size + $complsize;
								 $this->db->where("backup_id", $backup_id);
								 $this->db->update("backupsql", ["foldersdata" => json_encode($folderdata), "completed_database" => $cmpdb,"status" =>"processing",'total_size' => $total_size]);
							}
                        } else {
                            $folderdata[$indexid]->status = "processing";
                            $folderdata[$indexid]->logs = $returndata["error_log"];
                            $this->db->where("backup_id", $backup_id);
                            $this->db->update("backupsql", ["foldersdata" => json_encode($folderdata) ]);
							//$this->db->reset();
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
		
		
        public function backup_tables($host, $user, $pass, $dbname, $tables = '*', $path, $file_name, $backup_id, $user_id, $errorlog, $indexid, $folderdata) {
			
            //$getuser = $this->db->get_where("client", array("client_id" => $user_id))->row();
			// make connection to DB
			 set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                        throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                    }, E_WARNING);
                    try {
					$remote['hostname'] = $host;
					$remote['username'] = $user;
					$remote['password'] = $pass;
					$remote['database'] = $dbname;
					//$remote['port'] 	= $port_no;
					$remote['dbdriver'] = "mysqli";
					$remote['dbprefix'] = "";
					$remote['pconnect'] = FALSE;
					$remote['db_debug'] = TRUE;
					$remote['cache_on'] = FALSE;
					$remote['cachedir'] = "";
					$remote['char_set'] = "utf8";
					$remote['dbcollat'] = "utf8_general_ci";

					$conn = $this->load->database($remote, TRUE);
					$connected = $conn->initialize();
					$tables = $conn->query('SHOW TABLES')->result_array();
					
					
					
				$myutil = $this->load->dbutil($conn, TRUE);

				/*$dbpath = $path.'/db/'.$dbname;
				if (!is_dir($dbpath)) {
					mkdir($dbpath);   
				}
				*/
			$sqlFilePath = FCPATH.$path.'/dbcheck/'.$file_name;	
			$completedTable = 1;
			$totalTables =count($tables);
				foreach ($tables as $key => $table) {
					$array_data = array_values($table);

					$prefs = array(
					'tables'        => array($array_data[0]),   // Array of tables to backup.
					'ignore'        => array(),                     // List of tables to omit from the backup
					'format'        => 'zip',                       // gzip, zip, txt
					'filename'      => $dbname.'/'.$array_data[0],              // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
					'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
					'newline'       => "\n"                         // Newline character used in backup file
			);
			

	// Backup your entire database and assign it to a variable
		$backup = $myutil->backup($prefs);
		$this->load->helper('file');
    // Load the file helper and write the file to your server
    $result = write_file($sqlFilePath, $backup);
	
	$dbmessage[] = array("status" => "success", 'table_name' => $array_data[0]);
	
							$folderdata[$indexid]->status = "processing";
                            $folderdata[$indexid]->tableData = $dbmessage;
                            $this->db->update("backupsql",["completed_table" => $completedTable, "total_table" => $totalTables, "foldersdata" => json_encode($folderdata) ]);
                            $this->db->where("backup_id", $backup_id);
							
	$completedTable++;
		}

		//print_r($dbmessage); exit;	
		
        			
				$cmd = "rm -rf ".$sqlFolderPath.'/db/'.$dbname;
								exec($cmd." > /dev/null 2>/dev/null &");		
						
                if (file_exists($sqlFilePath)) {
                    return array("status" => "success", "error_log" => $errorlog, 'db_name' => $dbname, 'file_size' => $zipfileSize);
                } else {
                    return array("status" => "failed", "error_log" => $errorlog);
                }
            }
            catch(\Throwable $e) {
                $errorlog[] = $e->getMessage();
                return array("status" => "failed", "error_log" => $errorlog);
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

         $this->load->library('ftp');
		 
		 
		 
            /*$config['hostname'] = 'datalogysoftware.com';
            $config['username'] = 'csw1@datalogysoftware.com';
            $config['password'] = 'dharam444';
			*/
			$config['hostname'] = '162.253.126.178';
            $config['username'] = 'dharam';
            $config['password'] = 'jP3LW0%F';
			
			
			
			
			
            $config['port']     = 21;
            //$config['passive']  = FALSE;
            $config['debug']    = TRUE;
			
			$this->ftp->connect($config);
			
			
			$list = $this->ftp->list_files('/');

print_r($list);

$this->ftp->close();

exit;

			
            if ($this->ftp->connect($config)) {
                set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                    throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                }, E_WARNING);
                try {
                    echo "<pre>";
                        print_r($this->ftp->raw_files("/"));
						$this->ftp->close();
                }
                catch(\Throwable $e) {
                    $errorlog[] = $e->getMessage();
                     print_r(array("status" => "failed", "error_log" => $errorlog));
                }
            }else{
				//$errorlog[] = $this->ftp->getMessage();
                     print_r(array("status" => "failed", "error_log" => $errorlog));
			}
        
    }

    //run processing ftp restore process
    public function run_processing_ftp_restore() {
            $getbackups = $this->db->query("select restore_id, extract_flag from restore_ftp where cron_status = 0 AND status IN('processing','failed')")->result();
			//print_r($getbackups); exit;
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
		
            $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.key_filepath,fs.caption,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name, backupftp.total_size as backup_total_size from restore_ftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id inner join backupftp on backupftp.backup_id = bftp.backup_id WHERE bftp.restore_id = ".$restore_id."")->row();
			
            $hostname = $this->encryption->decrypt($bkpdata->hostname);
            $username = $this->encryption->decrypt($bkpdata->username);
            $password = $this->encryption->decrypt($bkpdata->password);
            $port 	  = $this->encryption->decrypt($bkpdata->port_no);
            $caption 	  = $this->encryption->decrypt($bkpdata->caption);
			$key_filepath = $getftp->key_filepath;

            if($this->Connection->check($hostname, $username, $password, $port, $key_filepath))
            {
            
            shell_exec("unzip -o ".$bkpdata->zippath." -d ".$bkpdata->baserootpath);	
           
			$filepath = "./projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/".$bkpdata->restore_folder."/syncbackup/";
			
            if(is_dir($filepath)){                      
				$this->db->where("restore_id",$restore_id)->update("restore_ftp",["extract_flag " => 1, "total_remote_size" => $bkpdata->backup_total_size]);
             return TRUE;
            
			}else{
				$this->db->where("restore_id",$restore_id)->update("restore_ftp",["extract_flag " => 0, "foldersdata" => '',"cron_status" => 0]);
			 return FALSE;							
			}
        }else{
            $ff = fopen(APPPATH . "logs/" . $hostname.'.log', "a+");
            $msg = '<p>Unable to connect Host:-<b>'.$hostname.'</b> for ('.$caption.') while restore on '.date("d/m/Y H:i:s").'</p>';
                    fwrite($ff, $msg);
                    $up = array();
                    if($bkpdata->error_mail_flag == 0){
                        $getemail = $this->db->get_where("client",array("client_id" => $bkpdata->client_id))->row();
                        $error_data = array("error_msg" => $msg);
                        //sendMail($getemail->email, 'ERROR_DATA', $error_data);  
                        sendMail('dharamds1104@gmail.com', 'ERROR_DATA', $error_data); 
                        $up["error_mail_flag"] = 1;
                    }
                    $up["cron_status"] = 0;
                    $this->db->where("restore_id", $restore_id);
                    $this->db->update("restore_ftp", $up);
                    exit('400');
        }			
            
    }   

public function update_restore_file_log($filepath, $restore_id){
		sleep(15);
		$filepath = base64_decode($filepath);
			
		$getRestoredata = $this->db->get_where("restore_ftp",array("restore_id" => $restore_id))->row();
		//echo $baserootpath; exit;
		
		if($getRestoredata->status == 'processing'){
						$cmd = 'du -sb '.$filepath;
						$result =  exec($cmd);
						list($total_size, $path) = explode("/", $result);
						$total_size = (int)$total_size;	
						if($total_size > 0){
						$total_size = $getRestoredata->total_remote_size - $total_size;		
						$this->db->where("restore_id",$restore_id)->update("restore_ftp",["total_size" => $total_size]);
						$this->update_restore_file_log(base64_encode($filepath), $restore_id);
						}else{
							exit;
						}
		}else{
			exit;
		}				
	}
	
    public function execute_restore_ftp_process($restore_id){
        $restoredata = $this->db->get_where("restore_ftp",array("restore_id" => $restore_id))->row();
		if($restoredata->extract_flag == 1){
		$bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.root_path,fs.key_filepath,fs.caption,fs.status as fstatus,p.folder_name,p.project_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = ".$restoredata->backup_id." ")->row();
		
		$dirpath = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/";
		$filepath = $dirpath.$restoredata->restore_folder."/syncbackup/";
		
		$this->db->where("restore_id",$restore_id)->update("restore_ftp",["total_remote_size" => $bkpdata->total_remote_size]);
		
		// calculating download file size
		
			
						/*$cmd = 'du -sb '.$filepath;
						$result =  exec($cmd);
						list($total_size, $path) = explode("/", $result);
						$total_size = (int)$total_size;				
		*/
                    

				
					
					// Save log data
						$cmd = "php " . FCPATH . "index.php cron update_restore_file_log " . base64_encode($filepath) .' '.$restore_id;
						exec($cmd."  > /dev/null 2>/dev/null &");	
					
					
				if($restoredata->restore_type == 'local'){
			$hostname = $this->encryption->decrypt($bkpdata->hostname);
            $username = $this->encryption->decrypt($bkpdata->username);
            $password = $this->encryption->decrypt($bkpdata->password);
			$password = ($password) ? $password :'dummy';
            $port = (int)$this->encryption->decrypt($bkpdata->port_no);
			$host = ($port == 22) ? 'sftp://'.$hostname : 'ftp://'.$hostname;
			
			
			$key_filepath =$bkpdata->key_filepath;
			$root_path = ($bkpdata->root_path) ? ' '.$bkpdata->root_path : ' /';
			}else{
			$remote_data = 	json_decode($restoredata->remote_data);
				
			$hostname = base64_decode($remote_data->hostname);
            $username = base64_decode($remote_data->username);
            $password = base64_decode($remote_data->password);
			$password = ($password) ? $password :'dummy';
            $port 	  = $remote_data->port_no;
			$host     = ($port == 22) ? 'sftp://'.$hostname : 'ftp://'.$hostname;	
			$root_path = ' '.$restoredata->remote_path;
			$key_filepath =$remote_data->public_key;
			}
			
			
			$key_fileData = ($key_filepath) ? '
set sftp:connect-program "ssh -v -a -x -i '.$key_filepath.'"' : '';
			$sftp_connection = ($key_filepath) ? '
set sftp:connect-program "ssh -a -x -T -c arcfour -o Compression=no"' : '';
						
			
				$filetext = '#!/bin/bash
#!/bin/env bash
lftp -u '.$username.','.$password.' '.$host.':'.$port.'<<EOF
set xfer:clobber on
set sftp:auto-confirm yes
set xfer:clobber on'.$sftp_connection.'
set ssl:verify-certificate no
set sftp:auto-confirm yes'.$key_fileData.'
mirror -R -c --Remove-source-files --Remove-source-dirs '.$filepath.$root_path.'
EOF';
            $startDate = date("Y-m-d H:i:s");
			$filePath = FCPATH.'cronjobs/'.$restore_id.'_r_.sh';
			if (write_file($filePath, $filetext, 'w'))
				{
						//$this->db->where("backup_id",$backup_id)->update("backupftp",["cron_status" => 1]);
						$cmd = 'bash '.$filePath;
						shell_exec($cmd);
               // unlink($filePath);                
                                
                                $enddate = date("Y-m-d H:i:s");
                               
                                $getproject_setting =  $this->db->get_where("project_setting",array("client_id" => $bkpdata->client_id))->row_array();
                                if(sizeof($getproject_setting) > 0){
                                            $getclient =  $this->db->get_where("client",array("client_id" => $bkpdata->client_id))->row();
                                            $first_date   = new DateTime($startDate);
                                            $second_date  = new DateTime($enddate);
                                            $interval = $first_date->diff($second_date);
                                            $nt = $interval->format("%H ".$this->lang->line('hours')." %I ".$this->lang->line('minutes')." ".$this->lang->line('and')." %S ".$this->lang->line('seconds')."");
                                            $complsize = 0;
                                            $fileloc = filesize(FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/".$this->encryption->decrypt($bkpdata->file_name));
                                            $content = array(
                                                                "backup_email" => $this->lang->line('restore_FTP_email'),
                                                                "backup_email_msg" => $this->lang->line('restore_FTP_email_msg').$this->encryption->decrypt($bkpdata->project_name)." - ".$this->encryption->decrypt($bkpdata->caption),
                                                                "startdate" => displayDate($bkpdata->added_date,true),
                                                                "Elapsed" => "Elapsed:",
                                                                "elapsed_duration" => $nt,
                                                                "processed_ftp" => "Backup (Zip)",
                                                                "processed_ftp_size" => $this->general->convert_size($fileloc),
                                                                "display_style_error" => "none",
                                                                "display_style_success" => "show",
                                                                "backup_detail_msg" => $this->lang->line('backup_detail_msg'),
                                                                "thank_you" => $this->lang->line('thank_you'),
                                                                "cpright" => $this->lang->line("cpright"),
                                                                "website_url" => base_url(),
                                                                "logo_url"  => base_url("public/public/front/img/frontend-logo.png"),
                                                                "date" => "Date:",
                                                                "check_image" =>  base_url("public/public/assets/img/check.png"),
                                                            );
                                        $geterrors    = @file_get_contents(FCPATH."restorelogs/".$restoredata->error_logfile); 
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
                                            $subject = $this->lang->line('restore_FTP_email');
                                            $getfilecontent = "ftp_restore_template.html";
                                            
                                            $stsetting    = $this->db->query("select name_value from site_setting ss where ss.setting_id IN(7,8)")->result(); 
                                            $content["down_content"] = $this->lang->line('down_content').$stsetting[0]->name_value;
                                            $msg   =$this->replace_content($content,$getfilecontent); 
                                            $this->email->set_newline("\r\n");
                                            $this->email->from($stsetting[0]->name_value, $stsetting[1]->name_value);
											//$email = $getclient->email;
                                            $email = "dharamds1104@gmail.com";
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
							// delete all unzip files
                              $cmd = "rm -rf ".$dirpath.'*';
								exec($cmd." > /dev/null 2>/dev/null &");
								
                             $this->db->where("restore_id",$restore_id)->update("restore_ftp",["foldersdata" => '',"completed_files_folders" => 0, "status" => 'success',"enddate" => $enddate]);
								
						}
					}
		exit('200');        
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
															sendMail('dharamds1104@gmail.com','ALIVE_SYSTEM_ERROR',["website" => $this->encryption->decrypt($key->url),'user_name' => $user->fname]);
															
															
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
							$this->db->where("restore_id",$bkp->restore_id)->update("restore_db",["cron_status" => 1]);
                            $this->execute_restore_db_process($bkp->restore_id,$bkp->ind_id);
                            //$cmd = "php " . FCPATH . "index.php cron execute_restore_db_process ".$bkp->restore_id." ".$bkp->ind_id." ";
                            //exec($cmd." > /dev/null 2>/dev/null &");
                   }
            }
			
			function in_multiarray($elem, $array,$field)
				{
					$top = sizeof($array) - 1;
					$bottom = 0;
					while($bottom <= $top)
					{
						if($array[$bottom][$field] == $elem)
							return true;
						else 
							if(is_array($array[$bottom][$field]))
								if(in_multiarray($elem, ($array[$bottom][$field])))
									return true;

						$bottom++;
					}        
					return false;
				}
			
            public function execute_restore_db_process($restore_id,$index_id = NULL){
				
                    $getbackups = $this->db->query("select * from restore_db where restore_id = ".$restore_id."")->row();
                    $bkpdata = $this->db->query("select bdbs.*,ms.mhostname,ms.mpassword,ms.musername,ms.port_no,ms.folder_path,ms.status as dbstatus,p.folder_name,ms.caption,p.project_name from backupsql bdbs INNER JOIN mysql_server ms on bdbs.db_id =ms.mysql_id INNER JOIN project p ON bdbs.project_id = p.project_id WHERE bdbs.backup_id = " . $getbackups->backup_id . "")->row();
                
				
				set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                         throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
                     }, E_WARNING);
					
                 //try {
					
					
					 error_reporting(0);
					$foo = fopen(FCPATH . "restorelogs/".$getbackups->db_name.".log","a+");
	;
                   
                    $dbhost = $this->encryption->decrypt($bkpdata->mhostname);
                    $dbuser = $this->encryption->decrypt($bkpdata->musername);
                    $dbpass = $this->encryption->decrypt($bkpdata->mpassword);
                    $port_no= $bkpdata->port_no;
                    $dbname = $getbackups->db_name;
					
					
					$remote['hostname'] = $dbhost;
					$remote['username'] = $dbuser;
					$remote['password'] = $dbpass;
					$remote['database'] = $dbname;
					$remote['port'] 	= $port_no;
					$remote['dbdriver'] = "mysqli";
					$remote['dbprefix'] = "";
					$remote['pconnect'] = FALSE;
					$remote['db_debug'] = TRUE;
					$remote['cache_on'] = FALSE;
					$remote['cachedir'] = "";
					$remote['char_set'] = "utf8";
					$remote['dbcollat'] = "utf8_general_ci";

					$conn = $this->load->database($remote, TRUE);
					$connected = $conn->initialize();
					
						
        if($connected){
		$zip_path = $getbackups->file_path.'.zip';
			//Unzip files
		$projectPath = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/db_server/".$bkpdata->folder_path."/";
		$unzipFilesPath = $projectPath.'dbrestore';
	
	

		if (!is_dir($unzipFilesPath)) {
			mkdir($unzipFilesPath);   
			}
			
			
		if($getbackups->unzip_status == 0){
		$cmd = "unzip -o ".$zip_path." -d ".$unzipFilesPath;				
		if(shell_exec($cmd)){
			$this->db->where("restore_id",$restore_id)->update("restore_db",["unzip_status" => 1]); 
		}
		
		}
		
			
		
					$restore_files = scandir($unzipFilesPath.'/'.$dbname, 0);
				
						$messagelog =[]; 	
					if($restore_files){
					$tablesCount = count($restore_files) - 2;
					if($getbackups->tables_data){
					$restoresTables = json_decode($getbackups->tables_data, true);	
					 array_pop($restoresTables);
					 
					 $messagelog = $restoresTables;
					}



					$currentTablesCount = 0;
					foreach($restore_files as $file) {
						
						
						//if($file == 'idp_commentmeta.sql'){
						$sqlFile = $unzipFilesPath.'/'.$dbname."/$file";
						//echo $sqlFile; 
						
					   if (!is_dir($sqlFile)) {	
					   
					   $currentTablesCount++;
						
						
						$TableFileName = pathinfo($file, PATHINFO_FILENAME);
						if($restoresTables){
							$check_restore_table = $this->in_multiarray($TableFileName, $restoresTables, 'table_name'); 
						}else{
							$check_restore_table = false;
						}
						

						
						 if (!$check_restore_table) {	
						 
                        $handle = fopen($sqlFile, "r");
						$LineCount = 0;
						$innerSql = '';
						$set_ins = true; 
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
									
									if (trim($line) == '' || preg_match('/^\s*(#|--\s)/sUi', $line)) {
										$lineIsComment = true;
									}
									
									
									
                                    if (!$lineIsComment) {
                                        $sql .= $line;
																			
                                        if (preg_match('/;$/', $line)) {
                                            // execute query
											$data = explode('VALUES(', $sql);
											
											
											if (strpos($sql, 'DROP') !== false || strpos($sql, 'CREATE') !== false ) {
											
													$conn->trans_start();
													$runquery = $conn->query("SET FOREIGN_KEY_CHECKS=0; ");
													$runquery = $conn->query("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';");
													$runquery = $conn->query($sql);
													$conn->trans_complete(); 
											
											if (preg_match('/^CREATE TABLE `([^`]+)`/i', $sql, $tableName)) {
													//$last_completed_table = ($last_completed_table) ? $last_completed_table :$tableName[1]; 
                                                    $messagelog[] = array("date" => date("d-m-Y H:i:s"),"msg" => "Table succesfully created: " . $tableName[1] ,"table_name" => $tableName[1],"status" => "success");
													$restoresTables = $messagelog;	
													$this->db->where("restore_id",$restore_id)->update("restore_db",["status" => "processing", "total_table" => $tablesCount,  "completed_table" => $currentTablesCount, "tables_data" => json_encode($messagelog)]); 

											   }
												
											if(!$runquery){
													$foo = fopen(FCPATH . "restorelogs/".$getbackups->db_name.".log","a+");
													fwrite($foo, $sql);	
												}
											
											//$innerData[] = $sql;
											
											}else{
											

											if($LineCount < 50){
												
											list($insql, $innerValue) = explode('VALUES', $sql);
											
											if($set_ins == true){
												$innerSql .= $insql.' VALUES '.rtrim($innerValue, ';');
												$set_ins = false;
											}else{
												$innerSql .= ', '.rtrim($innerValue, ';');
											}
											
											}else{
												
												list($insql, $innerValue) = explode('VALUES', $sql);
											
											if($set_ins == true){
												$innerSql .= $insql.' VALUES '.rtrim($innerValue, ';');
												$set_ins = false;
											}else{
												$innerSql .= ', '.rtrim($innerValue, ';');
											}
												
												
												if($innerSql){
													$conn->trans_start();
													$runquery = $conn->query("SET FOREIGN_KEY_CHECKS=0; ");
													$runquery = $conn->query("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';");
													$runquery = $conn->query($innerSql);
													$conn->trans_complete();
												}
												if(!$runquery){
													$foo = fopen(FCPATH . "restorelogs/".$getbackups->db_name.".log","a+");
													fwrite($foo, $sql);	
												}
											
												
												
												//$innerData[] = $innerSql;
												
												
												$innerSql = '';
												$LineCount = 0;
												$set_ins = true;
											}

											
												
											}
											 
											
											
											$sql = '';
                                            $LineCount++;
                                        }
										
										
                                    } else if (preg_match('/\*\/$/', $line)) {
                                        $multiLineComment = false;
                                    }
                                }
								
								
                            }
											// reamining records
												if($innerSql){
													$conn->trans_start();
													$runquery = $conn->query("SET FOREIGN_KEY_CHECKS=0; ");
													$runquery = $conn->query("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';");
													$runquery = $conn->query($innerSql);
													$conn->trans_complete();
												}
												if(!$runquery){
													$foo = fopen(FCPATH . "restorelogs/".$getbackups->db_name.".log","a+");
													fwrite($foo, $sql);	
												}
							
							
                            fclose($handle);
                        }
                       
					   }
					
					   }
						
					
				//}
			}
		}
					   //exit;
					  
					   // sending email for DB restore
								$enddate = date("Y-m-d H:i:s");
                               
                                $getproject_setting =  $this->db->get_where("project_setting",array("client_id" => $bkpdata->client_id))->row_array();
								
								//print_r($bkpdata); exit;
								
                                if(sizeof($getproject_setting) > 0){
								
									
                                            $getclient =  $this->db->get_where("client",array("client_id" => $bkpdata->client_id))->row();
                                            $first_date   = new DateTime($getbackups->startdate);
                                            $second_date  = new DateTime($enddate);
                                            $interval = $first_date->diff($second_date);
                                            $nt = $interval->format("%H ".$this->lang->line('hours')." %I ".$this->lang->line('minutes')." ".$this->lang->line('and')." %S ".$this->lang->line('seconds')."");
                                            $complsize = 0;
                                           
                                            $content = array(
                                                                "backup_email" => $this->lang->line("restore_DB_email"),
                                                                "backup_email_msg" => $this->lang->line("restore_DB_email_msg").$this->encryption->decrypt($bkpdata->project_name)." - ".$this->encryption->decrypt($bkpdata->caption),
                                                                "startdate" => displayDate($bkpdata->startdate,true),
                                                                "Elapsed" => "Elapsed:",
                                                                "elapsed_duration" => $nt,
                                                                "processed_ftp" => $this->lang->line('restore')." (Zip)",
                                                                "processed_ftp_size" => $this->general->convert_size(filesize($zip_path)),
                                                                "display_style_error" => "none",
                                                                "display_style_success" => "show",
                                                                "backup_detail_msg" => $this->lang->line('backup_detail_msg'),
                                                                "thank_you" => $this->lang->line('thank_you'),
                                                                "cpright" => $this->lang->line("cpright"),
                                                                "website_url" => base_url(),
                                                                "logo_url"  => base_url("public/public/front/img/frontend-logo.png"),
                                                                "date" => "Date:",
                                                                "check_image" =>  base_url("public/public/assets/img/check.png"),
                                                            );
                                        //$geterrors    = @file_get_contents(FCPATH."restorelogs/".$restoredat->error_logfile); 
                                        if($getproject_setting["notify_id"] == "success"){
                                            $content["display_style_error"] = "none";
                                            $content["display_style_success"] = "show";
                                        }else if($getproject_setting["notify_id"] == "error"){
                                            $content["ftp_errors"] = "Incompleted entries (DB Restore) & DB Restore Errors:";
                                            $content["ftp_errors_content"]= !empty($geterrors) ? $geterrors : 'NA';
                                            $content["display_style_error"] = "show";
                                            $content["display_style_success"] = "none";

                                        }else if($getproject_setting["notify_id"] == "both"){
                                            $content["ftp_errors"] = "Incompleted entries (DB Restore) & DB Restore Errors:";
                                            $content["ftp_errors_content"]= !empty($geterrors) ? $geterrors : 'NA';
                                            $content["display_style_error"] = "show";
                                            $content["display_style_success"] = "show";
                                        }else{
                                            $content = array();
                                        }
										
										
										
										
										
                                        if(sizeof($content) > 0){
                                            $subject = $this->lang->line("restore_DB_email");
                                            $getfilecontent = "ftp_restore_template.html";
                                            
                                            $stsetting    = $this->db->query("select name_value from site_setting ss where ss.setting_id IN(7,8)")->result(); 
                                            $content["down_content"] = $this->lang->line("down_content").$stsetting[0]->name_value;
                                            $msg   =$this->replace_content($content,$getfilecontent); 
                                            $this->email->set_newline("\r\n");
                                            $this->email->from($stsetting[0]->name_value, $stsetting[1]->name_value);
											//$email = $getclient->email;
                                            $email = "dharamds1104@gmail.com";
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
					   
								$cmd = "rm -rf ".$unzipFilesPath.'/*';
								exec($cmd." > /dev/null 2>/dev/null &");
					   
                           $this->db->where("restore_id",$restore_id)->update("restore_db",["status" => "success","enddate" => $enddate, "tables_data" => json_encode($messagelog)]); 
                       
                        
                    }else{
                        $msg = "<p> Database server credentials is wrong </p>";
                         $this->db->where("restore_id",$getbackups->restore_id);   
                         $this->db->update("restore_db",["error_msg" => $getbackups->error_msg.$msg]);
                    }



                /* }catch(\Throwable $e) {
                          $msg = "<p> ". $e->getMessage()."</p>";
                          $this->db->where("restore_id",$getbackups->restore_id);   
                          $this->db->update("restore_db",["error_msg" => $getbackups->error_msg.$msg]);
                 }*/
				
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
			public function run_all_schedule_queries(){
            $this->db->update("ftp_server",["scheduling_add_flag" => 0]);
            $this->db->update("mysql_server",["scheduling_add_flag" => 0]); 
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
    }
