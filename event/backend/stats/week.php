<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
require_once("utils.php");

$report_name="ugegraf";
$s="SELECT WEEK(start_time) as uge, SUM(hours) as timer, GROUP_CONCAT(DISTINCT boat)  as både FROM worklog GROUP BY uge ORDER BY uge";

$result = $rodb->query($s) or dbErr($rodb,$res,"week $q");
$data=[];
$weeks=[];
while ($row = $result->fetch_assoc()) {
    $weeks[$row["uge"]]=$row["timer"];
}
$wd=["labels"=>array_keys($weeks), series=>["alt"],data=>[array_values($weeks)]];

echo json_encode($wd);
$rodb->close();
