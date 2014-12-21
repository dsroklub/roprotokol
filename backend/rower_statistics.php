<?php
include("inc/common.php");

$season=date('Y');


$boatclause="";
if (isset($_GET["boattype"])) {
    $boattype=$_GET["boattype"];
    if ($boattype=="any") {
        $boatclause="";
    } elseif ($boattype=="kayak") {
        $boatclause="AND ((Gruppe.FK_BådKategoriID)=1) ";
    } elseif ($boattype=="rowboat") {
        $boatclause="AND ((Gruppe.FK_BådKategoriID)=2)";
    } else {
        error_log('unknown boattype: '.$boattype);
        echo "unknown boattype: ".$boattype;
        exit(0);
    }
}
// echo "boats:". $boatclause."\n<br>";
//$season='2013';
    $s="SELECT CAST(Sum(Meter) AS UNSIGNED) AS distance ,Medlem.Medlemsnr as id, Medlem.Fornavn as firstname, Medlem.Efternavn as lastname 
    FROM Gruppe,Trip,TripMember,Båd,Medlem 
    WHERE 
      Trip.TripID = TripMember.TripID AND
      Medlem.MedlemID = TripMember.MemberID AND
      Båd.BådID = Trip.BoatID AND     
      Gruppe.GruppeID = Båd.FK_GruppeID AND
      (((Year(OutTime))=".$season.") " . $boatclause .")".
    " GROUP BY Medlem.MedlemID 
    ORDER BY distance desc";


// echo $s;
if ($stmt = $rodb->prepare($s)) {
     $stmt->execute(); 
     $result= $stmt->get_result();
     echo '[';
     $rn=1;
     while ($row = $result->fetch_assoc()) {
         if ($rn>1) echo ',';
         $row['rank']=$rn;
         echo json_encode($row);
         $rn=$rn+1;
     }
     echo ']';     
     $stmt->close(); 
 } 
$rodb->close();
?> 
