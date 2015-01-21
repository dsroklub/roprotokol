<?
  session_start();
  session_register("username_session");
  session_register("password_session");
?>
<? // asp2php (vbscript) converted on Wed Jul 30 12:06:15 2014
 $CODEPAGE="1252";?>
<!-- #include file="databaseINC.php" -->

<html>
<head>
<title>Reservationer</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>

<body>
<h2>Reservationer</h2>

<? 
$xaction=${"action"};
$username=${"username"};
$password=${"password"};




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
  case "showfuturereservations":
    if (ValidateLogin(=="OK"))
    {

      showfuturereservations();
    } 


    break;
  case "insertreservation":
    if (ValidateLogin(=="OK"))
    {

      insertreservation();
    } 


    break;
  case "validateinsert":
    if (ValidateLogin(=="OK"))
    {

      ValidateInsert();
    } 


    break;
  case "deletereservation":
    if (ValidateLogin(=="OK"))
    {

      DeleteReservation();
    } 



    break;
  case "showmenu":

    if (ValidateLogin(=="OK"))
    {

      ShowMenu();
    } 


    break;
  case "login":

    ShowLoginForm();

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
function ShowLoginForm()
{
  extract($GLOBALS);


?>
	<form name="login" action="reservations.php" method="post">
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

function ShowMenu()
{
  extract($GLOBALS);


?>
	<p>Vælg handling:</p>
	<ul>
	<li><a href="reservations.php?action=showfuturereservations">Vis fremtidige reservationer</a></li>
	<li><a href="reservations.php?action=insertreservation">Opret ny reservation</a></li>
	</ul>
	<? 

  return $function_ret;
} 

function showfuturereservations()
{
  extract($GLOBALS);


  $sql="SELECT Reservation.ID, Båd.Navn, Reservation.Start, Reservation.Slut, Reservation.Beskrivelse, Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn FROM (Båd INNER JOIN Reservation ON Båd.BådID = Reservation.FK_BådID) INNER JOIN Medlem ON Reservation.FK_MedlemID = Medlem.MedlemID WHERE (((Reservation.Slut)>Now())) order by reservation.start desc;";
  $opendatabase;
  $rs=$db->execute;  $sql);
  if (!$rs->eof)
  {
    $Reservations=$rs->getrows();
  } 
  $closedatabase;

  if (is_array($reservations))
  {


?>
	<table class="rostat" width="600">
	<tr>
		<td bgcolor="#FFFFFF">Båd</td>
		<td bgcolor="#FFFFFF">Periode</td>
		<td bgcolor="#FFFFFF">Beskrivelse</td>
		<td bgcolor="#FFFFFF">Reserveret af</td>
		<td bgcolor="#FFFFFF"> </td>
	</tr>
	<? 

    for ($c1=0; $c1<=count($reservations); $c1=$c1+1)
    {

      $xID=$Reservations[0][$c1];
      $xBaad=$Reservations[1][$c1];
      $xPeriode=$Reservations[2][$c1]." til ".$Reservations[3][$c1];
      $xBeskrivelse=$Reservations[4][$c1];
      $xReserveretAf=$Reservations[5][$c1]." - ".$Reservations[6][$c1]." ".$Reservations[7][$c1];

      if (($c1%2)==0)
      {

        $rowhtml="<tr class=\"firstrow\">";
      }
        else
      {

        $rowhtml="<tr class=\"secondrow\">";
      } 


?>
		<?       echo $rowhtml;?><td><?       echo $xBaad;?></td><td><?       echo $xPeriode;?></td><td><?       echo $xBeskrivelse;?></td><td><?       echo $xReserveretAf;?></td><td><b><a href="reservations.asp?action=deletereservation&id=<?       echo $xID;?>">Slet</a></b></td></tr>
		<? 


    }

?>
	</table>
	<? 

  }
    else
  {

?>
	Ingen reservationer at vise.

	<ul>
	<li><a href="reservations.php?action=insertreservation">Opret ny reservation</a></li>
	</ul>
	
	<? 
  } 


  return $function_ret;
} 

function insertreservation()
{
  extract($GLOBALS);


  $opendatabase;
  $sql="select BådID, Navn from Båd";
  $RS=$db->execute;  $sql);
  $Boats=$rs->getrows();
  $RS->close;
  $closedatabase;

