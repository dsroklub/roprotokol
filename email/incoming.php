#!/usr/bin/php
<?php
include("../rowing/backend/inc/common.php");
require_once 'utils.php';

echo "INCOMING";
$fd = fopen("php://stdin", "r");
$email = "";
while (!feof($fd)) {
    $line = fread($fd, 1024);
    $email .= $line;
}
fclose($fd);

//echo "GOT EMAIL $email";

require_once 'Mail/mimeDecode.php';
$md=new Mail_mimeDecode($email);
$p=$md->decode();

$headers=$p->headers;

if (empty($headers["subject"]) || empty($headers["from"])) {
  exit(1);
}

$subject=sanestring($p->$headers["subject"]);

$froms=mailparse_rfc822_parse_addresses($p->headers["from"]);
$tos=mailparse_rfc822_parse_addresses($p->headers["to"]);
if (count($froms)!=1 || empty($froms[0]["address"])) {
  echo "no from address";
  exit(1);
}


$from=$froms[0]["address"];
$to=$toss[0]["address"];
$from=filter_var($from, FILTER_SANITIZE_EMAIL);
$to=filter_var($to, FILTER_SANITIZE_EMAIL);

// verify from
// verify forum

file_put_contents ("/tmp/ie.log",$email);
