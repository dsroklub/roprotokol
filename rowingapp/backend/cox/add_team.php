<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

error_log("add new team ");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);


$res=array ("status" => "ok");

if ($stmt = $rodb->prepare("INSERT INTO instruction_team (name, description, instructor) SELECT ?,?,Member.id FROM Member Where MemberID=?")) {
    $stmt->bind_param('sss',
    $reg->name,
    $reg->description,
    $reg->instructor->id
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