?>
	<form method="post" action="reservations.php">
		<table border=1 style="border-collapse: collapse" width=600>
			<tr>
			<td>
				<table border="0">
					<tr>
						<td>Båd</td>
						<td>
							<select name="boat">
								<? 
  $opendatabase;
  $sql="select * from [Båd]";
  $RS=$db->execute;  $sql);

  while(!($RS->eof))
  {


    $Ifselected="";
    if ($RS["BådID"]==$BoatID)
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
						</td>
					</tr>
					<tr>
						<td><p><font size=2 <?   echo $starterror;?>>Vises 
                fra (DD-MM-YYYY TT:MM:SS)</font></p></td>
						<td>
                        <input type="text" name="Start" value="<?   echo $start;?>" size="20"><img src="images/icon_reserveret.gif"></td>
					</tr>
					<tr>
						<td><p><font size=2 <?   echo $sluterror;?>>Vises 
                til (DD-MM-YYYY TT:MM:SS)</font></p></td>
						<td>
                        <input type="text" name="Slut" value="<?   echo $slut;?>" size="20"><img src="images/icon_reserveret.gif"></td>
					</tr>
					<tr>
						<td><p><font size=2 <?   echo $MedlemsnrError;?>>Reserveret 
                af (medlemsnr.)</font></p></td>
						<td><input type="text" name="Medlem" size=4 value="<?   echo $medlem;?>"></td>
					</tr>
					<tr>
						<td><p><font size=2 <?   echo $BeskrivelseError;?>>Beskrivelse</font></p></td>
						<td><input type="text" name="Beskrivelse" size="40" value="<?   echo $beskrivelse;?>"></td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	
	<input type="hidden" name="action" value="validateinsert">
	<input type="submit" value="Opret">
	<input type="button" value="Fortryd" onClick="window.location='reservations.asp?action=showmenu';">
	</form> 
	<? 

  return $function_ret;
} 

function ValidateInsert()
{
  extract($GLOBALS);


  $BoatID=${"boat"};
  $Start=${"Start"};
  $Slut=${"Slut"};
  $Medlem=${"Medlem"};
  $Beskrivelse=${"Beskrivelse"};

  if ($medlem=="")
  {
    $medlem="missing";
  } 

  if (strlen($Beskrivelse)<2)
  {
    $BeskrivelseError="color=\"red\"";
  } 
  $opendatabase;
  $sql="select MedlemID from Medlem where Medlemsnr='".$medlem."';";
  $rs=$db->execute;  $sql);
  if ($rs->eof)
  {

    $MedlemsnrError="color=\"red\"";
  }
    else
  {

    $Medlemsnr=$rs["MedlemID"];
  } 

  $rs->close;
  $closedatabase;

  if (!$isdate[$Start] || time()>$start)
  {
    $starterror="color=\"red\"";
  } 
  if (!$isdate[$Slut] || time()>$start)
  {
    $sluterror="color=\"red\"";
  } 

  if ($isdate[$start] && $isdate[$slut] && $Start>$Slut)
  {

    $starterror="color=\"red\"";
    $sluterror="color=\"red\"";
  } 


  $Errorstring=$MedlemsnrError.$BeskrivelseError.$StartError.$SlutError;

  if ($Errorstring!="")
  {

    insertreservation();
  }
    else
  {

    $sql="INSERT INTO Reservation (FK_BådID, Start, Slut, FK_MedlemID, Beskrivelse, OprettetDato) ".
      "VALUES (".$BoatID.", #".$start."#, #".$slut."#, ".$medlemsnr.", \"".$beskrivelse."\", #".strftime("%m/%d/%Y %H:%M:%S %p")."#)";
    $opendatabase;

    $db->execute($sql);
    $closedatabase;

?>
		<p>Reservationen er oprettet.</p>
		<button type="button" onClick="window.location='reservations.asp?action=showmenu';">Retur til menu</button>
		<? 

  } 


  return $function_ret;
} 

function DeleteReservation()
{
  extract($GLOBALS);

  $deleteid=${"id"};

  $opendatabase;
  $sql="DELETE Reservation.ID FROM Reservation WHERE (ID=".$deleteID.");";
  $db->execute($sql);
  $closedatabase;
?>
	<p>Reservationen er slettet</p>
	<button type="button" onclick="window.location='reservations.asp?action=showfuturereservations';">Retur til oversigt</button>		
	<? 


  return $function_ret;
} 

?>
