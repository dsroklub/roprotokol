<?php
include("../../public/inc/gitrevision.php");
$mem  = new Memcached();
$mem->addServer('127.0.0.1',11211);
$mem->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
header('Content-type: application/json');

$ts=42;
function dv($v) {
    if (empty($v)) {
        return 7;
    } else {
        return $v;
    }
}
$tsa=array(
    'member' =>  dv($mem->get('member')),
    'file' =>  dv($mem->get('file')),
    'stats' =>  dv($mem->get('stats')),
    'fora' =>  dv($mem->get('fora')),
    'boat' =>  dv($mem->get('boat')),
    'work' =>  dv($mem->get('work')),
    'message' =>  dv($mem->get('message')),
    'event' =>  dv($mem->get('event')),
    'gitrevision' => $gitrevision

);
$res=json_encode($tsa);
print($res);
