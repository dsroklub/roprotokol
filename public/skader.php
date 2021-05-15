<?php
set_include_path(get_include_path().':..');
ini_set('display_errors', 'On');
global $rodb;
$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);
include(__DIR__."/publicutils.php");
$format=$_GET["format"] ?? "xlsx";

$boattype=$_GET["boattype"] ?? "inrigger";
$boattypes=["inrigger"=>"inrigger%","sculler"=>"sculler%","coastal"=>"coastal%","alle"=>"%","kajak"=>"kajak%","gig"=>"gig%"];
if (!isset($boattypes[$boattype])) {
    die ("ukendt bådtype %boattype");
}

$result=$rodb->query("SELECT Boat.Name as båd,Damage.Degree as grad, Damage.description AS skade
FROM Boat,Damage
WHERE Boat.id=Damage.Boat AND Repaired IS NULL AND Boat.boat_type like '".$boattypes[$boattype]."' AND Boat.Decommissioned IS NULL
ORDER BY Boat.Name,grad desc
") or dbErr($rodb,$res,"skader");

process($result,$format,"skader","_auto",$colormap);
