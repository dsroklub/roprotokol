<?php
$mem  = new Memcached();
$mem->addServer('127.0.0.1',11211);
$mem->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
header('Content-type: application/json');

$ts=42;

$tsa=array(
    'boat' => $mem->get('boat'),
    'member' =>  $mem->get('member'),
    'file' =>  $mem->get('file'),
    'stats' =>  $mem->get('stats'),
    'fora' =>  $mem->get('forum'),
    'event' =>  $mem->get('event')
);
$res=json_encode($tsa);
    print($res);
?> 
