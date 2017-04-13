<?php 

class Util_Check{
	 
	static function check_keys($data,$keys){
		$ready = true;
		foreach($keys as $k){
			if(empty($data[$k])){
				$ready = false;
				break;
			}
		}
		return $ready;
	}
	
	 
}


