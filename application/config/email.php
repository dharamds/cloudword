<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| EMAIL SENDING SETTINGS
| -------------------------------------------------------------------
*/
       $CI =& get_instance();
    $getcred = $CI->db->query("select name,name_value from site_setting where setting_id IN(9,10,11,12,13)")->result();
    $config = array(
                               'protocol'  => $getcred[1]->name_value,
                               'smtp_host' => $getcred[0]->name_value,
                               'smtp_port' => $getcred[2]->name_value,
                               'smtp_user' => $getcred[3]->name_value,
                               'smtp_pass' => $getcred[4]->name_value,
                               'mailtype'  => 'html',
                               'charset'   => 'utf-8',
                               'validate' => FALSE
                        );

     // $config = array(
     //                           'protocol'  => 'smtp',
     //                           'smtp_host' => 'in-v3.mailjet.com',
     //                           'smtp_port' => 587,
     //                           'smtp_user' => 'fcb8a76c3f21307bb222e723761288cd',
     //                           'smtp_pass' => '2c0a102bbcc12c8b3f4073aa9cd250a7',
     //                           'mailtype'  => 'html',
     //                           'charset'   => 'iso-8859-1',
     //                           'validate' => FALSE
     //                    );
 
/* End of file email.php */
/* Location: ./application/config/email.php */