<?
  session_start();
  session_register("username_session");
  session_register("password_session");
?>
<? // asp2php (vbscript) converted on Wed Jul 30 12:05:26 2014
 $CODEPAGE="1252";?>
<!-- #include file="databaseINC.php" -->

<html>
<head>
<title>Reservationer</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>

<body>
<h2>Administration af både</h2>

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
  case "ShowAddBoatForm":

    if (ValidateLogin(=="OK"))
    {

      ShowAddBoatForm();
    } 


    break;
  case "ShowBoatList":

    if (ValidateLogin(=="OK"))
    {

      ShowBoatList();
    } 


    break;
  case "ValidateInsert":
    if (ValidateLogin(=="OK"))
    {

      ValidateInsert();
    } 


    break;
  case "ConfirmDeleteBoat":
    if (ValidateLogin(=="OK"))
    {

      ConfirmDeleteBoat();
    } 


    break;
  case "DeleteBoat":
    if (ValidateLogin(=="OK"))
    {

      DeleteBoat();
    } 


    break;
  case "ReportHeavyDamage":
    if (ValidateLogin(=="OK"))
    {

      ReportHeavyDamage();
    } 


    break;
  case "login":

    ShowLoginForm();

    break;
  case "notloggedin":

?>
Du er ikke logget ind. Gå til <a href="boatadmin.php?action=login">login</a>. 
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
	<form name="login" action="boatadmin.php" method="post">
		<table border=0>
			<tr>
				<td>Brugernavn</td><td><input type="text" name="username"></td>
			</tr>
			<tr>
				<td>Adgangskode</td><td><input type="password" name="password"></td>
			</tr>
		</table>
	<input type="submit" value="Log ind">
	<input type="hidden" value="ShowBoatList" name="action">
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



function ShowAddBoatForm()
{
  extract($GLOBALS);


?>
	<form method="post" action="boatadmin.php">
		<table border=1 style="border-collapse: collapse" width=600>
			<tr>
			<td>
				<table border="0">
					<tr>
						<td><font size=2 <?   echo $BoatNameError;?>>Navn</font></td>
						<td>
							<input type="text" name="Boatname" value="<?   echo $Boatname;?>">
						</td>
					</tr>
					<tr>
						<td>Bådtype</td>
						<td>
						<select name="GruppeID">
							<? 
  $opendatabase;
  $sql="SELECT GruppeID, Navn from Gruppe";
  $rs=$db->execute;  $sql);

  while(!($RS->eof))
  {

    $Ifselected="";
    if ($RS["GruppeID"]==$GroupID)
    {
      $Ifselected="selected";
    } 
    print "<option value=".$RS["GruppeID"]." ".$IfSelected.">".$RS["Navn"]."</option>";

    $RS->movenext;
  } 
  $RS->close;
  $closedatabase;
?>
						</select>

						</td>					
					</tr>
					<tr>
						<td>Beskrivelse</td>
						<td>
							<input type="text" name="Beskrivelse" size=40 value="<?   echo $Beskrivelse;?>">
						</td>
					</tr>
					<tr>
						<td><font size=2 <?   echo $InitialsError;?>>Oprettet af</font></td>
						<td>
							<input type="text" name="Initialer" size=6 value="<?   echo $Initialer;?>">
						</td>
					</tr>
					<tr>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	
	<input type="hidden" name="action" value="ValidateInsert">
	<input type="submit" value="Opret">
	<input type="button" value="Fortryd" onClick="window.location='boatadmin.asp?action=ShowBoatList';">
	</form> 
	<? 

  return $function_ret;
} 

function ValidateInsert()
{
  extract($GLOBALS);


  $BoatName=${"Boatname"};
  $GroupID=${"GruppeID"};
  $Initialer=${"Initialer"};
  $Beskrivelse=${"Beskrivelse"};

  $opendatabase;
  $sql="SELECT Pladser from Gruppe where GruppeID=".$GroupID;
  $rs=$db->execute;  $sql);
  $Pladser=$rs["Pladser"];
  $closedatabase;

  if (strlen($Boatname)==0)
  {
    $BoatNameError="color=\"red\"";
  } 
  if (strlen($Initialer)==0)
  {
    $InitialsError="color=\"red\"";
  } 

  $Errorstring=$BoatNameError.$InitialsError;

  if ($Errorstring!="")
  {

    ShowAddBoatForm();
  }
    else
  {

    $sql="INSERT INTO Båd (Navn, FK_GruppeID, Pladser, Beskrivelse, Initialer) ".
      "VALUES (\"".$Boatname."\", ".$GroupID.", ".$Pladser.", \"".$Beskrivelse."\", \"".$Initialer."\")";
    $opendatabase;
    $db->execute($sql);
    $closedatabase;

?>
		<p>Båden er oprettet.</p>
		<button type="button" onClick="window.location='boatadmin.asp?action=ShowBoatList';">Retur til menu</button>
		<? 

  } 


  return $function_ret;
} 

