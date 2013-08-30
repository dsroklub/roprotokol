<? // asp2php (vbscript) converted on Sun Aug 11 21:21:03 2013
 ?>
<!-- #include file="databaseINC.php" -->

<HTML>
<HEAD>
<META NAME="GENERATOR" Content="Microsoft FrontPage 5.0">
<link rel="stylesheet" type="text/css" href="../roprotokol.css">
</HEAD>
<BODY bgproperties="fixed" background="images/baggrund.jpg">

<? 
function ShowAlert($Alerttype)
{
  extract($GLOBALS);

  switch ($AlertType)
  {
    case "M+båd":


?>
	
	<h2>Motion+ båd</h2><br>

	Du er ved at udskrive <?       echo $boatname;?>, og har valgt en anden turtype end 'Motion+'. Bemærk at <?       echo $boatname;?> kun må anvendes efter aftale med motion+ træneren.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "Fafner":


?>
	
	<h2>Båden kan være reserveret</h2><br>
	Du er ved at udskrive <?       echo $boatname;?>. Bemærk at <?       echo $boatname;?> kan være reserveret. Du skal derfor kontrollere listen, der ligger i båden.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "InstruktionMO":


?>
	
	<h2>Bemærk instruktion kl. 17.15</h2><br>
	Bemærk, at båden skal være tilbage senest kl. 17.00 af hensyn til instruktionen.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "InstruktionLS":


?>
	
	<h2>Bemærk instruktion kl. 13.00</h2><br>
	Bemærk, at båden skal være tilbage senest kl. 13.00 af hensyn til instruktionen.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "Rovagt":


?>
	<h2>Bemærk rovagtsordning kl. 17.15</h2><br>
	Bemærk, at båden skal være tilbage senest kl. 17.00 af hensyn til rovagtsordningen.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "RettighederBåd":


?>
	
	<h2>Manglende rettigheder</h2><br>
	Du har udskrevet en båd af typen <?       echo $Boattype;?>. Ifølge medlemsdatabasen har du  ikke ret til at fungere som styrmand på denne båd.<br><br>
	Hvis dette er en fejl, bedes du indberette fejlen via roprotokollens <b><a href="rettelser.php?Rtype=2">rettelsesformular</a></b>, hvorefter det vil blive rettet hurtigst muligt.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.
	<? 

      break;
    case "RettighederKajak":


?>
	
	<h2>Manglende rettigheder</h2><br>
	Du har udskrevet en kajak, men du har ikke kajakret. Hvis der er tale om kajakinstruktion, skal du angive det ved udskrivning.<br><br>
	Hvis dette er en fejl, bedes du indberette fejlen via roprotokollens <b><a href="rettelser.php?Rtype=2">rettelsesformular</a></b>, hvorefter det vil blive rettet hurtigst muligt.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.
	<? 

      break;
    case "RettighederInst":


?>
	
	<h2>Manglende rettigheder</h2><br>
	Du har udskrevet en tur af typen instruktion. Ifølge klubbens rettighedsdatabase har du ikke instruktørret. Bemærk, at man skal have instruktørret, for at kunne fungere som styrmand på denne tur.<br><br>
	Hvis dette er en fejl, bedes du indberette fejlen via roprotokollens <b><a href="rettelser.php?Rtype=2">rettelsesformular</a></b>, hvorefter det vil blive rettet hurtigst muligt.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.
	<? 

      break;
    case "RettighederLtur":


?>
	
	<h2>Manglende rettigheder</h2><br>
	Du har udskrevet en tur af typen langtur. Ifølge klubbens rettighedsdatabase har du ikke langtursstyrmandsret, og derfor har du ikke ret til at fungere som styrmand på denne tur.<br><br>
	Hvis dette er en fejl, bedes du indberette fejlen via roprotokollens <b><a href="rettelser.php?Rtype=2">rettelsesformular</a></b>, hvorefter det vil blive rettet hurtigst muligt.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.
	<? 

      break;
    case "Sent ind":


?>
	
	<h2>Sen tur</h2><br>
	Husk at lukke portene og slukke lyset når turen er afsluttet.<br><br>
	
	<? 

      break;
    case "Reserveret":


?>
	
	<h2>Båden er reserveret</h2><br>
	Du har udskrevet en båd, der er reserveret af bestyrelsen. Bemærk, at det ikke tilladt at udskrive reserverede både uden aftale med bestyrelsen.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "Svært skadet":


?>
	
	<h2>Båden er svært skadet</h2><br>
	Du har udskrevet en båd, der er svært skadet. Det er ikke tilladt at benytte både, der står opført som svært skadede.<br><br>
	Hvis du fortsætter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "Ingen styrmand":


?>
	
	<h2>Der er ikke angivet en  styrmand</h2><br>
	Du skal angive en ansvarlig styrmand, når du udskriver turen. Hvis du er ansvarlig styrmand, og ikke har et medlemsnummer, skal dit navn angives i bemærkningsfeltet.<br><br>
	
	<? 

      break;
    case "Sidste båd på vandet":


?>
	
	<h2>Sidste båd på vandet</h2><br>
	Husk at lukke portene og slukke lyset i bådhallen.<br><br>
	
	<? 

      break;
  } 

  return $function_ret;
} 
?>

