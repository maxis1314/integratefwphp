<?php
define('IN_ECS', true);
//define('VERBOSE', true);
require(dirname(__FILE__) . '/../includes/init.php');
 
if(isset($_POST['password'])){
  if($_POST['password']=='' and $_POST['username']=='admin'){
     $_SESSION['user_id'] = '5';
     $_SESSION['gaga']       = 'gaga@gaga.com';
     ecs_header("Location: newtheme.php\n");
     exit;
  }else{
     $smarty->assign('error_str','用户名或密码错误!');
  }
}

 

$smarty->display('login.dwt');

?>

