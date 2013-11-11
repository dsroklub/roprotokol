<?
if(!isset($_SESSION))  session_start();
//  session_register("SorterEfter_session");
//  session_register("SortOrder_session");
include "DatabaseINC.php";
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
$RostatAction=get_key("rostataction",$_GET,"Rank");
$IDAction=get_key("ID",$_GET);
$Subgroup=get_key("subgroup",$_GET);
$ShowRettelseSpecs=get_key("ShowRettelseSpecs",$_GET);
$Medlid=get_key("medlid",$_GET);


// $opendatabase;
error_log(" rostat sg=".$Subgroup. "  RA =".$RostatAction,0);
switch ($Subgroup) {
  case "alle":
    //    WriteHit("Rostatistik");

    $s="SELECT Rostat_Rangorden.Medlemsnr AS Medlemsnr, Rostat_Rangorden.Navn, CONCAT(Rostat_Rangorden.Rodistance, 'km') AS Afstand, Rostat_Rangorden.Antal_ture AS Ture, CONCAT(Rostat_Rangorden.Gennemsnitslaengde,'km') AS Gennemsnit, If(HasRedKey=True,If(Vintervedligehold.Season=Year(Now()),1,0),0) AS RedKeyStatus FROM (Rostat_Rangorden INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Rostat_Rangorden.MedlemID = TurDeltager.FK_MedlemID) LEFT JOIN Vintervedligehold ON Rostat_Rangorden.Medlemsnr = Vintervedligehold.Medlemsnr GROUP BY Rostat_Rangorden.Medlemsnr, Rostat_Rangorden.Navn, CONCAT(Rostat_Rangorden.Rodistance,' km'), Rostat_Rangorden.Antal_ture, CONCAT(Rostat_Rangorden.Gennemsnitslaengde, ' km'), If(HasRedKey=True,If(Vintervedligehold.Season=Year(Now()),1,0),0), Rostat_Rangorden.Rodistance ";

    break;
  case "rob�dsroere":

    //    WriteHit("Rostatistik rob�dsroere");

    $s="SELECT Rostat_Rangorden_rob�d.Medlemsnr as Medlemsnr, Rostat_Rangorden_rob�d.Navn, CONCAT(Rostat_Rangorden_rob�d.Rodistance,' km') AS Afstand, Rostat_Rangorden_rob�d.Antal_ture as Ture, CONCAT(Rostat_Rangorden_rob�d.Gennemsnitslaengde,'km') AS Gennemsnit ";
    $s=$s."FROM Rostat_Rangorden_rob�d INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Rostat_Rangorden_rob�d.MedlemID = TurDeltager.FK_MedlemID ";
    $s=$s."GROUP BY Rostat_Rangorden_rob�d.Medlemsnr, Rostat_Rangorden_rob�d.Navn, CONCAT(Rostat_Rangorden_rob�d.Rodistance. 'km'), Rostat_Rangorden_rob�d.Antal_ture, CONCAT(Rostat_Rangorden_rob�d.Gennemsnitslaengde, ' km'), Rostat_Rangorden_rob�d.Rodistance ";

//s=  "SELECT [QRY Rostat_Rangorden].Medlemsnr_ AS Medlemsnr, [QRY Rostat_Rangorden].Navn, [QRY Rostat_Rangorden].[Rodistance] & "" km"" AS Afstand, [QRY Rostat_Rangorden].[Antal ture] AS Ture, [QRY Rostat_Rangorden].[Gennemsnitsl�ngde] & "" km"" AS Gennemsnit " & _
//	"FROM (B�dKategori INNER JOIN Gruppe ON B�dKategori.B�dKategoriID = Gruppe.FK_B�dKategoriID) INNER JOIN (B�d INNER JOIN ([QRY Rostat_Rangorden] INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON [QRY Rostat_Rangorden].MedlemID = TurDeltager.FK_MedlemID) ON B�d.B�dID = Tur.FK_B�dID) ON Gruppe.GruppeID = B�d.FK_GruppeID " & _
//	"WHERE (((B�dKategori.Navn)=""Almindelig roning"")) " & _
//	"GROUP BY [QRY Rostat_Rangorden].Medlemsnr_, [QRY Rostat_Rangorden].Navn, [QRY Rostat_Rangorden].[Rodistance] & "" km"", [QRY Rostat_Rangorden].[Antal ture], [QRY Rostat_Rangorden].[Gennemsnitsl�ngde] & "" km"", [QRY Rostat_Rangorden].Rodistance "

    break;
  case "kajakroere":


    // WriteHit("Rostatistik kajakroere");

    $s="SELECT Rostat_Rangorden_Kajak.Medlemsnr as Medlemsnr, Rostat_Rangorden_Kajak.Navn, CONCAT(Rostat_Rangorden_Kajak.Rodistance, ' km') AS Afstand, Rostat_Rangorden_Kajak.Antal_ture as Ture, CONCAT(Rostat_Rangorden_Kajak.Gennemsnitslaengde, ' km') AS Gennemsnit ";
    $s=$s."FROM Rostat_Rangorden_Kajak INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Rostat_Rangorden_Kajak.MedlemID = TurDeltager.FK_MedlemID ";
    $s=$s."GROUP BY Rostat_Rangorden_Kajak.Medlemsnr, Rostat_Rangorden_Kajak.Navn, CONCAT(Rostat_Rangorden_Kajak.Rodistance, ' km'), Rostat_Rangorden_Kajak.Antal_ture, CONCAT(Rostat_Rangorden_Kajak.Gennemsnitslaengde, ' km'), Rostat_Rangorden_Kajak.Rodistance ";

//s=  "SELECT [QRY Rostat_Rangorden].Medlemsnr_ AS Medlemsnr, [QRY Rostat_Rangorden].Navn, [QRY Rostat_Rangorden].[Rodistance] & "" km"" AS Afstand, [QRY Rostat_Rangorden].[Antal ture] AS Ture, [QRY Rostat_Rangorden].[Gennemsnitsl�ngde] & "" km"" AS Gennemsnit " & _
//	"FROM (B�dKategori INNER JOIN Gruppe ON B�dKategori.B�dKategoriID = Gruppe.FK_B�dKategoriID) INNER JOIN (B�d INNER JOIN ([QRY Rostat_Rangorden] INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON [QRY Rostat_Rangorden].MedlemID = TurDeltager.FK_MedlemID) ON B�d.B�dID = Tur.FK_B�dID) ON Gruppe.GruppeID = B�d.FK_GruppeID " & _
//	"WHERE (((B�dKategori.Navn)=""kajakker"")) " & _
//	"GROUP BY [QRY Rostat_Rangorden].Medlemsnr_, [QRY Rostat_Rangorden].Navn, [QRY Rostat_Rangorden].[Rodistance] & "" km"", [QRY Rostat_Rangorden].[Antal ture], [QRY Rostat_Rangorden].[Gennemsnitsl�ngde] & "" km"", [QRY Rostat_Rangorden].Rodistance "
    break;
  case "kaniner":


    // WriteHit("Rostatistik kaniner");

    $s="SELECT Rostat_Kaniner.Medlemsnr AS Medlemsnr, Rostat_Kaniner.Navn, CONCAT(Rostat_Kaniner.Rodistance, ' km') AS Afstand, Rostat_Kaniner.Antal_ture AS Ture, CONCAT(Rostat_Kaniner.Gennemsnitslaengde, ' km') AS Gennemsnit ".
      "FROM Rostat_Kaniner ".
      "GROUP BY Rostat_Kaniner.Medlemsnr, Rostat_Kaniner.Navn, CONCAT(Rostat_Kaniner.Rodistance, ' km'), Rostat_Kaniner.Antal_ture, CONCAT(Rostat_Kaniner.Gennemsnitslaengde, ' km'), Rostat_Kaniner.Rodistance ";

    break;
} 
// $closedatabase;