<table>
	<tr>
		<td valign="top">
			<img  border="0" src="images/icon_alert.gif">
		</td>
		<td width=90%>
<? 

$BoatID=${"BoatID"};
$Udtid=${"Udtid"};
$TurtypeID=${"Turtype"};
$Styrmand=${"Styrmand"};
$TurID=${"TurID"};
$Forvind=${"forvind"};
$Action=${"Action"};

if (${"Fortsæt"}=="Fortsæt")
{


  header("Location: "."dsrboats.php");

} 


$opendatabase;

if (${"Annuller"}=="Annuller")
{


  $db->execute("DELETE Tur.* FROM Tur WHERE TurID=".$TurID);
  $db->execute("DELETE Turdeltager.* FROM Turdeltager WHERE FK_TurID=".$TurID);

  $closedatabase;

  header("Location: "."dsrboats.php");

} 


if ($Action=="Indskrivning")
{


  $rs=$db->execute;
  if ($rs->eof)
  {

    $Alerttype="Sidste båd på vandet";
    ShowAlert($Alerttype);
  }
    else
  {

    header("Location: "."dsrboats.php");
  } 


}
  else
{


  $rs=$db->execute;  $BoatID);
  $Boatname=$Rs["Navn"];

  $rs=$db->execute;  $BoatID);
  $Boattype=$rs["navn"];

  $rs=$db->execute;  $TurtypeID);
  $Turtype=$Rs["Navn"];

  $rs=$db->execute;  $BoatID." AND Repareret Is Null)");
  while(!($rs->eof))
  {

    if ($rs["grad"]==3)
    {
      $Alerttype="Svært skadet";
    } 
    $rs->movenext;
  } 

  $rs=$db->execute;  $BoatID." AND Repareret Is Null)");
  while(!($rs->eof))
  {

    if ($rs["grad"]==3)
    {
      $Alerttype="Svært skadet";
    } 
    $rs->movenext;
  } 

  $rs=$db->execute;  $BoatID.")");
  if (!$rs->eof)
  {
    $Alerttype="Reserveret";
  } 

//QBoatsReserveret

  $Ugedag=strftime("%w",$udtid)+1;

//Check hvis der ikke er skrevet en styrmand på
  if ($Styrmand=="")
  {
    $Alerttype="Ingen styrmand";
  } 

  ShowAlert($Alerttype);
  if ($alerttype!="")
  {
    $alerttype="x";
  } 

//Check for motion+ båd
  if ($Turtype!="Motion+tur" && $Turtype!="Konkurrencetur" && ($Boatname=="Hu" || $Boatname=="Hjalte"))
  {
    $Alerttype="M+båd";
  } 

//Check for Fafner
  if (strftime("%m",$udtid)>=4 && strftime("%m",$udtid)<11)
  {

    if (($Ugedag==2 || $ugedag==4) && (strftime("%H",$udtid)>=16 && strftime("%H",$udtid)<18))
    {

      if ($Boatname=="Fafner")
      {
        $Alerttype="Fafner";
      } 
    } 

  } 


  ShowAlert($Alerttype);
  if ($alerttype!="")
  {
    $alerttype="x";
  } 

//Check for instruktion mandag og onsdag
  if (strftime("%m",$udtid)>=4 && strftime("%m",$udtid)<9)
  {

    if (($Ugedag==1 || $Ugedag==3) && (strftime("%H",$udtid)>=14 && strftime("%H",$udtid)<17) && $turtype!="Instruktion")
    {

      if ($boattype=="Inrigger 4+" || substr($boattype,0,3)=="gig")
      {

        if ($boatname!="Balder")
        {
          $Alerttype="InstruktionMO";
        } 
      } 

      if ($Boattype=="Inrigger 2+" && ($Boatname=="Frigg" || $Boatname=="Loke"))
      {
        $Alerttype="InstruktionMO";
      } 
    } 

  } 


