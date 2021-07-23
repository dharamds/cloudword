<?php
class Sqltesting extends MX_Controller {
    function __construct() {
        parent::__construct();
        if ($this->session->userdata("role_type") != "admin") {
            redirect(base_url() . "admin/login");
        }
    }


    public function index() {
        
        echo 'here in testing';

    }


    public function backupdb($project_id) {
        //$project_id = base64_decode($project_id);
            
        if(empty($project_id)){
            $project_id = 1;    
        }
        

        $getserver = $this->db->get_where("mysql_server", array("project_id" => $project_id))->row();
        $getftpserver = $this->db->get_where("ftp_server", array("project_id" => $project_id))->row();
        $getproject = $this->general->get_decrypt_project_data_by_id($project_id);
        $user_id = $getproject->client_id;
        $getusrdd = $this->db->get_where('client', array("client_id" => $user_id))->row();
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



        $startdate = date("Y-m-d H:i:s");

        // $dbhost = 'stadtwerke.mariadb.database.azure.com';
        // $dbuser = 'stadtwerke-bonn-development@stadtwerke';
        // $dbpass = 'zaAhkeKuUEIFU30Phd2F';
        // $dbname = 'stadtwerke-bonn-development';


        // $dbhost = 'stadtwerke.mariadb.database.azure.com';
        // $dbuser = 'stadtwerke-hilden-development@stadtwerke';
        // $dbpass = 's65yI8DhFnh8q92sqdPm';
        // $dbname = 'stadtwerke-hilden-development';


        // $dbhost = $getserver->mhostname;
        // $dbuser = $getserver->musername;
        // $dbpass = $getserver->mpassword;
        // $dbname = $getserver->mdatabase_name;


        $dbhost = 'localhost';
        $dbuser = 'cloud_world';
        $dbpass = 'cloud_world054709';
        $dbname = 'cloud_world';




        $tables = '*';


        $file_name = $dbname . "_" . time() . ".sql";
        $path = "./testingsqldata/" . $file_name;
        $returndata = $this->backup_tables($dbhost, $dbuser, $dbpass, $dbname, $tables, $path, $project_id, $user_id);

        
        echo json_encode($returndata);
        exit;


    }




    public function backup_tables($host, $user, $pass, $dbname, $tables = '*', $path, $project_id, $user_id) {
        $getuser = $this->db->get_where("client", array("client_id" => $user_id))->row();
        

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
                $return = '';
                $return = 'set global max_allowed_packet=104857600;';
                $return.= "\n\n";
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


        

        } catch(\Throwable $e) {
            $output = array("status" => "failed", "msg" => $e->getMessage());
            echo json_encode($output);
            exit;
        }

        restore_error_handler();




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
   
    
   

}
