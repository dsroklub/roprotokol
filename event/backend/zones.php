<?php
include("inc/common.php");
include("inc/utils.php");
$zones=$rodb->query("SELECT zone,description FROM zones") or dbErr($rodb,$res,"zones");
process($zones);
$rodb->close();
