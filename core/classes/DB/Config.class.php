<?php
class DB_Config{
    static $db_config =array(
        "default" => array("localhost","ruser", "", "gen", 'utf8'),
        "rico" => array("rico","rwuser", "", "gen", 'utf8'),
        "test" => array("localhost","root", "", "gen", 'utf8'),
        "prod" => array("localhost","root", "", "gen", 'utf8'),        
        "session" => array("localhost","wuser", "", "z_session", 'utf8'),
    );

    static $dbModelCache = array();

    static function getDbLink($name="default"){
        if(!isset(self::$dbModelCache[$name])){
            self::$dbModelCache[$name] = new DB_Link(self::$db_config[$name]);
        }
        return self::$dbModelCache[$name];
    }

    static function getEzSql($name="default"){
	$cname = "ez_$name";
        if(!isset(self::$dbModelCache[$cname])){
            self::$dbModelCache[$cname] = new V_EzSql(self::$db_config[$name]);
        }
        return self::$dbModelCache[$cname];
    }
	
}
