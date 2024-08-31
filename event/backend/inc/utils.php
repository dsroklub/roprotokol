<?php
$workyear=(date("m")>9)?date("Y")+1:date("Y");
$workseason =" ((YEAR(start_time)=YEAR(NOW()) AND (MONTH(start_time)>9 OR MONTH(NOW())<10)) OR (YEAR(start_time)=YEAR(NOW())-1 AND MONTH(start_time)>9 AND MONTH(NOW())<10))";

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

function user_log($msg="no message") {
    error_log($_SERVER['PHP_AUTH_USER'] . ": " . $msg);
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

function verify_right($requireds,$abort=true) {
    global $rodb;
    global $res;
    $tried="";
    //error_log("verify that user=$cuser has rights ".print_r($requireds,1));
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        roErr("bruger ukendt");
    }
    $cuser=$_SERVER['PHP_AUTH_USER'];
    foreach ($requireds as $required=>$args) {
        // one of
        foreach ($args as $arg) {
            // error_log("verify $arg");
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
    }
    if ($abort) {
        roErr("Medlem $cuser har ikke nogen af rettighederne: $tried");
    } else {
        return false;
    }
}
