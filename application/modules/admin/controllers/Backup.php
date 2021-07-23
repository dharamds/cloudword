<?php
class Backup extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
		
		//print_r($this->session->userdata('role_type')); exit;
        if($this->session->userdata("role_type") !== "admin"){
            redirect(base_url()."admin/login");
        }
    }
    public function index(){
            $user_id = $this->session->userdata("user_id");
            $data["backups"] = $this->db->query("select bf.*,p.project_name,p.slug,p.folder_name from backupftp bf inner join project p on p.project_id = bf.project_id AND p.status = 'active' ORDER BY bf.backup_id DESC")->result();
            $data["page"] = "backup";

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
        }


    	$this->load->view("admin/backup/list",$data);
    }

public function downloadftp($id = NULL) { 
        $this->load->helper('download');
        if($id){
            $user_id = $this->session->userdata("user_id");
            $user_role = $this->session->userdata("role_type");
            $bkp_id  = $id;
            //$bkpdata = $this->db->get_where("backupftp", array("backup_id" => $bkp_id))->row();

            $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = " . $bkp_id . "")->row();
            //echo json_encode($bkpdata);
            if(!empty($bkpdata)){
                if($bkpdata->file_name != ''){
                    $bkpdata->file_name = $this->encryption->decrypt($bkpdata->file_name);
                }
            }else{
                $this->session->set_flashdata('notallowtoaccess', true);
                redirect(base_url()."admin/project/backup_ftp/".$id);
                exit;    
            }
			
			
            if($user_id == $bkpdata->client_id || $user_role == 'admin'){
                $file = FCPATH."projects/" . $this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/".$bkpdata->file_name;
				
				
                //echo $file; exit;
                //$data = file_get_contents($file);
                //force download
                force_download($file, null);
                //force_download($bkpdata->file_name,$data);
            }else{
                $this->session->set_flashdata('notallowtoaccess', true);
                redirect(base_url()."admin/project/backup_ftp/".$id);
                exit;    
            }
            
        }
    }
	
    public function startftprestore(){

        $user_id = $this->session->userdata("user_id");
        $bkp_id  = $this->input->post("bkp_id");
       // $bkpdata = $this->db->get_where("backupftp",array("backup_id" => $bkp_id))->row();
        $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = " . $bkp_id . "")->row();
        if(!empty($bkpdata)){
            if($bkpdata->file_name != ''){
                $bkpdata->file_name = $this->encryption->decrypt($bkpdata->file_name);
            }
        }
       // $ftpdata = $this->general->get_decrypt_ftp_data_by_id($bkpdata->project_id);
        //$projectdata = $this->general->get_decrypt_project_data_by_id($bkpdata->project_id);
            try {
                $config['hostname'] = $this->encryption->decrypt($bkpdata->hostname);
                $config['username'] = $this->encryption->decrypt($bkpdata->username);
                $config['password'] = $this->encryption->decrypt($bkpdata->password);
                $config['port']     = $this->encryption->decrypt($bkpdata->port_no);
                $config['passive']  = TRUE;
                $config['debug']    = TRUE;

                if($this->encryption->decrypt($bkpdata->port_no) == 22){
                    $ftpcalling = $this->ftpbackup;
                }else{
                    $ftpcalling = $this->ftp;
                }

                if($ftpcalling->connect($config)){

                    $fdatadata = $ftpcalling->list_files("/");
                  if(!is_array($fdatadata)){
                        $config['passive']  = FALSE;
                        $ftpcalling->connect($config);
                        
                    }
                    $zipPath = str_replace('application\\',"",APPPATH)."projects\\".$this->encryption->decrypt($bkpdata->folder_name)."\\ftp_server\\".$bkpdata->folder_path."\\".$bkpdata->file_name;
                    $baserootpath = str_replace('application\\',"",APPPATH)."projects\\".$this->encryption->decrypt($bkpdata->folder_name)."\\ftp_server\\".$bkpdata->folder_path."\\"."temp";
                    
                     $zip_obj = new ZipArchive;
                    if ($zip_obj->open($zipPath) === TRUE) {
                       $zip_obj->extractTo($baserootpath);
                       $filepath = "./projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/syncbackup/";
                        $allFiles = scandir($filepath);
                        if (($key = array_search('.', $allFiles)) !== false) {
                            unset($allFiles[$key]);
                        }
                        if (($key = array_search('..', $allFiles)) !== false) {
                            unset($allFiles[$key]);
                        }
                        $allFiles = array_values($allFiles);
                        $allFilesCnt = count($allFiles);

                        $output = array("status" => "success", "msg" => "Zip extracted Successfully", "allFiles" => $allFiles, "allFilesCnt" => $allFilesCnt  );
                        echo json_encode($output);
                        exit;
                    }else{

                        $output = array("status" => "failed", "msg" => "Cant extract zip, Something went wrong");
                        echo json_encode($output);
                        exit;

                    }

                }
            } catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
            }


    }






    public function restorefilesonebyone(){

        $user_id = $this->session->userdata("user_id");
        $bkp_id  = $this->input->post("bkp_id");
        //$bkpdata = $this->db->get_where("backupftp",array("backup_id" => $bkp_id))->row();
          $bkpdata = $this->db->query("select bftp.*,fs.hostname,fs.password,fs.username,fs.port_no,fs.folder_path,fs.status as fstatus,p.folder_name from backupftp bftp INNER JOIN ftp_server fs on bftp.ftp_id =fs.ftp_id INNER JOIN project p ON bftp.project_id = p.project_id WHERE bftp.backup_id = " . $bkp_id . "")->row();
        if(!empty($bkpdata)){
            if($bkpdata->file_name != ''){
                $bkpdata->file_name = $this->encryption->decrypt($bkpdata->file_name);
            }
        }
      



        
                $config['hostname'] = $this->encryption->decrypt($bkpdata->hostname);
                $config['username'] = $this->encryption->decrypt($bkpdata->username);
                $config['password'] = $this->encryption->decrypt($bkpdata->password);
                $config['port']     = $this->encryption->decrypt($bkpdata->port_no);
                $config['passive']  = TRUE;
                $config['debug']    = TRUE;

                if($this->encryption->decrypt($bkpdata->port_no) == 22){
                    $ftpcalling = $this->ftpbackup;
                }else{
                    $ftpcalling = $this->ftp;
                }


                if($ftpcalling->connect($config)){
                    $fdatadata = $ftpcalling->list_files("/");
                    if(!is_array($fdatadata)){
                        $config['passive']  = FALSE;
                        $ftpcalling->connect($config);
                        $fdatadata = $ftpcalling->list_files("/");
                    }


                    $rtfolderremote = "/";
                    // if(in_array("public_html",$fdatadata)){
                    //     $rtfolderremote = "/public_html";
                    // }

                    $fname  = $this->input->post("fileQuery"); 
                    $filepath = "./projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/syncbackup/".$fname."/";

                    if(is_dir($filepath)) {
                        $filepath = "./projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/".$bkpdata->folder_path."/temp/syncbackup/".$fname."/";
                        // echo 'h1';
                        // exit;
                        $rtfolderremote = $rtfolderremote."/".$fname."/";
                        //$rtfolderremote = $rtfolderremote."/";

                        $mirror = $ftpcalling->mirror($filepath,$rtfolderremote);
                        if($mirror){
                            $output = array("status" => "success", "msg" => "Backup has been restored Successfully");
                            echo json_encode($output);
                            exit;
                        }else{
                            $output = array("status" => "failed", "msg" => "Backup not restored, Something went wrong");
                            echo json_encode($output);
                            exit;
                        }

                    }else{
                        $filepath = "./projects/".$this->encryption->decrypt($bkpdata->folder_name)."/ftp_server/temp/syncbackup/".$fname;

                        // echo 'h2';
                        // exit;

                        $upload = $ftpcalling->upload($filepath, ''.$rtfolderremote.'/'.$fname.'');

                        if($upload){
                            $output = array("status" => "success", "msg" => "Backup has been restored Successfully");
                            echo json_encode($output);
                            exit;
                        }else{
                            $output = array("status" => "failed", "msg" => "Backup not restored, Something went wrong");
                            echo json_encode($output);
                            exit;
                        }

                    }


                }

        

    }



    
    public function deletetempfolder(){

            $bkp_id  = $this->input->post("bkp_id");
            $bkpdata = $this->db->get_where("backupftp",array("backup_id" => $bkp_id))->row();
            $projectdata = $this->general->get_decrypt_project_data_by_id($bkpdata->project_id);

           

            try {

                
                $filepath = "./projects/".$projectdata->folder_name."/ftp_server/temp/syncbackup/";
                $del = $this->delTree($filepath);
                
                $output = array("status" => "success", "msg" => "temp file deleted");
                echo json_encode($output);
                exit;

            } catch(\Throwable $e) {


                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;

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





    public function restore(){
        $user_id = $this->session->userdata("user_id");
        $bkp_id  = $this->input->post("bkp_id");
        $bkpdata = $this->db->get_where("backupftp",array("backup_id" => $bkp_id))->row();

         if(!empty($bkpdata)){
            if($bkpdata->file_name != ''){
                $bkpdata->file_name = $this->encryption->decrypt($bkpdata->file_name);
            }
        }

        //$ftpdata = $this->db->get_where("ftp_server",array("project_id" => $bkpdata->project_id))->row();
        //$projectdata =  $this->db->get_where("project",array("project_id" => $bkpdata->project_id))->row();

        $ftpdata = $this->general->get_decrypt_ftp_data_by_id($bkpdata->project_id);
        $projectdata = $this->general->get_decrypt_project_data_by_id($bkpdata->project_id);
        
            $config['hostname'] = $ftpdata->hostname;
            $config['username'] = $ftpdata->username;
            $config['password'] = $ftpdata->password;
            $config['port']     = $ftpdata->port_no;
            $config['passive']  = TRUE;
            $config['debug']    = TRUE;
            if($this->ftp->connect($config)){
                $fdatadata = $this->ftp->list_files("/");
              if(!is_array($fdatadata)){
                    $config['passive']  = FALSE;
                    $this->ftp->connect($config);
                    
                 }
                $zipPath = str_replace('application\\',"",APPPATH)."projects\\".$projectdata->folder_name."\\ftp_server\\".$bkpdata->file_name;
                $baserootpath = str_replace('application\\',"",APPPATH)."projects\\".$projectdata->folder_name."\\ftp_server\\temp";
                 $zip_obj = new ZipArchive;
                if ($zip_obj->open($zipPath) === TRUE) {
                   $zip_obj->extractTo($baserootpath);
                   //echo "Zip exists and successfully extracted";
                }
                $filepath = "./projects/".$projectdata->folder_name."/ftp_server/temp/syncbackup/";


                $mirror = $this->ftp->mirror($filepath,'/');

                if($mirror){
                    
                    $output = array("status" => "success", "msg" => "Backup has been restored Successfully");
                    echo json_encode($output);
                    exit;

                }else{

                    $output = array("status" => "failed", "msg" => "Backup not restored, Something went wrong");
                    echo json_encode($output);
                    exit;
                }



            }
    }








    public function sqlbkp(){
       $user_id = $this->session->userdata("user_id");
       $data["backups"] = $this->db->query("select bf.*,p.project_name,p.slug,p.folder_name from backupsql bf inner join project p on p.project_id = bf.project_id AND p.status = 'active'  order by bf.backup_id DESC")->result();
       $data["page"] ="sqlbackup";

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
        }

        $this->load->view("admin/backup/sqllist",$data);

    }







    public function startrestore(){
        $user_id = $this->session->userdata("user_id");
        $bkp_id  = $this->input->post("db_id");
        $db_name  = $this->input->post("db_name");
        $file_name  = $this->input->post("file_name");
        //$bkpdata = $this->db->get_where("backupsql",array("backup_id" => $bkp_id))->row();
        $bkpdata = $this->db->query("select bdbs.*,ms.mhostname,ms.mpassword,ms.musername,ms.port_no,ms.folder_path,ms.status as dbstatus,p.folder_name,ms.caption from backupsql bdbs INNER JOIN mysql_server ms on bdbs.db_id =ms.mysql_id INNER JOIN project p ON bdbs.project_id = p.project_id WHERE bdbs.backup_id = " . $bkp_id . "")->row();

         set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);

            try {
                $dbhost = $this->encryption->decrypt($bkpdata->mhostname);
                $dbuser = $this->encryption->decrypt($bkpdata->musername);
                $dbpass = $this->encryption->decrypt($bkpdata->mpassword);
                $dbname = $db_name;
                $restorefile = FCPATH."projects/".$this->encryption->decrypt($bkpdata->folder_name)."/db_server/".$bkpdata->folder_path."/".$file_name;
                $conn =new mysqli($dbhost, $dbuser, $dbpass ,$dbname);
                $query = '';
                $sqlScript = file($restorefile);
                
                foreach ($sqlScript as $line){

                    $startWith = substr(trim($line), 0 ,2);
                    $endWith = substr(trim($line), -1 ,1);
                    if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                        continue;
                    }   
                    $query = $query . $line;
                    if ($endWith == ';') {
                        $queryArr[] = $query;
                        $query= '';     
                    }
                }

                $querycount = count($queryArr);
                echo "<pre>"; print_r($queryArr);die();
                $output = array("status" => "success", "querycount" => $querycount , "path" => $restorefile, "tabledata" => $queryArr);
                echo json_encode($output);
                exit;

                
            } catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
            }
            restore_error_handler();     
    }




    public function restoretableonebyone(){

            $user_id = $this->session->userdata("user_id");
            $bkp_id  = $this->input->post("db_id");
            $db_name  = $this->input->post("db_name");
            $file_name  = $this->input->post("file_name");
             //$bkpdata = $this->db->get_where("backupsql",array("backup_id" => $bkp_id))->row();
            $bkpdata = $this->db->query("select bdbs.*,ms.mhostname,ms.mpassword,ms.musername,ms.port_no,ms.folder_path,ms.status as dbstatus,p.folder_name,ms.caption from backupsql bdbs INNER JOIN mysql_server ms on bdbs.db_id =ms.mysql_id INNER JOIN project p ON bdbs.project_id = p.project_id WHERE bdbs.backup_id = " . $bkp_id . "")->row();


           
            set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
                throw new ErrorException($err_msg, 0, $err_severity, $err_file, $err_line);
            }, E_WARNING);

            try {


                $dbhost = $this->encryption->decrypt($bkpdata->mhostname);
                $dbuser = $this->encryption->decrypt($bkpdata->musername);
                $dbpass = $this->encryption->decrypt($bkpdata->mpassword);
                $dbname = $db_name;
                $conn =new mysqli($dbhost, $dbuser, $dbpass ,$dbname);

                $filepath   = $this->input->post("filepath");
                $tKey       = $this->input->post("tKey");
                $query      = $this->input->post("tableQuery");
                
                mysqli_query($conn,$query);
                $output = array("status" => "success", "msg" => $this->lang->line("Query processed Successfully") );
                echo json_encode($output);
                
            } catch(\Throwable $e) {
                $output = array("status" => "failed", "msg" => $e->getMessage());
                echo json_encode($output);
                exit;
            }

            restore_error_handler();

                       
    }







    public function sqlrestore(){


        //echo 'here';
        //exit;

        $user_id = $this->session->userdata("user_id");
        $bkp_id  = $this->input->post("bkp_id");

        $bkpdata = $this->db->get_where("backupsql",array("backup_id" => $bkp_id))->row();


        if(!empty($bkpdata)){
            if($bkpdata->file_name != ''){
                $bkpdata->file_name = $this->encryption->decrypt($bkpdata->file_name);
            }
        }

        //$getserver  = $this->db->get_where("mysql_server",array("project_id" => $bkpdata->project_id))->row();
        //$ftpdata = $this->db->get_where("ftp_server",array("project_id" => $bkpdata->project_id))->row();
        //$projectdata =  $this->db->get_where("project",array("project_id" => $bkpdata->project_id))->row();

        $getserver = $this->general->get_decrypt_mysql_data_by_id($bkpdata->project_id);
        $ftpdata = $this->general->get_decrypt_ftp_data_by_id($bkpdata->project_id);
        $projectdata = $this->general->get_decrypt_project_data_by_id($bkpdata->project_id);

        if($getserver->mhostname == "localhost" || $getserver->mhostname == "127.0.0.1"){


         $file = './projects/mysqlserverrestore.php';
            $current = file_get_contents($file);
            $filedata= str_replace('{username}',$getserver->musername, $filedata);
            $filedata= str_replace('{password}',$getserver->mpassword, $filedata);
            $filedata= str_replace('{database}',$getserver->mdatabase_name, $filedata);
            $filedata= str_replace('{testingcloud.sql}',$bkpdata->file_name, $filedata);
            $myfile = fopen("./projects/".$projectdata->folder_name."/mysql_server/dbrestore/Mysqldailyrestoredatabasefile.php", "w");
            fwrite($myfile, $filedata);
            fclose($myfile);
            $config['hostname'] = $ftpdata->hostname;
            $config['username'] = $ftpdata->username;
            $config['password'] = $ftpdata->password;
            $config['port']     = $ftpdata->port_no;
            $config['passive']  = TRUE;
            $config['debug']    = TRUE;
            if($this->ftp->connect($config)){
                 $fdatadata = $this->ftp->list_files("/");
              if(!is_array($fdatadata)){
                    $config['passive']  = FALSE;
                    $this->ftp->connect($config);
                    $fdatadata = $this->ftp->list_files("/");
                    
                 }
                copy("./projects/".$projectdata->folder_name."/mysql_server/".$bkpdata->file_name,"./projects/".$projectdata->folder_name."/mysql_server/dbrestore/".$bkpdata->file_name);
                if(in_array("public_html",$fdatadata)){
                    $this->ftp->mirror("./projects/".$projectdata->folder_name."/mysql_server/dbrestore/","/public_html/");
                }else{
                    $this->ftp->mirror("./projects/".$projectdata->folder_name."/mysql_server/dbrestore/","/");
                }
                
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ftpdata->root_path."/Mysqldailbackudatabasefile.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
            $output = curl_exec($ch); 
            curl_close($ch);
            $sdsdf = json_decode($output);
                if($sdsdf->status == "success"){
                    unlink("./projects/".$projectdata->folder_name."/mysql_server/dbrestore/".$bkpdata->file_name);
                    echo json_encode(array("status" => "success","msg" => $this->lang->line("sql_restore_msg")));
                }else{
                echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
                }
            }
        }else{



            $dbhost = $getserver->mhostname;
            $dbuser = $getserver->musername;
            $dbpass = $getserver->mpassword;
            $dbname = $getserver->mdatabase_name;
            $restorefile = "./projects/".$projectdata->folder_name."/mysql_server/".$bkpdata->file_name;
            $conn =new mysqli($dbhost, $dbuser, $dbpass ,$dbname);
            $query = '';
            $sqlScript = file($restorefile);
            foreach ($sqlScript as $line){
                $startWith = substr(trim($line), 0 ,2);
                $endWith = substr(trim($line), -1 ,1);
                if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                    continue;
                }   
                $query = $query . $line;
                if ($endWith == ';') {
                    mysqli_query($conn,$query);
                    $query= '';     
                }
            }



        }               
    }






    public function delete(){
        $user_id = $this->session->userdata("user_id");
        $bkp_id  = $this->input->post("bkp_id");
        $bkpdata = $this->db->get_where("backupftp",array("backup_id" => $bkp_id))->row();

        if(!empty($bkpdata)){
            if($bkpdata->file_name != ''){
                $bkpdata->file_name = $this->encryption->decrypt($bkpdata->file_name);
            }
        }


        //$projectdata =  $this->db->get_where("project",array("project_id" => $bkpdata->project_id))->row();
        $projectdata = $this->general->get_decrypt_project_data_by_id($bkpdata->project_id);

        unlink("./projects/".$projectdata->folder_name."/ftp_server/".$bkpdata->file_name);
        $this->db->where("backup_id",$bkp_id);
        if($this->db->delete("backupftp")){
            echo json_encode(array("status" => "success","msg" => $this->lang->line("backup_delete_msg")));
        }else{
             echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
        }
    }
    public function deletesql(){
        $user_id = $this->session->userdata("user_id");
        $bkp_id  = $this->input->post("bkp_id");
        $bkpdata = $this->db->get_where("backupsql",array("backup_id" => $bkp_id))->row();

         if(!empty($bkpdata)){
            if($bkpdata->file_name != ''){
                $bkpdata->file_name = $this->encryption->decrypt($bkpdata->file_name);
            }
        }

        //$projectdata =  $this->db->get_where("project",array("project_id" => $bkpdata->project_id))->row();
        $projectdata = $this->general->get_decrypt_project_data_by_id($bkpdata->project_id);

        unlink("./projects/".$projectdata->folder_name."/mysql_server/".$bkpdata->file_name);
        $this->db->where("backup_id",$bkp_id);
        if($this->db->delete("backupsql")){
            echo json_encode(array("status" => "success","msg" => $this->lang->line("backup_delete_msg")));
        }else{
             echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong")));
        }
    }
    public function testfff(){
        
    }

    public function downloaddb($id = NULL, $dbname = NULL) { 
        $this->load->helper('download');
        $this->load->library("zipp");  
        ini_set("display_errors", 1);
        $getdb = $this->db->get_where("backupsql",["backup_id" => $id]);
		
        if($getdb->num_rows() > 0){
            $data = $getdb->row();
            $filesdata = json_decode($data->foldersdata);
			
			
            $serverdata = $this->db->get_where("mysql_server", array("mysql_id" => $data->db_id))->row();
                $projectdata = $this->general->get_decrypt_project_data_by_id($data->project_id);
            
            $foldername = time().rand(000000000,999999999);
            $file = FCPATH."projects/".$projectdata->folder_name."/db_server/".$serverdata->folder_path."/";
			$dbname = base64_decode($dbname);
			//print_r($file); exit;
            mkdir($file.$foldername);
            if(count($filesdata) > 0){
                foreach($filesdata as $k) {
					if($k->db_name == $dbname){
						$filename = $k->db_name.'zip';	
						$filenPath = $file.'dbcheck/'.$k->file_name.'.zip';
						
						if(file_exists($filenPath)){
                     force_download($filenPath, null);
						}else{
							echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong"))); 
						}
					}	
                        //copy($file.$k->file_name.".sql",$file.$foldername."/".$k->file_name.".sql");
                }				
            }
            
        }else{
            echo "No file Found";
        }
    }
	



    // public function downloaddb($id = NULL) { 
    //     $this->load->helper('download');
    //     if($id){
    //         $user_id = $this->session->userdata("user_id");
    //         $user_role = $this->session->userdata("user_role");
    //         $bkp_id  = $id;
    //         $bkpdata = $this->db->get_where("backupsql", array("backup_id" => $bkp_id))->row();
    //         if(!empty($bkpdata)){
    //             if($bkpdata->file_name != ''){
    //                 $bkpdata->file_name = $this->encryption->decrypt($bkpdata->file_name);
    //             }
    //         }else{
    //             $this->session->set_flashdata('notallowtoaccess', true);
    //             redirect(base_url()."client/backup/sqlbkp/");
    //             exit;    
    //         }
    //         if($user_id == $bkpdata->client_id || $user_role == "admin"){
    //             $serverdata = $this->db->get_where("mysql_server", array("mysql_id" => $bkpdata->db_id))->row();
    //             $projectdata = $this->general->get_decrypt_project_data_by_id($bkpdata->project_id);
    //             $file = realpath("./projects/".$projectdata->folder_name."/db_server/")."\\".$serverdata->folder_path;
    //             $file_name = "database_".time().".zip"; 
    //                 $this->load->library("zipp");    
    //             $baserootpath = FCPATH."projects/".$projectdata->folder_name."/db_server/".$serverdata->folder_path;
    //             $zipPath = FCPATH."projects/".$projectdata->folder_name."/db_server/".$file_name;
    //             $checkzip = $this->zipp->create($baserootpath,$zipPath);
    //             if(file_exists($zipPath)){
    //                  $data = file_get_contents ($zipPath);
    //             //force download
    //                 force_download($file_name,$data);
    //             }else{
    //                 echo json_encode(array("status" => "failed","msg" => $this->lang->line("something_wrong"))); 
    //             }
    //         }else{
    //             $this->session->set_flashdata('notallowtoaccess', true);
    //             redirect(base_url()."client/backup/sqlbkp/");
    //             exit;    
    //         }  
    //     }
    // }
    public function restore_db($backup_id = NULL){
            $bkpdata = $this->db->query("select bdbs.*,ms.mhostname,ms.mpassword,ms.musername,ms.port_no,ms.folder_path,ms.status as dbstatus,p.folder_name,ms.caption from backupsql bdbs INNER JOIN mysql_server ms on bdbs.db_id =ms.mysql_id INNER JOIN project p ON bdbs.project_id = p.project_id WHERE bdbs.backup_id = " . $backup_id . "")->row();
            $data["project_data"] =   $bkpdata;
            $data["backup_id"] =   $backup_id;
            $data["page"] =   "projects";
            $this->load->view("admin/project/restoredb",$data);
    }
    public function restore_process($ftp_id){
            $data["ftp_data"] = $this->db->query("select fs.* from ftp_server fs inner join project p on p.project_id = fs.project_id where fs.ftp_id = ".$ftp_id." ")->row();
            $bkpdata = $this->db->query("select rf.*,fs.caption from restore_ftp rf inner join ftp_server fs on rf.ftp_id = fs.ftp_id where rf.ftp_id = ".$ftp_id." order by rf.restore_id DESC")->result();
						
            $data["restoredata"] =   $bkpdata;
            $data["page"] =   "projects";
            $this->load->view("admin/project/restore_process",$data);
    }
     public function restore_process_ajax()
    {
        $postData = $this->input->post();
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $ftp_id = $postData["ftp_id"];
        $records = $this->db->query("select rf.*,fs.caption from restore_ftp rf inner join ftp_server fs on rf.ftp_id = fs.ftp_id where rf.ftp_id = ".$ftp_id." order by rf.restore_id DESC")->result();
        $totalRecords = count($records);
        $totalRecordwithFilter = $totalRecords;
         $records = $this->db->query("select rf.*,fs.caption from restore_ftp rf inner join ftp_server fs on rf.ftp_id = fs.ftp_id where rf.ftp_id = ".$ftp_id." order by rf.restore_id DESC Limit ".$start.",".$rowperpage." ")->result();
         $data = array();
         $cnt = 1;
         foreach($records as $record ){
            $action = "";
            if($record->extract_flag == 1){
                $action .= '<a  href="javascript:" class="btn btn-primary" onclick="viewlogs('.$record->restore_id.')">'.$this->lang->line("view_logs").'</a>';
            }
            $st =  $record->status == 'processing' ? 'warning' : 'success';
            $status = '<span class="badge badge-'.$st.'">'.$this->lang->line($record->status).'</span>';
            $data[] = array( 
                "sr_no" => $cnt,
                "created" =>displayDate($record->added_date),
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
    public function restore_process_db($backup_id){
            $restore_data = $this->db->query("select * from restore_db WHERE backup_id = " . $backup_id . "")->result();
            if(count($restore_data) > 0){
            $backup_data = $this->db->query("select * from backupsql where backup_id = ".$backup_id."")->row();
            $data["backup_data"] = $backup_data;
            $data["server_data"] =  $this->db->query("select * from mysql_server WHERE mysql_id = " . $backup_data->db_id . "")->row();
            $data["restore_data"] =   $restore_data;
            $data["backup_id"] =   $backup_id;
            $data["page"] =   "projects";
            $this->load->view("admin/project/restore_process_db",$data);
            }else{
                redirect(base_url()."admin/backup/restore_db/".$backup_id);
            }
    }
    public function restore_process_db_ajax(){
        $postData = $this->input->post();
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $backup_id = $postData["backup_id"];
        $records = $this->db->query("select * from restore_db WHERE backup_id = " . $backup_id . "")->result();
        $totalRecords = count($records);
        $totalRecordwithFilter = $totalRecords;
         $records = $this->db->query("select * from restore_db WHERE backup_id = " . $backup_id . " order by restore_id DESC Limit ".$start.",".$rowperpage." ")->result();
         $data = array();
         $cnt = 1;
         foreach($records as $record ){
            $action = "";
            
                $action .= '<a  href="javascript:" class="btn btn-primary" onclick="viewlogs('.$record->restore_id.')">'.$this->lang->line("view_logs").'</a>';
            
            $st =  $record->status == 'processing' ? 'warning' : 'success';
            $status = '<span class="badge badge-'.$st.'">'.$this->lang->line($record->status).'</span>';
            $data[] = array( 
                "sr_no" => $cnt,
                "created" =>displayDate($record->created_at,true),
                "db_name" =>$record->db_name,
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
    public function restorelogsdb(){
        $restore_id = $this->input->post("restore_id");
        $getrestore = $this->db->query("select * from restore_db where restore_id = ".$restore_id." ");
        if($getrestore->num_rows() > 0){
			$data = $getrestore->row();
            $output = array("status" => $data->status, "totaltable" => $data->total_table, "complete_table" => $data->completed_table, "data" => json_decode($data->tables_data));
        }else{
           $output = array("status" => "failed", "msg" => $this->lang->line("no_records_found"));
        }
        echo json_encode($output);
    }
}