switch ($RostatAction) {
  case "Rank":
    if ($_SESSION['SorterEfter']=="Rank") {
      $_SESSION['SortOrder']=$_SESSION['SortOrder'] ^ 1;
    } else {
      $_SESSION['SorterEfter']="Rank";
      $_SESSION['SortOrder']=1;
    } 


    $direction=";";
    if ($_SESSION['SortOrder']==1) {
      $direction=" DESC;";
    } 

    switch ($Subgroup) {
      case "alle":
        $s=$s."ORDER BY Rostat_Rangorden.Rodistance".$direction;
        break;
      case "kajakroere":
        $s=$s."ORDER BY Rostat_Rangorden_Kajak.Rodistance".$direction;
        break;
      case "rob�dsroere":

        $s=$s."ORDER BY Rostat_Rangorden_Rob�d.Rodistance".$direction;
        break;
      case "kaniner":

        $s=$s."ORDER BY Rostat_Rangorden.Rodistance".$direction;
        break;
    } 

    $db=OpenDatabase();

  error_log(" rDSRSQL=".$s,0);

    $rs=$db->query($s);
    if ($rs) {
      Rostatistik($rs,$Subgroup,$Medlid);
      // NEL $rs->close;
    } else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
    } 

    //    $closedatabase;

    break;
  case "MembrID":
