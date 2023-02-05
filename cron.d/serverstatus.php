#!/usr/bin/php
<?php
define( 'ROOT_DIR', dirname(__FILE__) );
$config = parse_ini_file(ROOT_DIR . '/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

$r=["status"=>"ok"];
$r["time"]=time();
$r["load"]=sys_getloadavg();
$r["up"]=exec("uptime");
file_put_contents("/data/roprotokol/public/serverstatus.json",json_encode($r));

