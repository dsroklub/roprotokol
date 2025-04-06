<?php
include("../inc/common.php");
include("../inc/utils.php");
$data = file_get_contents("php://input");
$data=json_decode($data);
if ($data->right->member_right=='wrench') {
    $vr=verify_right(["admin"=>["roprotokol","right",'vedligehold',"bestyrelsen"]]);
} else {
    $vr=verify_right(["admin"=>["roprotokol","right","bestyrelsen"]]);
}
$error=null;
$res=array ("status" => "ok");
$admin="bådhalsbruger";
if (!empty($cuser)) {
    $admin=$cuser;
}
$rodb->begin_transaction();
error_log('delete rower right:  '.json_encode($data));
error_log('id='.$data->rower->id);
error_log('right'.$data->right->member_right." -".$data->right->arg);
if ($stmt = $rodb->prepare("DELETE FROM MemberRights WHERE MemberRight=? AND (argument IS NULL OR argument=?) AND member_id IN (SELECT id FROM Member WHERE MemberID=?)")) {
    $stmt->bind_param('sss', $data->right->member_right,$data->right->arg,$data->rower->id);
    $stmt->execute();
} else {
    error_log('OOOP'.$rodb->error);
}
$rodb->commit();
eventLog("$admin fjernede ".$data->right->member_right ." fra ".$data->rower->id);
$rodb->close();
invalidate('member');
echo json_encode($res);
