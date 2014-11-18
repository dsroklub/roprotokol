<?
  session_start();
  session_register("username_session");
  session_register("password_session");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<? // asp2php (vbscript) converted on Wed Jul 30 12:05:46 2014
 ?>
<!-- #include file="databaseINC.php" -->

<html>
<head>
<title>Administration af fejlrapporter</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="file:///C:/webserver/wwwroot/dsr/roprotokol.css">
</head>

<body>

<h2>Administration af fejlrapporter</h2>

<? 

$xaction=${"action"};
$username=${"username"};
$password=${"password"};
$ErrorNo=${"ErrorNo"};
$turID=${"turid"};

if ($_SESSION['username']!="admin")
{
//hvis man ikke er logget ind i forvejen
  if ($username=="admin" && $password=="olDSinneR")
  {

    $_SESSION['username']="admin";
    $_SESSION['password']="olDSinneR";
  }
    else
  {

    $action="login";
  } 

} 


switch ($xaction)
{
  case "showmenu":

    if (ValidateLogin(=="OK"))
    {

      ShowMenu();
    } 


    break;
  case "login":

    ShowLoginForm();

    break;
  case "showerrorlistrights":

    if (ValidateLogin(=="OK"))
    {

      showerrorlistrights();
    } 


    break;
  case "ShowErrorReportRights":

    if (ValidateLogin(=="OK"))
    {

      ShowErrorReportRights($Errorno);
    } 


    break;
  case "savechangesrights":

    if (ValidateLogin(=="OK"))
    {

      savechangesrights();
    } 


    break;
  case "confirmdeleteerrorrights":

    if (ValidateLogin(=="OK"))
    {

      confirmDeleteErrorRights($ErrorNo);
    } 


    break;
  case "deleteerrorrights":

    if (ValidateLogin(=="OK"))
    {

      DeleteErrorRights($ErrorNo);
    } 


    break;
  case "savechangestur":

    if (ValidateLogin(=="OK"))
    {

      savechangestur();
    } 


    break;
  case "showerrorreportformtur":

    if (ValidateLogin(=="OK"))
    {

      showerrorreportformtur($ErrorNo);
    } 


    break;
  case "showerrorlisttur":

    if (ValidateLogin(=="OK"))
    {

      showerrorlisttur();
    } 


    break;
  case "confirmslettur":

    if (ValidateLogin(=="OK"))
    {

      ConfirmSletTur($TurID,$errorno);
    } 


    break;
  case "slettur":

    if (ValidateLogin(=="OK"))
    {

      SletTur($TurID);
    } 


    break;
  case "confirmDeleteErrorTur":

    if (ValidateLogin(=="OK"))
    {

      confirmDeleteErrorTur($ErrorNo);
    } 


    break;
  case "DeleteErrorTur":

    if (ValidateLogin(=="OK"))
    {

      DeleteErrorTur($ErrorNo,true);
    } 


    break;
  case "notloggedin":

?>
Du er ikke logget ind. Gå til <a href="fejladmin.php?action=login">login</a>. 
<? 

    break;
} 

?>

</body>
</html>


<? 

function showerrorreportformtur($ErrorNo)
{
  extract($GLOBALS);


  $opendatabase;
  $sql="select * from Fejl_tur where fejlID=".$ErrorNo;
  $MyRS=$db->execute;  $sql);
  $ErrorSpecs=$MyRS->getrows();
  $myrs->close;
  $closedatabase;

  $xFejlID=$Errorspecs[0][0];
  $xSletTur=$Errorspecs[1][0];
  $xTurID=$Errorspecs[2][0];
  $xBaad=$Errorspecs[3][0];
  $xUd=$Errorspecs[4][0];
  $xInd=$Errorspecs[5][0];
  $xDestination=$Errorspecs[6][0];
  $xDistance=$Errorspecs[7][0];
  $xTurType=$Errorspecs[8][0];
  $xAarsagtilrettelsen=$Errorspecs[19][0];
  $xIndberetter=$Errorspecs[20][0];
  $xMail=$Errorspecs[21][0];
  $xFixed_comment=$Errorspecs[22][0];

  $opendatabase;
//sql = "SELECT Tur.TurID, Tur.FK_BådID, Tur.Ud, Tur.Ind, Tur.ForvInd, Tur.Destination, Tur.Meter, Tur.FK_TurTypeID, Tur.Kommentar, Tur.OprettetDato, Tur.RedigeretDato, Tur.Initialer, Tur.DESTID, TurDeltager.Plads, TurDeltager.FK_MedlemID, TurDeltager.Navn, 0 FROM Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID WHERE (((Tur.TurID)=" & xTurID & "));"
  $sql="SELECT Tur.TurID, Tur.FK_BådID, Tur.Ud, Tur.Ind, Tur.ForvInd, Tur.Destination, Tur.Meter, Tur.FK_TurTypeID, Tur.Kommentar, Tur.OprettetDato, Tur.RedigeretDato, Tur.Initialer, Tur.DESTID, TurDeltager.Plads, TurDeltager.FK_MedlemID, TurDeltager.Navn, Medlem.Medlemsnr FROM (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) INNER JOIN Medlem ON TurDeltager.FK_MedlemID = Medlem.MedlemID WHERE (((Tur.TurID)=".$xTurID."));";
  $MyRS=$db->execute;  $sql);
  if ($myrs->eof)
  {
    $ShowError="Ingen data at vise.";
  } 
  if (!$myrs->eof)
  {
    $TurSpecs=$MyRS->getrows();
  } 
  $myrs->close;

  $sql="SELECT Medlem.MedlemID, Medlem.Medlemsnr, [Fornavn] & \" \" & [Efternavn] AS Navn FROM Medlem order by Fornavn;";
  $MyRS=$db->execute;  $sql);
  $RoerDB=$MyRS->getrows();
  $myrs->close;
