<?
  session_start();
  session_register("username_session");
  session_register("password_session");
?>
<? // asp2php (vbscript) converted on Wed Jul 30 12:06:56 2014
 $CODEPAGE="1252";?>
<!-- #include file="databaseINC.php" -->

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>

<body>

<? 
$username=${"username"};
$password=${"password"};
$action=${"action"};
$season=${"season"};
$startdato=${"startdato"};
$slutdato=${"slutdato"};
$submitaction=${"submitaction"};

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


switch ($action)
{
  case "login":
    ShowLoginForm();

    break;
  case "showform":

    if (ValidateLogin(=="OK"))
    {

      ShowSeasonSelectorForm("");
    } 


    break;
  case "confirmbackupseason":

    if (ValidateLogin(=="OK"))
    {

      if ($isdate[$startdato] && $isdate[$slutdato])
      {

        ShowConfirmationForm();
      }
        else
      {

        ShowSeasonSelectorForm("Datoerne er ikke angivet rigtigt (DD-MM-YYYY)");
      } 

    } 


    break;
  case "backupseason":

    if (ValidateLogin(=="OK"))
    {

      if ($submitaction=="      Ja      ")
      {

        DoBackup($season);
      }
        else
      {

        ShowSeasonSelectorForm("");
      } 

    } 


    break;
} 

?>


</body>
</html>

<? 
function ShowConfirmationForm()
{
  extract($GLOBALS);


?>
<h3>Bekræft:</h3>
<p>Du er ved at flytte alle ture for perioden <strong><?   echo $startdato;?> kl 12:00:00</strong> til <strong><?   echo $slutdato;?> kl. 12:00:00 </strong> over i en backuptabel i databasen.</p>
<p>Ønsker du at fortsætte?</p>
<form action="season.php" name="confirmform" method="post">
	<input type="submit" name="submitaction" value="      Ja      ">
	<input type="submit" name="submitaction" value="Annuller">
	<input type="hidden" name="season" value="<?   echo $season;?>">
	<input type="hidden" name="startdato" value="<?   echo $startdato;?>">
	<input type="hidden" name="slutdato" value="<?   echo $slutdato;?>">
	<input type="hidden" name="action" value="backupseason">
</form>
<? 
  return $function_ret;
} 
?>

<? 
function ShowSeasonSelectorForm($errormessage)
{
  extract($GLOBALS);


  $ThisYear=strftime("%Y",time());

  if (strftime("%m",time())<5)
  {
    $SelectedYear=$ThisYear-1;
  } 

  if ($startdato=="")
  {

    $startdatoprefill="DD-MM-YYYY";
  }
    else
  {

    $startdatoprefill=$startdato;
  } 


  if ($slutdato=="")
  {

    $slutdatoprefill="DD-MM-YYYY";
  }
    else
  {

    $slutdatoprefill=$slutdato;
  } 


?>

<h3>Angiv sæson</h3>
<p>Når man skifter sæson, tages der en kopi af alle ture fra den foregående sæson. Foregående sæson angives med et årstal. </p>
<p><font color="red"><b><?   echo $errormessage;?></b></font></p>
<p><strong>Hvilken sæson skal der tages backup af?</strong>

<form action="season.php" name="seasonform" method="post">
	<select name="season">
		
		<? 
  if ($SelectedYear!="")
  {

?>
		<option value="<?     echo $SelectedYear;?>" selected="selected"><?     echo $SelectedYear;?></option>
		<option value="<?     echo $SelectedYear-1;?>"><?     echo $SelectedYear-1;?></option>
		<? 
  }
    else
  {

    for ($c1=0; $c1<=2; $c1=$c1+1)
    {
      $PickYear=$ThisYear-$c1;
?>
			<option value="<?       echo $PickYear;?>"><?       echo $PickYear;?></option>
			<? 

    }

  } 

?>
	</select></p>
	<p></p>
	<p><strong>Angiv startdato og slutdato for den sæson, du ønsker at kopiere data for</strong>
	<table border=0>
		<tr>
		<td><p>Fra (f.eks. standerhejsning)</p></td>
		<td><input type="text" name="startdato" id="startdato" size="12" value="<?   echo $startdatoprefill;?>" onclick="this.value='';"></td>
		</tr>
		<tr>
		<td><p>Til (f.eks. standerstrygning)</p></td>
		<td><input type="text" name="slutdato" id="slutdato" size="12" value="<?   echo $slutdatoprefill;?>" onclick="this.value='';"></td>
		</tr>
	</table></p>
	<p>Bemærk: Der kopieres ture fra standerhejsningsdatoen kl. 12 og til standerstrygningsdatoen kl. 12.</p>
	<input type="hidden" name="action" value="confirmbackupseason">
	<input type="submit" name="submit" id="submit" value="Tag backup">
	
</form>

<? 

  return $function_ret;
} 
?>

