<?php

$cip=$_SERVER['REMOTE_ADDR'];
#error_log("IP address from client ". $cip);

$verified=false;
$remotepw="";
if (isset($_SERVER['HTTP_PASSWORD'])) {
    $remotepw=$_SERVER['HTTP_PASSWORD'];
}
if (!empty($cuser)) {
    $stmt = $rodb->prepare(
        "SELECT Member.id as admin_id,Acquired FROM MemberRights,Member WHERE Member.MemberId=? AND Member.id=MemberRights.member_id AND MemberRight='admin' AND argument='roprotokol'"
    );
    $stmt->bind_param("s", $cuser);
    $stmt->execute();
    $result= $stmt->get_result() or die("Error in admin check: " . mysqli_error($rodb));
    $right=$result->fetch_assoc();
    if ($right) {
        global $admin_id;
        $admin_id=$right["admin_id"];
        error_log("verified $cuser by password");
        $verified=true;
    }
}

if (!$verified) {
    error_log("login failed");
    echo '{"status":"notauthorized","error":"forkert password"}';
    exit;
}
