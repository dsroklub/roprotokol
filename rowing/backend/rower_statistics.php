<?php
include("inc/common.php");

$boatclause="";
$boatclause="AND ((BoatType.boat_class) !='motor')";
if (isset($_GET["boattype"])) {
    $boattype=$_GET["boattype"];
    if (empty($boattype) or $boattype=="any") {
        $boatclause="AND ((BoatType.boat_class) !='motor')";
    } elseif ($boattype=="kayak") {
        $boatclause="AND ((BoatType.Category)=1) AND ((BoatType.boat_class) !='motor')";
    } elseif ($boattype=="rowboat") {
        $boatclause="AND ((BoatType.Category)=2) AND ((BoatType.boat_class) !='motor')";
    } elseif ($boattype=="motor") {
        $boatclause="AND ((BoatType.boat_class)='motor')";
    } else {
        error_log('unknown boattype: '.$boattype);
        echo "unknown boattype: ".$boattype;
        exit(0);
    }
}

// echo "boats:". $boatclause."\n<br>";
    $s="
SELECT
    JSON_OBJECT(
      'distance',distance,
      'summer',summer,
      'id',id,
      'firstname', firstname,
      'lastname',lastname,
      'wrenches',wrenches,
      'rank',CAST(ROW_NUMBER() OVER ( ORDER BY summer DESC) as UNSIGNED),
      'yrank',CAST(ROW_NUMBER() OVER ( ORDER BY distance DESC) as UNSIGNED)
)
 as json
FROM (
  SELECT
  CAST(Sum(Meter) AS UNSIGNED) AS distance,
  CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer,
  mm.MemberID as id,wrenches,mm.FirstName as firstname,mm.LastName as lastname
  FROM season,BoatType,Trip,TripMember,Boat,
   (SELECT GROUP_CONCAT(distinct mr.argument SEPARATOR ',') as wrenches,id,MemberID,FirstName,LastName FROM Member LEFT JOIN MemberRights mr ON Member.id=mr.member_id and mr.MemberRight='wrench' GROUP BY Member.id) as mm
WHERE
  Trip.id=TripMember.TripID AND
  mm.id=TripMember.member_id AND
  Boat.id=Trip.BoatID AND
  BoatType.Name=Boat.boat_type AND
  season.season=Year(OutTime) AND
 (((Year(OutTime))=?) " . $boatclause .")
  GROUP BY mm.id,mm.MemberID, firstname, lastname) as st
ORDER BY summer,distance DESC
";


if ($sqldebug) echo $s;
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"row stat p");
$stmt->bind_param("i",$season) || dbErr($rodb,$res,"row stat b");
$stmt->execute() || dbErr($rodb,$res,"rower stat");
$result= $stmt->get_result();
echo '[';
$rn=1;
while ($row = $result->fetch_assoc()) {
    if ($rn>1) echo ',';
    echo $row['json'];
    $rn=$rn+1;
}
echo ']';
$rodb->close();
