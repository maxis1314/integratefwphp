<?php

/**
 * ECSHOP 前台公用文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: init.php 17217 2011-01-19 06:29:08Z liubo $
*/

/* 取得当前ecshop所在的根目录 */
define('ROOT_PATH', str_replace('includes/init.php', '', str_replace('\\', '/', __FILE__)));

function my_autoloader($class) {
    $file1 = ROOT_PATH.'../gencore/classes/' . str_replace("_",'/',$class) . '.class.php';
    $file2 = ROOT_PATH.'classes/' . str_replace("_",'/',$class) . '.class.php';
    if(file_exists($file1)){
        include $file1;
    }elseif(file_exists($file2)){
        include $file2;
    }
}
spl_autoload_register('my_autoloader');


if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}



/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path', '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path', '.:' . ROOT_PATH);
}

require(ROOT_PATH . 'data/config.php');

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);

//require(ROOT_PATH . 'includes/inc_constant.php');
//require(ROOT_PATH . 'includes/cls_ecshop.php');
//require(ROOT_PATH . 'includes/cls_error.php');
//require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_base.php');
//require(ROOT_PATH . 'includes/lib_common.php');
//require(ROOT_PATH . 'includes/lib_main.php');
//require(ROOT_PATH . 'includes/lib_insert.php');
//require(ROOT_PATH . 'includes/lib_goods.php');
//require(ROOT_PATH . 'includes/lib_article.php');
//require(ROOT_PATH . 'includes/lib_code.php');
//require(ROOT_PATH . 'includes/lib_ecmoban.php');


/* 对用户传入的变量进行转义操作。*/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}

$_CFG = array();

if (!defined('INIT_NO_USERS'))
{
    /* 初始化session */
    include(ROOT_PATH . 'includes/cls_session.php');

    $sess = new T_Session('gen_sessions', 'gen_sessions_data');

    define('SESS_ID', $sess->get_session_id());
}

if (!defined('INIT_NO_SMARTY'))
{
    header('Cache-control: private');
    header('Content-type: text/html; charset='.EC_CHARSET);
    /* 创建 Smarty 对象。*/
    require(ROOT_PATH . 'includes/cls_template.php');
    $smarty = new cls_template;

    $smarty->cache_lifetime = 3600;
    $_CFG['template'] = "pc";

    $smarty->template_dir   = ROOT_PATH . 'themes/' .$_CFG['template']  ;
    $smarty->compile_dir    = ROOT_PATH . 'temp/compiled/' . $_CFG['template'];
    $smarty->cache_dir      = ROOT_PATH . 'temp/caches/' . $_CFG['template'];
    $smarty->assign('ecs_charset', EC_CHARSET);
    $smarty->assign('ecs_css_path', 'themes/' . $_CFG['template'] . '/style.css');
}


if (real_ip()!='220.112.230.63') {
	//header("Location:http://www.baidu.com");exit;		   
}
if(defined('VERBOSE'))
{
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_STRICT)); 
}
else
{
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_STRICT)); 
}
 


/* 判断是否支持 Gzip 模式 */
if (!defined('INIT_NO_SMARTY') && gzip_enabled())
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}


function get_caller_info($html=true) {
    $c = '<hr>';
     
    $trace = array_reverse(debug_backtrace());
    foreach($trace as $one){
        $line = $one['line'];
        $line = $line > 5 ? $line-10 : 1;
        $c.=$one["file"].":L".$one["line"].":".$one["function"]." \n";
        if($html){
        	$c.= "&nbsp;<a href='/hpx/tools/seefile.php?filename=$one[file]&key=$one[function]&line=$one[line]#line$line' target=_blank>see</a><br>";
        }
    }
    return($c);
}


/*
function check_access(){
	if (empty($_SESSION['user_id'])){
		ecs_header("Location: /login.php\n");
		exit;
	}
}



if (!defined('INIT_NO_USERS')){
//	$not_login_arr =array('genreport_new.php','genreport.php','callapi.php','genreportready.php');
//	$urlnow= isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);
//	$action = preg_replace('/^.*\//', '', $urlnow);
	if (empty($_SESSION['user_id']))
	{
		if (!in_array($action, $not_login_arr))
		{
			if($_POST['pass']=='sichang0923'){		
				$_SESSION['user_id'] = '5';
				$_SESSION['gaga']       = 'gaga@gaga.com';
			}else{
				echo "<form method=post><input type=password name=pass><input type=submit></form>";
				exit;
			}
		}
	}
}
*/

function log2file($str){
	if(defined('LOG_TO_FILE')){
		$logfilename = ROOT_PATH. '/a.log';
		$str =var_export($str,true). get_caller_info(false) . "\n\n\n\n\n";
		file_put_contents($logfilename, var_export($str,true), FILE_APPEND);
	}
}


function get($name){
	if(!empty($_GET[$name])){
		return $_GET[$name];
	}else if(!empty($_POST[$name])){
		return $_POST[$name];
	}else{
		return null;
	}
}

function getInt($name){
	$val = get($name);
	if($val){
		return intval($val);
	}
	return 0;
}

function getFloat($name){
    $val = get($name);
    if($val){
        return floatval($val);
    }
    return 0;
}

function getEng($name){
	$val = get($name);
	if($val && preg_match('/^[a-zA-Z0-9_\.\,]+$/', $val)){
		return $val;
	}
	return null;
}




?>
