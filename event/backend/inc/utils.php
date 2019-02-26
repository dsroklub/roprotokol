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

function verify_forum_owner($forum) {
    global $rodb;
    global $res;
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        return false;
    }
    $cuser=$_SERVER['PHP_AUTH_USER'];
    $stmt=$rodb->prepare("SELECT 'x' FROM forum, Member owner WHERE owner.MemberId=? and forum.owner=owner.id AND forum.name=?") or dbErr($rodb,$res,"verify forum owner");
    $stmt->bind_param("ss", $cuser,$forum) or dbErr($rodb,$res,"owner verify bind");
    $stmt->execute() or dbErr($rodb,$res,"forum owner verify");
    $result= $stmt->get_result() or dbErr($rodb,$res,"owner verify res");
}
