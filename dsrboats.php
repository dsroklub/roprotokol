<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once("DatabaseINC.php");
$db=OpenDatabase();
// FIXME  session_register("BådKategori_session");
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
      oInterval = window.setInterval("RSLite.call('getvar.php',whichVar.value);",10000);
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
<?php 

      function arget($nm) {
      $rs="";
      if (isset($_GET[$nm])) {
	  $rs=$_GET[$nm];
	}
      return $rs;
    }

$GruppeId=$_GET["gruppeid"];
$ShowType=arget('ShowType'); //Skal der vises tur, skade eller etc.
$ShowID=arget("ID"); //Kommer ind med ID'et for den info, der skal vises

if ($GruppeId=="") {
  $GruppeId=$_SESSION['BådKategori'];
} 
$_SESSION['BådKategori']=$GruppeId;
$opendatabase;
?>


<INPUT type=Hidden value=<?php echo $GruppeId;?> name=whichVar>
<table width="100%" class="rostat">
  <tr>
    <th class="tablehead" width="25%">Båd</th>
    <th class="tablehead" width="25%">Status</th>
    
    <?php 
switch ($ShowType) {
  case "Reservation":
  $Myrs=$db->execute($ShowID);

?>
		<th class="tablehead" width="48%">Reservationer - <?php echo $myrs["navn"];?></th>
		<th class="tablehead" width="2%"><a href="dsrboats.php?GruppeID=<?php echo $gruppeID;?>"><img src="images/icon_close.gif" border="0"></a></th>
	<?php     $myrs->close;

    break;
  case "Skade":

  $Myrs=$db->execute($ShowID);
?>
		<th class="tablehead" width="48%">Aktuelle skader - <?php echo $myrs["navn"];?></th>
		<th class="tablehead" width="2%"><a href="dsrboats.php?GruppeID=<?php echo $gruppeID;?>"><img src="images/icon_close.gif" border="0"></a></th>
	<?php $myrs->close;

    break;
  case "Tur":
  $Myrs=$db->execute($ShowID);

?>
		<th class="tablehead" width="48%">Igangv�rende tur - <?php echo $myrs["navn"];?></th>
		<th class="tablehead" width="2%"><a href="dsrboats.php?GruppeID=<?php echo $gruppeID;?>"><img src="images/icon_close.gif" border="0"></a></th>
		<?php 
    $myrs->close;
    break;
  default:
?>
		<th class="tablehead" width="50%">Information</th>		
	<?php break;
} ?>
</tr>
<?php 

LockRemoveInactive();
$BoatHTML=array();

$s = <<<SQL
   SELECT Båd.BådID, Båd.Navn, Båd.FK_GruppeID, Båd.Pladser, qBoatsReserveret.FK_BådID, qBoatsOnWater2.FK_BådID, qBoatsSkadet.FK_BådID, qBoatsSkadet.grad, LåsteBåde.locktimeout, qBoatsOnWater2.TurType_Navn AS TurType_navn, qBoatsOnWater2.TurID 
   FROM ((qBoatsReserveret RIGHT JOIN (qBoatsSkadet RIGHT JOIN Båd ON qBoatsSkadet.FK_BådID = Båd.BådID) ON qBoatsReserveret.FK_BådID = Båd.BådID) 
   LEFT JOIN LåsteBåde ON Båd.BådID = LåsteBåde.BoatID) 
   LEFT JOIN qBoatsOnWater2 ON Båd.BådID = qBoatsOnWater2.FK_BådID    
SQL;

if ($GruppeId!=0) {
  $s=$s." WHERE fk_gruppeid=".$GruppeId." ORDER BY Båd.Navn";
  $rs=$db->query($s);
} else {
  $s=$s." ORDER BY Båd.Navn";
  $rs=$db->query($s);
} 

error_log(" DSRSQL=".$s,0);
//listrs(rs)

$CNT=0;
foreach ($rs as $baad) {

  $breserveret= isset($baad["qBoatsReserveret.FK_BådID"]);
  $bOnWater= isset($baad["qBoatsOnWater2.FK_BådID"]);
  $bSkadet=isset($baad["qBoatsSkadet.FK_BådID"]);
  $bLocked=isset($baad["locktimeout"]);
  $BoatHTML[$CNT]="";
  if (($CNT%2)==0) {
    $rowhtml="<tr class=\"firstrow\">";
  } else {
    $rowhtml="<tr class=\"secondrow\">";
  } 
  $BoatHTML[$CNT]=$BoatHTML[$CNT].$rowhtml."<td><A href=dsrbookboat.php?boatid=".$baad["BådID"].">".$baad["Navn"]."</a></td>";
  $Secondfield="<td>";
  if ($bSkadet) {
    switch ($rsi["Grad"]) {
      case 1:
        $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$baad["BådID"]."\"><img border=\"0\" src=\"images/icon_skadet1.gif\" width=\"16\" height=\"17\">  Let skadet</a><br>";
        break;
      case 2:
        $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$baad["BådID"]."\"><img border=\"0\" src=\"images/icon_skadet2.gif\" width=\"16\" height=\"17\">  Middel skadet</a><br>";
        break;
      case 3:
        $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$baad["BådID"]."\"><img border=\"0\" src=\"images/icon_skadet3.gif\" width=\"16\" height=\"16\">  Svært skadet</a><br>";
        break;
      default:
        $Secondfield="<td>OK</td>";
        break;
    } 
  } 

  if ($breserveret) {
    $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Reservation&ID=".$baad["BådID"]."\"><img border=\"0\" src=\"images/icon_reserveret.gif\" width=\"16\" height=\"17\">  Reserveret</a><br>";
  } 

  if ($bOnWater) {
    $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Tur&ID=".$baad["TurID"]."\"><img border=\"0\" src=\"images/icon_paavandet.gif\" width=\"16\" height=\"17\">  På vandet</a><br>";
  } 

  if ($bLocked) {
    $Secondfield=$Secondfield."<img border=\"0\" src=\"images/icon_laast.gif\" width=\"16\" height=\"17\">  Låst af anden klient<br>";
  } 

  $Secondfield=$Secondfield."</td>";
  $BoatHTML[$CNT]=$BoatHTML[$CNT].$Secondfield;
  $CNT=$CNT+1;
}

