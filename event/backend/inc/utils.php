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




function saneEmail($s) {
    $sm=["ø"=>"oe","Ø"=>"Oe","æ"=>"ae","Æ"=>"Ae","å"=>"aa","Å"=>"Aa","X"=>"xxx"];
    $allowedchars=".abcdefghijklmnopqrstuvwxyz01234567890=:_-#";
    $r=$s;
    foreach ($sm as $s => $p) {
        $r=str_replace($s,$p,$r);
    }
    $s=strtolower(mb_convert_encoding($r,"ascii"));
    $r="";
    for ($i=0; $i < strlen($s);$i++) {
        $c=$s[$i];
        if (is_numeric(strpos($allowedchars,$c))){
            $r.=$c;
        }
    }
    return $r;
}

function verify_right($requireds) {
    global $rodb;
    global $res;
    $tried="";
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        return false;
    }
    $cuser=$_SERVER['PHP_AUTH_USER'];
    foreach ($requireds as $required=>$arg) {
        // one of
        if ($arg) {
            $tried .= "$required/$arg ";
            $stmt=$rodb->prepare("SELECT 'x' FROM Member,MemberRights WHERE Member.MemberId=? AND MemberRights.member_id=Member.id AND MemberRight=? AND argument=?") or dbErr($rodb,$res,"verify right");
            $stmt->bind_param("sss", $cuser,$required,$arg) or dbErr($rodb,$res,"owner verify bind");
        } else {
            $tried .= "$required ";
            $stmt=$rodb->prepare("SELECT 'x' FROM Member,MemberRights WHERE Member.MemberId=? AND MemberRights.member_id=Member.id AND MemberRight=?") or dbErr($rodb,$res,"verify right");
            $stmt->bind_param("ss", $cuser,$required) or dbErr($rodb,$res,"owner verify bind");
        }
        $stmt->execute() or dbErr($rodb,$res,"right verify");
        $result= $stmt->get_result() or dbErr($rodb,$res,"right verify res");
        if ($result->num_rows>0) {
            return true;
        }
    }
    roErr("medlem $cuser har ikke nogen af rettighederne $tried");
}
