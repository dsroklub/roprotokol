<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT JSON_OBJECT('sculler_open',sculler_open) as json FROM status";

if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result() or die("Error in status query: " . mysqli_error($rodb));
     if ($row = $result->fetch_assoc()) {
         echo $row["json"];
     }
}
$rodb->close();
?>
