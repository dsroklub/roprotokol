<?
  session_start();
//  session_register("username_session");
//  session_register("password_session");
?>
<? // asp2php (vbscript) converted on Sun Aug 11 21:21:27 2013
 $CODEPAGE="1252";?>
<!-- #include file="databaseINC.php" -->
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="../roprotokol.css">
</head>

<BODY background="images/baggrund.jpg" bgproperties="fixed">

	<? 
$Action=${"action"};
$BoatID=${"BoatID"};
$username=${"username"};
$password=${"password"};
$returnaction=${"returnaction"};

if ($action=="ShowConvert" || $action=="Convert")
{

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

} 


switch ($Action)
{
  case "":
    ShowMemberForm();
    break;
  case "login":
    ShowLoginForm();
    break;
  case "ValidateInsert":
    ValidateInsert();
//Case "Insert":  InsertNewMember
    break;
case "ShowConvert":
  if (ValidateLogin()=="OK") {
    ShowConverterForm();
    } 
  break;
case "Convert":
  if (ValidateLogin() == "OK") {
    DoConversion();
  } 
  break;
} 
?>

</body>
</html>

<? 
function ValidateInsert()
{
  extract($GLOBALS);


  if (!$isdate[${"Birthdate"}])
  {

    $errormessage=$errormessage."Fødselsdagen skal angives som en valid dato. ";
  } 

  if (strlen(${"firstname"})<2 || strlen(${"firstname"})<2)
  {

    $errormessage=$errormessage."Du skal angive både fornavn og efternavn.";
  } 

  if ($errormessage=="")
  {

    InsertNewMember();
  }
    else
  {

    ShowMemberForm();
  } 


  return $function_ret;
} 
?>

<? 
function ShowLoginForm()
{
  extract($GLOBALS);


?>
	<h2>Overfør ture fra midlertidigt medlemsnummer til permanent</h2>
	<form name="login" action="opretroer.php" method="post">
		<table border=0>
			<tr>
				<td>Brugernavn</td><td><input type="text" name="username"></td>
			</tr>
			<tr>
				<td>Adgangskode</td><td><input type="password" name="password"></td>
			</tr>
		</table>

	<input type="submit" value="Log ind">
	<input type="hidden" value="ShowConvert" name="action">
	</form>
	<? 

  return $function_ret;
} 


function ValidateLogin() {
  extract($GLOBALS);


  $function_ret="";
  if ($_SESSION['username']=="admin" && $_SESSION['password']=="olDSinneR")
  {
    $function_ret="OK";
  } 

  return $function_ret;
} 
?>

<? 
function InsertNewMember()
{
  extract($GLOBALS);


  $tempmemberid=${"tempmemberid"};
  $Birthdate=str_replace(chr(34),"´",str_replace("'","´",${"Birthdate"}));
  $Firstname=str_replace(chr(34),"´",str_replace("'","´",${"Firstname"}));
  $Lastname=str_replace(chr(34),"´",str_replace("'","´",${"Lastname"}));

  $opendatabase;
  $sql="INSERT INTO Medlem (Medlemsnr, Fornavn, Efternavn, Fødselsdag) VALUES ('".$tempmemberID."', '".$Firstname."', '".$Lastname."', '".$Birthdate."');";
  $db->execute($sql);
  $closedatabase;



?>
	<h3>Roeren er oprettet</h3>
	<p>Du kan enten vælge at fortsætte ind-/udskrigningen af turen eller du kan oprette flere roere.</p>
	<form action="opretroer.php" method="post">
	<input type="hidden" name="BoatID" value="<?   echo $BoatID;?>">
	<input type="button" onClick="window.location=('dsrbookboat.php?boatid=<?   echo $boatID;?>&action=EditerTur&ForceRefreshCache=1&returnaction=<?   echo $returnaction;?>');" value="Tilbage til ind-/udskrivning">
	<input type="submit" value="Opret flere roere">
	</form>
	<? 

  return $function_ret;
} 
?>

<? 
function DoConversion()
{
  extract($GLOBALS);


  $temporarymemberID=${"temporarymemberID"};
  $MemberID=${"MemberID"};

  $opendatabase;
  $sql="select MedlemID from Medlem WHERE Medlemsnr='".$temporarymemberID."'";
  $rs=$db->query($sql);
  $SourceID=$rs["MedlemID"];
  $rs->close;
  $sql="select MedlemID from Medlem WHERE Medlemsnr='".$MemberID."'";
  $rs=$db->query($sql);
  $DestID=$rs["MedlemID"];
  $rs->close;

  $sql="UPDATE Turdeltager SET FK_MedlemID=".$DestID." WHERE FK_medlemID=".$SourceID;
  $db->execute($sql);

  $sql="DELETE FROM Medlem WHERE MedlemID=".$SourceID;
  $db->execute($sql);

  $closedatabase;

?>
	<p>Roerens ture er nu flyttet fra det midlertidige medlemsnummer til det permanente.</p>
	<input type="button" onClick="window.location=('opretroer.php?action=ShowConvert');" value="Ny konvertering">
	</form>
	<? 

  return $function_ret;
} 
?>

