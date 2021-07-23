<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_error', 1);
Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed
class Api_management extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    public function download_backup($backup_id=NULL){
        $this->load->helper('download');
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
    public function downloaddb($id = NULL) { 
        $this->load->helper('download');
        if($id){
                $bkp_id  = $id;
                $bkpdata = $this->db->get_where("backupsql", array("backup_id" => $bkp_id))->row();
                $serverdata = $this->db->get_where("mysql_server", array("mysql_id" => $bkpdata->db_id))->row();
                $projectdata = $this->general->get_decrypt_project_data_by_id($bkpdata->project_id);
                $file = realpath("./projects/".$projectdata->folder_name."/db_server/")."\\".$serverdata->folder_path;
                $file_name = "database_".time().".zip"; 
                    $this->load->library("zipp");    
                $baserootpath = FCPATH."projects/".$projectdata->folder_name."/db_server/".$serverdata->folder_path;
                $zipPath = FCPATH."projects/".$projectdata->folder_name."/db_server/".$file_name;
                $checkzip = $this->zipp->create($baserootpath,$zipPath);
                if(file_exists($zipPath)){
                     $data = file_get_contents($zipPath);
                //force download
                    force_download($file_name,$data);
                }else{
                    echo json_encode(array("status" => false,"message" => $this->lang->line("something_wrong"))); 
                }
        }
    }
}