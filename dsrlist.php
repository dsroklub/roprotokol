<?
if(!isset($_SESSION))  session_start();
include "DatabaseINC.php";
// FIXME
//  session_register("SortOrder_session");
//  session_register("SortOrder_boat_session");
?>

<HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>
<BODY bgproperties="fixed" background="images/baggrund.jpg">
<P>
<table align="center"><tr><td>
<? 
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
// Liste over b�de (til printer)
//s="SELECT Gruppe.Navn,qAvailableboats.Navn, qAvailableboats.qBoatsOnWater.FK_B�dID, qAvailableboats.qBoatsSkadet.FK_B�dID, qAvailableboats.grad "
//s=s & " FROM qAvailableboats INNER JOIN Gruppe ON qAvailableboats.FK_GruppeID = Gruppe.GruppeID"
//s=S & " ORDER BY qAvailableboats.FK_GruppeID, qAvailableboats.Navn;"

    break;
  case 6:

    $s="select * from QRYDagensRoere";
    $sql2="Select * from QBoatsonwater";
//QRYDagensRoere

    break;
  case 7:
//Vis �vrig statistik
    $s="SELECT * FROM B�d";

    break;
  case 8:
    header("Location: "."rostat.php?rostataction=Rank&ID=0&subgroup=rob�dsroere");
    break;
  case 9:
    header("Location: "."rostat.php?rostataction=Rank&ID=0&subgroup=kajakroere");
    break;
  case 10:
    header("Location: "."rostatboat.php?rostataction=RankB&ID=0&subgroup=rob�de");
    break;
  case 11:
    header("Location: "."rostatboat.php?rostataction=RankB&ID=0&subgroup=kajakker");
    break;
  case 12:
    header("Location: "."rostat.php?rostataction=Rank&ID=0&subgroup=kaniner");
    break;
  case 13:


    $WhichYear=strftime("%Y",time());

    $s="SELECT Sum([Meter]\\1000) AS Km FROM Gruppe RIGHT JOIN (B�d RIGHT JOIN (Medlem LEFT JOIN (Tur RIGHT JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON B�d.B�dID = Tur.FK_B�dID) ON Gruppe.GruppeID = B�d.FK_GruppeID WHERE (((Tur.Ud)<=\"01-\" & Month(Now()) & \"-\" & Year(Now())) AND ((Year([ud]))=".$Whichyear.") AND ((Gruppe.FK_B�dKategoriID)=2)) GROUP BY Medlem.MedlemID;";

    break;
  default:
    print "Wrong type (".$action.")";
    exit();
    break;
} 

error_log(" DSRLIST s now=".$s,0);

if ($s != "") {
  $s2=arget("Boatid");
  if ($s2 != "")  {
    $s=$s." where fk_b�did=".$s2;
  } 
  //  $opendatabase();
  $rs=$db->query($s);
  if ($sql2!="")  {
    $rs2=$db->query($sql2);
  } 

  if (!$rs->eof) {
    switch ($action) {
      case 1:

	// $WriteHit("B�de p� vandet");
        BaadePaaVandet($RS);
        break;
      case 2:

        // $WriteHit"Skadede b�de"
        $SkadedeBaade[$RS];
        break;
      case 3:
      case 8:
      case 9:

        //$WriteHit"Rostatistik"
        $Rostatistik[$rs];
        break;
      case 4:
      case 10:
      case 11:

        // $WriteHit"B�dstatistik"
	error_log(" DO Baadstat ",0);
        $Baadstatistik($RS,$subgroup);
        break;
      case 6:

        // $WriteHit"Dagens ture"
?>
			<h3>F�lgende b�de er p� vandet</h3>
			<? 
        $BaadePaaVandet[$RS2];
?>
			<h3>Dagens ture</h3>
			<? 
        $DagensTure[$RS];
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
		<STRONG>Ingen b�de at vise</STRONG>
		<? 
  } 
} 

//NEL RM $rs->close();
//if ($sql2!="") {
//  $rs2->close();
//} 

//$closedatabase();
?>
</td></tr></table>
</P>
</BODY>
</HTML>
