<?php
include("../inc/common.php");
include("../inc/utils.php");
$s="SELECT name,selected FROM reservation_configuration ORDER by name";
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"get rc");
$stmt->execute();
$result= $stmt->get_result() or dbErr($rodb,$res,"GET res conf");
output_rows($result);
$rodb->close();
