<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$data = file_get_contents("php://input");
error_log($data);
$reg=json_decode($data);

$res=array ("status" => "ok");



$disp=0;
if (!empty($reg->dispensation) and $reg->dispensation) {
    $disp=1;
}

error_log("disp $disp");

if ($stmt = $rodb->prepare("INSERT INTO course_requirement  (name, description, expiry,dispensation) VALUES (?,?,?,?)")) {
    $stmt->bind_param('ssii',
    $reg->name,
    $reg->description,
    $reg->expiry,
    $disp
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
