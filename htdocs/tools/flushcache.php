<?php
/*
 * memcachedtest.php	| 	Jan Pieter Waagmeester (jieter@jpwaag.com)
 *
 *
 */
require_once 'configuratie.include.php';

if(LoginModel::mag('P_ADMIN')){
	echo '<h1>MemCached flushen</h1>';
	Memcached::instance()->flush();
	
}
?>
