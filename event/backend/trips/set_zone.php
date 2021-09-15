<?php
include("../inc/common.php");
include("../inc/utils.php");
$vr=verify_right(["admin"=>["roprotokol","trip"]]);
$data = json_decode(file_get_contents("php://input"));
$stmt = $rodb->prepare("UPDATE Destination SET Zone=? WHERE Name=? AND Location=?") or dbErr($rodb,$res,"cannot set zone (Prepare)");
$stmt->bind_param('sss', $data->zone,$data->name,$data->location) || dbErr($rodb,$res,"cannot set zone (bind)");
$stmt->execute() || dbErr($rodb,$res,"cannot set zone");
$rodb->close();
invalidate('destination');
echo json_encode($res);