//RoerDB
//0 = MedlemID
//1 = MedlemsNr (public)
//2 = Navn

  $closedatabase;

  if ($ShowError=="")
  {


    $xTurBaadID=$Turspecs[1][0];
    $xTurUd=$Turspecs[2][0];
    $xTurInd=$Turspecs[3][0];
    $xTurMeter=$Turspecs[6][0];
    $xTurTurTypeID=$Turspecs[7][0];
    $xTurDestination=$Turspecs[5][0];
    $xTurDestID=$Turspecs[12][0];

?>
	<table border=1 style="border-collapse: collapse" width=600>
		<tr>
			<td>
			<table border=0>
			<td colspan=4><b>Data fra fejlrapporten</b></td>				
				<tr><td>FejlID:</td><td bgcolor="#FFFFFF"><?     echo $ErrorNo;?></td><td width=10></td><td>TurID:</td><td bgcolor="#FFFFFF"><?     echo $xTurID;?></td></tr>
				<tr>
					<td>Slet turen? </td>
					<td bgcolor="#FFFFFF">
					<? 
    if (!$xSletTur)
    {

      print "Nej";
    }
      else
    {

      print "Ja";
    } 

?>
					</td>
					<td width=10></td><td></td><td></td>
				</tr>
				<tr><td>Båd:</td><td bgcolor="#FFFFFF"><?     echo $xBaad;?></td><td width=10></td><td>Destination:</td><td bgcolor="#FFFFFF"><?     echo $xDestination;?></td></tr>
				<tr><td>Ud:</td><td bgcolor="#FFFFFF"><?     echo $xUd;?></td><td width=10></td><td>Ind:</td><td bgcolor="#FFFFFF"><?     echo $xInd;?></td></tr>
				<tr><td>Distance:</td><td bgcolor="#FFFFFF"><?     echo $xDistance;?></td><td width=10></td><td>Turtype:</td><td bgcolor="#FFFFFF"><?     echo $xTurtype;?></td></tr>
				<tr><td>Indberettet af:</td><td bgcolor="#FFFFFF"><?     echo $xIndberetter;?></td><td width=10></td><td>Mail:</td><td bgcolor="#FFFFFF"><?     echo $xMail;?></td></tr>
				<tr><td>Fejlbeskrivelse:</td><td bgcolor="#FFFFFF" colspan=4><?     echo $xAarsagtilrettelsen;?></td></tr>
				<? 
    for ($c1=0; $c1<=9; $c1=$c1+1)
    {
      if ($Errorspecs[9+$c1][0]!="")
      {

?>
					<tr><td>Roer <?         echo $c1+1;?>:</td><td bgcolor="#FFFFFF"><?         echo $Errorspecs[9+$c1][0];?></td></tr>
					<? 
      } 


    }

?>
				<tr><td>Rettet:</td><td colspan=4><input type="text" size=48 value="<?     echo $xFixed_comment;?>"></td></tr>
			</table>		
			</td>
		</tr>
	</table>
	<form action="fejladmin.php" name="turform" method="post">
	<table border=1 style="border-collapse: collapse" width=600>
		<tr>
			<td>
			<table border=0>
				<td colspan=4><b>Data registreret på turen ifølge roprotokollen</b></td>
				<tr>
					<td>Båd:</td>
					<td bgcolor="#FFFFFF">					
					<? 
    $opendatabase;
    $sql="select * from Båd where BådId=".$xTurBaadID;
    $RS=$db->execute;    $sql);
    print $RS["Navn"];
    $xTurBaadNavn=$RS["Navn"];
    $RS->close;
    $closedatabase;
?>
					</td>
					<td width=10></td>
					<td>					
					<select name="TurBaad">
							<? 
    $opendatabase;
    $sql="select * from [Båd]";
    $RS=$db->execute;    $sql);

    while(!($RS->eof))
    {


      $Ifselected="";
      if ($xTurBaadID==$RS["BådID"])
      {
        $Ifselected="selected";
      } 
      print "<option value=".$RS["BådID"]." ".$IfSelected.">".$RS["Navn"]."</option>";

      $RS->movenext;
    } 
    $RS->close;
    $closedatabase;
?>
						</select>
						<? 
    if ($xTurBaadNavn!=$xBaad)
    {
      print "<font color=red><b>RET</b></font>";
    } ?>					
					</td>

				</tr>
				<tr>
					<td>Destination:</td>
					<td bgcolor="#FFFFFF"><?     echo $xTurDestination;?></td>
					<td width=10></td>
					<td>
					<select name="TurDestination">
							<? 
    $opendatabase;
    $sql="select * from Destination";
    $RS=$db->execute;    $sql);

    while(!($RS->eof))
    {


      $Ifselected="";
      if ($xDestination==$RS["Navn"])
      {
        $Ifselected="selected";
      } 
      print "<option ".$IfSelected.">".$RS["Navn"]."</option>";

      $RS->movenext;
    } 
    $RS->close;
    $closedatabase;
