<?
  session_start();
  session_register("BådKategori_session");
?>
<? // asp2php (vbscript) converted on Wed Jul 30 12:05:38 2014
 ?>
<!-- #include file="databaseINC.php" -->
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="roprotokol.css">


  <script language="javascript" src="rslite.js"></script>
  <script language="javascript">
  
  var oInterval = "";
	 // Sætter en timer igang, så siden kan blive opdateret hvert sekund (1000 milisekunder)
	 // kører på clienten
	 function body_onLoad(){
	
      // Create object
      RSLite = new RSLiteObject();
      
      // set the callback 
      RSLite.callback = myCallback;
      // set update interval
      oInterval = window.setInterval("RSLite.call('getvar.asp',whichVar.value);",1000);
    }
    
    // Denne callback rutine kommer alle data tilbage til i response variablen
    // kører på clienten
    function myCallback( response ){            
      var sCookie = response.split(",");
      i=0
      if (sCookie.length>=3)
      {
			do 
				{
				var sCookiePart = sCookie[i]
				if (sCookiePart.length>0) 
				{
					var sCookieColor = sCookie[i+2]
					var sCookieText = sCookie[i+1]
				
					document.getElementById('TD'+sCookiePart).innerHTML=sCookieText 
					document.getElementById('TD'+sCookiePart).style.backgroundColor=sCookieColor 
				}
				
				i=i+3
			}
		while (i < sCookie.length-1)
		}
    }
  </script>
  <title>Vis både</title>
</head>
<body onLoad="body_onLoad();" bgproperties="fixed" background="images/baggrund.jpg">
<? 

$GruppeId=${"gruppeid"};
$ShowType=${"ShowType"}; //Skal der vises tur, skade eller etc.
$ShowID=${"ID"}; //Kommer ind med ID'et for den info, der skal vises

if ($gruppeid=="")
{

  $gruppeid=$_SESSION['BådKategori'];
} 

$_SESSION['BådKategori']=$Gruppeid;
$opendatabase;
?>


<INPUT type=Hidden value=<? echo $Gruppeid;?> name=whichVar>
<table width="100%" class="rostat">
  <tr>
    <th class="tablehead" width="16%">Båd</th>
    <th class="tablehead" width="10%">Niveau</th>
    <th class="tablehead" width="10%">Type</th>
    <th class="tablehead" width="20%">Særligt</th>
    <th class="tablehead" width="8%">Status</th>
    
    <? 
switch ($Showtype)
{
  case "Reservation":

    $Myrs=$db->execute;    $ShowID);

?>
		<th class="tablehead" width="34%">Reservationer - <?     echo $myrs["navn"];?></th>
		<th class="tablehead" width="2%"><a href="dsrkayaks.asp?GruppeID=<?     echo $gruppeID;?>"><img src="images/icon_close.gif" border="0"></a></th>
	<?     $myrs->close;

    break;
  case "Skade":

    $Myrs=$db->execute;    $ShowID);

?>
		<th class="tablehead" width="34%">Aktuelle skader - <?     echo $myrs["navn"];?></th>
		<th class="tablehead" width="2%"><a href="dsrkayaks.asp?GruppeID=<?     echo $gruppeID;?>"><img src="images/icon_close.gif" border="0"></a></th>
	<?     $myrs->close;

    break;
  case "Tur":

    $Myrs=$db->execute;    $ShowID);

?>
		<th class="tablehead" width="34%">Igangværende tur - <?     echo $myrs["navn"];?></th>
		<th class="tablehead" width="2%"><a href="dsrkayaks.asp?GruppeID=<?     echo $gruppeID;?>"><img src="images/icon_close.gif" border="0"></a></th>	
		<? 
    $myrs->close;

    break;
  case "Anvendelse":


?>
		<th class="tablehead" width="34%">Anvendelse</th>
		<th class="tablehead" width="2%"><a href="dsrkayaks.asp?GruppeID=<?     echo $gruppeID;?>"><img src="images/icon_close.gif" border="0"></a></th>	
		<? 

    break;
  default:
?>
		<th class="tablehead" width="36%">Information</th>
		
	<?     break;
} ?>
</tr>
<? 

$LockRemoveInactive;

$s="SELECT Båd.BådID, Båd.Navn, Båd.FK_GruppeID, Kajak_typer.Typenavn, Båd.Anvendelse as AnvID, Kajak_anvendelser.Anvendelse, Båd.Pladser, Båd.Niveau, QBoatsReserveret.FK_BådID, qBoatsOnWater2.FK_BådID, qBoatsSkadet.FK_BådID, qBoatsSkadet.grad, LåsteBåde.locktimeout, qBoatsOnWater2.TurType.Navn AS TurType_navn, qBoatsOnWater2.TurID ".
  "FROM Kajak_typer RIGHT JOIN (Kajak_anvendelser RIGHT JOIN (((QBoatsReserveret RIGHT JOIN (qBoatsSkadet RIGHT JOIN Båd ON qBoatsSkadet.FK_BådID = Båd.BådID) ON QBoatsReserveret.FK_BådID = Båd.BådID) LEFT JOIN LåsteBåde ON Båd.BådID = LåsteBåde.BoatID) LEFT JOIN qBoatsOnWater2 ON Båd.BådID = qBoatsOnWater2.FK_BådID) ON (Kajak_anvendelser.ID = Båd.Anvendelse) AND (Kajak_anvendelser.ID = Båd.Anvendelse)) ON Kajak_typer.ID = Båd.Type";

