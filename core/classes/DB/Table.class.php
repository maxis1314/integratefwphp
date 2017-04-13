<?php 

class DB_Table extends AllBase{
	var $db = null;
	var $table = null;
	var $db_name = "gen";
	
	function __construct($table,$name="default"){
		$this->db = DB_Config::getDbLink($name);//$GLOBALS ['db'];
		$this->table = $table;
	}
	
	function table($table){
		$this->table = $table;
	}
	
	function getOne($where){
		if($data = $this->get($where)){
			return count($data)>0?$data[0]:false;
		}
		return false;
	}
	
	function cacheGet($where=null,$order=null){
		$var_export = var_export(array($where,$order), true);
		$key = "SQL_".$this->db_name."_".$this->table."_".md5($var_export);
		if($cached = Util_Cache::get($key)){
			return $cached;
		}else{
			$cached = $this->get($where,$order);
			Util_Cache::set($key,$cached,$this->db_name."_".$this->table."_".$var_export);
			return $cached;
		}
	}
	
	function get($where=null,$order=null){
		$wheres = array();
		if($where!=null){
			foreach($where as $k=>$v){
				$wheres[]="$k = '".$this->db->escape_string($v)."'";
			}
		}else{
			$wheres[]="1=1";
		}
		 
		$sql = "select * from `".$this->table."` where ". implode(" and ",$wheres);
		if($order){
			$sql = "$sql order by $order";
		}
		$res =  $this->db->query($sql);
		if ($res){
			$arr = array();
			while ($row = mysql_fetch_assoc($res))
			{
				$arr[] = $row;
			}
			 
			return $arr;
		}
		return false;
	}
	
	function delete($where){

		$wheres = array();
		foreach($where as $k=>$v){
			$wheres[]="$k = '".$this->db->escape_string($v)."'";
		}

		 
		$sql = "delete from `".$this->table."` where ". implode(" and ",$wheres);
		$res =  $this->db->query($sql);
	}
	
	function add($arr,$returnsql=false){
		$keys = array_keys($arr);
		$values= array_values($arr);
		
		foreach($values as &$one){
			$one = $this->db->escape_string($one);
		}
 
		$sql = "insert into `".$this->table."` (".
				implode(',',$keys).") values(\"".
				implode("\",\"",$values)."\")";
		if($returnsql){return $sql;}
		return $this->db->query($sql);
	}
	
	function update($arr,$where){
		$keys = array_keys($arr);
		$values= array_values($arr);
		foreach($values as &$one) {
			$one = $this->db->escape_string($one);
		}
		$sets = array();
		foreach($arr as $k=>$v){
			if($k!="id"){
				$sets[]="$k = '".$this->db->escape_string($v)."'";
			}
		}
		 
		$wheres = array();
		foreach($where as $k=>$v){
			$wheres[]="$k = '".$this->db->escape_string($v)."'";
		}
	
		$sql = "update `".$this->table."` set ".
				implode(',',$sets).") where ".
				implode(" and ",$wheres);
		return $this->db->query($sql);
	}
	
	function cacheQuery($sql,$arr=null){
		$var_export = var_export(array($sql,$arr), true);
		$key = "SQL_".$this->db_name."_".$this->table."_".md5($var_export);
		if($cached = Util_Cache::get($key)){
			return $cached;
		}else{
			$cached = $this->queryBind($sql,$arr,true);
			Util_Cache::set($key,$cached,$var_export);
			return $cached;
		}
	}
	
	function queryBind($sql,$arr=null,$needresult=false){
		if($arr){
			foreach($arr as &$one) {
				$one = $this->db->escape_string($one);
			}

			$sql =  vsprintf($sql,$arr);
		}
		$res = $this->db->query($sql);
		if ($res && $needresult){
			$arr = array();
			while ($row = mysql_fetch_assoc($res))
			{
				$arr[] = $row;
			}
	
			return $arr;
		}
		return false;
	}
	
	function getCache($sql){
		$key = "Table_getCache_$sql".md5($sql);
		$api_data = read_static_cache($key);
	
		if($api_data === false)
		{
			$api_data = $this->db->getAll($sql);
			write_static_cache($key, $api_data);
		}
	
		return $api_data;
	}
	
	function getAsHash($sql,$k,$v){
		$records = $this->db->getAll($sql);
		$hash = array();
		foreach($records as $one){
			$hash[$one[$k]] = $one[$v];
		}
		return $hash;
	}
	
	function getAsHashComplex($sql,$k){
		$records = $this->db->getAll($sql);
		$hash = array();
		foreach($records as $one){
			$hash[$one[$k]] = $one;
		}
		return $hash;
	}
	function cacheGetAsHashComplex($sql,$k){
		$records = $this->cacheQuery($sql);
		$hash = array();
		foreach($records as $one){
			$hash[$one[$k]] = $one;
		}
		return $hash;
	}
	
	function import($fname,$separator=","){
		$handle=fopen("$fname","r");
		$header=fgetcsv($handle,10000,$separator);
		if(empty($header)){
			echo "$fname no header!";exit;
		}
		$checkdata = array();
		$linenum=1;
		while($data=fgetcsv($handle,10000,$separator)){
			if(empty($data)){
				continue;
			}
			if(count($data)!=count($header)){
				echo "$fname Line $linenum: columns not right! expect ".count($header)."<br>".implode(',',$data);exit;
			}
			$hashdata = array();
			for($i=0;$i<count($header);$i++){
				$hashdata[$header[$i]] = $data[$i];
			}
			$checkdata[] = $hashdata;
			$linenum++;
		}
		$this->db->query("delete from `".$this->table."`" );
		foreach($checkdata as $data){
			
			$this->add($data);			 
		}
		
		
		fclose($handle);
		return $linenum-1;
	}
	
}