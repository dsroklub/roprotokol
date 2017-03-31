<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);

$res=array ("status" => "ok");

if ($reg->pass) {
    $q="INSERT INTO course_requirement_pass (requirement,member_id,passed) SELECT ?,id,NOW() From Member Where MemberId=?";
} else {
    $q="DELETE FROM course_requirement_pass WHERE requirement=? and member_id IN (SELECT id FROM Member where MemberId=?)";
}

if ($stmt = $rodb->prepare($q)) {
    $stmt->bind_param('ss',
    $reg->requirement,
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
