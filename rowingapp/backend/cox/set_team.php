<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

error_log("set new team ");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);


$res=array ("status" => "ok");

if ($stmt = $rodb->prepare("UPDATE team_requests SET team=? WHERE member_id=(SELECT id from Member where MemberID=?)")) {
    $stmt->bind_param('ss',
    $reg->team,
    $reg->member_id
    );
    if (!$stmt->execute()) {
        dbErr(@$rodb,@$res);
    }
    invalidate("gym");
    $rodb->close();
} else {
    dbErr();
}
echo json_encode($res);
?> 
