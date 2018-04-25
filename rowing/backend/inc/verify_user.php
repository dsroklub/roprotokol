<?php
# Validate JWT token
#include("jwt.php");

$cip=$_SERVER['REMOTE_ADDR'];
#error_log("IP address from client ". $cip);
    
if(!$skiplogin) {
    $verified=false;
    $remotepw="";
    if (isset($_SERVER['HTTP_PASSWORD'])) {
        $remotepw=$_SERVER['HTTP_PASSWORD'];
    }
    if (!empty($cuser)) {
        $stmt = $rodb->prepare("SELECT Acquired FROM MemberRights,Member WHERE Member.MemberId=? AND Member.id=MemberRights.member_id AND MemberRight='admin' AND argument='roprotokol'");
        $stmt->bind_param("s", $cuser);
        $stmt->execute();
        $result= $stmt->get_result() or die("Error in admin check: " . mysqli_error($rodb));
        $right=$result->fetch_assoc();
        if ($right) {
            error_log("verified $cuser by password");
            $verified=true;
        }
    } 
    if ($adminpw == $remotepw) {
        $verified=true;
        error_log("verified $cuser by password");
    }
    
    if (!$verified) {
        error_log("login failed");
        echo '{"status":"notauthorized","error":"forkert password"}';
        exit;
    }
}
