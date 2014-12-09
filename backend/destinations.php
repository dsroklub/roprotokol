<?php
ini_set('default_charset', 'utf-8');

if(!isset($_SESSION))  session_start();
$rodb=new mysqli("localhost","roprotokol","","roprotokol");

if ($rodb->connect_errno) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
 
if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}

    $s="SELECT DestID as id, Navn as name, Meter as distance, Gennemsnitlig_varighed_Normal AS duration, Gennemsnitlig_varighed_Instruktion AS duration_instruction
    FROM Destination ORDER BY name";


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
