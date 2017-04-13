<?php
class V_TeamToy {
	var $api = 'http://115.28.24.177:8088/tms/?c=api';
	var $email = '';
	var $pass= '';
 	var $token= '';
	function __construct($email="",$pass=""){
		$this->email='hpx1011@qq.com';
		$this->pass='17cd7b16327e8df';
		$this->get_token();
	}
	function get_token(){	
		$info = json_decode(
			file_get_contents( $this->api .'&a=user_get_token&email=' . urlencode($this->email) . '&password='. urlencode($this->pass)),true);
		$this->token = $info['data']['token'];
		//echo $this->token;

	}

	
	function publish_msg($type,$text){
		$addurl = "feed_publish&type=$type&text=".urlencode($text);
		return json_decode(file_get_contents($this->api."&a=$addurl&token=".$this->token), true  ) ;
 	}
	
        function pm_send($text){
		$addurl = "feed_publish&type=&text=".urlencode($text);
		return json_decode(file_get_contents($this->api."&a=$addurl&token=".$this->token), true  ) ;
 	}


	function todo_add($uid,$text,$public=1){
		$addurl = "todo_add&uid=$uid&text=".urlencode($text);
		return json_decode(file_get_contents($this->api."&a=$addurl&token=".$this->token), true  ) ;
 	}
	
	function im_send($uid,$text){
	       $addurl = "im_send&uid=$uid&text=".urlencode($text);
 	       return json_decode(file_get_contents($this->api."&a=$addurl&token=".$this->token), true  ) ;
 
	}

}

/*
$tt = new TeamToy();
var_dump($tt->publish_msg('',"froa mcommand"));
var_dump($tt->todo_add(1,"from command"));
var_dump($tt->im_send(1,"from command"));
*/
