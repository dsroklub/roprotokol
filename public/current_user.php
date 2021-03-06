<?php

include("../rowing/backend/inc/common.php");
include("utils.php");
$r=["id"=>"0","name"=>"Ikke logget ind"];
$pf=explode('.',$_SERVER['REMOTE_ADDR'])[0];
$r["local"]=( $pf=="127" || $pf=="10");

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
    // error_log("CU public c=$cuser");
    
    $s="SELECT Member.MemberId as member_id, CONCAT(Member.FirstName,' ', Member.LastName) as name, Member.Email as member_email 
    FROM Member 
    Where Member.MemberId=?
    ";

    if ($stmt = $rodb->prepare($s)) {
        $stmt->bind_param('s',$cuser);
        if (!$stmt->execute()) {
            error_log("OOOP ".$rodb->error);
            $res['status']=$rodb->error;
            http_response_code(500);
        }
        $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
    } else {
        error_log("Prepare OOOP ".$rodb->error);
    }
    if ($result) {
        $row = $result->fetch_assoc();
        $r["id"]=$row["member_id"];
        $r["name"]=$row["name"];
    } else {
        error_log("user not found in DB");
        http_response_code(500);
    }
}
echo json_encode($r);
