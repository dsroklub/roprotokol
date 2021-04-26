<?php
include("inc/common.php");
include("inc/verify_user.php");
$data = file_get_contents("php://input");
$damage=json_decode($data);
$stmt = $rodb->prepare("UPDATE Damage SET degree=?, description=? WHERE id=?") or dbErr($rodb,$res,"upd damage");
$stmt->bind_param('isi', $damage->degree, $damage->description, $damage->id) || dbErr($rodb,$res,"upd damage bind");
$stmt->execute() || dbErr($rodb,$res,"upd damage bind");
$rodb->close();
invalidate('boat');
echo json_encode($res);
