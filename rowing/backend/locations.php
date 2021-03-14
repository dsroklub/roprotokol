<?php
include("inc/common.php");
include("inc/utils.php");
$s="SELECT name,description From Locations ORDER by name";
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"get locs");
$stmt->execute();
$result= $stmt->get_result() or dbErr($rodb,$res,"GET locs");
output_rows($result);
$rodb->close();
