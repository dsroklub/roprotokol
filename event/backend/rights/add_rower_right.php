<?php
include("../inc/common.php");
$vr=verify_right(["admin"=>"roprotokol","admin"=>"right"]);

$error=null;
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$data=json_decode($data);
$admin="baadhal";
if (!empty($cuser)) {
    $admin=$cuser;
}
$rodb->begin_transaction();
//error_log('add rower right right '.json_encode($data));
//error_log('id='.$data->rower->id);
//error_log('right='.$data->right->member_right.":".$data->right->arg);
$arg=$data->right->arg;
if (!$arg) {
    $arg="";
}

$stmt=$rodb->prepare("INSERT INTO  MemberRights (member_id,MemberRight,argument,Acquired,created_by,created) SELECT m.id,?,?,?,mc.id,NOW()
                      FROM Member m, Member mc WHERE m.MemberID=? AND mc.MemberID=? ON DUPLICATE KEY UPDATE Acquired=NOW()") or dbErr($rodb,$res,"add rower right");
$stmt->bind_param('sssss', $data->right->member_right,$arg,$data->newrightdate, $data->rower->id,$admin) || dbErr($rodb,$res,"bind add rower right");
$stmt->execute()  || dbErr($rodb,$res,"exe add rower right");
$rodb->commit();
invalidate('member');
eventLog("$admin tildelte ".$data->right->member_right ." til ".$data->rower->id);
echo json_encode($res);
$rodb->close();
