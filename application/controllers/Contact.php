<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contact extends CI_Controller {
	
	public function index()
	{
			
		// $db_process_ids = $this->get_running_process('execute_background_db');
		// $ftp_process_ids = $this->get_running_process('ftploopdataback');
		
		$cap = $this->get_captcha_img();
		$data["image"] = $cap['image'];
		$data["msgtype"] = "success";		
		$this->load->view("contact",$data);
	}
	
	public function get_captcha_img(){
		$this->load->helper('captcha'); 
		
		
		$vals = array(
        'word'          => '',
        'img_path'      => '/var/www/vhosts/cloudserviceworld.com/captcha/',
        'img_url'       => 'https://cloudserviceworld.com/captcha/',
        'font_path'     => '/var/www/vhosts/cloudserviceworld.com/public/public/front/fonts/Calibri-Bold.ttf',
        'img_width'     => '150',
        'img_height'    => 50,
        'expiration'    => 7200,
        'word_length'   => 6,
        'font_size'     => 22,
        'img_id'        => 'Imageid',
        'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

        // White background and border, black text and red grid
        'colors'        => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(58, 108, 171)
        )
);

	$cap = create_captcha($vals);

	$data = array(
			'captcha_time'  => $cap['time'],
			'ip_address'    => $this->input->ip_address(),
			'word'          => $cap['word']
	);

	$query = $this->db->insert_string('captcha', $data);
	$this->db->query($query);
		return $cap;
	}
	
	
	public function get_running_process($prefix = 'ftploopdataback'){
		
		 $ret =  shell_exec("ps aux | grep php | grep ".$prefix);
		 $retData = explode('www-data' ,$ret);
		 $retDataNew = explode('root' ,$retData[0]);
		
		 foreach($retDataNew as $key => $item):
			$index = strpos($item, $prefix) + strlen($prefix);
			$number_text = substr($item, $index);
			$process_id = explode(' ', trim($number_text));
			$result[] = $process_id[0];
		 endforeach;
		 return (array_unique(array_filter($result)));
	}
	
	
	public function save(){
		$this->load->helper('basic_helper');
		$this->load->helper('url');
		$insdata = $this->input->post();
		if(count($insdata) == 0){			
			redirect(base_url('contact'), 'location');
		}
		//$this->load->helper('captcha');
		
		$cap = $this->get_captcha_img();
		
		// First, delete old captchas
		$expiration = time() - 200; // Two hour limit
		$this->db->where('captcha_time < ', $expiration)
				->delete('captcha');

		// Then see if a captcha exists:
		$sql = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
		$binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();
				
		
	
		
			$data["email"] = $insdata['email'];
			$data["message"] = $insdata['message'];
			$data["image"] = $cap['image'];
			
		if($row->count > 0){
			
		//USER MAIL
        sendMail($insdata['email'], 'CONTACT_EMAIL', [
            'user_name' => $insdata['email'],
        ]);

        //ADMIN MAIL
        $settings = $this->db->select('name_value')->where_in('slug',['email'] )->get("site_setting")->result();
        sendMail($settings[0]->name_value, CONTACT_US_USER_EMAIL, [
            'user_name' => $insdata['email'],
            'email' => $insdata['email'],
            'message_content' => $insdata['message'],
        ]);
		
		unset($insdata["captcha"]);
		unset($insdata["ci_csrf_token"]);
		unset($insdata["i_agree"]);
		
			
		
		if($this->db->insert("contacts",$insdata)){
			$data["msg"] = $this->lang->line("contact_thank");
			$data["msgtype"] = "success";
			$data["email"] = '';
			$data["message"] = '';
		}else{
			$data["msg"] = $this->lang->line("something_wrong");
			$data["msgtype"] = "failed";
			
		}
	}else{
					
			$data["msg"] = $this->lang->line("captcha_failed");
			$data["msgtype"] = "failed";
			
			
		}
		$this->load->view("contact",$data);
	}
	public function test(){
		echo date("Y-m-d H:i:s");
	}
}
