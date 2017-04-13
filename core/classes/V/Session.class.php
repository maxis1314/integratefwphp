<?php 

load_vendor("V_ZSESSION");

class V_Session{
	function __construct($lifetime=80000,$name="PHPSESSID"){
	    @session_name($name);
	    $session = new Zebra_Session(DB_Config::getDbLink('session'),$lifetime);
	}

}
