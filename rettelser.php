<?php // asp2php (vbscript) converted on Sun Aug 11 21:06:43 2013
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$xdebug_default_enable=1;

include "DatabaseINC.php";
 ?>

<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</HEAD>
<BODY bgproperties="fixed" background="images/baggrund.jpg">
<?php 

$RType=$_GET["RType"];
$TurID=$_GET["TurID"];
$MemberID=$_GET{"MemberID"};
$Postback=$_GET{"Postback"}; //1=Gem data

$TurID=$_GET["turid"];
$Ud=$_GET["ud"];
$Ind=$_GET["ind"];
$Destination=$_GET["Destination"];
$Baad=$_GET["båd"];
$Turtype=$_GET["turtype"];
$Distance=$_GET["distance"];

$Opendatabase;
/*WriteHit("Rettelser",$Rtype);*/
$closedatabase;

switch ($Postback)
{
  case 1:
//Forudfyldelse af turdata efter at der er clicket på Slå op-knappen
    if (${"SlaaOpKnap"}=="Slå op")
    {


      if (!is_numeric($turid)) {
        $turid=-1;
      } 

      $opendatabase;


      $sql="SELECT Tur.TurID, Tur.Ud, Tur.Ind, Tur.Destination, Båd.Navn AS Båd, TurType.Navn AS Turtype, TurDeltager.Navn AS Turdeltager, Tur.Meter AS Distance ".
        "FROM TurType INNER JOIN (Båd RIGHT JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Båd.BådID = Tur.FK_BådID) ON TurType.TurTypeID = Tur.FK_TurTypeID ".
        "WHERE Tur.TurID = ".$Turid;

      $MyRS=$db->execute($SQL);

      if ($myrs->eof) {
        $TurID="Det indtastede TurID kendes ikke.";
      } else {
        $TurID=$Myrs["turid"];
        $Ud=$myrs["ud"];
        $Ind=$myrs["ind"];
        $Destination=$myrs["Destination"];
        $Baad=$myrs["båd"];
        $Turtype=$myrs["turtype"];
        $Distance=$myrs["distance"]/1000;

        $c1=0;
        while(!($myrs->eof))
        {

          $Turdeltager[$c1]=$myrs["turdeltager"];
          $c1=$c1+1;
          $myrs->movenext;
        } 
      } 


      $myrs->close;
      $closedatabase;

    }
      else
    {
//Hvis der submittes normalt
      $Errorstate=0;
      $opendatabase;
      $SletTur=$Change2DBBool[${"SletTur"}];

      $TurID=${"TurID"};
      if (!is_numeric($turid)) {
        $Errorstate=1;
      } 

      $Baad=$removeping[${"Baad"}];
      $Ud=$removeping[${"ud"}];
      $Ind=$removeping[${"ind"}];
      $Destination=$removeping[${"Destination"}];
      $Distance=$removeping[${"Distance"}];
      $TurType=$removeping[${"TurType"}];
      $Aarsag=$removeping[${"Aarsag"}];
      $Indberetter=$removeping[${"Indberetter"}];
      $Mail=$removeping[${"Mail"}];
      $TurDeltager[0]=$removeping[${"TurDeltager0"}];
      $TurDeltager[1]=$removeping[${"TurDeltager1"}];
      $TurDeltager[2]=$removeping[${"TurDeltager2"}];
      $TurDeltager[3]=$removeping[${"TurDeltager3"}];
      $TurDeltager[4]=$removeping[${"TurDeltager4"}];
      $TurDeltager[5]=$removeping[${"TurDeltager5"}];
      $TurDeltager[6]=$removeping[${"TurDeltager6"}];
      $TurDeltager[7]=$removeping[${"TurDeltager7"}];
      $TurDeltager[8]=$removeping[${"TurDeltager8"}];
      $TurDeltager[9]=$removeping[${"TurDeltager9"}];

      $SQL="INSERT INTO Fejl_tur (SletTur, TurID, Båd, Ud, Ind, Destination, Distance, TurType, TurDeltager0, TurDeltager1, TurDeltager2, TurDeltager3, TurDeltager4, TurDeltager5, TurDeltager6, TurDeltager7, TurDeltager8, TurDeltager9, [Årsag til rettelsen], Indberetter, Mail) ".
        "VALUES (".$Slettur.", '".$TurID."', '".$Baad."', '".$Ud."', '".$Ind."', '".$Destination."', '".$Distance."', '".$TurType."', '".$TurDeltager[0]."', '".$TurDeltager[1]."', '".$TurDeltager[2]."', '".$TurDeltager[3]."', '".$TurDeltager[4]."', '".$TurDeltager[5]."', '".$TurDeltager[6]."', '".$TurDeltager[7]."', '".$TurDeltager[8]."', '".$TurDeltager[9]."', '".$Aarsag."', '".$Indberetter."', '".$Mail."')";

      if ($errorstate==0)
      {

        $db->execute($SQL);
?>
				<table align="center" width="95%">
					<tr>
						<td>
							<H2>Indberetning af rettelser til registreret tur</H2>
						</td>
					</tr>
					<tr>
						<td>
							Din indrapportering er nu gemt.<br><br>
						</td>
					</tr>
				</table>
			<?php 
        $Rtype=99;
      } else
	if ($errorstate==1)
	  {
	    $TurID="TurID skal angives.";
	  } 
      $closedatabase;
    } 

    break;
  case 2:

    if (${"SlaaOpKnap"}=="Slå op")
    {


      if (!is_numeric($MemberID))
      {
        $MemberID=-1;
      } 

      $opendatabase;
      $SQL="SELECT * from QRYMedlemsrettigheder Where MemberID=".$MemberID;
      $MyRS=$db->query($SQL);

      if ($myrs->eof) {
        $Membername="Det indtastede medlemsnummer er ukendt.";
      } else {
        $Roret=$Change2ASPBool[$MyRS["Roret"]];
        $TeoretiskStyrmandKursus=$Change2ASPBool[$MyRS["TeoretiskStyrmandKursus"]];
        $Styrmand=$Change2ASPBool[$MyRS["Styrmand"]];
        $Langtur=$Change2ASPBool[$MyRS["Langtur"]];
        $Ormen=$Change2ASPBool[$MyRS["Ormen"]];
        $Svava=$Change2ASPBool[$MyRS["Svava"]];
        $Sculler=$Change2ASPBool[$MyRS["Sculler"]];
        $Kajak=$Change2ASPBool[$MyRS["Kajak"]];
        $RoInstruktoer=$Change2ASPBool[$MyRS["RoInstruktoer"]];
        $StyrmandInstruktoer=$Change2ASPBool[$MyRS["StyrmandInstruktoer"]];
        $ScullerInstruktoer=$Change2ASPBool[$MyRS["ScullerInstruktoer"]];
        $KajakInstruktoer=$Change2ASPBool[$MyRS["KajakInstruktoer"]];
        $Kaproer=$Change2ASPBool[$MyRS["Kaproer"]];
        $Motorbaad=$Change2ASPBool[$MyRS["Motorboat"]];

        $Myrs=$db->query($SQL);

        $Membername=$myrs["Fornavn"]." ".$myrs["Efternavn"];
      } 
      $myrs->close;
      $closedatabase;
    } else {
//Normalt submit
      $opendatabase;

      $Membername=$removeping[${"Membername"}];
      $Roret=$Change2DBBool[${"Roret"}];
      $TeoretiskStyrmandKursus=$Change2DBBool[${"TeoretiskStyrmandKursus"}];
      $Styrmand=$Change2DBBool[${"Styrmand"}];
      $Langtur=$Change2DBBool[${"Langtur"}];
      $Ormen=$Change2DBBool[${"Ormen"}];
      $Svava=$Change2DBBool[${"Svava"}];
      $Sculler=$Change2DBBool[${"Sculler"}];
      $Kajak=$Change2DBBool[${"Kajak"}];
      $RoInstruktoer=$Change2DBBool[${"RoInstruktoer"}];
      $StyrmandInstruktoer=$Change2DBBool[${"StyrmandInstruktoer"}];
      $ScullerInstruktoer=$Change2DBBool[${"ScullerInstruktoer"}];
      $KajakInstruktoer=$Change2DBBool[${"KajakInstruktoer"}];
      $Kaproer=$Change2DBBool[${"Kaproer"}];
      $Motorbaad=$Change2DBBool[${"Motorboat"}];
      $Indberetter=$removeping[${"Indberetter"}];
      $Mail=$removeping[${"Mail"}];
      $Kommentar=$removeping[${"Kommentar"}];

      $SQL="INSERT INTO Fejl_tblMembersSportData (Navn, Kommentar, Indberetter, Mail, MemberID, Roret, TeoretiskStyrmandKursus, Styrmand, Langtur, Ormen, Svava, Sculler, Kajak, RoInstruktoer, StyrmandInstruktoer, ScullerInstruktoer, KajakInstruktoer, Kaproer, Motorboat) ".
        "VALUES ('".$Membername."', '".$Kommentar."', '".$Indberetter."', '".$Mail."', ".$MemberID.", ".$Roret.", ".$TeoretiskStyrmandKursus.", ".$Styrmand.", ".$Langtur.", ".$Ormen.", ".$Svava.", ".$Sculler.", ".$Kajak.", ".$RoInstruktoer.", ".$StyrmandInstruktoer.", ".$ScullerInstruktoer.", ".$KajakInstruktoer.", ".$Kaproer.", ".$Motorbaad.")";

//Response.Write sql

      $db->execute($SQL);

      $closedatabase;
?>
			<table align="center" width="95%">
				<tr>
					<td>
						<H2>Indberetning af rettelser til rettighedsbilledet</H2>
					</td>
				</tr>
				<tr>
					<td>
						Din indrapportering er nu gemt.<br><br>
					</td>
				</tr>
			</table>
		<?php 

      $Rtype=99;
    } 


    break;
  case 3:

    $opendatabase;
    $Beskrivelse=$removeping[${"Beskrivelse"}];
    $Dato=$removeping[${"dato"}];
    $Indberetter=$removeping[${"indberetter"}];
    $Mail=$removeping[${"mail"}];

    $SQL="INSERT INTO Fejl_system (Beskrivelse, Dato, Indberetter, Mail) ".
      "VALUES ('".$Beskrivelse."', '".$Dato."', '".$Indberetter."', '".$Mail."')";

    $db->execute($SQL);
    $closedatabase;
?>
		<table align="center" width="95%">
			<tr>
				<td>
					<H2>Indberetning af systemfejl eller kommentar</H2>
				</td>
			</tr>
			<tr>
				<td>
					Din indrapportering er nu gemt.<br><br>Tak for hjælpen!
				</td>
			</tr>
		</table>
	<?php 
    $Rtype=99;
    break;
} 

