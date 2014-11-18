<?
  session_start();
  session_register("username_session");
  session_register("password_session");
?>
<? // asp2php (vbscript) converted on Wed Jul 30 12:06:03 2014
 $CODEPAGE="1252";?>
<!-- #include file="databaseINC.php" -->

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>

<body>
<h2>Røde svensknøgler</h2>
<? 
$username=${"username"};
$password=${"password"};
$action=${"action"};
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

      ShowRedkeyForm();
    } 


    break;
  case "showlist":

    if (ValidateLogin(=="OK"))
    {

      ShowRedkeyList();

    } 


    break;
  case "insertredkey":

    if (ValidateLogin(=="OK"))
    {

      InsertRedkey();
    } 


    break;
  case "confirmremoveredkey":

    if (ValidateLogin(=="OK"))
    {

      ConfirmRemoveRedkey();
    } 


    break;
  case "removeredkey":

    if (ValidateLogin(=="OK"))
    {

      if ($submitaction=="Bekræft")
      {

        RemoveRedkey();
      }
        else
      {

        ShowRedkeyList();
      } 

    } 


    break;
  case "showredkeymenu":

    if (ValidateLogin(=="OK"))
    {

      ShowRedkeyMenu();
    } 



    break;
} 

?>


</body>
</html>


<? 
function ConfirmRemoveRedkey()
{
  extract($GLOBALS);


  $xid=${"id"};

?>


<p>Ønsker du at fjerne den røde svensknøgle?</p>
<form action="redkey.php" name="confirmform" method="post">
	Angiv årsag<br> <input type="text" size=50><br>
	<input type="submit" name="submitaction" value="Bekræft">
	<input type="submit" name="submitaction" value="Annuller">
	<input type="hidden" name="id" value="<?   echo $xid;?>">
	<input type="hidden" name="action" value="removeredkey">
</form>
<? 
  return $function_ret;
} 
?>

<? 
function RemoveRedkey()
{
  extract($GLOBALS);

  $deleteid=${"id"};

  $opendatabase;
  $sql="Update Vintervedligehold SET HasRedKey=false WHERE (ID=".$deleteID.");";
  $db->execute($sql);
  $closedatabase;
?>
	<p>Den røde svensknøgle er fjernet</p>
	<button type="button" onclick="window.location='redkey.asp?action=showlist';">Retur til oversigt</button>		
	<? 


  return $function_ret;
} 
?>

<? 
function ShowRedkeyMenu()
{
  extract($GLOBALS);

?>
	<p>Vælg handling:</p>
	<ul>
	<li><a href="redkey.php?action=showlist">Vis tildelte svensknøgler</a></li>
	<li><a href="redkey.php?action=showform">Tildel rød svensknøgle</a></li>
	</ul>
	<? 

  return $function_ret;
} 
?>

