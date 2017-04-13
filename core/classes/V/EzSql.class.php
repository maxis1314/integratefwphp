<?php 

load_vendor("V_EZSQL");

class V_EzSql{
        var $db = null;
	function __construct($params){
		$this->db = new ezSQL_mysql($params[1],$params[2],$params[3],$params[0],$params[4]);
	}

}
