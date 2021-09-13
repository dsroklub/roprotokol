<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");

$q="none";
if (isset($_GET["q"])) {
    $q=$_GET["q"];
}

switch ($q) {
case "worktasks":
    $s="SELECT * FROM worktask";
    break;
default:    
    $res=["status" => "error", "error"=>"invalid query' .$q"];
    echo json_encode($res);
    exit(0);
}

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"mystats $q");
$stmt->bind_param("s",$cuser);
$stmt->execute() || dbErr($rodb,$res,"fetch");
$result= $stmt->get_result();
output_rows($result);
$stmt->close(); 
$rodb->close();
