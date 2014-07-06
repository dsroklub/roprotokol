<?php
if(!isset($_SESSION))  session_start();
include "DatabaseINC.php";
$db=OpenDatabase();
// FIXME  session_register("BÂdKategori_session");
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
  <script language="javascript" src="rslite.js"></script>
  <script language="javascript">
  
  var oInterval = "";
	 // SÊtter en timer igang, sÂ siden kan blive opdateret hvert sekund (1000 milisekunder)
	 // k¯rer pÂ clienten
	 function body_onLoad(){
	
      // Create object
      RSLite = new RSLiteObject();
      
      // set the callback 
      RSLite.callback = myCallback;
      // set update interval
      oInterval = window.setInterval("RSLite.call('getvar.php',whichVar.value);",10000);
    }
    
    // Denne callback rutine kommer alle data tilbage til i response variablen
    // k¯rer pÂ clienten
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
  <title>Vis bÂde</title>
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
  $GruppeId=$_SESSION['BÂdKategori'];
} 
$_SESSION['BÂdKategori']=$GruppeId;
$opendatabase;
?>


<INPUT type=Hidden value=<?php echo $GruppeId;?> name=whichVar>
<table width="100%" class="rostat">
  <tr>
    <th class="tablehead" width="25%">BÂd</th>
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
		<th class="tablehead" width="48%">IgangvÊrende tur - <?php echo $myrs["navn"];?></th>
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

$s="SELECT BÂd.BÂdID, BÂd.Navn, BÂd.FK_GruppeID, BÂd.Pladser, qBoatsReserveret.FK_BÂdID, qBoatsOnWater2.FK_BÂdID, qBoatsSkadet.FK_BÂdID, qBoatsSkadet.grad, LÂsteBÂde.locktimeout, qBoatsOnWater2.TurType_Navn AS TurType_navn, qBoatsOnWater2.TurID FROM ((qBoatsReserveret RIGHT JOIN (qBoatsSkadet RIGHT JOIN BÂd ON qBoatsSkadet.FK_BÂdID = BÂd.BÂdID) ON qBoatsReserveret.FK_BÂdID = BÂd.BÂdID) LEFT JOIN LÂsteBÂde ON BÂd.BÂdID = LÂsteBÂde.BoatID) LEFT JOIN qBoatsOnWater2 ON BÂd.BÂdID = qBoatsOnWater2.FK_BÂdID";

if ($GruppeId!=0) {
  $s=$s." WHERE fk_gruppeid=".$GruppeId." ORDER BY BÂd.Navn";
  $rs0=$db->query($s);
} else {
  $s=$s." ORDER BY BÂd.Navn";
  $rs0=$db->query($s);
} 

error_log(" DSRSQL=".$s,0);
$rs=$rs0->fetch_array(MYSQLI_ASSOC);
//listrs(rs)

$CNT=0;
while (! is_null($rs)) {
  $breserveret= isset($rs["qBoatsReserveret.FK_BÂdID"]);
  $bOnWater= isset($rs["qBoatsOnWater2.FK_BÂdID"]);
  $bSkadet=isset($rs["qBoatsSkadet.FK_BÂdID"]);
  $bLocked=isset($rs["locktimeout"]);
  $BoatHTML[$CNT]="";
  if (($CNT%2)==0) {
    $rowhtml="<tr class=\"firstrow\">";
  } else {
    $rowhtml="<tr class=\"secondrow\">";
  } 
  $BoatHTML[$CNT]=$BoatHTML[$CNT].$rowhtml."<td><A href=dsrbookboat.php?boatid=".$rs["BÂdID"].">".$rs["Navn"]."</a></td>";
  $Secondfield="<td>";
  if ($bSkadet) {
    switch ($rs["Grad"]) {
      case 1:
        $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$rs["BÂdID"]."\"><img border=\"0\" src=\"images/icon_skadet1.gif\" width=\"16\" height=\"17\">  Let skadet</a><br>";
        break;
      case 2:
        $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$rs["BÂdID"]."\"><img border=\"0\" src=\"images/icon_skadet2.gif\" width=\"16\" height=\"17\">  Middel skadet</a><br>";
        break;
      case 3:
        $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Skade&ID=".$rs["BÂdID"]."\"><img border=\"0\" src=\"images/icon_skadet3.gif\" width=\"16\" height=\"16\">  SvÊrt skadet</a><br>";
        break;
      default:
        $Secondfield="<td>OK</td>";
        break;
    } 
  } 

  if ($breserveret) {
    $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Reservation&ID=".$rs["BÂdID"]."\"><img border=\"0\" src=\"images/icon_reserveret.gif\" width=\"16\" height=\"17\">  Reserveret</a><br>";
  } 

  if ($bOnWater) {
    $Secondfield=$Secondfield."<a href=\"dsrboats.php?GruppeID=".$gruppeID."&ShowType=Tur&ID=".$rs["TurID"]."\"><img border=\"0\" src=\"images/icon_paavandet.gif\" width=\"16\" height=\"17\">  PÂ vandet</a><br>";
  } 

  if ($bLocked) {
    $Secondfield=$Secondfield."<img border=\"0\" src=\"images/icon_laast.gif\" width=\"16\" height=\"17\">  LÂst af anden klient<br>";
  } 

  $Secondfield=$Secondfield."</td>";
  $BoatHTML[$CNT]=$BoatHTML[$CNT].$Secondfield;
  $CNT=$CNT+1;
  $rs=$rs0->fetch_array(MYSQLI_ASSOC);
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
          $DetailInfo=$DetailInfo."<tr><td width=\"30%\"><b>FormÂl</b></td><td>".$Resrs["Beskrivelse"]."</td></tr>";
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
              $SDescript="SvÊrt skadet <br>(MÂ ikke benyttes)";
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
        $DetailInfo=$DetailInfo."<tr><td width=\"50%\"><b>Turens lÊngde:</b></td>";
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
//DetailInfo=DetailInfo & "<br>Husk at vintersÊsonen er startet, og at der gÊlder sÊrlige regler for vinterroning frem til standerhejsning.<br><br>"
//
//end if

        $DetailInfo=$DetailInfo."<tr><td><b>Ind-/udskriv b√•d:</b><br>Click p√• b√•dens navn for at komme videre til ind-/udskrivning.<br><br>".
          "<b>B√•dens status:</b><br>".
          "Hvis b√•den allerede er udskrevet, kan du clicke p√• dens status for at se yderligere oplysninger om den igangv√¶rende tur.".
          "<br><br>Hvis b√•den er skadet, kan du clicke p√• dens status, for at se en liste over b√•dens skader.".
          "<br><br>Er b√•den  reserveret, kan du clicke p√• dens status, for at se, hvorn√•r den er reserveret.";
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
