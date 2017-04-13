<?php 

load_vendor("V_CACHE");

class V_Cache{
	var $m_cache;
	function __construct($type="memcached"){		
		$this->m_cache = phpFastCache($type);
	}
	function get($name){
		return  $this->m_cache->get($name);
	}
	function set($name,$v,$seconds=3600){
		return  $this->m_cache->set($name,$v,$seconds);
	}


}