//Sorter efter medlemsnr.
    if ($_SESSION['SorterEfter']=="MembrID")
    {

      $_SESSION['SortOrder']=$_SESSION['SortOrder'] ^ 1;
    }
      else
    {

      $_SESSION['SorterEfter']="MembrID";
      $_SESSION['SortOrder']=1;
    } 


    $Direction=";";
    if ($_SESSION['SortOrder']==0)
    {
      $Direction=" DESC;";
    } 

    $s=$s."ORDER BY Rostat_Rangorden.Medlemsnr".$Direction;

    $opendatabase;
    $rs=$db->query($s);
    if ($rs) {
      Rostatistik($rs,$Subgroup);
    } else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
    } 

    $rs->close;
    // NEL $closedatabase;

    break;
  case "Name":
//Sorter efter navn
    if ($_SESSION['SorterEfter']=="Name") {

      $_SESSION['SortOrder']=$_SESSION['SortOrder'] ^ 1;
    } else {
      $_SESSION['SorterEfter']="Name";
      $_SESSION['SortOrder']=1;
    } 

    $Direction=";";
    if ($_SESSION['SortOrder']==0)  {
      $Direction=" DESC;";
    } 

    $s=$s."ORDER BY Rostat_Rangorden.Navn".$Direction;
    $db=OpenDatabase();
    $rs=$db->query($s);
    if ($rs) {
      Rostatistik($rs,$Subgroup);
    } else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
    } 

    $rs->close;
    //$closedatabase;

    break;
  case "Trips":
//Sorter efter antal ture
    if ($_SESSION['SorterEfter']=="Trips") {
      $_SESSION['SortOrder']=$_SESSION['SortOrder'] ^ 1;
    } else {
      $_SESSION['SorterEfter']="Trips";
      $_SESSION['SortOrder']=1;
    } 

    $Direction=";";
    if ($_SESSION['SortOrder']==0)  {
      $Direction=" DESC;";
    } 

    $s=$s."ORDER BY Count(Tur.TurID)".$Direction;

    $opendatabase;
    $rs=$db->query($s);
    if ($rs) {
      Rostatistik($rs,$Subgroup);
    } else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
    } 
    $rs->close;
    //NEL $closedatabase;

    break;
  case "AvrLen":