?>
						</select>
						<? 
    if ($xTurDestination!=$xDestination)
    {
      print "<font color=red><b>RET</b></font>";
    } ?>					
					</td>
				</tr>
				<tr>
					<td>Ud:</td><td bgcolor="#FFFFFF"><?     echo $xTurUd;?></td>
					<td width=10></td>
					<td>
                    <input type="text" name="TurUd" value="<?     echo $xTurUd;?>" size="20">
						<? 
    if ($cdate[$xTurUd]!=$cdate[$xUd])
    {
      print "<font color=red><b>RET</b></font>";
    } ?>					
					</td>
				</tr>
				<tr>
					<td>Ind:</td><td bgcolor="#FFFFFF"><?     echo $xTurInd;?></td>
					<td width=10></td>
					<td>
                    <input type="text" name="TurInd" value="<?     echo $xTurInd;?>" size="20">
						<? 
    if ($cdate[$xTurInd]!=$cdate[$xInd])
    {
      print "<font color=red><b>RET</b></font>";
    } ?>					
					</td>
				</tr>
				<tr>
					<td>Distance:</td>
					<td bgcolor="#FFFFFF"><?     echo $xTurMeter;?></td>
				<td width=10></td>
					<td>
						<? 
    $Errormsg="";
    $xTurMeterFixed=$xTurMeter;
    if ($xDistance<1000)
    {

      if ($xTurMeter\1000!=$xDistance)
      {

        $Errormsg="<font color=red><b>RET</b></font>";
        $xTurMeterFixed=$xDistance*1000;
      } 

    }
      else
    {

      if ($xTurMeter!=$xDistance)
      {

        $Errormsg="<font color=red><b>RET</b></font>";
        $xTurMeterFixed=$xDistance;
      } 

    } 

?>
					<input type="text" name="TurDistance" value="<?     echo $xTurMeterFixed;?>" size="20"> <?     echo $Errormsg;?>
					</td>
				</tr>
				<tr>
					<td>Turtype:</td>
					<td bgcolor="#FFFFFF">
					<? 
    $opendatabase;
    $sql="select * from TurType where TurTypeID=".$xTurTurtypeID;
    $RS=$db->execute;    $sql);
    print $RS["Navn"];
    $xTurTurtypeNavn=$RS["Navn"];
    $RS->close;
    $closedatabase;
?>
					</td>
					<td width=10></td>
					<td>
						<select name="TurTurtype">
							<? 
    $opendatabase;
    $sql="select * from TurType";
    $RS=$db->execute;    $sql);

    while(!($RS->eof))
    {


      $Ifselected="";
      if ($xTurtype==$RS["Navn"])
      {
        $Ifselected="selected";
      } 
      print "<option value=".$RS["TurTypeID"]." ".$IfSelected.">".$RS["Navn"]."</option>";

      $RS->movenext;
    } 
    $RS->close;
    $closedatabase;
?>
						</select>
						<? 
    if ($xTurTurtypeNavn!=$xTurtype)
    {
      print "<font color=red><b>RET</b></font>";
    } ?>					
					</td>
				</tr>
				<? 
    for ($c1=0; $c1<=count($Turspecs); $c1=$c1+1)
    {

?>
						<tr>
							<td>Roer <?       echo $c1+1;?>:</td>
							<td bgcolor="#FFFFFF">
							<?       echo $Turspecs[16][$c1]." - ".$Turspecs[15][$c1];?> 
							</td>
							<td width="10"></td>
							<td>
							<? 
      $Errormsg="";
      if (trim($Turspecs[15][$c1])!=trim($Errorspecs[9+$c1][0]))
      {
        $Errormsg="<font color=red><b>RET</b></font>";
      } 

      $ThisOptions="";
      $OtherOptions="<option>---------------</option>";
      for ($c2=0; $c2<=count($RoerDB); $c2=$c2+1)
      {
        if ($Roerdb[2][$c2]==$Errorspecs[9+$c1][0] || $Roerdb[1][$c2]==$Errorspecs[9+$c1][0])
        {

          $ThisOptions=$ThisOptions."<option>".$Roerdb[1][$c2]." - ".$Roerdb[2][$c2]."</option>";
        }
          else
        {

          $OtherOptions=$OtherOptions."<option>".$Roerdb[1][$c2]." - ".$Roerdb[2][$c2]."</option>";
        } 


      }

?>							
							<select name="Roer<?       echo $c1;?>" style="width: 200px;"> 
								<?       echo $ThisOptions.$OtherOptions;?>	
							</select>
							<?       echo $errormsg;?>
							</td>
						</tr>
						<? 

    }

