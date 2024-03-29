<?php
include("inc/common.php");
include("inc/utils.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
// error_log("Currentuser ".$_SERVER['PHP_AUTH_USER']);
    $cuser=$_SERVER['PHP_AUTH_USER'];
     error_log("CU=$cuser");
    $s="SELECT
       sha1(CONCAT(authentication.password,?)) as token,
       IFNULL(mrc.MemberRight,'') as is_cox,
       IFNULL(mrf.MemberRight,'') as is_fora_admin,
       IFNULL(mrr.MemberRight,'') as has_remote_access,
       Member.MemberId as member_id, CONCAT(Member.FirstName,' ', Member.LastName) as name, Member.Email as member_email
    FROM Member
       LEFT JOIN MemberRights mrc ON mrc.member_id=Member.id AND mrc.MemberRight='cox'
       LEFT JOIN MemberRights mrf ON mrf.member_id=Member.id AND mrf.MemberRight='event' AND mrf.argument='fora'
       LEFT JOIN MemberRights mrr ON mrr.member_id=Member.id AND mrr.MemberRight='remote_access' AND mrr.argument='roprotokol',
     authentication
    WHERE Member.MemberId=? AND authentication.member_id=Member.id AND Member.RemoveDate IS NULL and member_type>=0
  ";
    $stmt = $rodb->prepare($s) or dbErr($rodb,$res,"current user p");
    $stmt->bind_param('ss',$config['secret'],$cuser) || dbErr($rodb,$res,"current user b");
    $stmt->execute() || dbErr($rodb,$res,"current user e");
    $result= $stmt->get_result() or dbErr($rodb,$res,"current user r");
    if ($result) {
        $row = $result->fetch_assoc();
        //error_log("got ".print_r($row,true));
        echo json_encode($row);
    } else {
        error_log("user not found in DB");
        http_response_code(500);
    }
} else {
    echo '{"id":"0","name":"Ikke logget ind"}';
}
