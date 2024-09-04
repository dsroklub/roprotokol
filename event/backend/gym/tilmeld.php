<?php
include("../inc/common.php");
include("../inc/utils.php");
header('Content-type: text/html;charset=utf-8');
header('Cache-Control: max-age=10000');

echo "<html>
<head>
 <meta charset=\"UTF-8\">
 <link rel=\"gymico\" sizes=\"180x180\" href=\"gymico.png\">
 <link rel=\"stylesheet\" href=\"gym.css\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>DSR gymnastik tilmeld</title>
  <link rel=\"manifest\" href=\"/backend/event/gym/manifest.json\">
</head>
<body>

<button id=\"addHomeScreen\">Tilføj som app på telefonskærm</button>\n

<script>


if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/backend/event/gym/service-worker.js').then(() => {console.log('Service Worker registered'); }).catch(error => { console.error('Service Worker registration failed:', error);});
}


let deferredPrompt;
const addBtn = document.getElementById(\"addHomeScreen\");
addBtn.style.display = 'none';

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  addBtn.style.display = 'block';
  addBtn.addEventListener('click', () => {
    addBtn.style.display = 'none';
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then((choiceResult) => {
      if (choiceResult.outcome === 'accepted') {
      console.log('User accepted the A2HS prompt');} else { console.log('User dismissed the A2HS prompt');}
      deferredPrompt = null;
    });
  });
});

window.addEventListener('appinstalled', () => {
  console.log('PWA was installed');
});
</script>
";

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}
$res=array ("status" => "ok");
$weekdays=["Søndag","Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag"];
$weekday=$weekdays[date("w")];

if (isset($_GET["hold"])) {
    error_log(" arg= ".$_GET["hold"]);
    $a=explode("::",$_GET["hold"],2);
    $hold=$a[0];
    $holdtid=$a[1];
    $stmt = $rodb->prepare(
        "INSERT IGNORE INTO team_participation (team, dayofweek,timeofday, member_id, start_time, classdate,created_by)
         SELECT team.name, team.dayofweek, team.timeofday, Member.id,NOW(), CURDATE(), Member.id
         FROM team,Member
         WHERE team.name=? AND team.dayofweek=? AND
         MemberID=? AND team.timeofday=?") or dbErr($rodb,$res,"gym individual registerer"
         );

    $stmt->bind_param('ssss',
                      $hold,
                      $weekday,
                      $cuser,
                      $holdtid
    ) || dbErr($rodb,$res,"gym registerer simple");
    $stmt->execute() || dbErr($rodb,$res,"gym simple registerer exe");
    invalidate("gym");
}



$s="SELECT name as hold,team.dayofweek,team.timeofday,teacher, ts.start_time started
 FROM team LEFT JOIN (SELECT start_time,team FROM team_participation,Member WHERE MemberId=? AND Member.id=member_id AND classdate=CURDATE()) as ts  ON ts.team=team.name
WHERE team.dayofweek=?
";

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"tilmeld P");
$stmt->bind_param("ss", $cuser,$weekday) || dbErr($rodb,$res,"tilmeld B");
$stmt->execute() || dbErr($rodb,$res,"MEMBER SETTING");

$result=$stmt->get_result() or dbErr($rodb,$res,"Error in tilmeld query: ");
echo "\n<H1>DSR ${weekday}shold for $cuser</H1>";
echo "\n<form  action=\"tilmeld.php\"><table>";
echo "<tr><th>Hold</th> <th>Start</th> <th>Underviser</th><th>vælg</th></tr>\n";
$even=true;
foreach ($result as $hold) {
    echo "<tr class=\"" . ($even?"even":"odd") ."\">";
    echo "\n  <td>".$hold["hold"]."</td><td>".$hold["timeofday"]."</td><td> ".$hold["teacher"]."</td>";
    if (empty($hold["started"])) {
        echo '<td><input type="radio" value="'.$hold["hold"]."::".$hold["timeofday"].'" name="hold">  </td>';
    } else {
        echo '<td>du deltager </td>';
    }
    //    echo json_encode($row,JSON_PRETTY_PRINT,JSON_FORCE_OBJECT);
    echo "\n  </tr>\n";
$even=!$even;

}

echo '<tr><td span="3"><input type="submit" value="Deltag"></td></tr>';
echo "</table></form>";

invalidate("settings");
?>

</body></html>