?>
				<tr>
					<td colspan=4>
					<input type="hidden" name="errorno" value="<?     echo $errorno;?>">
					<input type="hidden" name="TurID" value="<?     echo $xTurID;?>">
					<input type="hidden" name="action" value="savechangestur">
					<button type="submit">Gem ændringer</button>		
					<button type="button" onclick="window.location='fejladmin.asp?action=confirmslettur&turid=<?     echo $xTurID;?>&errorno=<?     echo $ErrorNo;?>';">Slet tur</button>		
					<button type="button" onclick="window.location='fejladmin.asp?action=showerrorlisttur';">Til oversigt</button>		
					</td>
				</tr>
			</table>
			</td>
	</table>	
	</form>

	<? 
  }
    else
  {
//showerror

    print "<p>".$ShowError."</p>";

?>
		<button type="button" onClick="window.location='fejladmin.asp?action=showerrorlisttur';">Tilbage til fejlliste</button>
		<? 

  } 
  $if'showerror;

  return $function_ret;
} 

function ShowErrorlistTur()
{
  extract($GLOBALS);


  ShowMenu();

  $opendatabase;
  $sql="select [Ud], [FejlID], [Årsag til rettelsen], [Fixed], [Båd], [Destination], [TurDeltager0], [TurID] from Fejl_tur order by FejlId desc";
  $MyRS=$db->execute;  $sql);
  $Errorlist=$MyRS->getrows();
  $myrs->close;
  $closedatabase;

//Errorlist
//0 -- Ud
//1 -- FejlID
//2 -- Årsag til rettelsen
//3 -- Fixed
//4 -- Båd
//5 -- Destination
//6 -- Styrmand
//7 -- TurID


?>
	<table class=""rostat"" width=""600"">
		<tr bgcolor="#ffffff">
			<td>ID</td>
			<td>Tur</td>
			<td>Beskrivelse</td>
			<td>Rettet</td>
			<td> </td>
		</tr>
	<? 

  for ($c1=0; $c1<=count($Errorlist); $c1=$c1+1)
  {


    $xUd=$Errorlist[0][$c1];
    $xFejlID=$Errorlist[1][$c1];
    $xAarsag=$Errorlist[2][$c1];
    $xFixed=$Errorlist[3][$c1];
    $xBoat=$Errorlist[4][$c1];
    $xDestination=$Errorlist[5][$c1];
    $xStyrmand=$Errorlist[6][$c1];
    $xTurID=$Errorlist[7][$c1];

    if ($xFixed=="True" || $xFixed=="Sand")
    {

      $xFixed="<font color='green'>Ja</font>";
    }
      else
    {

      $xFixed="<font color='red'>Nej</font>";
    } 


    if (($c1%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 

?>
		<td width="50"><a href="fejladmin.asp?action=showerrorreportformtur&ErrorNo=<?     echo $xFejlID;?>"><?     echo $xFejlID;?></a></td>
		<td width="150"><a href="fejladmin.asp?action=showerrorreportformtur&ErrorNo=<?     echo $xFejlID;?>"><?     echo $xTurID;?><br><?     echo $xUD;?><br><?     echo $xBoat;?> - <?     echo $xStyrmand;?></a></td>
		<td width="400"><a href="fejladmin.asp?action=showerrorreportformtur&ErrorNo=<?     echo $xFejlID;?>"><?     echo $xAarsag;?></a></td>
		<td width="50"><a href="fejladmin.asp?action=confirmDeleteErrorTur&ErrorNo=<?     echo $xFejlID;?>"><?     echo $xfixed;?></a></td>
		<td width="50"><a href="fejladmin.asp?action=confirmDeleteErrorTur&ErrorNo=<?     echo $xFejlID;?>"><strong>Slet</strong></a></td>
		</td>
		<? 



  }


?>
	</table>
	<? 

  return $function_ret;
} 


function ConfirmSletTur($myTurID,$myErrorNo)
{
  extract($GLOBALS);


?>
	<p>Er du sikker på, at du vil slette turen?</p>
	<button type="button" onclick="window.location='fejladmin.asp?action=showerrorreportformtur&ErrorNo=<?   echo $myErrorNo;?>';">Annuller</button>		
	<button type="button" onclick="window.location='fejladmin.asp?action=slettur&errorno=<?   echo $myErrorNo;?>&turid=<?   echo $myTurID;?>';">Slet tur</button>		
	<? 

  return $function_ret;
} 

function SletTur($myTurID)
{
  extract($GLOBALS);


  $opendatabase;
  $sql="DELETE TurDeltager.FK_TurID FROM TurDeltager WHERE (((TurDeltager.FK_TurID)=".$myTurID."));";
  $db->execute($sql);
  $sql="DELETE Tur.TurID FROM Tur WHERE (((Tur.TurID)=".$myTurID."));";
  $db->execute($sql);
  $closedatabase;

//DeleteErrorTur Errorno, false
  SetTurErrorAsFixed($Errorno);

?>
	<p>Turen er slettet</p>
	<button type="button" onclick="window.location='fejladmin.asp?action=showerrorlisttur';">Retur til oversigt</button>		
	<? 

  return $function_ret;
} 


function ConfirmDeleteErrorTur($ErrorNo)
{
  extract($GLOBALS);


?>
	<p>Er du sikker på, at du vil slette fejlrapporten?</p>
	<button type="button" onclick="window.location='fejladmin.asp?action=showerrorlisttur';">Annuller</button>		
	<button type="button" onclick="window.location='fejladmin.asp?action=DeleteErrorTur&ErrorNo=<?   echo $ErrorNo;?>';">Slet</button>		
	<? 

  return $function_ret;
} 

function DeleteErrorTur($ErrorNo,$ShowHTML)
{
  extract($GLOBALS);


  $opendatabase;
  $sql="DELETE Fejl_Tur.FejlID FROM Fejl_Tur WHERE (((Fejl_Tur.FejlID )=".$ErrorNo."));";
  $db->execute($sql);
  $closedatabase;

  SetTurErrorAsFixed($Errorno);

  if ($ShowHTML)
  {


?>

	<p>Turen er slettet</p>
	<button type="button" onclick="window.location='fejladmin.asp?action=showerrorlisttur';">Retur til oversigt</button>		
	<? 

  } 


  return $function_ret;
} 


function SaveChangesTur()
{
  extract($GLOBALS);


  $xTurID=${"TurID"};
  $xTurBaad=${"TurBaad"}; //Er konverteret til BådID	
  $xTurDestination=${"TurDestination"};
  $xTurUd=${"TurUd"};
  $xTurInd=${"TurInd"};
  $xTurDistance=${"TurDistance"};
  $xTurTurtype=${"TurTurtype"}; //Er konverteret til TurtypeID

  $opendatabase;
  $sql="UPDATE Tur SET ".
    "Tur.Ud = \"".$xTurUd."\", ".
    "Tur.Ind = \"".$xTurInd."\", ".
    "Tur.Destination = \"".$xTurDestination."\"".", ".
    "Tur.Meter = ".$xTurDistance.", ".
    "Tur.FK_TurTypeID = \"".$xTurTurType."\", ".
    "Tur.FK_BådID = \"".$xTurBaad."\" ".
    "WHERE (((Tur.TurID)=".$xTurID."));";

  $db->execute($sql);

  for ($c1=0; $c1<=8; $c1=$c1+1)
  {
    $Roer=${"Roer".$c1};

    if ($Roer!="")
    {

      $StopSrch=(strpos($Roer," ",1) ? strpos($Roer," ",1)+1 : 0)$xRoerIDExt=(substr($Roer,0,$stopsrch-1));

      $sql="SELECT MedlemID FROM Medlem where Medlemsnr=\"".$xRoerIDExt."\";";
      $rs=$db->execute;      $sql);
      $xRoerID=$rs["MedlemID"];
      $rs->close;
      $xRoerNavn=substr($Roer,$StopSrch+3-1,strlen($roer)-$StopSrch+1);
      $xPlads=$c1;
      $sql="UPDATE TurDeltager set FK_MedlemID=".$xRoerID.", Navn=\"".$xRoerNavn."\" WHERE (TurDeltager.FK_TurID=".$xTurID." and Plads=".$xPlads.");";
      $db->execute($sql);
    } 



  }


  $closedatabase;

//DeleteErrorTur ErrorNo, false
  SetTurErrorAsFixed($Errorno);

?>
	<p>Ændringen er gemt.</p>
	<button type="button" onClick="window.location='fejladmin.asp?action=showerrorlisttur';">Til oversigt</button>
	<? 

  return $function_ret;
} 


