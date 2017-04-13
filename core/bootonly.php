<?php

if(!defined('CORE_ROOT_PATH')){
	define('CORE_ROOT_PATH', __DIR__);
}
define('VENDOR_PATH', CORE_ROOT_PATH.'/vendor/');
$VENDOR_LIST = array(
    'V_CACHE'=>VENDOR_PATH."/phpfastcache/phpfastcache.php",
    'V_MAILER'=>VENDOR_PATH."/phpmailer/class.phpmailer.php",
    'V_ZSESSION'=>VENDOR_PATH."/zsession/Mem_Zebra_Session.php",
    'V_DEBUG'=>VENDOR_PATH."/phpdebug/ChromePhp.php",
    'V_EZSQL'=>VENDOR_PATH."/ezsql/ezsql.php",
);

function load_core_class($class) {
    include CORE_ROOT_PATH.'/classes/' . str_replace("_",'/',$class) . '.class.php';
}


function load_vendor($name){
    global $VENDOR_LIST;
    if(isset($VENDOR_LIST[$name])){
        require_once($VENDOR_LIST[$name]);
    }
}

function open_debug(){
    error_reporting(E_ALL);
    ini_set('display_errors', 1); 
}
