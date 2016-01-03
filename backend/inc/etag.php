<?php

$mem  = new Memcached();
$mem->addServer('127.0.0.1',11211);

# usertag boattag triptag

function getetag($tn){
   $etag=$mem->get("usertag");
   if (!$etag) {
       $etag=$a=100000*round(time()/1000);
       $mem->set($tn,$etag);
   }
}

function invalidate_etag($tn) {
    $mem->increment($tn);
}

function_etag($tn) {
  setETag ($mem->get($tn))
}

?>