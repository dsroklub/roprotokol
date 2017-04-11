<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);

$res=array ("status" => "ok");
$cuser=$_SERVER['PHP_AUTH_USER'];

if ($reg->pass) {
    $q="INSERT INTO course_requirement_pass (requirement,member_id,passed) SELECT ?,id,NOW() From Member Where MemberId=?
INSERT INTO cox_log (timestamp, member_id,action) SELECT NOW(),id,'Remove '.requirement.' from '.id From Member Where MemberId=?";
} else {
    $q="DELETE FROM course_requirement_pass WHERE requirement=? and member_id IN (SELECT id FROM Member where MemberId=?)
   INSERT INTO cox_log (timestamp, member_id,action) SELECT NOW(),id,' pass '.requirement.' from '.id From Member Where MemberId=?";
}

if ($stmt = $rodb->prepare($q)) {
    $stmt->bind_param('sss',
    $reg->requirement,
    $reg->aspirant,
        $reg->aspirant
    );
    if (! $stmt->execute()) {
        dbErr(@$rodb,@$res);
    }
    invalidate("cox");
    $rodb->close();
} else {
    dbErr();
}
echo json_encode($res);
?> 
