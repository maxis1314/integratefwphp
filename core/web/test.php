<?php

require("../bootcore.php");

  
$session = new V_Session();
print_r('<pre>');
print_r($_SESSION);
print_r('</pre>');

    // add some values to the session
$_SESSION['value1'] = 'hello';
$_SESSION['value2'] = 'world';


// simple Caching with:
$cache = new V_Cache();

$products = $cache->get("product_page");

if($products == null) {
	$products = "DB QUERIES | FUNCTION_GET_PRODUCTS | ARRAY | STRING | OBJECTS";
	// Write products to Cache in 10 minutes with same keyword
	$cache->set("product_page",$products , 600);

	echo " --> NO CACHE ---> DB | Func | API RUN FIRST TIME ---> ";

} else {
	echo " --> USE CACHE --> SERV 10,000+ Visitors FROM CACHE ---> ";
}

// use your products here or return it;
echo $products;


$tt = new V_TeamToy();
$tt->publish_msg('',"froa mcommand");


