<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------
// Paypal library configuration
// ------------------------------------------------------------------------
// PayPal environment, Sandbox or Live

$CI =& get_instance();
    

$config['sandbox'] = false; // FALSE for live environment

if(!empty($CI->session->userdata("role_type"))){
	if($CI->session->userdata("role_type") == "reseller_user"){
		$getcreds = $CI->db->get_where("reseller_setting",array("reseller_id" => $CI->session->userdata("parent_id")))->row();
		$config['paypal_lib_currency_code'] = $getcreds->currency;
		$config['business'] = $getcreds->paypal_account;	
	}else{
		$getcreds = $CI->db->query(" select * from site_setting where setting_id IN(15,16)")->result();
		$config['paypal_lib_currency_code'] = $getcreds[0]->name_value;
		$config['business'] = $getcreds[1]->name_value;
	}
	
}else{
		$getcreds 							= $CI->db->query(" select * from site_setting where setting_id IN(15,16)")->result();
		$config['paypal_lib_currency_code'] = $getcreds[0]->name_value;
		$config['business'] 				= $getcreds[1]->name_value;
}

// PayPal business email
// What is the default currency?


// Where is the button located at?
$config['paypal_lib_button_path'] 	= 'assets/images/';
// If (and where) to log ipn response in a file
$config['paypal_lib_ipn_log'] 		= TRUE;
$config['paypal_lib_ipn_log_file'] 	= BASEPATH . 'logs/paypal_ipn.log';