<?php
class EncryptionLoader
{
    function initialize() {
        $ci =& get_instance();
        $ci->encryption->initialize(
        array(
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => config_item('encryption_key')
        )
        );
    }
}