<?php

define('CORE_ROOT_PATH', __DIR__);
require(CORE_ROOT_PATH."/bootonly.php");

spl_autoload_register('load_core_class');