switch ($RType)
{
  case 0:

?>
		<table class="rostat" width=50%>
		<tr>
			<th class="tablehead">Rettelser</th>
		</tr>
		<tr>
			<td width="95%" class=DetailInfo valign="top"><center>
				<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="95%">
					<tr>
						<td>
							<br>
							Vælg fra venstremenuen, hvilken type rettelse, du ønsker at foretage.
							<br>
							<br>
							<b>Tur</b><br>
							Hvis du har rettelser til en tur, der allerede er indskrevet, skal de indtastes her. Du kan ligeledes anmode om at få en tur slettet.			
							<br>
							<br>
							<b>Medlemsoplysninger</b><br>
							Hvis du har rettelser til de medlemsoplysninger, du står opført med i roprotokollen, kan du give meddelelse til instruktionschefen og kontingentkassereren via denne formular.
							<br>
							<br>
							<b>Fejl og kommentarer</b><br>
							Hvis du opdager en fejl i roprotokollen, kan du indberette den her. Hvis du har kommentarer eller ændringsforslag, er det ligeledes her, de skal indberettes.
							<br><br>
							<u>Bemærk</u> at rettelserne først slår igennem i roprotokollen, når en systemadministrator har godkendt dem. 
							<br>
							<br>
						</td>
					</tr>	
				</table>
			</center></td>
		</tr>
		</table>

	<?php 
    break;
  case 1:

?>
		<table align="center"><tr><td>
		<H2>Rettelser til tur</H2>
		Her kan du indtaste rettelser til ture, der allerede er indskrevet. <br>Du kan indtaste et TurID og derefter clicke 'Slå op' for at fremsøge den tur, du ønsker at rette.<br>
			<form method="post" action="rettelser.php?Postback=1&Rtype=1" id=form1 name=form1>
			<TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
				<tr>
				  <td width="32%">TurID</td>
				  <td width="68%"><input type="text" name="TurID" size="36" value="<?php     echo $TurID;?>">
				  <input type=submit value="Slå op" name="SlaaOpKnap">
				  </td>
				</tr>
				<tr>
				  <td width="32%" valign=top>Slet tur</td>
				  <td width="68%"><input type="checkbox" name="SletTur" value="on"></td>
				</tr>
				<tr>
				  <td width="32%">Båd</td>
				  <td width="68%"><input type="text" name="Baad" size="36" value="<?php     echo $Baad;?>"></td>
				</tr>
				<tr>
				  <td width="32%">Ud</td>
				  <td width="68%"><input type="text" name="Ud" size="36" value="<?php     echo $Ud;?>"></td>
				</tr>	
				<tr>
				  <td width="32%">Ind</td>
				  <td width="68%"><input type="text" name="Ind" size="36" value="<?php     echo $Ind;?>"></td>
				</tr>					
				<tr>
				  <td width="32%">Destination</td>
				  <td width="68%"><input type="text" name="Destination" size="36" value="<?php     echo $Destination;?>"></td>
				</tr>					
				<tr>
				  <td width="32%">Distance (km)</td>
				  <td width="68%"><input type="text" name="Distance" size="36" value="<?php     echo $Distance;?>"></td>
				</tr>	
				<tr>
				  <td width="32%">Turtype</td>
				  <td width="68%"><input type="text" name="Turtype" size="36" value="<?php     echo $Turtype;?>"></td>
				</tr>				
	<?php     for ($c1=0; $c1<=8; $c1=$c1+1)
    {?>
				<tr>
				  <td width="32%">Turdeltager <?php       echo $c1;?></td>
				  <td width="68%"><input type="text" name="Turdeltager<?php       echo $c1;?>" size="36" value="<?php       echo $Turdeltager[$c1];?>"></td>
				</tr>
	<?php 
    }?>
				<tr>
				  <td width="32%" valign=top>Årsag til rettelsen</td>
				  <td width="68%"><textarea rows="3" name="Aarsag" cols="44"></textarea></td>
				</tr>
				<tr>
				  <td width="32%">Indberettet af</td>
				  <td width="68%"><input type="text" name="Indberetter" size="36" value=""></td>
				</tr>
				<tr>
				  <td width="32%">Mailadresse / telefon</td>
				  <td width="68%"><input type="text" name="Mail" size="36" value=""></td>
				</tr>
				<tr>
				  <td width="32%"></td>
				  <td width="68%"><br><INPUT id=SubmitTur type=Submit value="Send" name=SubmitTur></td>
				</tr>
			</table>
			</form>
	<?php 
    break;
  case 2:

?>
		<table align="center"><tr><td>
		<H2>Rettelser til rettighedsbilledet</H2>
		Her kan du indtaste rettelser til de rettigheder, du står opført med i roprotokollen.<br>
			<form method="post" action="rettelser.php?Postback=2&Rtype=2" id=form1 name=form1>
			<TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
			<tr>
				<td width="32%" valign=top>Medlemsnummer</td>
				<td width="68%"><input type="text" name="MemberID" size="8" value="<?php     echo $memberID;?>">
				<input type=submit value="Slå op" name="SlaaOpKnap">
			</td>
			</tr>
			<tr>
				<td width="32%" valign=top>Navn</td>
				<td width="68%" valign=top><INPUT size=36 id="Membername" name="Membername" Value="<?php     echo $MemberName;?>"></INPUT></td>
			</tr>
			<tr><td width="32%" valign=top>Roret</td><td width="68%">
              <input type="checkbox" name="Roret" <?php     echo $Roret;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Instruktør</td><td width="68%">
              <input type="checkbox" name="RoInstruktoer" <?php     echo $RoInstruktoer;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Styrmandsinstruktør</td><td width="68%">
              <input type="checkbox" name="StyrmandInstruktoer" <?php     echo $StyrmandInstruktoer;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Gig 8'er styrmand</td><td width="68%">
              <input type="checkbox" name="Ormen" <?php     echo $Ormen;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Teor. styrmandskursus</td><td width="68%">
              <input type="checkbox" name="TeoretiskStyrmandKursus" <?php     echo $TeoretiskStyrmandKursus;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Styrmand</td><td width="68%">
              <input type="checkbox" name="Styrmand" <?php     echo $Styrmand;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Langtursstyrmand</td><td width="68%">
              <input type="checkbox" name="Langtur" <?php     echo $Langtur;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Kaproer</td><td width="68%">
              <input type="checkbox" name="Kaproer" <?php     echo $Kaproer;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Scullerinstruktør</td><td width="68%">
              <input type="checkbox" name="ScullerInstruktoer" <?php     echo $ScullerInstruktoer;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Svavaret</td><td width="68%">
              <input type="checkbox" name="Svava" <?php     echo $Svava;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Scullerret</td><td width="68%">
              <input type="checkbox" name="Sculler" <?php     echo $Sculler;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Kajakret</td><td width="68%">
              <input type="checkbox" name="Kajak" <?php     echo $Kajak;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Kajakinstruktør</td><td width="68%">
              <input type="checkbox" name="KajakInstruktoer" <?php     echo $KajakInstruktoer;?> value="ON"></td></tr>
			<tr><td width="32%" valign=top>Motorbådsret</td><td width="68%">
              <input type="checkbox" name="Motorbaad" <?php     echo $Motorbaad;?> value="ON"></td></tr>				<tr>
				  <td width="32%" valign=top>Kommentar</td>
				  <td width="68%"><textarea rows="3" name="Kommentar" cols="44"></textarea></td>
				</tr>
				<tr>
				  <td width="32%">Indberettet af</td>
				  <td width="68%"><input type="text" name="Indberetter" size="36" value="<?php     echo $MemberName;?>"></td>
				</tr>
				<tr>
				  <td width="32%">Mailadresse / telefon</td>
				  <td width="68%"><input type="text" name="Mail" size="36" value=""></td>
				</tr>

			<tr>
				  <td width="32%"></td>
				  <td width="68%"><br><INPUT id=SubmitRettighed type=Submit value="Send" name=SubmitRettighed></td>
			</tr>
			</table>
			</form>
	<?php 
    break;
  case 3:

?>
		<table align="center"><tr><td>
		<H2>Indrapporter systemfejl eller skriv en kommentar</H2>
		Her kan du indberette fejl eller skrive en kommentar, hvis du har ændringsforslag til roprotokollen.<br><br>
			<form method="post" action="rettelser.php?Postback=3&Rtype=3" id=form1 name=form1>
			<TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
				<tr>
				  <td width="32%" valign=top>Beskrivelse</td>
				  <td width="68%"><textarea rows="11" name="Beskrivelse" cols="44"></textarea></td>
				</tr>
				<tr>
				  <td width="32%">Hvornår opstod fejlen?</td>
				  <td width="68%"><input type="text" name="Dato" size="36" value="<?php     echo time();?>"></td>
				</tr>
				<tr>
				  <td width="32%">Indberettet af</td>
				  <td width="68%"><input type="text" name="Indberetter" size="36"></td>
				</tr>
				<tr>
				  <td width="32%">Mailadresse / telefon</td>
				  <td width="68%"><input type="text" name="Mail" size="36"></td>
				</tr>
				<tr>
				  <td width="32%"></td>
				  <td width="68%"><br><INPUT id=SubmitComment type=Submit value="Send" name=SubmitComment></td>
				</tr>
			</table>
			</form>
	<?php 
    break;
} //RType
?>
</BODY>
</HTML>