<? 
function ShowMemberForm()
{
  extract($GLOBALS);

  $NextMemberIDK=GetNextMemberID("K");
  $NextMemberIDN=GetNextMemberID("N");
  $NextMemberIDX=GetNextMemberID("X");
?>

	<h2>Opret roer</h2>
	<p>Her kan du oprette en roer, som ikke i forvejen findes i roprotokollen.</p>
	<p><font color="red"><?   echo $errormessage;?></font> 

	<form name="opretform" action="opretroer.php" method="post">
	<table border=0>
		<tr>
			<td width=200><p>Type</p></td>
			<td>
				<select name="Roertype" onChange="SetTempMemberID(this.value);">
					<option selected value="k">Kanin</option>
					<option value="n">Nyt medlem (ikke kanin!)</option>
					<option value="r">Roer fra anden klub</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><p>Fornavn</p></td>
			<td>
				<? 
  if (${"firstname"}!="")
  {
    $prefill=${"firstname"};
  } ?>
				<input name="Firstname" id="Firstname" size="20" value="<?   echo $prefill;?>" onChange="ValidateFields();" onBlur="ValidateFields();" onKeyUp="ValidateFields();"><font color="red"> *</font>
			</td>
			<td>
				<p>Efternavn</p>
			</td>
			<td>
				<? 
  if (${"lastname"}!="")
  {
    $prefill=${"lastname"};
  } ?>
				<input name="Lastname" id="Lastname" size="20" value="<?   echo $prefill;?>" onChange="ValidateFields();" onBlur="ValidateFields();" onKeyUp="ValidateFields();"><font color="red"> *</font>
			</td>
		</tr>
		<tr>
			<td><p>Fødselsdato</p></td>
			<td>
				<? 
  $prefill="DD-MM-YYYY";
  if (${"Birthdate"}!="")
  {
    $prefill=$birthdate;
  } ?>
				<input name="Birthdate" id="Birthdate" size="20" onFocus="if (this.value=='DD-MM-YYYY'){this.value='';this};" onChange="ValidateFields();" onBlur="ValidateFields();" onKeyUp="ValidateFields();" value="<?   echo $prefill;?>"><font color="red"> *</font>
			</td>
		</tr>
		<tr>
			<td><p>Midlertidigt medlemsnummer</p></td>
			<td><input name="tempmemberid" type="hidden" id="tempmemberid" value="<?   echo $NextMemberIDK;?>"> 
			<table border=1 style="border-collapse: collapse;" bordercolor="#000000" cellpadding="3">
				<tr><td>
				<b><span id="tempmemberid2"><?   echo $NextMemberIDK;?></span></b>
				</td></tr>
			</table>
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="returnaction" value="<?   echo $returnaction;?>">
	<input type="hidden" name="BoatID" value="<?   echo $BoatID;?>">
	<input type="hidden" name="action" value="ValidateInsert">
	<input type="submit" id="Submitbutton" value="Opret" disabled>
	</form>

	<table border=1 bgcolor="#FFFFFF" bordercolor="#000000" style="border-collapse: collapse">
		<tr>
		<td>
		<p>
		<b>VIGTIGT!</b>
		Når du har oprettet roeren, får roeren et <u><strong>midlertidigt medlemsnummer</strong></u>, og kan udskrives. 
		Hvis roeren senere får et permanent medlemsnummer flyttes turene fra det midlertidige medlemsnummer til det permanente. Kontakt <u><b>instruktionsrochefen</b></u>, hvis dette ikke sker automatisk.</p>
		</td>
		</tr>
	</table>

	
	<script language="JavaScript">
		function SetTempMemberID(mType)
			{
			switch (mType)
				{
				case "k": 
					document.getElementById('tempmemberid').value = "<?   echo $NextMemberIDK;?>";
					document.getElementById('tempmemberid2').innerHTML = "<?   echo $NextMemberIDK;?>";
					break;
				case "n": 
					document.getElementById('tempmemberid').value = "<?   echo $NextMemberIDN;?>";
					document.getElementById('tempmemberid2').innerHTML = "<?   echo $NextMemberIDN;?>";
					break;
				case "r": 
					document.getElementById('tempmemberid').value = "<?   echo $NextMemberIDX;?>";
					document.getElementById('tempmemberid2').innerHTML = "<?   echo $NextMemberIDX;?>";
					break;
				}
			}
			
		function ValidateFields()
			{
			Firstname=document.getElementById('Firstname').value;
			Lastname=document.getElementById('Lastname').value;
			Birthdate=document.getElementById('Birthdate').value;
			
			document.getElementById('Submitbutton').disabled = true; 
			if (Firstname.length != 0)
				{
				if (Lastname.length != 0)
					{
					if (Birthdate.length != 0 && Birthdate!='DD-MM-YYYY')
						{
						document.getElementById('Submitbutton').disabled = false;
						}			
					}
				}
			}
	</script>
	<? 
  return $function_ret;
} 
?>

