<?php
if(!isset($_SESSION))  session_start();
include "DatabaseINC.php";
// FIXME
//  session_register("SortOrder_session");
//  session_register("SortOrder_boat_session");
?>

<HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>
<BODY bgproperties="fixed" background="images/baggrund.jpg">
<P>
<table align="center"><tr><td>
<?php 
      function arget($nm) {
      $rs="";
      if (isset($_GET[$nm])) {
	  $rs=$_GET[$nm];
	}
      return $rs;
    }

$action=arget("action");

$_SESSION['SortOrder']=0;
$_SESSION['SortOrder_boat']=0;

$s="";
$sWater="";
error_log(" DSRLIST action=".$action,0);

switch ($action) {
  case 0:
    header("Location: "."index.html");
    //header("Location: "."startmain.htm");
    break;
  case 1:

    $s="Select * from QBoatsonwater";
    break;
  case 2:

    $s="Select * from qBoatsSkader";
    break;
  case 3:

    header("Location: "."rostat.php?rostataction=Rank&ID=0&subgroup=alle&medlid=".arget("medlid"));
    break;
  case 4:

    header("Location: "."rostatboat.php?rostataction=RankB&ID=0&subgroup=alle");
    break;
  case 5:
// Liste over både (til printer)
//s="SELECT Gruppe.Navn,qAvailableboats.Navn, qAvailableboats.qBoatsOnWater.FK_BådID, qAvailableboats.qBoatsSkadet.FK_BådID, qAvailableboats.grad "
//s=s & " FROM qAvailableboats INNER JOIN Gruppe ON qAvailableboats.FK_GruppeID = Gruppe.GruppeID"
//s=S & " ORDER BY qAvailableboats.FK_GruppeID, qAvailableboats.Navn;"

    break;
  case 6:

    $s="select * from DagensRoere";
    $sWater="Select * from qBoatsOnWaterRoere";
// QRYDagensRoere

    break;
  case 7:
// Vis øvrig statistik
    $s="SELECT * FROM Båd";
    break;
  case 8:
    header("Location: "."rostat.php?rostataction=Rank&ID=0&subgroup=robådsroere");
    break;
  case 9:
    header("Location: "."rostat.php?rostataction=Rank&ID=0&subgroup=kajakroere");
    break;
  case 10:
    header("Location: "."rostatboat.php?rostataction=RankB&ID=0&subgroup=robåde");
    break;
  case 11:
    header("Location: "."rostatboat.php?rostataction=RankB&ID=0&subgroup=kajakker");
    break;
  case 12:
    header("Location: "."rostat.php?rostataction=Rank&ID=0&subgroup=kaniner");
    break;
  case 13:
    $WhichYear=strftime("%Y",time());
    $s="SELECT Sum(Meter/1000) AS Km FROM Gruppe RIGHT JOIN (Båd RIGHT JOIN (Medlem LEFT JOIN (Tur RIGHT JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Båd.BådID = Tur.FK_BådID) ON Gruppe.GruppeID = Båd.FK_GruppeID WHERE (((Tur.Ud)<=\"01-\" & Month(Now()) & \"-\" & Year(Now())) AND ((Year(ud))=".$Whichyear.") AND ((Gruppe.FK_BådKategoriID)=2)) GROUP BY Medlem.MedlemID;";

    break;
default:
	print "Wrong type (".$action.")";
        exit();
        break;
} 

//error_log(" DSRLIST s now=".$s,0);

if ($s != "") {
  $db=OpenDatabase();
  $rs=$db->query($s);
  if ($sWater!="")  {
    $rsWater=$db->query($sWater);
  } 

  if ($rs) {
    switch ($action) {
      case 1:

	// $WriteHit("Både på vandet");
        BaadePaaVandet($rs);
        break;
      case 2:

        // $WriteHit"Skadede både"
        SkadedeBaade($rs);
        break;
      case 3:
      case 8:
      case 9:

        //$WriteHit"Rostatistik"
        Rostatistik($rs,$subgroup);
        break;
      case 4:
      case 10:
      case 11:
	Baadstatistik($rsWater,$subgroup);
        break;
      case 6:

        // $WriteHit"Dagens ture"
?>
	<h3>F&oslash;lgende b&aring;de er p&aring; vandet</h3>

<?php 
     BaadePaaVandet($rsWater);
?>
			<h3>Dagens ture</h3>
<?php 
        $DagensTure[$rs];
        break;
      case 7:
        // $WriteHit"Statistikoversigt"
        $Statistikoversigt;

        break;
      case 13:

        // $WriteHit"DM motionsroning"
        $DMMotionsroning;

        break;
      default:
        $listrs[$rs];
        break;
    } 
  } else {
?>
		<STRONG>Ingen både at vise</STRONG>
		<?php 
  } 
} 
?>
</td></tr></table>
</P>
</BODY>
</HTML>