<? 
function ShowRedkeyList()
{
  extract($GLOBALS);


  $sql="SELECT Vintervedligehold.Medlemsnr, [Fornavn] & \" \" & [Efternavn] AS Navn, Vintervedligehold.Season, Vintervedligehold.HasRedKey, Vintervedligehold.DeletedReason, Vintervedligehold.Id ".
    "FROM Vintervedligehold INNER JOIN Medlem ON Vintervedligehold.Medlemsnr = Medlem.Medlemsnr GROUP BY Vintervedligehold.Medlemsnr, [Fornavn] & \" \" & [Efternavn], Vintervedligehold.Season, Vintervedligehold.HasRedKey, Vintervedligehold.DeletedReason, Vintervedligehold.Id ".
    "ORDER BY Vintervedligehold.Medlemsnr, Vintervedligehold.Season DESC;";

  $opendatabase;
  $rs=$db->execute;  $sql);
  if (!$rs->eof)
  {
    $Redkeys=$rs->getrows();
  } 
  $closedatabase;

  if (is_array($Redkeys))
  {


?>
	<table class="rostat" width="600">
	<tr>
		<td bgcolor="#FFFFFF"><p>Medlemsnr</p></td>
		<td bgcolor="#FFFFFF"><p>Navn</p></td>
		<td bgcolor="#FFFFFF"><p>Sæson</p></td>
		<td bgcolor="#FFFFFF"><p>Status</p></td>
		<td bgcolor="#FFFFFF"> </td>
	</tr>
	<? 

    for ($c1=0; $c1<=count($Redkeys); $c1=$c1+1)
    {

      $xMedlemsnr=$Redkeys[0][$c1];
      $xNavn=$Redkeys[1][$c1];
      $xSaeson=$Redkeys[2][$c1];
      $xHasRedKey=$Redkeys[3][$c1];
      $xDeletedReason=$Redkeys[4][$c1];
      $xID=$Redkeys[5][$c1];

      if (($c1%2)==0)
      {

        $rowhtml="<tr class=\"firstrow\">";
      }
        else
      {

        $rowhtml="<tr class=\"secondrow\">";
      } 


      if ($xHasRedKey=="Sand")
      {

        $xHasRedKey="<img src=\"images/icon_redwrench.gif\" border=0 alt=\"Har ikke deltaget i vintervedligehold\">";
        $DelPhrase="<a href='redkey.asp?action=confirmremoveredkey&id=".$xID."'>Slet</a>";
      }
        else
      {

        $xHasRedKey="<font color='green'>Slettet. ".$xDeletedReason."</font>";
        $DelPhrase="";
      } 


?>
		<?       echo $rowhtml;?><td><?       echo $xMedlemsnr;?></td><td><?       echo $xNavn;?></td><td><?       echo $xSaeson;?></td><td><?       echo $xHasRedKey;?></td><td><strong><?       echo $DelPhrase;?></strong></td></tr>
		<? 


    }

?>
	</table>
	<? 

  }
    else
  {

?>
	<p>Ingen tildelinger at vise.</p>

	<? 
  } 


?>
	<button type="button" onclick="window.location='redkey.asp?action=showredkeymenu';">Retur til menuen</button>		
	<? 


  return $function_ret;
} 
?>

<? 
function InsertRedkey()
{
  extract($GLOBALS);


  $MemberID=${"MemberID"};
  $Saeson=${"Saeson"};

  $opendatabase;
  $sql="SELECT Vintervedligehold.Medlemsnr,* FROM Vintervedligehold WHERE  (((Vintervedligehold.Medlemsnr)=\"".$MemberID."\") AND ((Vintervedligehold.Season)=".$Saeson.") AND ((Vintervedligehold.HasRedKey)=True));";

  $rs=$db->execute;  $sql);
  if ($rs->eof)
  {


    $rs->close;
    $sql="INSERT INTO Vintervedligehold (Medlemsnr, Season, HasRedKey) VALUES (".$MemberID.", ".$Saeson.", true);";
    $db->execute($sql);
    $closedatabase;

?>
	<p>Du har tildelt en rød svensknøgle til <strong><?     echo ${"Membername"};?></strong> for sæsonen <strong><?     echo $saeson;?></strong>.</p>
	<? 

  }
    else
  {


    $rs->close;
?>
	<p>Roeren <strong><?     echo ${"Membername"};?></strong> har en rød svensknøgle for sæsonen <strong><?     echo $saeson;?></strong> i forvejen.</p>
	<? 

  } 


?>
	<button type="button" onclick="window.location='redkey.asp?action=showredkeymenu';">Retur til menuen</button>		
	<? 

  return $function_ret;
} 
?>

<? 
function ShowRedkeyForm()
{
  extract($GLOBALS);


  $opendatabase;
  $sql="SELECT Medlem.Medlemsnr, [Fornavn] & ' ' & [Efternavn] AS Navn FROM Medlem;";
  $rs=$db->execute;  $sql);
  $MemberPickerArray=$rs->getrows();
  $rs->close;

?>
	
	<? 

  if (is_array($MemberPickerArray))
  {


    $ArrayBlock=$ArrayBlock."MembernameArray = new Array(".count($MemberPickerArray).");"."\r\n";
    $ArrayBlock=$ArrayBlock."MemberIDArray= new Array(".count($MemberPickerArray).");"."\r\n";

    for ($c1=0; $c1<=count($MemberPickerArray); $c1=$c1+1)
    {
      $ArrayBlock=$ArrayBlock."MembernameArray[".$c1."]=\"".$MemberPickerArray[1][$c1]."\";"."\r\n";
      $ArrayBlock=$ArrayBlock."MemberIDArray[".$c1."]=\"".$MemberPickerArray[0][$c1]."\";"."\r\n";

    }


  } 


  $closedatabase;

