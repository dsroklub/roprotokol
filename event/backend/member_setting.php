<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
$res=array ("status" => "ok");
$s="SELECT * FROM member_setting
   WHERE member IN (SELECT id FROM Member WHERE MemberId=?)";


if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("s", $cuser);
    $stmt->execute();
     $result= $stmt->get_result() or die("Error in stat query: " . mysqli_error($rodb));
     if ($row = $result->fetch_assoc()) {
         echo json_encode($row,	JSON_PRETTY_PRINT,JSON_FORCE_OBJECT );
     } else {
         echo '{}';             
     }
} else {
    $error=" member setting ".mysqli_error($rodb);
    if (!empty($error)) {
        error_log($error);
        $res['message']=$message;
        $res['status']='error';
        $res['error']=$error;
        echo json_encode($res);
    }
}
invalidate("settings");

?> 
