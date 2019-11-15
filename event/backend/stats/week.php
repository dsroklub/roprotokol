<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
require_once("utils.php");

$report_name="ugegraf";

$l = $rodb->query("SELECT SUM(requirement) AS 'timer' FROM worker WHERE assigner='vedligehold'")->fetch_assoc()["timer"] or dbErr($rodb,$res,"weeksum $q");

$s="SELECT WEEK(start_time) as uge, SUM(hours) as timer, GROUP_CONCAT(DISTINCT boat)  as både FROM worklog GROUP BY uge ORDER BY start_time";

$result = $rodb->query($s) or dbErr($rodb,$res,"week $q");
$data=[];
$weeks=[];
$left=[];
$gns=[];
$numweeks=$leftweeks=14+52-41+1;
$avg=round($l/$numweeks,1);
while ($row = $result->fetch_assoc()) {
    $weeks[$row["uge"]]=round($row["timer"],1);
    $l=$l-$row["timer"];
    $left[$row["uge"]]=round($l,1);
    $gns[$row["uge"]]=$avg;
    $leftweeks--;
    $w=$row["uge"];
}
$w++;
$perweek=$l/$leftweeks;
$weeks[$w]=0;
$left[$w]=round(($leftweeks-1)*$perweek,1);
$gns[$w]=round($perweek,1);
$w++;
while ($w > 42 || $w<13 ) {
    $weeks[$w]=0;
    $left[$w]=null;
    $gns[$w]=null;
    $w++;
    if ($w>52) $w=1;
}
$left[$w]=0;
$gns[$w]=round($perweek,1);

$wd=[
    "labels"=>array_keys($weeks),
    "series"=>["gjort","tilbage","gns"],
    "data"=>[array_values($weeks),array_values($left),array_values($gns)],
    "override"=>[
        ["yAxisID"=>"y1","type"=>"bar","label"=>"ugetimer"],
        ["yAxisID"=>"y2","type"=>"line","label"=>"tilbage","spanGaps"=>true],
        ["yAxisID"=>"y1","type"=>"line","label"=>"gennemsnit","spanGaps"=>true,"lineTension"=>0]
    ],
    "options"=>[
        "scales"=>[
            "yAxes"=>[
                ["id"=>"y1","position"=>"left","type"=>"linear"],
                ["id"=>"y2","position"=>"right","type"=>"linear","display"=>true]
            ]
        ]
    ]
];

echo json_encode($wd,JSON_PRETTY_PRINT);
$rodb->close();