?>

	<script language="JavaScript">
	
		<?   echo $Arrayblock;?>
	
		function AutoFillName()
			{
			memberid = document.getElementById('MemberID').value;
					document.getElementById('giveredkey').disabled=true;
			for (var i=0;i<MemberIDArray.length;i++)
				{
				if (memberid == MemberIDArray[i])
					{
					document.getElementById('Membername').value=MembernameArray[i];
					document.getElementById('giveredkey').disabled=false;
					}
				}

			}
	
	function FillMemberSelector()
		{
		SearchWord=document.getElementById('Membername').value;
		
		if (SearchWord.length>=3)
			{
			HitCnt = 0;			
			NewInnerHTML= "<SELECT style='WIDTH: 200px' name='Cmbmembername' id='Cmbmembername' onchange='FillFromSelector();'>"; 
					for (var i=0;i<MembernameArray.length;i++)
						{
						MemberID=MemberIDArray[i];
						Membername=MembernameArray[i];
							MyReg = new RegExp(SearchWord , "i");		
							result =Membername.search(MyReg);	
							if (result>=0) 
								{
								NewInnerHTML = NewInnerHTML + "<option value='" + MemberID + "'>" + MemberID + " - " + Membername + "</option>"
								HitCnt++;
								if (HitCnt==1)
									{
									MemberIDx=MemberID;
									Membernamex = Membername;
									}
								}
						}	
					NewInnerHTML = NewInnerHTML + "</select>"	
					document.getElementById('MemberSelector').innerHTML=NewInnerHTML;
				if (HitCnt==1)
					{
					document.getElementById('MemberID').value = MemberIDx;
					document.getElementById('Membername').value = Membernamex;
					}
			}
		}
	
	function FillFromSelector()
		{
		Selected = document.getElementById('Cmbmembername').selectedIndex;
		SelectedValue= document.getElementById('Cmbmembername').options[Selected].value;
		document.getElementById('MemberID').value = SelectedValue;
			for (var i=0;i<MemberIDArray.length;i++)
				{
				if (SelectedValue == MemberIDArray[i])
					{
					document.getElementById('Membername').value=MembernameArray[i];
					}
				}

		}
	
	</script>

<h3>Tildel rød svensknøgle</h3>
<p>Her kan du tilføje nye "røde svensknøgler". Du skal starte med at fremsøge roeren på navn eller medlemsnummer.
<form action="redkey.php" name="confirmform" method="post">
	<table border=0>
		<tr>
				<td>
				<strong>Medlemsnr</strong><br>
				<INPUT id="MemberID" size="8" name="memberid" onChange="AutoFillName();"></TD>
				</td>
				<td>
				<strong>Navn</strong> (indtast min. 3 tegn for at søge)<br>
				<INPUT id="Membername" size="32" name="membername"  onKeyUp="FillMemberSelector();"></TD>
				</td>
				<td>
				<strong>Søgeresultater</strong> (vælg fra liste)<br>
				<span id="MemberSelector">
				<SELECT style="WIDTH: 200px" name="Cmbmembername" id="Cmbmembername"> 
					<option>...</option>
				</SELECT>						
				</span>
				</td>
		</tr>
		<tr>
			<td colspan="2"><p>Sæson som den røde nøgle skal gælde for</p></td>
			<td><input type="text" name="saeson" size=5 value="<?   echo strftime("%Y",time());?>"></td>
		</tr>
	</table>
	<input type="submit" value="Tildel rød nøgle" id="giveredkey">
	<input type="hidden" name="action" value="insertredkey">
</form>
<? 
  return $function_ret;
} 
?>


<? 
function ShowLoginForm()
{
  extract($GLOBALS);


?>

	<form name="login" action="redkey.php" method="post">
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
	<input type="hidden" value="showredkeymenu" name="action">
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