function ConfirmDeleteBoat()
{
  extract($GLOBALS);


  $Boatid=${"Boatid"};
  $FirstDate=$cdate["1/4/".strftime("%Y",time())];
  $LastDate=$cdate["1/11/".strftime("%Y",time())];
  if (time()>$Firstdate && time()<$Lastdate)
  {

    $DimmDelete="disabled";
?>
		<p>I perioden fra 1/4 til 1/11 er det ikke tilladt at slette både fra roprotokollen. I denne periode skal båden i stedet meldes svært skadet.</p>	
		<input type="button" onclick="window.location='boatadmin.asp?Action=ReportHeavyDamage&BoatID=<?     echo $BoatID;?>';" value="Rapporter båden svært skadet">
		<? 
  }
    else
  {

?>
		<p>Vil du slette båden?</p>
		<input type="button" onclick="window.location='boatadmin.asp?Action=DeleteBoat&BoatID=<?     echo $BoatID;?>';" value="Slet båden" <?     echo $DimmDelete;?>>
		<? 
  } 


  return $function_ret;
} 

function ReportHeavyDamage()
{
  extract($GLOBALS);

  $Boatid=${"Boatid"};

  $opendatabase;
  $sql="INSERT INTO Skade (FK_BådID, FK_Ansvarlig, Grad, Beskrivelse, Initialer) VALUES (".$BoatID.", 5083, 3, \"Meddelelse fra administratoren: Båden kan ikke længere udskrives.\", \"ADM\");";
  $db->execute($sql);
  $closedatabase;
?>
	<p>Båden er meldt svært skadet. Båden kan eventuelt slettes i vinterpausen.</p>
	<button type="button" onclick="window.location='boatadmin.asp?action=ShowBoatList';">Retur til oversigt</button>		
	<? 
  return $function_ret;
} 

function DeleteBoat()
{
  extract($GLOBALS);

  $Boatid=${"Boatid"};

  $opendatabase;
  $sql="DELETE [Båd].ID FROM [Båd] WHERE (BådID=".$BoatID.");";
  $db->execute($sql);
  $closedatabase;
?>
	<p>Båden er slettet</p>
	<button type="button" onclick="window.location='boatadmin.asp?action=ShowBoatList';">Retur til oversigt</button>		
	<? 

  return $function_ret;
} 

function ShowBoatList()
{
  extract($GLOBALS);


  $opendatabase;
  $sql="SELECT Båd.BådID, Gruppe.Navn AS Gruppe, Båd.Navn, Båd.Pladser, Båd.OprettetDato, Båd.Initialer FROM Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID ORDER BY Gruppe.Navn, Båd.Navn;";
  $MyRS=$db->execute;  $sql);
  $BoatArray=$MyRS->getrows();
  $myrs->close;
  $closedatabase;

//BoatArray
//0 -- BådID
//1 -- Bådtype
//2 -- Navn
//3 -- Pladser
//4 -- Oprettelsesdato
//5 -- Initialer


?>
	
	<input type="button" name="CreateNew" onclick="window.location='boatadmin.asp?action=ShowAddBoatForm';" value="Opret ny båd"> 
	
	<table class=""rostat"" width=""600"">
		<tr bgcolor="#ffffff">
			<td>ID</td>
			<td>Navn</td>
			<td>Type (Pladser)</td>
			<td>Oprettet</td>
			<td>Oprettet af</td>
			<td> </td>
		</tr>
	<? 

  for ($c1=1; $c1<=count($BoatArray); $c1=$c1+1)
  {


    $xBoatId=$BoatArray[0][$c1];
    $xBoatType=$BoatArray[1][$c1];
    $xBoatName=$BoatArray[2][$c1];
    $xBoatSeats=$BoatArray[3][$c1];
    $xBoatCRDate=$BoatArray[4][$c1];
    $xBoatCRInitials=$BoatArray[5][$c1];

    if (($c1%2)==0)
    {

      print "<tr class=\"firstrow\">";
    }
      else
    {

      print "<tr class=\"secondrow\">";
    } 

?>
		<td width="50"><?     echo $xBoatID;?></td>
		<td width="200"><?     echo $xBoatName;?></td>
		<td width="100"><?     echo $xBoatType;?> (<?     echo $xBoatSeats;?>)</td>
		<td width="80"><?     echo $xBoatCRDate;?></td>
		<td width="50"><?     echo $xBoatCRInitials;?></td>
		<td width="50"><a href="boatadmin.asp?action=ConfirmDeleteBoat&BoatID=<?     echo $xBoatID;?>"><strong>Slet</strong></a></td>
		</td>
		<? 



  }


?>
	</table>
	<? 

  return $function_ret;
} 

?>

