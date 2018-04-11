<?php
function sanestring($s,$slash=false,$allowedchars=".:;@abcdefghijklmnopqrstuvwxyzæøåABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ01234567890_-#") {
   if ($slash) {
       $allowedchars.="/";
   }
    $r="";
    for ($i=0; $i<100 && $i < strlen($s) ;$i++) {
        $c=$s[$i];
        if (strpos($allowedchars,$c)>=0){
            $r.=$c;
        }        
    }
    return $r;
}

function verify_real_user($action="gøre dette") {
    if (!isset($_SERVER['PHP_AUTH_USER']) or $_SERVER['PHP_AUTH_USER'] == "baadhal") {
        global $res;
        $res["status"]="error";
        $res["error"]="Bådhallen kan ikke $action";
        error_log("Bådhallen kan ikke $action");
        echo json_encode($res);
        exit(-1);
    }
}