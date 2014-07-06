<?php 
ini_set('display_errors', 'On');
include "DatabaseINC.php";
 // asp2php (vbscript) converted on Sun Aug 11 21:12:09 2013
 $CODEPAGE="1252";
?>
<!-- #include file="databaseINC.php" -->

<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>



<?php 
  $db=OpenDatabase();

//Undersøg om der skal generes en ny medlemsdatafil
//FIXME $LastLoad=$_COOKIE["LastLoad"];

$sql="select count(*) From Medlem";
$rs=$db->query($sql)->fetch_row();
$ArraySize=$rs[0];


$ArrayBlock=$ArrayBlock."MnAry = new Array(".$ArraySize.");"."\r\n";
$ArrayBlock=$ArrayBlock."MIDAry= new Array(".$ArraySize.");"."\r\n";

//Hvis cookien ikke er sat, eller hvis den er udløbet, skal der sættes en ny cookie. Samtidig opdateres den lokale fil.
if ($lastload=="") {
  $lastload=time();
  setcookie("LastLoad",$lastload,0,"","",0);
  // Unsupported: Response.Cookie. expires = Date + 1

  $sql="SELECT Medlem.Medlemsnr, [Fornavn] & ' ' & [Efternavn] AS Navn FROM Medlem;";
  $rs=$db->query($sql);
  $MemberPickerArray=$rs->getrows();
  $rs->close();
  if (is_array($MemberPickerArray)) {
    for ($c1=0; $c1<=count($MemberPickerArray); $c1=$c1+1) {
      $ArrayBlock=$ArrayBlock."MnAry[".$c1."]=\"".$MemberPickerArray[1][$c1]."\";"."\r\n";
      $ArrayBlock=$ArrayBlock."MIDAry[".$c1."]=\"".$MemberPickerArray[0][$c1]."\";"."\r\n";
    }
  } 
  $OnLoadCommand="WriteToFile();";
} else {
  $OnLoadCommand="ReadFromFile(".$boatID.");";
} 

$closedatabase();
?>

<script language="JavaScript">	
	<?php echo $Arrayblock;?><%
	SeatArray = new Array(9);

function WriteToFile()
	{
	var fso = new ActiveXObject("Scripting.FileSystemObject");
	var s = fso.CreateTextFile($membersfile, true);
	for (var i=0;i<MIDAry.length;i++)
		{
		s.WriteLine(MIDAry[i]);
		s.WriteLine(MnAry[i]);
		}
	s.Close();
	}

function ReadFromFile(boatid)
	{
	var fso = new ActiveXObject("Scripting.FileSystemObject");
	fileBool = fso.FileExists($membersfile);

	if (!fileBool) 
		{
		window.location.href = ('dsrbookboat.php?boatid=' + boatid + '&ForceRefreshCache=1');
		}
	else
		{

		ts = fso.OpenTextFile($membersfile, 1);
	
		var i=0;
		while(!ts.AtEndOfStream)
			{
			s = ts.ReadLine();
			MIDAry[i]=s;
			s = ts.ReadLine();
			MnAry[i]=s;
			i++;
			}
		ts.Close();

		}
	}
	
	function AutoFillName()
		{
		memberid = document.getElementById('MemberID').value;
			for (var i=0;i<MIDAry.length;i++)
				{
				if (memberid == MIDAry[i])
					{
					document.getElementById('Membername').value=MnAry[i];
					}
				}

		}
	
	function FillMemberSelector()
		{
		
		MyKeyCode = event.keyCode;
		var MemberID;
		var Membername;
		
		if (MyKeyCode!=9)
			{
			SearchWord=document.getElementById('Membername').value;
			
			if (SearchWord.length>=3)
				{
				HitCnt = 0;			
				NewInnerHTML= "<SELECT style='WIDTH: 200px' name='Cmbmembername' id='Cmbmembername' onchange='FillFromSelector()'>";
				NewInnerHTML = NewInnerHTML + "<option value='999' selected>... vælg fra liste ...</option>"; 
					for (var i=0;i<MnAry.length;i++)
						{
						MemberID=MIDAry[i];
						Membername=MnAry[i];
							MyReg = new RegExp(SearchWord , "i");		
							result =Membername.search(MyReg);	
							if (result>=0) 
								{
								NewInnerHTML = NewInnerHTML + "<option value='" + MemberID + "' onselect='myalert();' >" + MemberID + " - " + Membername + "</option>"
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
				}
			}
		}
	
	function FillFromSelector()
		{
		Selected = document.getElementById('Cmbmembername').selectedIndex;
		SelectedValue= document.getElementById('Cmbmembername').options[Selected].value;
		
		if (SelectedValue!='999')
			{		
			document.getElementById('MemberID').value = SelectedValue;
					for (var i=0;i<MIDAry.length;i++)
						{
						if (SelectedValue == MIDAry[i])
							{
							document.getElementById('Membername').value=MnAry[i];
							}
						}
			}
		}
	
</script>

<BODY bgproperties="fixed" background="images/baggrund.jpg" onLoad="<?php echo $OnLoadCommand;?>">

<h3>Indtast medlemsnummer eller navn for at søge</h3>

<form action="rostat.php" method="post">

	<table border=0>
		<TR>
			<TD width="79" height="20"><strong>Nummer</strong></TD>
			<TD width="230" height="20"><strong>Navn</strong> (indtast min. 3 tegn for at søge)</TD>
			<TD width="200" height="20"><strong>Søgeresultater</strong> (vælg fra liste)</TD></TR>
		</tr>
		<tr>
			<TD>
				<INPUT id="MemberID" size="8" name="ID" onChange="AutoFillName();">
			</TD>
			<TD><INPUT id="Membername" size="32" name="smembername"  onKeyUp="FillMemberSelector();"></TD>
			<TD>
            
			<span id="MemberSelector">
			<SELECT style="WIDTH: 200px" name="Cmbmembername" id="Cmbmembername"> 
				<option>...</option>
			</SELECT>						
          	</span>
		  						
			</TD>
		</tr>
	</table>

<input type="hidden" name="rostataction" value="ShowTrips">
<input type="submit" value="Find roer">

</form>

</body>
</html>