//Sorter efter gennemsnitsl�ngde
    if ($_SESSION['SorterEfter']=="AvrLen") {
      $_SESSION['SortOrder']=$_SESSION['SortOrder'] ^ 1;
    } else {
      $_SESSION['SorterEfter']="AvrLen";
      $_SESSION['SortOrder']=1;
    } 

    $Direction=";";
    if ($_SESSION['SortOrder']==0) {
      $Direction=" DESC;";
    } 

    $s=$s."ORDER BY (Int([Rodistance]/Count(Tur.TurID)*10)/10)".$Direction;

    $db=OpenDatabase();

    $rs=$db->query($s);
    if ($rs) {
      Rostatistik($rs,$Subgroup,$Medlid);
    } else {
?>
		<STRONG>Ingen data at vise</STRONG>
		<? 
    } 

    $rs->close;
    //$closedatabase;
    break;
  case "ShowTrips":


    $_SESSION['SorterEfter']="Nothing";

    $db=OpenDatabase();
    WriteHit("Roerens ture".$IDAction);

    $s="SELECT Medlem.fornavn, Medlem.efternavn, medlem.medlemsnr ".
      "FROM Medlem ".
      "WHERE (((Medlem.Medlemsnr)='".$IDAction."'));";

    $rs=$db->query($s);
    print "<h2>".$rs["fornavn"]." ".$rs["efternavn"]." (".$idaction.")</h2	>";
    print "<h3>Rettigheder</h3>";

    $s="SELECT Medlemsrettigheder.MemberID, Medlemsrettigheder.* ".
      "FROM Medlemsrettigheder ".
      "WHERE (((Medlemsrettigheder.MemberID)=".$IDAction."));";

    $rs=$db->query($s);
    if ($rs) {
      $RoerensStamdata[$RS];
    } else {
?>
		<STRONG>Der er endnu ikke registreret rettigheder for denne roer.</STRONG>
		<? 
    } 

    $rs->close;

    $s="SELECT TurType.Navn AS Turtype, Count(Rostat_KunTureMedStyrmand.TurID) AS Antal_ture, Sum(Meter/1000) AS Rodistance, Int([Rodistance]/[Antal Ture]*10)/10 as Gennemsnit ";
    $s=$s."FROM (Rostat_KunTureMedStyrmand LEFT JOIN (Medlem RIGHT JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Rostat_KunTureMedStyrmand.TurID = TurDeltager.FK_TurID) LEFT JOIN TurType ON Rostat_KunTureMedStyrmand.FK_TurTypeID = TurType.TurTypeID ";
    $s=$s."GROUP BY TurType.Navn, Medlem.Medlemsnr HAVING (((Medlem.Medlemsnr)=\"".$IDAction."\"));";

    $rs=$db->query($s);
    if ($rs) {
      $Turtypesummary[$RS];
    } 
    $rs->close;
?><br><? 

    $s="SELECT Tur.TurID, B�d.Navn AS B�d, Tur.Destination, Tur.OprettetDato as Oprettet, Int([Meter]*10)/10000 & \" km\" AS Turl�ngde, Medlem.Medlemsnr, [Fornavn] & \" \" & [Efternavn] AS Navn ";
    $s=$s."FROM B�d RIGHT JOIN (Medlem INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON B�d.B�dID = Tur.FK_B�dID ";
    $s=$s."WHERE (((Medlem.Medlemsnr)=\"".$IDAction."\")) ORDER BY Tur.TurID DESC;";

    $rs=$db->query($s);
    if ($rs) {
      $Turoversigt[$RS];
    } else {
?>
		<STRONG>Ingen ture at vise</STRONG>
		<? 
    } 
    $rs->close;
