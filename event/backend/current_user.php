<?php

set_include_path(get_include_path().':/');
include("../../rowing/backend/inc/common.php");
include("utils.php");

if (isset($_SERVER['PHP_AUTH_USER'])) {

    $cuser=$_SERVER['PHP_AUTH_USER'];
    error_log("CU=$cuser");
    
    $s="SELECT Member.MemberId as member_id, CONCAT(Member.FirstName,' ', Member.LastName) as name, Member.Email as member_email 
    FROM Member 
    Where Member.MemberId=?
  ";

    if ($stmt = $rodb->prepare($s)) {
        $stmt->bind_param('s',$cuser);
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
        echo json_encode($row);
    } else {
        error_log("user not found in DB");
        http_response_code(500);
    }
} else {
    echo '{"id":"0","name":"Ikke logget ind"}';
}

?>