function SetTurErrorAsFixed($errorno)
{
  extract($GLOBALS);


  $opendatabase;

  $sql="update Fejl_Tur SET fixed=true where FejlID=".$errorno;
  $db->execute($sql);

  $closedatabase;

  return $function_ret;
} 

function SetRightsErrorAsFixed($errorno)
{
  extract($GLOBALS);


  $opendatabase;

  $sql="update Fejl_tblMembersSportData SET fixed=true where FejlID=".$errorno;
  $db->execute($sql);

  $closedatabase;

  return $function_ret;
} 


function ConfirmDeleteErrorRights($ErrorNo)
{
  extract($GLOBALS);


?>
	<p>Er du sikker på, at du vil slette fejlrapporten?</p>
	<button type="button" onclick="window.location='fejladmin.asp?action=showerrorlistrights';">Annuller</button>		
	<button type="button" onclick="window.location='fejladmin.asp?action=deleteerrorrights&ErrorNo=<?   echo $ErrorNo;?>';">Slet</button>		
	<? 

  return $function_ret;
} 

function DeleteErrorRights($ErrorNo)
{
  extract($GLOBALS);


  $opendatabase;
  $sql="DELETE Fejl_tblMembersSportData.FejlID FROM Fejl_tblMembersSportData WHERE (((Fejl_tblMembersSportData.FejlID )=".$ErrorNo."));";
  $db->execute($sql);
  $closedatabase;
?>
	<p>Fejlrapporten er slettet</p>
	<button type="button" onclick="window.location='fejladmin.asp?action=showerrorlistrights';">Retur til oversigt</button>		
	<? 

  return $function_ret;
} 