if ($gruppeid!=0)
{


  $s=$s." where fk_gruppeid=".$GruppeId." order by Båd.navn";
  $rs=$db->execute;  $s);

}
  else
{

  $s=$s." order by Båd.navn";
  $rs=$db->execute;  $s);
} 

//listrs(rs)


$CNT=0;
while(!$rs->eof)
{


  $breserveret=$not!isset($rs["QBoatsReserveret.FK_BådID"]);
  $bOnWater=$not!isset($rs["qBoatsOnWater2.FK_BådID"]);
  $bSkadet=$not!isset($rs["qBoatsSkadet.FK_BådID"]);
  $bLocked=$not!isset($rs["locktimeout"]);

  if (($CNT%2)==0)
  {

    $rowhtml="<tr class=\"firstrow\">";
  }
    else
  {

    $rowhtml="<tr class=\"secondrow\">";
  } 


  $BoatHTML[$CNT]=$BoatHTML[$CNT].$rowhtml."<td><A href=dsrbookboat.asp?boatid=".$rs["BådID"].">".$rs["navn"]."</a></td>";


//Her fastlægges niveaufeltets indhold	

  $Niveaufield="";
  switch ($rs["Niveau"])
  {
    case 1:
      $Niveaufield="<img border=\"0\" src=\"images/icon_easy.gif\"> Let</a><br>";
      break;
    case 2:
      $Niveaufield="<img border=\"0\" src=\"images/icon_medium.gif\"> Mellem</a><br>";
      break;
    case 3:
      $Niveaufield="<img border=\"0\" src=\"images/icon_hard.gif\"> Svær</a><br>";
      break;
  } 

  $BoatHTML[$CNT]=$BoatHTML[$CNT]."<td>".$Niveaufield."</td>";

//Her fastlægges typefeltets indhold	
  $BoatHTML[$CNT]=$BoatHTML[$CNT]."<td>".$rs["Typenavn"]."</td>";

//Her fastlægges anvendelsesfeltets indhold	
  $BoatHTML[$CNT]=$BoatHTML[$CNT]."<td><a href=\"dsrkayaks.asp?GruppeID=".$gruppeID."&ShowType=Anvendelse&ID=".$rs["AnvID"]."\">".$rs["Anvendelse"]."</a></td>";




//Her fastlægges statusfeltets indhold	

  $Statusfield="<td>";

  if ($bSkadet)
  {

    switch ($rs["Grad"])
    {
      case 1:
        $Statusfield=$Statusfield."<a href=\"dsrkayaks.asp?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$rs["BådID"]."\"><img border=\"0\" src=\"images/icon_skadet1.gif\" width=\"16\" height=\"17\"> Skadet</a><br>";
        break;
      case 2:
        $Statusfield=$Statusfield."<a href=\"dsrkayaks.asp?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$rs["BådID"]."\"><img border=\"0\" src=\"images/icon_skadet2.gif\" width=\"16\" height=\"17\"> Skadet</a><br>";
        break;
      case 3:
        $Statusfield=$Statusfield."<a href=\"dsrkayaks.asp?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$rs["BådID"]."\"><img border=\"0\" src=\"images/icon_skadet3.gif\" width=\"16\" height=\"16\"> Skadet</a><br>";
        break;
      default:

        $Statusfield="<td>OK</td>";
        break;
    } 
  } 



  if ($breserveret)
  {

    $Statusfield=$Statusfield."<a href=\"dsrkayaks.asp?GruppeID=".$gruppeID."&ShowType=Reservation&ID=".$rs["BådID"]."\"><img border=\"0\" src=\"images/icon_reserveret.gif\" width=\"16\" height=\"17\">  Reserveret</a><br>";
  } 


  if ($bOnWater)
  {

    $Statusfield=$Statusfield."<a href=\"dsrkayaks.asp?GruppeID=".$gruppeID."&ShowType=Tur&ID=".$rs["TurID"]."\"><img border=\"0\" src=\"images/icon_paavandet.gif\" width=\"16\" height=\"17\">  På vandet</a><br>";
  } 


  if ($blocked)
  {

    $Statusfield=$Statusfield."<img border=\"0\" src=\"images/icon_laast.gif\" width=\"16\" height=\"17\">  Låst af anden klient<br>";
  } 


  $Statusfield=$Statusfield."</td>";

  $BoatHTML[$CNT]=$BoatHTML[$CNT].$Statusfield;

  $cnt=$cnt+1;
  $rs->movenext;
} 

