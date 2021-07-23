<?php
defined('BASEPATH') or exit('No direct script access allowed');


/*
 * Slugify Helper
 *
 * Outputs the given string as a web safe filename
 */

if (!function_exists('sendMail')) {
   function sendMail($email = '', $template_code = '', $data = [])
    {   
        $CI = &get_instance();
        $message = '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><title>Cloud Service World</title><link href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i" rel="stylesheet" /></head><body style="padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; font-family: "Lato", sans-serif; font-size:16px; font-weight:400; line-height:24px; color: #555; background:#f4f4f4"><div style="width:650px; margin:0 auto; background:#fff"><div style="background:#f8f9fa; text-align: center; padding:15px; display: inline-block; width: 100%;box-sizing: border-box;"><a href="'.base_url().'" target="blank"><img src="'.base_url('public/public/front/img/frontend-logo.png').'"></a></div><div style="display: inline-block; width: 100%; padding:0 20px; box-sizing:border-box;">';



        $settings = $CI->db->select('name_value')->where_in('slug',['mail_from_name', 'email', 'smtp_host', 'mail_protocol', 'smtp_port', 'smtp_user', 'smtp_pass'] )->get("site_setting")->result();

        $template_ = $CI->db->where(["temp_code"=>$template_code, 'status' => 1])->get('email_templates')->row();
        if(empty($template_)){
            $message .= '';
            $subject = "Testing";
        }else{
           $message .= $template_->message;
           $subject = $template_->subject; 
        }
        $from_name = $settings[1]->name_value;
        $from_email = $settings[0]->name_value;
        
        $message .= '</div><div style="background:#3a6cab; padding:15px; color: #fff; text-align: center; font-size:14px">Copyrights © 2021 Cloud Service World by SSN Computer</div></div></body></html>';
        
        foreach ($data as $key => $setVars) {
            $message = str_replace('{' . $key . '}', $setVars, $message);
        }
        foreach ($data as $key => $setVars) {
            $subject = str_replace('{' . $key . '}', $setVars, $subject);
        }
        $CI->email->set_newline("\r\n");
        $CI->email->from($from_email, $from_name);
        $CI->email->to($email);
        $CI->email->subject($subject);
        $CI->email->message($message);
        if($CI->email->send()){
            return true;
        }else{
            return false;            
        }
    }
}
if (!function_exists('displayDate')) {
   function displayDate($date = null, $time = true)
    {  
        $format = 'd-m-Y';
        $formatTime = 'd-m-Y H:i:s';
        if($time)
            return $date == null ? date($formatTime) : date($formatTime, strtotime($date));
        else
            return $date == null ? date($format) : date($format, strtotime($date));
    }
}