$i=0;
while(!($i==$CNT)) {
  $ThirdField="";
  if ($i==0) {
    $ThirdField="<td width=\"34%\" rowspan=".$CNT." & colspan=2 class=DetailInfo valign=\"top\">";
    $DetailInfo="<br>";
    switch ($ShowType) {
      case "Reservation":
        $ResRS=$db->execute($ShowID);
        $DetailInfo=$DetailInfo."<center><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"95%\">";
        while(!($ResRS->eof)) {
          $DetailInfo=$DetailInfo."<tr><td width=\"30%\"><b>Reserveret</b></td><td>".$Resrs["start"]." til ".$Resrs["slut"]."</td></tr>";
          $DetailInfo=$DetailInfo."<tr><td width=\"30%\"><b>Formål</b></td><td>".$Resrs["Beskrivelse"]."</td></tr>";
          $DetailInfo=$DetailInfo."<tr><td width=\"30%\"><b>Reserveret af</b></td><td>".$Resrs["Fornavn"]." ".$Resrs["Efternavn"]."</td></tr>";
          $DetailInfo=$DetailInfo."<tr><td width=\"100%\" colspan=2><hr noshade color=\"#000000\" size=\"1\"></td></tr>";
          $resrs->movenext;
        } 
        $DetailInfo=$DetailInfo."</table></center>";
        break;
      case "Skade":

        $SkadeRS=$db->execute($ShowID." AND Skade.Repareret Is Null ORDER BY Skade.Grad DESC");
        $DetailInfo=$DetailInfo."<center><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"95%\">";
        while(!($SkadeRS->eof)) {

          switch ($SkadeRS["grad"]) {
            case 1:
              $SDescript="Let skadet";
              break;
            case 2:
              $SDescript="Middel skadet";
              break;
            case 3:
              $SDescript="Sv�rt skadet <br>(Må ikke benyttes)";
              break;
          } 
          $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Grad:</b> ".$skaders["grad"]." - ".$SDescript."</td>";
          $DetailInfo=$DetailInfo."<td width=\"50%\" valign=top><b>Oprettet:</b> ".substr($skaders["dato"],0,strlen($skaders["dato"])-9)."</td></tr>";
          $DetailInfo=$DetailInfo."<tr><td colspan=2 width=\"100%\"><br><b>Beskrivelse:</b><br>".$skaders["Beskrivelse"]."<hr noshade color=\"#000000\" size=\"1\"></td></tr>";
          $SkadeRS->movenext;
        } 
        $DetailInfo=$DetailInfo."</table></center>";

        break;
      case "Tur":
        $TurRS=$db->execute($ShowID." ORDER BY TurDeltager.Plads");

        $DetailInfo=$DetailInfo."<center><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" width=\"95%\">";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Destination:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".substr($turrs["Destination"],0,(strpos($turrs["Destination"]," (",1) ? strpos($turrs["Destination"]," (",1)+1 : 0))."</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Turens l�ngde:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".intval($turrs["Meter"]/1000)." km</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Udskrevet:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".$turrs["ud"]."</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Forventet ind:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".$turrs["forvind"]."</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Turtype:</b></td>";
        $DetailInfo=$DetailInfo."<td width=\"50%\">".$turrs["Turtype"]."</td></tr>";
        $DetailInfo=$DetailInfo."<tr><td colspan=2 width=\"100%\"><br><b>Deltagere:</b><br>";

        while(!($TurRS->eof)) {

          $DetailInfo=$DetailInfo.$TurRs["navn"];
          if ($turrs["plads"]==0) {

            $DetailInfo=$DetailInfo." (styrmand)<br>";
          } else {
            $DetailInfo=$DetailInfo."<br>";
          } 
//DetailInfo=DetailInfo & skaders("Beskrivelse") & "<br><br>"
          $TurRS->movenext;
        } 
        $DetailInfo=$DetailInfo."</td></tr></table></center>";
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
        $DetailInfo=$DetailInfo."</td></tr></table></center>";
        break;
    } 

    $ThirdField=$ThirdField.$DetailInfo."<br></td>";
  } 


//if Showtype="" then Thirdfield=""
  print $BoatHTML[$i].$ThirdField;
  $i=$i+1;
} 
$closedatabase;
?>
</TABLE>

</body>
</HTML>
