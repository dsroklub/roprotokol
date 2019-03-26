<?php

function sanestring($s) {
   $allowedchars=".;@abcdefghijklmnopqrstuvwxyzæøåABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ01234567890=:_-#";
   $s1=filter_var(str_replace(">","",str_replace("<","",$s)), FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_BACKTICK|FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_AMP);
   $r=preg_replace('/&#\d+;/',"",$s1);
    return $r;
}