<? 
function DoxBackup($backupyear)
{
  extract($GLOBALS);


  $WherePart1="(Tur.Ud>#".$startdato." 12:00:00# And Tur.Ud<#".$slutdato." 12:00:00#)";
  $WherePart2="(OprettetDato>#".$startdato." 12:00:00# And OprettetDato<#".$slutdato." 12:00:00#)";
  $sql_createtur="CREATE TABLE Tur_backup".$Backupyear." (TurID INTEGER, FK_BådID INTEGER, Ud DATETIME, Ind DATETIME, ForvInd DATETIME, Destination TEXT, Meter INTEGER, FK_TurTypeID INTEGER, Kommentar NOTE, OprettetDato DATETIME, RedigeretDato DATETIME, Initialer TEXT, DESTID INTEGER)";
  $sql_createturdeltager="CREATE TABLE Turdeltager_backup".$Backupyear." (FK_TurID INTEGER, Plads INTEGER, FK_MedlemID INTEGER, Navn TEXT, OprettetDato DATETIME, RedigeretDato DATETIME, Initialer TEXT);";
  $sql_inserttur="INSERT INTO Tur_backup".$Backupyear." SELECT * FROM Tur Where (".$WherePart1.")";
  $sql_insertturdeltager="INSERT INTO Turdeltager_backup".$Backupyear." SELECT * FROM Turdeltager WHERE (".$WherePart2.");";
  $sql_deletetur="DELETE Tur.Ud FROM Tur WHERE (".$WherePart1.")";
//sql_deleteturdeltager = "DELETE Turdeltager.Oprettetdato FROM Turdeltager WHERE (" & WherePart2 & ");"
  $sql_deleteturdeltager="DELETE TurDeltager.*, Tur.TurID FROM TurDeltager LEFT JOIN Tur ON TurDeltager.FK_TurID = Tur.TurID WHERE (((Tur.TurID) Is Null));";
//Den sidste sql sletter alle urealterede turdeltagere i stedet for at slette dem per kriterie, 
//da dette ikke virkeded på grund af, at tidspunktet blot er angivet som en dato i Turdeltager-tabellen

  print $_POST[];

  print "<p>".$sql_createtur."</p>";
  print "<p>".$sql_createturdeltager."</p>";

  return $function_ret;
} 
?>

<? 
function DoBackup($Backupyear)
{
  extract($GLOBALS);


//startdato=replace(startdato,"-","/")
//slutdato=replace(slutdato,"-","/")

  $WherePart1="(Tur.Ud>#".$startdato." 12:00:00# And Tur.Ud<#".$slutdato." 12:00:00#)";
  $WherePart2="(OprettetDato>#".$startdato." 12:00:00# And OprettetDato<#".$slutdato." 12:00:00#)";
  $sql_createtur="CREATE TABLE Tur_backup".$Backupyear." (TurID INTEGER, FK_BådID INTEGER, Ud DATETIME, Ind DATETIME, ForvInd DATETIME, Destination TEXT, Meter INTEGER, FK_TurTypeID INTEGER, Kommentar NOTE, OprettetDato DATETIME, RedigeretDato DATETIME, Initialer TEXT, DESTID INTEGER)";
  $sql_createturdeltager="CREATE TABLE Turdeltager_backup".$Backupyear." (FK_TurID INTEGER, Plads INTEGER, FK_MedlemID INTEGER, Navn TEXT, OprettetDato DATETIME, RedigeretDato DATETIME, Initialer TEXT);";
  $sql_inserttur="INSERT INTO Tur_backup".$Backupyear." SELECT * FROM Tur Where (".$WherePart1.")";
  $sql_insertturdeltager="INSERT INTO Turdeltager_backup".$Backupyear." SELECT * FROM Turdeltager WHERE (".$WherePart2.");";
  $sql_deletetur="DELETE Tur.Ud FROM Tur WHERE (".$WherePart1.")";
  $sql_deleteturdeltager="DELETE Turdeltager.Oprettetdato FROM Turdeltager WHERE (".$WherePart2.");";

  $fExistTable=0;
  $strTableName="Tur_backup".$Backupyear;

  $opendatabase;
  // $oCat is of type "ADOX.Catalog"
  $oCat=$ActiveConnection;  echo $db;

  foreach ($oCat->Tables as $tbl)
  {
    if ($tbl->Name==$strTableName)
    {

      $fExistsTable=1;
    } 

  }
  $oCat=null;



  if ($fExistsTable!=1)
  {

//db.execute (sql5)
//db.execute (sql6)	
    $db->execute($sql_createtur);
    $db->execute($sql_createturdeltager);
  } 


  $db->execute($sql_inserttur);
  $db->execute($sql_insertturdeltager);

  $db->execute($sql_deletetur);
  $db->execute($sql_deleteturdeltager);

  $closedatabase;
?>
<h3>Systemmeddelelse</h3>
<p>Backuprutinen er udført.</p>
<? 

  return $function_ret;
} 
?>

<? 
function ShowLoginForm()
{
  extract($GLOBALS);


?>
<h2>Skift sæson</h2>

	<form name="login" action="season.php" method="post">
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
	<input type="hidden" value="showform" name="action">
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
?>
