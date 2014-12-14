<?php
include("inc/common.php");

$s="SELECT Medlemsnr as id,CONCAT(Fornavn,' ',Efternavn) as name,Initialer as initials
    FROM Medlem";


// echo $s;
$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
echo '[';
 $first=1;
 while ($row = $result->fetch_assoc()) {
	  if ($first) $first=0; else echo ',';	  
	  echo json_encode($row);
}
echo ']';
$rodb->close();
?> 