<? 
function GetNextMemberID($FirstLetter)
{
  extract($GLOBALS);

  $opendatabase;
  // FIXME NEL hvad er MID?
  $sql="SELECT TOP 1 Mid([Medlemsnr],2,5) AS MemberExp FROM Medlem WHERE (Medlem.Medlemsnr Like '".$FirstLetter."%') GROUP BY Mid(Medlemsnr,2,5) ORDER BY Mid(Medlemsnr,2,5) DESC;";
  $rs=$db->query($sql);
  if (!$rs->eof) {

    $function_ret=$FirstLetter.substr("0000".intval($rs["MemberExp"])+1,strlen("0000".intval($rs["MemberExp"])+1)-(4));
  }
    else
  {

    $function_ret=$FirstLetter."0000";
  } 

  $rs->close;
  $closedatabase;
  return $function_ret;
} 
?>

<? 
function ShowConverterForm()
{
  extract($GLOBALS);

?>
	<h2>Konverter midlertidig roer til permanent</h2>
	<p>Denne funktion skal du bruge, hvis du skal overflytte ture fra et midlertidigt medlemsnummer til et permanent medlemsnummer.</p>
	
	<? 
  $opendatabase;
  $sql="SELECT Medlem.Medlemsnr, [Fornavn] & ' ' & [Efternavn] AS Navn FROM Medlem;";
  $rs=$db->query($sql);
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
					document.getElementById('convertsubmit').disabled=true;
			for (var i=0;i<MemberIDArray.length;i++)
				{
				if (memberid == MemberIDArray[i])
					{
					document.getElementById('Membername').value=MembernameArray[i];
					document.getElementById('convertsubmit').disabled=false;
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

	<form action="opretroer.php?action=Convert" method="post">
		<table border=0>
			<tr>
				<td width=40><b><font size="+3">1.</font></b></td>
				<td colspan=3>Vælg det midlertidige medlem, der skal konverteres:</td>
			</tr>
			<tr>
				<td></td>
				<td colspan=3>				
					<select name="temporarymemberID">
						<? 
  $opendatabase;
  $sql="select Medlemsnr, Fornavn, Efternavn, Fødselsdag FROM Medlem WHERE ((Medlem.Medlemsnr Like 'K%') OR (Medlem.Medlemsnr Like 'N%')) ORDER BY [Medlemsnr]";
  $rs=$db->query($sql);

  if (!$rs->eof)
  {

    $MyArray=$rs->getrows();
    for ($c1=0; $c1<=count($MyArray); $c1=$c1+1)
    {
      print "<option value='".$MyArray[0][$c1]."'>".$MyArray[0][$c1]." - ".$MyArray[1][$c1]." ".$MyArray[2][$c1]." (".$MyArray[3][$c1].")</option>";

    }

  } 

  $rs->close;
  $closedatabase;
?>
					</select>
					<p></p>
				</td>
			</tr>
			<tr>
				<td width=40><b><font size="+3">2.</font></b></td>
				<td colspan=3>
				Angiv det permanente medlemsnummer, turene skal flyttes til.<br>
				</td>
			</tr>
			<tr>			
				<td></td>
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
				<td width=40><b><font size="+3">3.</font></b></td>
				<td colspan=3>
				<input type="submit" value="Udfør konvertering" id="convertsubmit" disabled>
				</td>
			</tr>
		</table>
	</form>
	
	<table border=1 bgcolor="#FFFFFF" bordercolor="#000000" style="border-collapse: collapse">
		<tr>
		<td>
		<p>
		<b>VIGTIGT!</b>
		Når du har konverteret en roer til et permanent medlemsnummer, kan ændringen ikke fortrydes.</td>
		</tr>
	</table>
	
	<? 
  return $function_ret;
} 
?>
