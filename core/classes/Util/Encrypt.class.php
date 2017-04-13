<?php 

class Util_Encrypt{
	static $GEN_MD5_KEY = 'fJakCCVAb4ArJAdSV1bYBFojZ1yIgDbB';//'SC*UT%^KU*GT56';
	static function genreport_md5check($identify_id,$type){
		return md5($identify_id.$type.Util_Encrypt::$GEN_MD5_KEY);
	}
	
	static function show_alert($smarty,$title,$content){
		if(is_array($content)){
			$content=var_export($content,true);
		}
		$smarty->assign('message',array('title'=>$title,'content'=>$content));
		$smarty->display('alert.dwt');
	}
}


