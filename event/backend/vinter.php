<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
$s="
"

    echo '[';
    $first=1;
    while ($row = $result->fetch_assoc()) {
        if ($first) $first=0; else echo ",\n";
        echo $row["json"];
    }
    echo ']';