?><br><? 
    $s="SELECT * from AlleTurRettelser WHERE Medlemsnr=\"".$IDAction."\";";

    $rs=$db->query($s);
    if ($rs) {
      RoerensRettedeTure($RS,$ShowRettelseSpecs);
    } 
    $rs->close;
    //$closedatabase;

    break;
  case "TripSpecs":

    $_SESSION['SorterEfter']="Nothing";

    $s="SELECT Tur.TurID, B�d.Navn AS B�d, Tur.Destination, Tur.OprettetDato, Int([Meter]*10)/10000 AS Turl�ngde, Medlem.Medlemsnr, [Fornavn] & \" \" & [Efternavn] AS Navn, IIf([Plads]=0,\" (Styrmand)\",\"\") AS Styrmand, TurType.Navn AS Turtype, Tur.Ud, Tur.Ind, Tur.ForvInd, Tur.Kommentar ";
    $s=$s."FROM TurType RIGHT JOIN (B�d RIGHT JOIN (Medlem INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON B�d.B�dID = Tur.FK_B�dID) ON TurType.TurTypeID = Tur.FK_TurTypeID ";
    $s=$s."WHERE (((Tur.TurID)=".$IDAction.")) ORDER BY IIf([Plads]=0,\"(Styrmand)\",\"\") DESC";

    $db=OpenDatabase();
    WriteHit("Turspecifikation ". $IdAction);
    $rs=$db->query($s);
    if ($rs) {
      $Turspecifikation[$RS];
    } else {
?>
		<STRONG>Ingen data at vise</STRONG>
		<? 
    } 

    $rs->close;
    // NEL $closedatabase;
    break;
  case "BoatSpecs":

    $_SESSION['SorterEfter']="Nothing";

    $db=OpenDatabase();
    WriteHit("B�dens ture".$IdAction."'");

    $s="SELECT Tur.TurID, TurDeltager.Navn AS Styrmand, Tur.Destination, Tur.OprettetDato, Int([Meter]*10)/10000 & \" km\" AS Turl�ngde, Gruppe.Navn AS B�dtype, B�d.Navn, Medlem.Medlemsnr ";
    $s=$s."FROM Medlem INNER JOIN ((Gruppe INNER JOIN (B�d INNER JOIN Tur ON B�d.B�dID = Tur.FK_B�dID) ON Gruppe.GruppeID = B�d.FK_GruppeID) INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID ";
    $s=$s."WHERE (((B�d.Navn)=\"".$Idaction."\") AND ((TurDeltager.Plads)=0)) ORDER BY Tur.OprettetDato DESC;";

    $rs=$db->query($s);
    if ($rs) {
      BaadensTuroversigt($RS);
    } else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
    } 

    $rs->close;

    $s="SELECT TurType.Navn AS Turtype, Count(Rostat_KunTureMedStyrmand.TurID) AS Antal_ture, Sum([Meter]/1000) AS Rodistance, Int(Rodistance/Antal_Ture*10)/10 as Gennemsnit ";
    $s=$s."FROM (Rostat_KunTureMedStyrmand LEFT JOIN B�d ON Rostat_KunTureMedStyrmand.FK_B�dID = B�d.B�dID) LEFT JOIN TurType ON Rostat_KunTureMedStyrmand.FK_TurTypeID = TurType.TurTypeID ";
    $s=$s."GROUP BY TurType.Navn, B�d.Navn HAVING (((B�d.Navn)=\"".$Idaction."\"));";

    $rs=$db->query($s);
    if ($rs) {
      $Turtypesummary[$RS];
    } else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
    } 
    $rs->close;
    $s="SELECT B�d.Navn, Skade.OprettetDato AS Anmeldt, Skade.Grad, Skade.Beskrivelse, Skade.Repareret ";
    $s=$s."FROM B�d INNER JOIN Skade ON B�d.B�dID = Skade.FK_B�dID ";
    $s=$s."WHERE (((B�d.Navn)=\"".$Idaction."\")) ORDER BY Skade.OprettetDato DESC;";

    $rs=$db->query($s);
    if (!$rs->eof) {
      $Skadesoversigt($RS);
    } 
    $rs->close;

    // NEL $closedatabase;

    break;
  default:

    print "Forkert rostat parameter (".$RostatAction.")";
    exit();

    break;
} 

?>
</td></tr></table>
</P>
</BODY>
</HTML>
