<?php
class LanguageLoader
{
    function initialize() {
        $ci =& get_instance();
        $ci->load->helper('language');
        $siteLang = $ci->session->userdata('lang');
        if($siteLang) {
	    $ss = $siteLang == 'en' ? 'english' : 'german'; 		
            $ci->lang->load('english',$ss);
        }else {
            $ci->lang->load('english','german');
        }
    }
}