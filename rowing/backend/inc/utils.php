<?php

function sanestring($s) {
   $allowedchars=".;@abcdefghijklmnopqrstuvwxyzæøåABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ01234567890=:_-#";
   $s1=filter_var(str_replace(">","",str_replace("<","",$s)), FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_BACKTICK|FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_AMP);
   $r=preg_replace('/&#\d+;/',"",$s1);
    return $r;
}

function verify_right($required,$arg) {
    global $rodb;
    global $res;
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        return false;
    }
    $cuser=$_SERVER['PHP_AUTH_USER'];
    if ($arg) {
        $stmt=$rodb->prepare("SELECT 'x' FROM Member,MemberRights WHERE Member.MemberId=? AND MemberRights.member_id=Member.id AND MemberRight=? AND argument=?") or dbErr($rodb,$res,"verify right");
        $stmt->bind_param("sss", $cuser,$required,$arg) or dbErr($rodb,$res,"owner verify bind");
    } else {
        $stmt=$rodb->prepare("SELECT 'x' FROM Member,MemberRights WHERE Member.MemberId=? AND MemberRights.member_id=Member.id AND MemberRight=?") or dbErr($rodb,$res,"verify right");
        $stmt->bind_param("ss", $cuser,$required) or dbErr($rodb,$res,"owner verify bind");
    }
    $stmt->execute() or dbErr($rodb,$res,"right verify");
    $result= $stmt->get_result() or dbErr($rodb,$res,"right verify res");
    if ($result->num_rows<1) {
        roErr("medlem $cuser har ikke rettigheden $required ".$arg??"");
    }
}
