<?php 

load_vendor("V_DEBUG");

class V_Debug{
	function __construct(){
	}
	function log($txt){
		ChromePhp::log($txt);
	}
	function warn($txt){	
		ChromePhp::warn($txt);
 	}

}
