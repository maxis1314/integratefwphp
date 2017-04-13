<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/../../includes/init.php');

$controller = new Controller_Admin();
$controller->index();