//Check for instruktion lørdag og søndag
  if (strftime("%m",$udtid)>=4 && strftime("%m",$udtid)<7)
  {

    if ($ugedag>5 && strftime("%H",$udtid)>=10 && strftime("%H",$udtid)<13 && $turtype!="Instruktion")
    {

      if ($boattype=="Inrigger 4+" || substr($boattype,0,3)=="gig")
      {

        if ($boatname!="Balder")
        {
          $Alerttype="InstruktionLS";
        } 
      } 

      if ($Boattype=="Inrigger 2+" && ($Boatname=="Frigg" || $Boatname=="Loke"))
      {
        $Alerttype="InstruktionLS";
      } 
    } 

  } 


//Check for rovagt tirsdag og torsdag
  if (strftime("%m",$udtid)>=4 && strftime("%m",$udtid)<11)
  {

    if (($Ugedag==2 || $ugedag==4) && (strftime("%H",$udtid)>=14 && strftime("%H",$udtid)<17))
    {

      if (substr($boattype,0,3)=="Inr" || substr($boattype,0,3)=="gig")
      {

        if ($turtype!="Motion+tur" && $Boatname!="Fafner")
        {
          $Alerttype="Rovagt";
        } 
      } 

    } 

  } 


//Check for om det er en sen tur
  if (strftime("%H",$forvind)>21 || strftime("%H",$udtid)>20)
  {
    $Alerttype="Sent ind";
  } 

  ShowAlert($Alerttype);
  if ($alerttype!="")
  {
    $alerttype="x";
  } 

//Check roerens rettigheder

  if ($styrmand!="" && is_numeric($styrmand))
  {


    $rs=$db->execute;    $Styrmand);

//Normal styrmandsret
    if (substr($Boattype,0,3)=="Inr" || substr($Boattype,0,3)=="Gig")
    {

      if ($turtype!="Styrmandsinstruktion" && $turtype!="Racerkanin")
      {

        if (!isset($rs["styrmand"]))
        {
          $Alerttype="RettighederBåd";
        } 
      } 

    } 


//Gig 8-ret
    if ($Boattype=="Gig 8+" && !isset($rs["Ormen"]))
    {
      $Alerttype="RettighederBåd";
    } 

//Scullerret
    if (substr($Boattype,0,7)=="Sculler" && $turtype!="Outrigger/ScullerInstruktion" && !isset($rs["Sculler"]))
    {
      $Alerttype="RettighederBåd";
    } 

//Svavaret
    if ($Boattype=="Svava 1x" && $turtype!="Outrigger/ScullerInstruktion" && !isset($rs["Svava"]))
    {
      $Alerttype="RettighederBåd";
    } 

//Kajakret 1'er kajak
    if ($Boattype=="Kajak 1" && !isset($rs["Kajak"]) && $turtype!="Instruktion")
    {
      $Alerttype="RettighederKajak";
    } 

//Kajakret 2'er kajak
    if ($Boattype=="Kajak 2" && !isset($rs["Kajak"]) && $turtype!="Instruktion")
    {
      $Alerttype="RettighederKajak";
    } 

    ShowAlert($Alerttype);
    if ($alerttype!="")
    {
      $alerttype="x";
    } 

//Instruktørret
//''if turtype="Instruktion" and isnull(rs("RoInstruktoer")) then Alerttype="RettighederInst"

//Instruktørret
    if ($turtype=="Langtur" && !isset($rs["Langtur"]))
    {
      $Alerttype="RettighederLtur";
    } 

    ShowAlert($Alerttype);
    if ($alerttype!="")
    {
      $alerttype="x";
    } 

  } 


  if ($alerttype=="")
  {
    header("Location: "."dsrboats.php");
  } 

} 



$closedatabase;


?>
		<form action="showalert.php" id=form1 name=form1>
		<input type="submit" value="Fortsæt" id=Fortsæt name=Fortsæt>
		<input type="submit" value="Annuller" id=Annuller name=Annuller>
		<input type="hidden" value="<? echo $TurID;?>" name="TurID">
		</form>
		</td>
	</tr>
</table>

</BODY>
</HTML>
