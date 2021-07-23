<?php
class Dashboard extends MX_Controller 
{
    function __construct()
    {
        parent::__construct();
        if($this->session->userdata("user_id") == "" || $this->session->userdata("role_type") == "client" && $this->session->userdata("user_id") != ""){   
            redirect(base_url()."admin/login");
        }
    }
    public function index(){
        $user_id = $this->session->userdata("user_id");
        $data["user_count"] = $this->db->query("select * from client where status ='active'")->num_rows();
        $data["ftp_used"] = $this->db->query("select sum(total_size) as ftpsize from backupftp")->row()->ftpsize;
        $data["db_used"] = $this->db->query("select sum(total_size) as dbsize from backupsql")->row()->dbsize;
        $getallprojects = $this->db->get("project");
        $data["project_count"] = $getallprojects->num_rows();
        $data["page"]      = "dashboard";
        $data["ftp_count"] = $this->db->get("backupftp")->num_rows();
        $data["sql_count"] = $this->db->get("backupsql")->num_rows(); 
    	$this->load->view("admin/dashboard_new",$data);
    }
    public function getsizes(){
        header('Content-type: application/json');
            $user_id = $this->session->userdata("user_id");
            $getallprojects = $this->db->get_where("project")->result();
            $rootfolder = "./projects/";
            $sqlsize = 0;
            $ftpsize = 0;
            foreach($getallprojects as $proj) {
                    $ftpfolder = $rootfolder.$proj->folder_name."/ftp_server/";
                    $sqlfolder = $rootfolder.$proj->folder_name."/mysql_server/";
                    if(is_dir($ftpfolder)){
                    $sqlsize   += $this->foldersize($sqlfolder);
                    $ftpsize   += $this->foldersize($ftpfolder);
                    }
            }
            echo json_encode(array("ftpsize" => $this->format_size($ftpsize),"sqlsize" => $this->format_size($sqlsize)));
    }
    public function foldersize($path){
                                $total_size = 0;
                                $files = scandir($path);
                                $cleanPath = rtrim($path, '/'). '/';
                                foreach($files as $t) {
                                    if ($t<>"." && $t<>"..") {
                                        $currentFile = $cleanPath . $t;
                                        if (is_dir($currentFile)) {
                                            $size = $this->foldersize($currentFile);
                                            $total_size += $size;
                                        }
                                        else {
                                            $size = filesize($currentFile);
                                            $total_size += $size;
                                        }
                                    }   
                                }

                                return $total_size;
        }
    function format_size($size) {
                                $units = explode(' ', 'B KB MB GB TB PB');
                                $mod = 1024;
                                for ($i = 0; $size > $mod; $i++) {
                                    $size /= $mod;
                                }
                                $endIndex = strpos($size, ".")+3;
                                return substr( $size, 0, $endIndex).' '.$units[$i];
    }
    public function change_language(){
        $langg = $this->input->post("langvar");
        $user_id = $this->session->userdata("user_id");
        $newdata = array(
        'lang'  => $langg
        );
            $this->session->set_userdata($newdata);
            echo json_encode(array("status" => "success"));
    }

}