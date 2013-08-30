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
    case "M+b�d":


?>
	
	<h2>Motion+ b�d</h2><br>

	Du er ved at udskrive <?       echo $boatname;?>, og har valgt en anden turtype end 'Motion+'. Bem�rk at <?       echo $boatname;?> kun m� anvendes efter aftale med motion+ tr�neren.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "Fafner":


?>
	
	<h2>B�den kan v�re reserveret</h2><br>
	Du er ved at udskrive <?       echo $boatname;?>. Bem�rk at <?       echo $boatname;?> kan v�re reserveret. Du skal derfor kontrollere listen, der ligger i b�den.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "InstruktionMO":


?>
	
	<h2>Bem�rk instruktion kl. 17.15</h2><br>
	Bem�rk, at b�den skal v�re tilbage senest kl. 17.00 af hensyn til instruktionen.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "InstruktionLS":


?>
	
	<h2>Bem�rk instruktion kl. 13.00</h2><br>
	Bem�rk, at b�den skal v�re tilbage senest kl. 13.00 af hensyn til instruktionen.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "Rovagt":


?>
	<h2>Bem�rk rovagtsordning kl. 17.15</h2><br>
	Bem�rk, at b�den skal v�re tilbage senest kl. 17.00 af hensyn til rovagtsordningen.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "RettighederB�d":


?>
	
	<h2>Manglende rettigheder</h2><br>
	Du har udskrevet en b�d af typen <?       echo $Boattype;?>. If�lge medlemsdatabasen har du  ikke ret til at fungere som styrmand p� denne b�d.<br><br>
	Hvis dette er en fejl, bedes du indberette fejlen via roprotokollens <b><a href="rettelser.php?Rtype=2">rettelsesformular</a></b>, hvorefter det vil blive rettet hurtigst muligt.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.
	<? 

      break;
    case "RettighederKajak":


?>
	
	<h2>Manglende rettigheder</h2><br>
	Du har udskrevet en kajak, men du har ikke kajakret. Hvis der er tale om kajakinstruktion, skal du angive det ved udskrivning.<br><br>
	Hvis dette er en fejl, bedes du indberette fejlen via roprotokollens <b><a href="rettelser.php?Rtype=2">rettelsesformular</a></b>, hvorefter det vil blive rettet hurtigst muligt.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.
	<? 

      break;
    case "RettighederInst":


?>
	
	<h2>Manglende rettigheder</h2><br>
	Du har udskrevet en tur af typen instruktion. If�lge klubbens rettighedsdatabase har du ikke instrukt�rret. Bem�rk, at man skal have instrukt�rret, for at kunne fungere som styrmand p� denne tur.<br><br>
	Hvis dette er en fejl, bedes du indberette fejlen via roprotokollens <b><a href="rettelser.php?Rtype=2">rettelsesformular</a></b>, hvorefter det vil blive rettet hurtigst muligt.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.
	<? 

      break;
    case "RettighederLtur":


?>
	
	<h2>Manglende rettigheder</h2><br>
	Du har udskrevet en tur af typen langtur. If�lge klubbens rettighedsdatabase har du ikke langtursstyrmandsret, og derfor har du ikke ret til at fungere som styrmand p� denne tur.<br><br>
	Hvis dette er en fejl, bedes du indberette fejlen via roprotokollens <b><a href="rettelser.php?Rtype=2">rettelsesformular</a></b>, hvorefter det vil blive rettet hurtigst muligt.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.
	<? 

      break;
    case "Sent ind":


?>
	
	<h2>Sen tur</h2><br>
	Husk at lukke portene og slukke lyset n�r turen er afsluttet.<br><br>
	
	<? 

      break;
    case "Reserveret":


?>
	
	<h2>B�den er reserveret</h2><br>
	Du har udskrevet en b�d, der er reserveret af bestyrelsen. Bem�rk, at det ikke tilladt at udskrive reserverede b�de uden aftale med bestyrelsen.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "Sv�rt skadet":


?>
	
	<h2>B�den er sv�rt skadet</h2><br>
	Du har udskrevet en b�d, der er sv�rt skadet. Det er ikke tilladt at benytte b�de, der st�r opf�rt som sv�rt skadede.<br><br>
	Hvis du forts�tter, vil turen blive udskrevet.<br><br>
	<? 

      break;
    case "Ingen styrmand":


?>
	
	<h2>Der er ikke angivet en  styrmand</h2><br>
	Du skal angive en ansvarlig styrmand, n�r du udskriver turen. Hvis du er ansvarlig styrmand, og ikke har et medlemsnummer, skal dit navn angives i bem�rkningsfeltet.<br><br>
	
	<? 

      break;
    case "Sidste b�d p� vandet":


?>
	
	<h2>Sidste b�d p� vandet</h2><br>
	Husk at lukke portene og slukke lyset i b�dhallen.<br><br>
	
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

if (${"Forts�t"}=="Forts�t")
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

    $Alerttype="Sidste b�d p� vandet";
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
      $Alerttype="Sv�rt skadet";
    } 
    $rs->movenext;
  } 

  $rs=$db->execute;  $BoatID." AND Repareret Is Null)");
  while(!($rs->eof))
  {

    if ($rs["grad"]==3)
    {
      $Alerttype="Sv�rt skadet";
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

//Check hvis der ikke er skrevet en styrmand p�
  if ($Styrmand=="")
  {
    $Alerttype="Ingen styrmand";
  } 

  ShowAlert($Alerttype);
  if ($alerttype!="")
  {
    $alerttype="x";
  } 

//Check for motion+ b�d
  if ($Turtype!="Motion+tur" && $Turtype!="Konkurrencetur" && ($Boatname=="Hu" || $Boatname=="Hjalte"))
  {
    $Alerttype="M+b�d";
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


//Check for instruktion l�rdag og s�ndag
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
          $Alerttype="RettighederB�d";
        } 
      } 

    } 


//Gig 8-ret
    if ($Boattype=="Gig 8+" && !isset($rs["Ormen"]))
    {
      $Alerttype="RettighederB�d";
    } 

//Scullerret
    if (substr($Boattype,0,7)=="Sculler" && $turtype!="Outrigger/ScullerInstruktion" && !isset($rs["Sculler"]))
    {
      $Alerttype="RettighederB�d";
    } 

//Svavaret
    if ($Boattype=="Svava 1x" && $turtype!="Outrigger/ScullerInstruktion" && !isset($rs["Svava"]))
    {
      $Alerttype="RettighederB�d";
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

//Instrukt�rret
//''if turtype="Instruktion" and isnull(rs("RoInstruktoer")) then Alerttype="RettighederInst"

//Instrukt�rret
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
		<input type="submit" value="Forts�t" id=Forts�t name=Forts�t>
		<input type="submit" value="Annuller" id=Annuller name=Annuller>
		<input type="hidden" value="<? echo $TurID;?>" name="TurID">
		</form>
		</td>
	</tr>
</table>

</BODY>
</HTML>
