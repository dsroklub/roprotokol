<?php
include("inc/common.php");
$res=array ("status" => "ok");

$data = file_get_contents("php://input");
$rower=json_decode($data);
$message="rower  ".json_encode($rower);
$error=null;
$rodb->begin_transaction();
$prefix="k";
if ($rower->type == "guest") {
    $prefix="g";
}

$findcurrent="SELECT Mid(MemberID,2,5) AS tid FROM Member WHERE (Member.MemberID LIKE '".$prefix."%') GROUP BY Mid(MemberID,2,5) ORDER BY Mid(MemberID,2,5) DESC LIMIT 1";

$maxid="0001";

if ($stmt = $rodb->prepare($findcurrent)) {
    $stmt->execute();
    $result= $stmt->get_result();
    if ($maxk=$result->fetch_assoc()) {
        $kx=intval($maxk["tid"])+1;
        $maxid=sprintf('%04d', $kx);
    } else {
        error_log("no tmp rower found, is this the first?");
    }
} else {
    error_log($rodb->error);
}


$newid=$prefix.$maxid;

if ($stmt = $rodb->prepare("INSERT INTO Member (MemberID,FirstName, LastName, Created) VALUES (?,?,?,NOW())" )) { 
    $stmt->bind_param('sss', $newid,$rower->firstName,$rower->lastName);
    $stmt->execute() |  error_log($rodb->error);
} else {
    error_log("ERROR in INSERT ".$rodb->error);
}

if ($error) {
    error_log('DB error ' . $error);
    $res['message']=$message.'\n'.$error;
    $res['status']='error';
    $res['error']=$error;
    $rodb->rollback();
} else {
    $rodb->commit();
}

$res['id']=$newid;
$res['name']=($rower->firstName." ".$rower->lastName);
$res['message']=$message;
$res['search']=$newid." ".$res['name'];
invalidate("member");
$rodb->close();
echo json_encode($res);

