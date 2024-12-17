<?php
include("../inc/common.php");


$stats=$rodb->query("SELECT DISTINCT name FROM team ORDER by name ASC");
process($stats);
$rodb->close();

