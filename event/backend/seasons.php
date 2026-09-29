<?php
include("inc/common.php");
include("inc/utils.php");
header('Content-type: application/json');

$season=$rodb->query(
    "SELECT season,summer_start,summer_end
    FROM season
    WHERE season >= YEAR(NOW())
    ORDER BY season");
    process($season,"xlsx","sæsondatoer",true);
$rodb->close();
                     

