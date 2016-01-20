<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$s="SELECT name,description From Locations ORDER by name";

if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result() or die("Error in location query: " . mysqli_error($rodb));
     $first=1;
     echo '[';

     while ($row = $result->fetch_assoc()) {
         if ($first) $first=0; else echo ',';	  
         echo json_encode($row);
     }
     echo ']';
}
$rodb->close();
?> 