$i=0;
while(!($i==$cnt))
{


  $Infofield="";
  if ($i==0)
  {

    $Infofield="<td width=\"34%\" rowspan=".$cnt." & colspan=2 class=DetailInfo valign=\"top\">";

    $DetailInfo="<br>";
    switch ($Showtype)
    {
      case "Anvendelse":

        $Myrs=$db->execute;        $ShowID);
        $DetailInfo=$DetailInfo.$myrs["Beskrivelse"];
        $myrs->close;

        break;
      case "Reservation":

        $ResRS=$db->execute;        $ShowID);

        $DetailInfo=$DetailInfo."<center><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"95%\">";

        while(!($ResRS->eof))
        {

          $DetailInfo=$DetailInfo."<tr><td width=\"30%\"><b>Reserveret</b></td><td>".$Resrs["start"]." til ".$Resrs["slut"]."</td></tr>";
          $DetailInfo=$DetailInfo."<tr><td width=\"30%\"><b>Formål</b></td><td>".$Resrs["Beskrivelse"]."</td></tr>";
          $DetailInfo=$DetailInfo."<tr><td width=\"30%\"><b>Reserveret af</b></td><td>".$Resrs["Fornavn"]." ".$Resrs["Efternavn"]."</td></tr>";

          $DetailInfo=$DetailInfo."<tr><td width=\"100%\" colspan=2><hr noshade color=\"#000000\" size=\"1\"></td></tr>";
          $resrs->movenext;
        } 

        $Detailinfo=$Detailinfo."</table></center>";

        break;
      case "Skade":

        $SkadeRS=$db->execute;        $ShowID." AND Skade.Repareret Is Null ORDER BY Skade.Grad DESC");

        $DetailInfo=$DetailInfo."<center><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"95%\">";
        while(!($SkadeRS->eof))
        {

          switch ($SkadeRS["grad"])
          {
            case 1:

              $SDescript="Let skadet";
              break;
            case 2:

              $SDescript="Middel skadet";
              break;
            case 3:

              $SDescript="Svært skadet <br>(Må ikke benyttes)";
              break;
          } 

          $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Grad:</b> ".$skaders["grad"]." - ".$SDescript."</td>";
          $DetailInfo=$DetailInfo."<td width=\"50%\" valign=top><b>Oprettet:</b> ".substr($skaders["dato"],0,strlen($skaders["dato"])-9)."</td></tr>";
          $DetailInfo=$DetailInfo."<tr><td colspan=2 width=\"100%\"><br><b>Beskrivelse:</b><br>".$skaders["Beskrivelse"]."<hr noshade color=\"#000000\" size=\"1\"></td></tr>";
          $SkadeRS->movenext;
        } 
        $Detailinfo=$Detailinfo."</table></center>";

        break;
      case "Tur":

        $TurRS=$db->execute;        $ShowID." ORDER BY TurDeltager.Plads");

        $DetailInfo=$DetailInfo."<center><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"95%\">";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Destination:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".substr($turrs["Destination"],0,(strpos($turrs["Destination"]," (",1) ? strpos($turrs["Destination"]," (",1)+1 : 0))."</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Turens længde:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".intval($turrs["Meter"]/1000)." km</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Udskrevet:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".$turrs["ud"]."</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Forventet ind:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".$turrs["forvind"]."</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Turtype:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".$turrs["Turtype"]."</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td colspan=2 width=\"100%\"><br><b>Deltagere:</b><br>";

        while(!($TurRS->eof))
        {

          $DetailInfo=$DetailInfo.$TurRs["navn"];

          if ($turrs["plads"]==0)
          {

            $DetailInfo=$DetailInfo." (styrmand)<br>";
          }
            else
          {

            $DetailInfo=$DetailInfo."<br>";
          } 

//DetailInfo=DetailInfo & skaders("Beskrivelse") & "<br><br>"
          $TurRS->movenext;
        } 

        $Detailinfo=$Detailinfo."</td></tr></table></center>";

        break;
      default:
//Default

        $DetailInfo=$DetailInfo."<center><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"95%\">";

//if now<"27-03-2005 12:00:00" then 
//
//DetailInfo=DetailInfo & "<tr><td><font face=""Wingdings"" size=6>TTT</font><br><b>Vinterroning</b>"
//DetailInfo=DetailInfo & "<br>Husk at vintersæsonen er startet, og at der gælder særlige regler for vinterroning frem til standerhejsning.<br><br>"
//
//end if

        $DetailInfo=$DetailInfo."<tr><td><b>Ind-/udskriv båd:</b><br>Click på bådens navn for at komme videre til ind-/udskrivning.<br><br>".
          "<b>Bådens status:</b><br>".
          "Hvis båden allerede er udskrevet, kan du clicke på dens status for at se yderligere oplysninger om den igangværende tur.".
          "<br><br>Hvis båden er skadet, kan du clicke på dens status, for at se en liste over bådens skader.".
          "<br><br>Er båden  reserveret, kan du clicke på dens status, for at se, hvornår den er reserveret.";
        $Detailinfo=$Detailinfo."</td></tr></table></center>";

        break;
    } 

    $Infofield=$Infofield.$DetailInfo."<br></td>";
  } 


//if Showtype="" then Infofield=""

  print $BoatHTML[$i].$Infofield;

  $i=$i+1;
} 


$closedatabase;
?>
</TABLE>

</body>
</HTML>
