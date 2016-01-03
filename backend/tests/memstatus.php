#! /usr/bin/php5

<?php
$mem  = new Memcached();
$mem->addServer('127.0.0.1',11211);

$ld=$mem->get("logindebug");
print("logindebug ".$ld);
?> 
