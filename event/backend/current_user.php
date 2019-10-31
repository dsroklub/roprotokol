<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

if (isset($_SERVER['PHP_AUTH_USER'])) {

    $cuser=$_SERVER['PHP_AUTH_USER'];
    error_log("CU=$cuser");
    $s="SELECT
       sha1(CONCAT(authentication.password,?)) as token,
       IFNULL(mrc.MemberRight,'') as is_cox,
       IFNULL(mrlc.MemberRight,'') as is_long_cox,
       IFNULL(mrf.MemberRight,'') as is_fora_admin,
       IFNULL(mrr.MemberRight,'') as has_remote_access,
       IFNULL(mrw.argument,'') as is_winter_admin,
       Member.MemberId as member_id, CONCAT(Member.FirstName,' ', Member.LastName) as name, Member.Email as member_email 
    FROM Member  
       LEFT JOIN MemberRights mrc ON mrc.member_id=Member.id AND mrc.MemberRight='cox'
       LEFT JOIN MemberRights mrlc ON mrlc.member_id=Member.id AND mrlc.MemberRight='longdistance'
       LEFT JOIN MemberRights mrf ON mrf.member_id=Member.id AND mrf.MemberRight='event' AND mrf.argument='fora'
       LEFT JOIN MemberRights mrr ON mrr.member_id=Member.id AND mrr.MemberRight='remote_access' AND mrr.argument='roprotokol'
       LEFT JOIN MemberRights mrw ON mrw.member_id=Member.id AND mrw.MemberRight='admin' AND mrw.argument='vedligehold',
     authentication 
    WHERE Member.MemberId=? AND authentication.member_id=Member.id AND Member.RemoveDate IS NULL and member_type >= 0;
  ";
    if ($stmt = $rodb->prepare($s)) {
        $stmt->bind_param('ss',
            $config['secret'],
            $cuser);
        if (!$stmt->execute()) {
            error_log("OOOP ".$rodb->error);
            http_response_code(500);
        }
        $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
    } else {
        error_log("Prepare OOOP ".$rodb->error);
        http_response_code(500);
    }
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row) {
        echo json_encode($row);
        }  else {
            echo '{"member_id":"baadhal","name":"Baadhal"}';
        }
    } else {
        error_log("user not found in DB");
        http_response_code(500);
    }
} else {
    echo '{"id":"0","name":"Ikke logget ind"}';
}