function SaveChangesRights()
{
  extract($GLOBALS);


  $opendatabase;

  $fixed_comment=${"fixed_comment"};
  $errorno=${"errorno"};
  $memberID=${"memberID"};
  $sql="UPDATE Fejl_tblMembersSportData SET fixed_comment=\"".$fixed_comment."\" where fejlID=".$ErrorNo;
  $db->execute($sql);

  $Today=substr("0".$day[time()],strlen("0".$day[time()])-(2))."-".substr("0".strftime("%m",strftime("%m/%d/%Y %H:%M:%S %p")),strlen("0".strftime("%m",strftime("%m/%d/%Y %H:%M:%S %p")))-(2))."-".strftime("%Y",strftime("%m/%d/%Y %H:%M:%S %p"));
  $UpdateStatement="SET ";
  foreach ($_POST as $Item)
  {
    $fieldName=$Item;
    $RightDate=$Today;
    if (substr($Fieldname,0,2)=="ff")
    {

      $sql="select ".substr($Fieldname,2,strlen($fieldname)-2)." FROM tblMembersSportData WHERE MemberID=".$memberID;
      $rs=$db->execute;      $sql);
      if ($rs[0]!="")
      {
        $RightDate=$rs[0];
      } 
      $rs->close;
      $UpdateStatement=$Updatestatement.substr($Fieldname,2,strlen($fieldname)-2)."=\"".$RightDate."\", ";
    } 

  }

//response.write updatestatement

  $sql="UPDATE tblMembersSportData SET Roret =Null, TeoretiskstyrmandKursus = Null, Styrmand = Null, Langtur = Null, Ormen = Null, Svava = Null, Sculler = Null, Kajak = Null, RoInstruktoer = Null, styrmandInstruktoer = Null, ScullerInstruktoer = Null, KajakInstruktoer = Null, Kaproer = Null, Motorboat = Null WHERE MemberID=".$memberID;
  $db->execute($sql);

  $sql="UPDATE tblMembersSportData ".substr($UpdateStatement,0,strlen($UpdateStatement)-2)." WHERE MemberID=".$memberID;
  $db->execute($sql);

  $closedatabase;

  SetRightsErrorAsFixed($Errorno);

?>
	<p>Ændringen er gemt.</p>
	<button type="button" onClick="window.location='fejladmin.asp?action=showerrorlistrights';">Til oversigt</button>
	<? 

  return $function_ret;
} 

function ShowErrorReportRights($Errorno)
{
  extract($GLOBALS);


  $opendatabase;
  $sql="select * from Fejl_tblMembersSportData where fejlID=".$ErrorNo;
  $MyRS=$db->execute;  $sql);
  $ErrorSpecs=$MyRS->getrows();
  $myrs->close;

//x-variable indeholder påstanden, y indeholder status

  $xFejlID=$ErrorSpecs[0][0];
  $xNavn=$ErrorSpecs[1][0];
  $xMemberID=$ErrorSpecs[2][0];

  $xRoret=$ErrorSpecs[3][0];
  $xTeoretiskStyrmandKursus=$ErrorSpecs[4][0];
  $xStyrmand=$ErrorSpecs[5][0];
  $xLangtur=$ErrorSpecs[6][0];
  $xOrmen=$ErrorSpecs[7][0];
  $xSvava=$ErrorSpecs[8][0];
  $xSculler=$ErrorSpecs[9][0];
  $xKajak=$ErrorSpecs[10][0];
  $xKajak_2=$ErrorSpecs[11][0];
  $xRoInstruktoer=$ErrorSpecs[12][0];
  $xStyrmandInstruktoer=$ErrorSpecs[13][0];
  $xScullerInstruktoer=$ErrorSpecs[14][0];
  $xKajakInstruktoer=$ErrorSpecs[15][0];
  $xKaproer=$ErrorSpecs[16][0];
  $xMotorboat=$ErrorSpecs[17][0];
  $xIndberetter=$ErrorSpecs[18][0];
  $xMail=$ErrorSpecs[19][0];
  $xKommentar=$ErrorSpecs[20][0];
  $xFixed_Comment=$ErrorSpecs[21][0];

  $sql="SELECT * from tblMembersSportData WHERE MemberID=".$xMemberID;
  $rs=$db->execute;  $sql);
  if (!$rs->eof)
  {
    $RightsSpecs=$RS->getrows();
  } 
  $closedatabase;

  $xMemberID=$RightsSpecs[0][0];

  $yRoret=$RightsSpecs[1][0];
  $yTeoretiskStyrmandKursus=$RightsSpecs[2][0];
  $yStyrmand=$RightsSpecs[3][0];
  $yTeoretiskLangtursStyrmandKursus=$RightsSpecs[4][0];
  $yLangtur=$RightsSpecs[5][0];
  $yOrmen=$RightsSpecs[8][0];
  $ySvava=$RightsSpecs[9][0];
  $ySculler=$RightsSpecs[10][0];
  $yKajak=$RightsSpecs[11][0];
  $yKajak_2=$RightsSpecs[12][0];
  $yRoInstruktoer=$RightsSpecs[13][0];
  $yStyrmandInstruktoer=$RightsSpecs[14][0];
  $yScullerInstruktoer=$RightsSpecs[15][0];
  $yKajakInstruktoer=$RightsSpecs[16][0];
  $yKaproer=$RightsSpecs[17][0];
  $yMotorboat=$RightsSpecs[18][0];


?>
	
	<h3><?   echo $xMemberID." - ".$xNavn.", indberettet af ".$xIndberetter;?></h3>
	

	
