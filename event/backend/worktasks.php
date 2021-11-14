<?php
include("inc/common.php");
$s="SELECT * FROM worktasks";
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"mystats $q");
$stmt->execute() || dbErr($rodb,$res,"fetch");
$result= $stmt->get_result();
output_rows($result);
$stmt->close(); 
$rodb->close();
