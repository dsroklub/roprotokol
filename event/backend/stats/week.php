<?php
include("../inc/common.php");
require_once("../inc/utils.php");
$lt = $rodb->query("
SELECT IFNULL(SUM(requirement),0) AS 'timer'
FROM worker,Member
WHERE worker.member_id=Member.id AND assigner='vedligehold'
") or dbErr($rodb,$res,"week chart $l");
$l = $lt->fetch_assoc()["timer"];
$w=46;
$s="
SELECT WEEK(start_time) as uge, SUM(hours) as timer, GROUP_CONCAT(DISTINCT boat)  as både
FROM worklog
WHERE $workseason
GROUP BY uge,YEAR(start_time)
ORDER BY YEAR(start_time),uge
";
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
while ( $w > 46 ||  $w<13) {
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
                ["id"=>"y1","position"=>"left","type"=>"linear","scaleLabel"=>["labelString"=>"ugetimer","display"=>true]],
                ["id"=>"y2","position"=>"right","type"=>"linear", "scaleLabel"=>["labelString"=>"sæsontimer","display"=>true]]
            ]
        ]
    ]
];

echo json_encode($wd,JSON_PRETTY_PRINT);
$rodb->close();