<form name="rightsform" action="fejladmin.php" method="post">
  <table border=1 style="border-collapse: collapse" width=600>
			<table border=0>
			<tr><td width=200><strong>Rettighed</strong></td><td width=80><strong>Aktuel status</strong></td><td width=80><strong>Ønsket ændring</strong></td></tr>

			<tr><td>Roret</td><td><input name="xRoret" type="checkbox" value="<?   echo $xRoret;?>" <?   echo ReturnCheckStatus($yRoret);?> disabled></td><td><input name="ffRoret" type="checkbox" value="<?   echo $xRoret;?>" <?   echo ReturnCheckStatus($xRoret);?>></td></tr>
			<tr><td>Teor. styrmandskursus</td><td><input name="xTeoretiskStyrmandKursus" type="checkbox" value="<?   echo $xTeoretiskStyrmandKursus;?>" <?   echo ReturnCheckStatus($yTeoretiskStyrmandKursus);?> disabled></td><td><input name="ffTeoretiskStyrmandKursus" type="checkbox" value="<?   echo $xTeoretiskStyrmandKursus;?>" <?   echo ReturnCheckStatus($xTeoretiskStyrmandKursus);?>></td></tr>
			<tr><td>Styrmand</td><td><input name="xStyrmand" type="checkbox" value="<?   echo $xStyrmand;?>" <?   echo ReturnCheckStatus($yStyrmand);?> disabled></td><td><input name="ffStyrmand" type="checkbox" value="<?   echo $xStyrmand;?>" <?   echo ReturnCheckStatus($xStyrmand);?>></td></tr>
			<tr><td>Langtursstyrmand</td><td><input name="xLangtur" type="checkbox" value="<?   echo $xLangtur;?>" <?   echo ReturnCheckStatus($yLangtur);?> disabled></td><td><input name="ffLangtur" type="checkbox" value="<?   echo $xLangtur;?>" <?   echo ReturnCheckStatus($xLangtur);?>></td></tr>
			<tr><td>Gig 8'er styrmand</td><td><input name="xOrmen" type="checkbox" value="<?   echo $xOrmen;?>" <?   echo ReturnCheckStatus($yOrmen);?> disabled></td><td><input name="ffOrmen" type="checkbox" value="<?   echo $xOrmen;?>" <?   echo ReturnCheckStatus($xOrmen);?>></td></tr>
			<tr><td>Svavaret</td><td><input name="xSvava" type="checkbox" value="<?   echo $xSvava;?>" <?   echo ReturnCheckStatus($yOrmen);?> disabled></td><td><input name="ffSvava" type="checkbox" value="<?   echo $xSvava;?>" <?   echo ReturnCheckStatus($xSvava);?>></td></tr>
			<tr><td>Scullerret</td><td><input name="xSculler" type="checkbox" value="<?   echo $xSculler;?>" <?   echo ReturnCheckStatus($ySculler);?> disabled></td><td><input name="ffSculler" type="checkbox" value="<?   echo $xSculler;?>" <?   echo ReturnCheckStatus($xSculler);?>></td></tr>
			<tr><td>Kajakret</td><td><input name="xKajak" type="checkbox" value="<?   echo $xKajak;?>" <?   echo ReturnCheckStatus($yKajak);?> disabled></td><td><input name="ffKajak" type="checkbox" value="<?   echo $xKajak;?>" <?   echo ReturnCheckStatus($xKajak);?>></td></tr>
			<tr><td>Instruktør</td><td><input name="xRoInstruktoer" type="checkbox" value="<?   echo $xRoInstruktoer;?>" <?   echo ReturnCheckStatus($yRoInstruktoer);?> disabled></td><td><input name="ffRoInstruktoer" type="checkbox" value="<?   echo $xRoInstruktoer;?>" <?   echo ReturnCheckStatus($xRoInstruktoer);?>></td></tr>
			<tr><td>Styrmandsinstruktør</td><td><input name="xStyrmandInstruktoer" type="checkbox" value="<?   echo $xStyrmandInstruktoer;?>" <?   echo ReturnCheckStatus($yStyrmandInstruktoer);?> disabled></td><td><input name="ffStyrmandInstruktoer" type="checkbox" value="<?   echo $xStyrmandInstruktoer;?>" <?   echo ReturnCheckStatus($xStyrmandInstruktoer);?>></td></tr>
			<tr><td>Scullerinstruktør</td><td><input name="xScullerInstruktoer" type="checkbox" value="<?   echo $xScullerInstruktoer;?>" <?   echo ReturnCheckStatus($yScullerInstruktoer);?> disabled></td><td><input name="ffScullerInstruktoer" type="checkbox" value="<?   echo $xScullerInstruktoer;?>" <?   echo ReturnCheckStatus($xScullerInstruktoer);?>></td></tr>
			<tr><td>Kajakinstruktør</td><td><input name="xKajakInstruktoer" type="checkbox" value="<?   echo $xKajakInstruktoer;?>" <?   echo ReturnCheckStatus($yKajakInstruktoer);?> disabled></td><td><input name="ffKajakInstruktoer" type="checkbox" value="<?   echo $xKajakInstruktoer;?>" <?   echo ReturnCheckStatus($xKajakInstruktoer);?>></td></tr>
			<tr><td>Kaproer</td><td><input name="xKaproer" type="checkbox" value="<?   echo $xKaproer;?>" <?   echo ReturnCheckStatus($yKaproer);?> disabled></td><td><input name="ffKaproer" type="checkbox" value="<?   echo $xKaproer;?>" <?   echo ReturnCheckStatus($xKaproer);?>></td></tr>
			<tr><td>Motorbådsret</td><td><input name="xMotorboat" type="checkbox" value="<?   echo $xMotorboat;?>" <?   echo ReturnCheckStatus($yMotorboat);?> disabled></td><td><input name="ffMotorboat" type="checkbox" value="<?   echo $xMotorboat;?>" <?   echo ReturnCheckStatus($xMotorboat);?>></td></tr>
			
			<tr><td colspan=3>Kommentar fra indberetteren</td></tr>
			<tr><td colspan=3 bgcolor="#FFFFFF"><?   echo $xKommentar;?>&nbsp;</td></tr>

			<tr><td colspan=3>Rettelse - noter</td></tr>
			<tr><td colspan=3><input type="text" name="Fixed_comment" value="<?   echo $xFixed_comment;?>" size=80></td></tr>

			</table>
		</table>
		
	<input type="hidden" name="action" value="savechangesrights">
	<input type="hidden" name="errorno" value="<?   echo $Errorno;?>">
	<input type="hidden" name="memberid" value="<?   echo $xMemberID;?>">
	<input type="submit" value="Gem ændringer">
	<input type="button" value="Fortryd" onClick="window.location='fejladmin.asp?action=showerrorlistrights';">
	</form>
	<? 

  return $function_ret;
} 

