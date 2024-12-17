<?php
$format="xlsx";
$y=date('Y');

if (isset($_GET["format"]) && $_GET["format"]=="xlsx") {
    $format="xlsx";
}
if (isset($_GET["year"]) {
        if ($_GET["year"] <0) {
            $y=$y+(int)($_GET["year"]);
        } else {
            $y=((int)($_GET["year"]));
        }
}


set_include_path(get_include_path().':..');
include("../inc/common.php");
$s=
"SELECT COUNT('x'), dayofweek,timeofday,team, week(start_time) w
FROM team_participation
WHERE YEAR(start_time)=?
GROUP BY w, team
ORDER BY w
";
$stmt=$rodb->prepare($s) or dbErr($rodb,$res,"prep");

$stmt->bind_param("i", $y) || dbErr($rodb,$res,"bind");
$stmt->execute() || dbErr($rodb,$res,"exe");
$result=$stmt->get_result() or dbErr($rodb,$res,"Error in team stats query: ");

process($result,$format,"gymnastikQ$quarter","_auto");
