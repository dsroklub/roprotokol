<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$st=[];
$s="SELECT * FROM status";
$cuser=null;
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

if ($stmt = $rodb->prepare($s)) {
     $stmt->execute();
     $result= $stmt->get_result() or die("Error in status query: " . mysqli_error($rodb));
     if ($row = $result->fetch_assoc()) {
         $st["sculler_open"]=$row["sculler_open"];
         $st["reservation_configuration"]=$row["reservation_configuration"];
     }
}
$rodb->close();
$pf=explode('.',$_SERVER['REMOTE_ADDR'])[0];
$st["local"]=( $pf=="127" || $pf=="10");
$st["user"]=$cuser;
echo json_encode($st);