function ShowErrorlistRights()
{
  extract($GLOBALS);


  ShowMenu();

  $opendatabase;
  $sql="select [Navn], [FejlID], [Kommentar], [Fixed] from Fejl_tblMembersSportData order by FejlId desc";
  $MyRS=$db->execute;  $sql);
  $Errorlist=$MyRS->getrows();
  $myrs->close;
  $closedatabase;

//Errorlist
//0 -- Navn
//1 -- FejlID
//2 -- Kommentar
//3 -- fixed


?>
	<table class=""rostat"" width=""600"">
		<tr bgcolor="#ffffff">
			<td>ID</td>
			<td>Navn</td>
			<td>Beskrivelse</td>
			<td>Rettet</td>
			<td> </td>
		</tr>
	<? 

  for ($c1=0; $c1<=count($Errorlist); $c1=$c1+1)
  {


    $xNavn=$Errorlist[0][$c1];
    $xFejlID=$Errorlist[1][$c1];
    $xAarsag=$Errorlist[2][$c1];
    $xFixed=$Errorlist[3][$c1];

    if ($xFixed=="True" || $xFixed=="Sand")
    {

      $xFixed="<font color='green'>Ja</font>";
    }
      else
    {

      $xFixed="<font color='red'>Nej</font>";
    } 


    if (($c1%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 

?>
		<td width="50"><a href="fejladmin.asp?action=ShowErrorReportRights&ErrorNo=<?     echo $xFejlID;?>"><?     echo $xFejlID;?></a></td>
		<td width="200"><a href="fejladmin.asp?action=ShowErrorReportRights&ErrorNo=<?     echo $xFejlID;?>"><?     echo $xNavn;?></a></td>
		<td width="400"><a href="fejladmin.asp?action=ShowErrorReportRights&ErrorNo=<?     echo $xFejlID;?>"><?     echo $xAarsag;?></a></td>
		<td width="50"><a href="fejladmin.asp?action=confirmdeleteerrorrights&ErrorNo=<?     echo $xFejlID;?>"><?     echo $xfixed;?></a></td>
		<td width="50"><a href="fejladmin.asp?action=confirmdeleteerrorrights&ErrorNo=<?     echo $xFejlID;?>"><strong>Slet</strong></a></td>
		</td>
		<? 



  }


?>
	</table>
	<? 

  return $function_ret;
} 


function ShowLoginForm()
{
  extract($GLOBALS);


?>
	<form name="login" action="fejladmin.php" method="post">
		<table border=0>
			<tr>
				<td>Brugernavn</td><td>
                <input type="text" name="username" size="20"></td>
			</tr>
			<tr>
				<td>Adgangskode</td><td>
                <input type="password" name="password" size="20"></td>
			</tr>
		</table>
	<input type="submit" value="Log ind">
	<input type="hidden" value="showmenu" name="action">
	</form>
	<? 

  return $function_ret;
} 


function ValidateLogin()
{
  extract($GLOBALS);


  $function_ret="";
  if ($_SESSION['username']=="admin" && $_SESSION['password']=="olDSinneR")
  {
    $function_ret="OK";
  } 

  return $function_ret;
} 

function ReturnCheckStatus($SomeBoolean)
{
  extract($GLOBALS);


  if ($SomeBoolean==true || $SomeBoolean=="TRUE" || $SomeBoolean=="true" || $SomeBoolean=="True")
  {
    $function_ret=" checked";
  } 
  if ($isdate[$SomeBoolean])
  {
    $function_ret=" checked";
  } 

  return $function_ret;
} 

function ShowMenu()
{
  extract($GLOBALS);


?>
	<p>Hop til fejllog:</p>
	<ul>
	<li><a href="fejladmin.php?action=showerrorlisttur">Listen over fejl vedr. ture</a></li>
	<li><a href="fejladmin.php?action=showerrorlistrights">Listen over vedr. rettigheder</a></li>
	</ul>
	<? 

  return $function_ret;
} 

?>
