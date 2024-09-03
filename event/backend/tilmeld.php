<?php
include("inc/common.php");
include("inc/utils.php");
        header('Content-type: text/html');

echo "<html>";

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
$res=array ("status" => "ok");
$weekdays=["Søndag","Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag"];
$weekday=$weekdays[date("w")];

if ($_GET["hold"]) {
    $a=explode("::",$_GET["hold"],1);
    $hold=$a[0];
    $holdtid=$a[1];
    $stmt = $rodb->prepare(
        "INSERT INTO team_participation (team, dayofweek,timeofday,member_id, start_time, classdate)
         SELECT team.name,team.dayofweek,team.dayofweek,id,NOW(),CURDATE() FROM Team,Member
         WHERE team.name=? AND team.dayofweek=?
         MemberID=? AND team.timeofday=?") or dbErr($rodb,$res,"gym individual registerer");

    $stmt->bind_param('ssss',
                      $hold,
                      $weekday,
                      $holdtid,
                      $reg->member->id
    ) || dbErr($rodb,$res,"gym registerer");
    $stmt->execute() || dbErr($rodb,$res,"gym registerer exe");
    invalidate("gym");
}



$s="SELECT name as hold,team.dayofweek,team.timeofday,teacher, ts.start_time started
 FROM team LEFT JOIN (SELECT start_time,team FROM team_participation,Member WHERE MemberId=? AND Member.id=member_id) as ts  ON ts.team=team.name
WHERE team.dayofweek=?
";

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"tilmeld P");
$stmt->bind_param("ss", $cuser,$weekday) || dbErr($rodb,$res,"tilmeld B");
$stmt->execute() || dbErr($rodb,$res,"MEMBER SETTING");

$result=$stmt->get_result() or dbErr($rodb,$res,"Error in tilmeld query: ");
echo "\n<H1>${weekday} Hold</H1>";
echo "\n<form  action=\"tilmeld.php\"><table>";
echo "<tr><th>Hold</th> <th>Start</th> <th>Underviser</th></tr>\n";
foreach ($result as $hold) {
    echo "<tr>";
    echo "\n  <td>".$hold["hold"]."</td><td>".$hold["timeofday"]." ".$hold["teacher"]."</td>";
    if (empty($hold["started"])) {
        echo '<td><input type="radio" value="'.$hold["hold"]."::".$hold["timeofday"].'" name="hold">  </td>';
    }
    //    echo json_encode($row,JSON_PRETTY_PRINT,JSON_FORCE_OBJECT);
    echo "\n  </tr>\n";

}

echo '<tr><td></td><td></td><td></td><td><input type="submit" value="Deltag"></td></tr>';
echo "</table></form>";

invalidate("settings");
?>

</html>
