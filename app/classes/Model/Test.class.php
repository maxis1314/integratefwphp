<?php


class Model_Test extends DB_Table{
	function __construct(){
		parent::__construct('xxx');
	}

	function getAllPosOpt(){
		$data = $this->queryBind('SELECT * from xxx',null,true);
		
		return $data;
	}

}