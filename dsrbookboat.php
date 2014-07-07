<?php
  session_start();
  session_register("BådKategori_session");
  session_register("Navn_session");
?>
<?php // asp2php (vbscript) converted on Sun Aug 11 21:19:38 2013
 ?>
<!-- #include file="databaseINC.php" -->
<!-- kommer ind med boatID -->

<?php 

$BoatID=${"BoatID"};
$ForceRefreshCache=${"ForceRefreshCache"};

$opendatabase;

// Undersøg om der skal generes en ny medlemsdatafil
$LastLoad=$_COOKIE["LastLoad"];

$sql="select count(*) From Medlem";
$rs=$db->query($sql);
$ArraySize=$rs[0];
$rs->close;

$ArrayBlock=$ArrayBlock."MnAry = new Array(".$ArraySize.");"."\r\n";
$ArrayBlock=$ArrayBlock."MIDAry= new Array(".$ArraySize.");"."\r\n";

//Hvis cookien ikke er sat, eller hvis den er udløbet, skal der sættes en ny cookie. Samtidig opdateres den lokale fil.
if ($lastload=="" || $ForceRefreshCache==1) {
  $LastLoad=time();
  setcookie("LastLoad",$LastLoad,0,"","",0);
  // Unsupported: Response.Cookie. expires = Date + 1

  $sql="SELECT Medlem.Medlemsnr, [Fornavn] & ' ' & [Efternavn] AS Navn FROM Medlem;";
  $rs=$db->query($sql);

  $MemberPickerArray=$rs->getrows();

  if (is_array($MemberPickerArray))  {
    for ($c1=0; $c1<=count($MemberPickerArray); $c1=$c1+1) {
      $ArrayBlock=$ArrayBlock."MnAry[".$c1."]=\"".$MemberPickerArray[1][$c1]."\";"."\r\n";
      $ArrayBlock=$ArrayBlock."MIDAry[".$c1."]=\"".$MemberPickerArray[0][$c1]."\";"."\r\n";
    }
  } 
  $OnLoadCommand="WriteToFile();";
} else {
  $OnLoadCommand="ReadFromFile(".$boatID.");";
} 

$closedatabase;

?>

<HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../roprotokol.css">

<script language="JavaScript">	

	<?php echo $Arrayblock;?><%

	SeatArray = new Array(9);

function noenter() {
  return !(window.event && window.event.keyCode == 13); 
  }

function WriteToFile()
	{
	document.getElementById('arrayblockspan').innerHTML='<font color="#c1e6ff">The cached memberlist has been renewed</font>';
	var fso = new ActiveXObject("Scripting.FileSystemObject");
	var s = fso.CreateTextFile($membersfile, true);
	for (var i=0;i<MIDAry.length;i++)
		{
		s.WriteLine(MIDAry[i]);
		s.WriteLine(MnAry[i]);
		}
	s.Close();
	}

function ReadFromFile(boatid) {
	
	var fso = new ActiveXObject("Scripting.FileSystemObject");
	fileBool = fso.FileExists($membersfile);

	if (!fileBool) 
		{
		window.location.href = ('dsrbookboat.php?boatid=' + boatid + '&ForceRefreshCache=1');
		} else {
		document.getElementById('arrayblockspan').innerHTML='<font color="#c1e6ff">Using cached memberlist</font>';
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

</script>

</head>
<BODY bgproperties="fixed" background="images/baggrund.jpg" onLoad="<?php echo $OnLoadCommand;?> ValidateRowers();">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td width="79%">
<OBJECT RUNAT=server PROGID=ADODB.Connection id=OBJECT1> </OBJECT>

<?php 

//--------------------------------------------------------------------------

function GetBoatInfo($BoatID) {
  extract($GLOBALS);
  $rs=$db->query("SELECT * FROM skade WHERE FK_BådID=$BoatID AND Repareret IS NULL ORDER BY Ødelagt DESC");
  $res="<h3>Skader</h3>";

  if (!$rs->eof) {

    while(!$rs->eof)  {

//s="<strong>" & mid(rs("Ødelagt"),1,10) & " Grad: " & rs("Grad") & "</strong>" & "<a href=""RapSkade.php?boatid=" & BoatID & "&skadeid=" & rs("skadeid") & BoatID & "&Postback=2""> - Klarmeld</a><BR>" & rs("Beskrivelse") & "<BR>"

//s="<strong>" & mid(rs("Ødelagt"),1,10) & " Grad: " & rs("Grad") & "</strong>" & "<a href=""Klarmeld.php?boatid=" & BoatID & "&skadeid=" & rs("skadeid") & """> - Klarmeld</a><BR>" & rs("Beskrivelse") & "<BR>"

      switch ($rs["Grad"]) {
        case 1:
          $skadesgrad="<img border=\"0\" src=\"images/icon_skadet1.gif\" width=\"16\" height=\"17\">  Let skade";
          break;
        case 2:
          $skadesgrad="<img border=\"0\" src=\"images/icon_skadet2.gif\" width=\"16\" height=\"17\">  Middel skade";
          break;
        case 3:
          $skadesgrad="<img border=\"0\" src=\"images/icon_skadet3.gif\" width=\"16\" height=\"16\">  Svær skade";
          break;
      } 

      $s="<strong>".$skadesgrad." opstået ".substr($rs["Ødelagt"],0,10)."</strong><BR>".$rs["Beskrivelse"]."<BR><a href=\"Klarmeld.php?Origin=IndUdskriv&boatid=".$BoatID."&skadeid=".$rs["skadeid"]."\"><u>[Klarmeld]</u></a><br><br>";


      $res=$res.$S;
      $rs->movenext;
    } 
  } else {
    $res="";
  } 

  $rs->close;
  $function_ret=$res;
  return $function_ret;
} 


//--------------------------------------------------------------------------
function GetMemberBestMatch($MemberName)
{
  extract($GLOBALS);


//s1=trim(MemberName)
//sl=membername
//if s1 <>"" then
//	i=instr(s1," ")
//	if i<>0 then 
//		s="(Fornavn like ('" & mid(s1,1,i-1) & "'')) and  "
//		s1=mid(s1,i+1)
//	end if

  $sl="";
  for ($i=1; $i<=strlen($membername); $i=$i+1)  {
    if (substr($membername,$i-1,1)==" ") {

      $sl=$sl+"%";
    } else {
      $sl=$sl+substr($membername,$i-1,1);
    } 
  }


  $s="Navn like ('%".$sl."%')";

//	s=s & " (efternavn like (""" & s1 & """"))"
//	set rs=db.execute("select Medlemsnr from medlem where " & S)

  $rs=$db->query($s);

  if (!$rs->eof) {
    $l=$rs["Medlemsnr"];
    $rs->movenext;
    if ($rs->eof)  {
      $function_ret=$l;
    } 
  } else {
    $function_ret="0";
  } 
  $rs->close;
  $rs=null;

//response.write l

//end if
  return $function_ret;
} 
//--------------------------------------------------------------------------
function GetMemberName($MemberID)
{
  extract($GLOBALS);
  $rs=$db->execute;  $MemberID."'");
  if (!$rs->eof) {
    $function_ret=$rs["Fornavn"]." ".$rs["Efternavn"];
  } else {
    $function_ret="-";
  } 
  $rs->close;
  $rs=null;
  return $function_ret;
} 

//--------------------------------------------------------------------------
function SkrivTur($turid,$IndUdOpt) {
  extract($GLOBALS);
  // $rstur is of type "Adodb.Recordset"

  if (($turid=="") || (!isset($TURID))) {
    $s="Select * from tur";
    $bCreate=true;
  } else {
    $s="Select * from tur where turid=".$turid;
    $bCreate=false;
  } 

rstur(->$open[$s][$db][$adOpenForwardOnly][$adLockOptimistic]);

// Hvis IndUdOpt = 3 (dvs. hvis der er valgt Annuller tur):
  if ($IndUdOpt==3)
  {
    $with$rstur;
    $movefirst;
    $delete;
    $update;
  } 
  $with;

// Kør det følgende, hvis IndUdOpt <> 3 (dvs. hvis der ikke er valgt Annuller tur)
} else {
  $with$rstur;
  if ((!$eof) && (!$bof))  {
    $MoveFirst;
  } 
  if ($bCreate==true) {
    $AddNew;
  } 

  $Fields["UD"]=$timUd;
  $Fields["forvind"]=$timForventetind;

//	response.write("timind: " & timind & " komment: " & scomment & " ->" & IndUdOpt)

  if (($IndUdOpt==2)) {

    if ($timind=="0") {
      $timind=time();
    } 
    $Fields["IND"]=$timInd;
  } else {
    $Fields["IND"]=null;
  } 
  $Fields["destid"]=$iDestination;
  $Fields["Destination"]=$Destination;
  $s=0;
  $s2=null;
  if ($iDestination!=0) {

    $rstmp=$db->execute("Select meter,navn from destination where Destid=".$iDestination);
    $s=$rstmp["meter"];
    $s2=$rstmp["navn"];
    $rstmp=null;
  } 


  $Fields["destination"]=$s2;

  if ($kmdist!=0) {
    $Fields["meter"]=$kmdist*1000;
  } else {
    $Fields["meter"]=$S;
  } 


//Check om det er en motorbåd og sæt i så fald km til 0
  if ($_SESSION['BådKategori']==13) {
    $Fields["meter"]=0;
  } 

  $Fields["fk_TurTypeid"]=$iTurType;
  $Fields["kommentar"]=$sComment;
  $Fields["initialer"]="WEB";
  $Fields["FK_BådID"]=$boatid;

  if (($err==0)) {
    $Fields["Redigeretdato"]=time()();
  } 
  $Update;
  return $function_ret;
} 
$turid=$rstur["turid"];

// Slet alle deltagere
$s="delete from turdeltager where fk_turid=".$turid;
db(->$execute[$S]);

// Indsæt alle deltagere
for ($i=1; $i<=9; $i=$i+1) {
  if ($iMemberID[$i]!="") {
    $rs=$Db->execute;    $iMemberID[$i]."'");
    if (!$rs->eof) {

      $ID=$rs[0];
      $s="insert into turdeltager (fk_turid,plads,fk_medlemid,navn,initialer) values (".$turid.",".$i-1.",".$ID.",'".$sMemberName[$i]."','web')";
      $db->execute($S);
    } 
    $rs->close;
  } 
}


'Slut på den if-konstruktion, der afgør, om der skal indsættes eller slettes en tur

SkrivTur=turid
end function

'--------------------------------------------------------------------------
function GetTur()
dim i 

if mid(Request.form("ImemberID1"),1,6)="rohold" then
	For I=1 TO 9
		sMemberName(i)="Roholdet"
	next	
else
	' Find dem der skal med på turen og udfyld dem så godt som muligt.
	for i=1 to 9
		iMemberID(i)=Request.form("ImemberID" & i)
		
		if IsNumeric(iMemberID(i)) or IsNumeric(mid(iMemberID(i),2,4)) then
			sMemberName(i)=GetMemberName(iMemberID(i))
		else
			sMemberName(i)=Request.form("smembername" & i)
			if sMemberName(i)<>"" then
				j=GetMemberBestMatch(sMemberName(i))
				if j<>"0" then
					iMemberID(i)=j
					sMemberName(i)=GetMemberName(iMemberID(i))
				end if
			end if
		end if
	next
end if

	on error resume next  
	datetime=DateValue(Request.form("datetimud")) & " " & Request.form("hrtimud") & ":" & Request.form("mintimud")	
	if err<>0 or not isdate(datetime) then
		fejlmsg=fejlmsg & "Fejl i udskrivningsdato eller tid<BR>"
		err=0
	end if
	timud=Datetime
	fejlmsg=datetime
	
	datetime=DateValue(Request.form("dateforvtimind")) & " " & Request.form("hrforvtimind") & ":" & Request.form("minforvtimind")	
	if (err<>0) or not isdate(datetime) then
		l_fejlmsg=fejlmsg & "Fejl i forventetind  dato eller tid<BR>"
		datetime=now
	end if	
	timForventetind =Datetime
	
	datetime=DateValue(Request.form("datetimind")) & " " & Request.form("hrtimind") & ":" & Request.form("mintimind")
	if (err<>0) or mid(datetime,1,8)="00:00:00" then
		if Request.form("datetimind")<>"00:00" then
			l_fejlmsg=l_fejlmsg & "Fejl i ind dato eller tid<BR>" & Request.form("datetimind")
		end if
		datetime=Null
	end if
	timInd=Datetime
	on error goto 0
	
	iDestination=cint(Request.form("Destination"))
	iTurType=cint(Request.form("turtype"))
	sComment=Request.form("sComment")
	kmdist=Request.form("kmDist")
	if kmdist="" then kmdist=0
	GetTur=true

end function

'--------------------------------------------------------------------------
%>

<?php if ()
{
  $timind=$datevalue[time()]." 00:00";
} 
$strIndUd="Udskrivning";

$returnaction=${"returnaction"};
$TurID=${"TurID"};
$action=${"action"};
if ($action=="")
{
  $action=$action.${"subaction"};
} 

?>


	<script language="JavaScript">
	
	
		function AutoFillName(FieldNumber)
			{
			memberid = document.getElementById('MemberID' + FieldNumber).value;
			ChangeSeatStatus(FieldNumber,0);
				for (var i=0;i<MIDAry.length;i++)
					{
					if (memberid == MIDAry[i])
						{
						document.getElementById('Membername' + FieldNumber).value=MnAry[i];
						ChangeSeatStatus(FieldNumber,1);						
						}
					}

			}
	
	function FillMemberSelector(FieldNumber)
		{
		
		MyKeyCode = event.keyCode;
		
		if (MyKeyCode!=9)
			{
			SearchWord=document.getElementById('Membername' + FieldNumber).value;
			
			ChangeSeatStatus(FieldNumber,0);
			
			if (SearchWord.length>=3)
				{
				HitCnt = 0;			
				NewInnerHTML= "<SELECT style='WIDTH: 200px' name='Cmbmembername" + FieldNumber + "' id='Cmbmembername" + FieldNumber + "' onchange='FillFromSelector(" + FieldNumber + ")'>";
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
						document.getElementById('MemberSelector' + FieldNumber).innerHTML=NewInnerHTML;
					//if (HitCnt==1)
					//	{
					//	document.getElementById('MemberID' + FieldNumber).value = MemberIDx;
					//	document.getElementById('Membername' + FieldNumber).value = Membernamex;
					//	}
				}
			}
		}
	
	function FillFromSelector(FieldNumber)
		{
		Selected = document.getElementById('Cmbmembername' + FieldNumber).selectedIndex;
		SelectedValue= document.getElementById('Cmbmembername' + FieldNumber).options[Selected].value;
		
		if (SelectedValue!='999')
			{		
			document.getElementById('MemberID' + FieldNumber).value = SelectedValue;
			ChangeSeatStatus(FieldNumber,1);
					for (var i=0;i<MIDAry.length;i++)
						{
						if (SelectedValue == MIDAry[i])
							{
							document.getElementById('Membername' + FieldNumber).value=MnAry[i];
							}
						}
			}
		}
	
	function ChangeSeatStatus(FieldNumber, newstatus)
		{
		SeatArray[FieldNumber]=newstatus;
		if (newstatus==1)
			{
			document.getElementById('roerstatus' + FieldNumber).src ="images/traflight_green.gif";
			}
		else
			{
			document.getElementById('roerstatus' + FieldNumber).src ="images/traflight_red.gif";
			}
		}
	
function AllValidations(AntalPladser)
	{
	Result = ValidateDestination();
	if (Result!='invalid')
		{
		ValidateTrafficLights(AntalPladser);
		}
	else
		{
		alert ('Du har ikke angivet en destination. Hvis du har valgt destinationen "Øvrige" skal du angive en destination i feltet Bemærkninger.');
		}
	}

function ValidateDestination()
	{
	Validation = "valid"
	//Checker at der er angivet en beskrivelse, hvis destinationen ier sat til øvrige
	Selected = document.getElementById('Destination').selectedIndex;
	SelectedValue= document.getElementById('Destination').options[Selected].value;
	
	//16 = øvrige og 20 = vælg dest her
	if (SelectedValue == 16 || SelectedValue == 20 )	
		{
		if (document.getElementById('sComment').value.length<2)
			{
			Validation = "invalid"
			}
		}	
	return Validation;
	}
	
function ValidateTrafficLights(AntalPladser)
	{
	AlertMessage="";
	for (i=1; i<=AntalPladser; i++)
		{
		ThisSeat = document.getElementById('roerstatus' + i).src.indexOf("green");
		if (ThisSeat<1)
			{
			AlertMessage = "Du har ikke angivet samtlige roere. Ønsker du at fortsætte?"
			}
		}
		if (AlertMessage != "")
			{
			answer = confirm(AlertMessage);
			if (answer)
				{
				document.boatbookform.submit();
				}
			}
		else
			{
			document.boatbookform.submit();
			}
	}	
	
	
	</script>

<?php 

$timUd=time();
//Den sættes default til +2 timer som nu. Først når der vælges en destination, overskrives denne værdi
$timForventetind=$DateAdd["h"][2][time()];

$l_bShowDebug=$_POST["ShowDebug"];
if ($l_bShowDebug!="")
{

  print "debug info: Action=".$action." session: ".$_SESSION['Navn'];

  foreach ($_POST as $v)
  {
    print "->".$v."-".${$v}."<br>";
  }

} 

$opendatabase;

$bHentIkkeData=false;

if ($action=="") {

// vi kommer ind uden at vide hvad brugeren vil
// Er båden ude, skal den kunne skrives ind igen.
  $rs=$db->execute;  $boatID);
  if (!$rs->eof)  {
    $turid=$rs["turid"];
    $timInd=time();
    $strIndUd="Indskrivning";
  } 

  if ($rs->eof)  {
    $WriteHit"IndUdskrivning"    $BoatID; //Hvis rs.eof er det en udskrivning.
  } 
} else {

  switch ($action) {
    case "Tilbage":
      header("Location: "."dsrboats.php");
      break;
    case "Udskriv båd":
      if (gettur())
      {

        $rs=$db->execute;        $boatID);
        if ($rs->eof) {
          $turid=SkrivTur($turid,1);
          if ($l_fejlmsg=="") {
            header("Location: "."showalert.php?BoatID=".$Boatid."&Turid=".$turid."&Turtype=".${"Turtype"}."&Udtid=".$timUd."&Styrmand=".${"iMemberID1"}."&forvind=".${"hrforvtimind"}.":".${"minforvtimind"}.":00");
          } 
        } else {
//Hvis båden allerede er udskrevet, slettes den
//Dette forekommer hvis brugeren har benyttet browser back eller hvis turen er under editering
          $OldTurID=$rs["turid"];
          $sql="DELETE FROM Tur WHERE TurID=".$OldTurID;
          $db->execute($sql);
          $sql="DELETE FROM Turdeltager WHERE FK_TurID=".$OldTurID;
          $db->execute($sql);
          $turid=SkrivTur("",1);
          if ($l_fejlmsg=="") {
            header("Location: "."showalert.php?BoatID=".$Boatid."&Turid=".$turid."&Turtype=".${"Turtype"}."&Udtid=".$timUd."&Styrmand=".${"iMemberID1"}."&forvind=".${"hrforvtimind"}.":".${"minforvtimind"}.":00");
          } 
        } 

        $rs->close;
      } 

      break;
    case "Indskriv båd":
      if (gettur()) {
        $turid=SkrivTur($turid,2);
        if ($l_fejlmsg=="") {
          header("Location: "."showalert.php?Action=Indskrivning");
        } 
      } 

      break;
    case "Slet Tur":
      if (gettur()) {
        $turid=SkrivTur($turid,3); //Ny action, der sletter turens entry i databasen
        if ($l_fejlmsg=="") {
          header("Location: "."dsrboats.php");
        } 
      } 

      break;
    case "Opret roere":
      if (gettur()) {

        $turid=SkrivTur($turid,1);
        if ($l_fejlmsg=="") {
          header("Location: "."opretroer.php?BoatID=".$Boatid."&turid=".$turid."&returnaction=".$returnaction);
        } 
      } 

      break;
    case "EditerTur":
      if ($returnaction=="Indskrivning") {
        $rs=$db->execute;        $boatID);
        $turid=$rs["turid"];
        $strIndUd="Indskrivning";
      } else {
//turid=request("turid")
        $rs=$db->execute;        $boatID);
        $turid=$rs["turid"];
        $strIndUd="Udskrivning";
        $bHentIkkeData=false;
      } 

      break;
  } 
} 


if ($boatid!="") {
// Vi låser båden
  if (!$Lockboat[$boatid]) {
    exit();
  } 
} 


if (($TurID=="") || ($bHentIkkeData)) {
  $rsturmembers=null;

} else {

// Find turen i databasen og vis den.
  $rsTurMembers=$db->execute;  $turid." order by plads");
  $i=1;

  while(!$rsturmembers->eof)  {

    $iMemberID[$i]=$rsturmembers["medlemsnr"];
    $sMemberName[$i]=$rsturmembers["fornavn"]." ".$rsturmembers["efternavn"];
    $rsturmembers->movenext;
    $i=$i+1;
  } 
  $rsturmembers=null;
  $rs=$Db->execute;  $TurID);
  $iDestination=$rs["destID"];
  if (!isset($iDestination)) {
    $iDestination=0;
  } 
  $iTurType=$rs["fk_turtypeid"];

  $timUd=$rs["ud"];
  $timForventetind=$rs["forvind"];
  if ($timind=="") {
    $timInd=$rs["ind"];
    if (!isset($timind)) {
      $timind="00:00 0:0";
    } 
  } 

  $boatid=$rs["fk_bådid"];
  $kmdist=$rs["Meter"]/1000;
  $scomment=$rs["Kommentar"];
  $rs->close;
} 


$rs=$Db->execute;
// opbyg destionation options
//Response.Write("IDest:" & iDestination & "<BR>")
while(!$rs->eof) {

  if (intval($iDestination)!=$rs["destid"]) {

    $sDestination=$sDestination."<OPTION value=".$rs["destid"].">";
  } else {
    $sDestination=$sDestination."<OPTION value=".$rs["destid"]." selected>";
    if ($kmdist==0) {
      $kmdist=intval($rs["meter"]/100)/10;
    } 
  } 

  $sDestination=$sDestination.$rs["navn"];

//Det næste er indented, fordi vi ikke vil have det til at stå to steder
//& " " & (rs("meter")/100)/10 & " km" & "</OPTION>"
  $rs->movenext;
} 
$rs->close;

$rs=$Db->execute;
// opbyg turtype options
while(!$rs->eof
)

  if ($iTurType!=$rs["turtypeid"]) {

    $sTurtype=$sTurtype."<OPTION value=".$rs["turtypeid"].">";
  } else {

    $sTurtype=$sTurtype."<OPTION value=".$rs["turtypeid"]." selected>";
  } 

  $sTurtype=$sTurtype.$rs["navn"]."</OPTION>";
  $rs->movenext;
} 
$rs->close;

$rsboat=$Db->execute;$BoatID);

$AntalPladser=$rsboat["pladser"];

if (($timind=="") || (!isset($timind))) {
  $timind="00:00";
} 

?>

<h2><?php echo $StrIndUD;?>    af båd:&nbsp;&nbsp; <?php echo $rsboat["Navn"];?></h2>
    </td>
    <td width="21%">
    </td>
  </tr>
</table>
<?php 
if ($l_fejlmsg!="")
{
  print "<B><FONT color=red>FEJL :  </B>".$l_fejlmsg."";
} 

?>
<form method="post" action="dsrbookboat.php" id="boatbookform" name="boatbookform">
<INPUT type=hidden value=<?php echo $boatid;?> id=boatid name=boatid>
<INPUT type=hidden value="<?php echo $turid;?>" id=turid name=turid>
<TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
<TR>
<TD width=75% colspan="2">
<TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
  <TR>
    <TD width="100" >Destination</TD>
    <TD colspan="3">
	<SELECT style="WIDTH: 205px" name="Destination"> 
       <?php echo $sDestination;?>
    
      </SELECT>
      
      <?php if ($_SESSION['BådKategori']!=13)
{
?>
      <INPUT value="<?php   echo $kmDist;?>" style="WIDTH: 50px;" maxlength=6 id=kmDist name=kmDist size="20">km&nbsp;&nbsp;&nbsp;
      <?php } ?>
      </TD></TR>
  <TR>
    <TD width="100">Ud</TD>
    <TD colspan="3">
    
    <INPUT value=<?php echo strftime("%H",$timud);?> style="WIDTH: 25px;" maxlength=2 id=hrtimud name=hrtimud size="20"> :
    <INPUT value=<?php echo strftime("%M",$timud);?> style="WIDTH: 25px;" id=mintimud name=mintimud maxlength=2 size=2>
    <INPUT value=<?php echo $timud;?> id=datetimud name=datetimud size="20"></TD></TR>
    
  <TR>
    <TD width="100">Forventet ind</TD>
    
    <TD colspan="3">
    
    <INPUT value=<?php echo strftime("%H",$timForventetind);?> style="WIDTH: 25px;" maxlength=2 id=hrtimforvind name=hrforvtimind size="20"> :
    <INPUT value=<?php echo strftime("%M",$timForventetind);?> style="WIDTH: 25px;" id=minforvtimind name=minforvtimind maxlength=2 size="20" >
    <INPUT value=<?php echo $timForventetind;?> id=dateforvtimind name=dateforvtimind size="20">
    <INPUT size=25 id="Varighed" readonly style="border:0; background-color:transparent"></INPUT></TD></TR>
        <?php 

$MyRS=$db->execute;

while(!($myrs->eof))
{


  print "<input type=hidden id=\"DivDestination".$MyRS["destid"]."\" value=\"".$MyRS["Gennemsnitlig_varighed_Normal"]."\"></input>";  $chr[10];
  print "<input type=hidden id=\"DivDestinationI".$MyRS["destid"]."\" value=\"".$MyRS["Gennemsnitlig_varighed_Instruktion"]."\"></input>";  $chr[10];

  $myrs->movenext;
} 

$myrs->close;

?>
  <TR>
    <TD width="100">Ind</TD>
    <TD colspan="3">
    
    <INPUT value=<?php echo strftime("%H",$timind);?> style="WIDTH: 25px;" maxlength=2 id=hrtimind name=hrtimind size="20"> :
    <INPUT value=<?php echo strftime("%M",$timind);?> style="WIDTH: 25px;" id=mintimind name=mintimind maxlength=2 size="20" >
    <INPUT value=<?php echo $timind;?> id=datetimind name=datetimind size="20"></TD></TR>
   
  <TR>
    <TD width="100">Turtype</TD>
    <TD colspan="3">
    <SELECT style="WIDTH: 205px" name="Turtype" id=xTurtype> 
    <?php echo $sTurtype;?>
      </SELECT></TD></TR>
  <TR>
    <TD width="100" >Bemærkning</TD>
    <TD colspan="3"><TEXTAREA name="sComment" style="width: 485; height: 37" cols=33 rows="1"><?php echo $scomment;?>
</TEXTAREA></TD></TR>
</table>
 <table border=0>
  <TR>
    <TD width=50 height="20"></TD>
    <TD width=20 height="20"></TD>
    <TD width="79" height="20"><strong>Nummer</strong></TD>
    <TD width="230" height="20"><strong>Navn</strong> (indtast min. 3 tegn for at søge)</TD>
    <TD width="200" height="20"><strong>Søgeresultater</strong> (vælg fra liste)</TD></TR>
    <?php 
if (!$rsturmembers==null)
{

  $rsturmembers->movefirst;
  $sMemberID=$rsturmembers["medlemsnr"];
  $sMembername=$rsturmembers["fornavn"]." ".$rsturmembers["efternavn"];
  $rsturmembers->movenext;
} 


//<INPUT value="<==sMemberName(i)" style="WIDTH: 224px; HEIGHT: 22px" size=32 id=text6 name=smembername<==
for ($i=1; $i<=$rsboat["pladser"]; $i=$i+1)
{
  if ($I==1)
  {
    $s="Styrmand";
  }
    else
  {
    $s="Roer ".$i-1;
  } ?>
	   <TR>
			<TD><?php   echo $s;?></TD>
			<td>
			<?php   if (strlen($iMemberID[$i])<1)
  {

?>
				<img id="roerstatus<?php     echo $i;?>" src="images/traflight_yellow.gif">
				<?php 
  }
    else
  {

?>
				<img id="roerstatus<?php     echo $i;?>" src="images/traflight_green.gif">
			<?php   } ?>
			</td>
			<TD>
				<INPUT value="<?php   echo $iMemberID[$i];?>" id="MemberID<?php   echo $i;?>" size="8" name="imemberid<?php   echo $I;?>" onChange="AutoFillName(<?php   echo $i;?>);" onkeypress="return noenter();">
			</TD>
			<TD><INPUT value="<?php   echo $sMemberName[$i];?>" id="Membername<?php   echo $i;?>" size="32" name="smembername<?php   echo $I;?>"  onKeyUp="FillMemberSelector(<?php   echo $i;?>);" onkeypress="return noenter();"></TD>
			<TD>
            
			<span id="MemberSelector<?php   echo $i;?>">
			<SELECT style="WIDTH: 200px" name="Cmbmembername<?php   echo $I;?>" id="Cmbmembername<?php   echo $I;?>"> 
				<option>...</option>
			</SELECT>						
          	</span>		  						
			</TD>
	   </TR>
	   <?php 
}

?>
    </TABLE>


</TD>
</TR>
</table>
<table border=0>
<TR>
<TD height="25" width="50"></TD>

<TD height="25" align="left">
<?php 
//turid="" OR action="Udskriv båd" or  
if ($strIndUd=="Udskrivning")
{

?>
<input name="subaction" value="Udskriv båd" type="hidden" tabindex="0">
<INPUT type="button" value="Udskriv båd" onclick="AllValidations(<?php   echo $AntalPladser;?>);">
<?php 
} else {

?>
<input name="subaction" type="hidden" value="Indskriv båd" tabindex="0">
 <INPUT type="button" value="Indskriv båd" onclick="AllValidations(<?php   echo $AntalPladser;?>);"> 
<?php 
} 


?>
<input name="returnaction" type="hidden" value="<?php echo $strindud;?>" tabindex="0">
 <INPUT name="action" type=submit value="Opret roere" tabindex="2">
<?php 

if ($turid!="") {

?>
 <INPUT name="action" type=submit value="Slet Tur" tabindex="1">
<?php 
} 


$s=GetBoatInfo($BoatID);?>
<INPUT type="button" value="Meld Skade" id=ButMeldSkade name=butmeldskade onclick="ButMeldSkade_Onclick '<?php echo $boatid;?>','<?php echo $turid;?>'" LANGUAGE="VBScript" tabindex="3">
<INPUT name="action" type="submit" value="Tilbage" tabindex="4">
</TD>

</TR>
</table>
</form>
<?php 
$CloseDatabase;
// Det der kommer nedenfor kommer med til clienten
?>
<SCRIPT LANGUAGE="VBScript">

//fun=1->Clear membernumber
//fun=2->Clear Membername

'--------------------------------------------------------------------------


function MemberClear(ID, fun)
Dim Element
Dim txtRange

if fun=1 then
	set Element=document.getElementById("iMemberID" & id)
else
	set Element=document.getElementById("sMemberName" & id)
end if
set txtRange=element.createTextRange()
txtRange.text=""
end function

'--------------------------------------------------------------------------

Sub Pressed(Id)
	dim s
	Dim Element
	Dim txtRange
	Dim i
	Dim stxt

	set Element=document.getElementById("cmbmembername" & id)
	s=Element.getAttribute("Value")

	for i=0 to Element.options.length-1
		if Element.options(i).selected then
			sTxt= Element.options(i).text
			exit for
		end if
	next 

	i=instr(stxt,"-")
	if i<>0 then stxt=mid(stxt,i+1)

	'alert("Værdi:" & s & ", " & Stxt & " id=" & id)

	' For at opdatere INPUT feltet skal man definere en textRange
	set Element=document.getElementById("sMemberName" & id)
	set txtRange=element.createTextRange()
	txtRange.text=stxt

	set Element=document.getElementById("iMemberID" & id)
	set txtRange=element.createTextRange()
	txtRange.text=s
end sub
	
'--------------------------------------------------------------------------

function ButMeldSkade_Onclick(boatid,turid)
	'set Element=document.getElementById("boatid")
	'boatid=Element.getAttribute("Value")
	'set Element=document.getElementById("turid")
	'turid=Element.getAttribute("Value")

	window.navigate("rapskade.php?Origin=indudskriv&BoatID=" & boatid & "&turid=" & turid)
end function

'--------------------------------------------------------------------------

sub Destination_Onchange
	stxt="-0"
	DestDurElement="-0"
	set Element=document.getElementById("destination")
	for u=0 to Element.options.length-1
		if Element.options(u).selected then
			sTxt= Element.options(u).text
			p = Element.getAttribute("Value")
			exit for
		end if
	next

	i=instr(stxt,"(")
	j=instr(stxt,"km")

	if j<>0 then

		stxt=mid(stxt,i+1,(j-i)-1)
	
		set Element=document.getElementById("kmDist")
		set txtRange=element.createTextRange()
		txtRange.text=stxt
	
		'Beregn den estimerede varighed
		'Først: Returner turtypen, for at se, om det er instruktion
	
		set Element=document.getElementByID("xTurtype")
		for u=0 to Element.options.length-1
			if Element.options(u).selected then
				MinTurtype= Element.options(u).text
				exit for
			end if
		next
	
		If MinTurtype="Instruktion" then
			set Element=document.getElementById("DivDestinationI" & p)
			Duration=Element.getAttribute("Value")
		Else
			set Element=document.getElementById("DivDestination" & p)
			Duration=Element.getAttribute("Value")
		End if
	
		timForventetind=now()+Duration/24
	
		set Element=document.getElementById("minforvtimind")
		set txtRange=element.createTextRange()
		txtRange.text=minute(timForventetind)
	
		set Element=document.getElementById("hrtimforvind")
		set txtRange=element.createTextRange()
		txtRange.text=hour(timForventetind)
	
		set Element=document.getElementById("dateforvtimind")
		set txtRange=element.createTextRange()
		txtRange.text=cdate(day(timForventetind)&"-"&month(timForventetind)&"-"&year(timForventetind))
	
		set Element=document.getElementById("Varighed")
		set txtRange=element.createTextRange()
		txtRange.text="Forventet varighed: " & duration & " timer"
	else
	end if

end sub

'--------------------------------------------------------------------------

sub Turtype_Onchange
	stxt="-0"
	DestDurElement="-0"
	set Element=document.getElementById("destination")
	for u=0 to Element.options.length-1
		if Element.options(u).selected then
			sTxt= Element.options(u).text
			p = Element.getAttribute("Value")
			exit for
		end if
	next

	i=instr(stxt,"(")
	j=instr(stxt,"km")

	if j>0 then

		stxt=mid(stxt,i+1,(j-i)-1)
	
		set Element=document.getElementById("kmDist")
		set txtRange=element.createTextRange()
		txtRange.text=stxt
	
		'Beregn den estimerede varighed
		'Først: Returner turtypen, for at se, om det er instruktion
	
		set Element=document.getElementByID("xTurtype")
		for u=0 to Element.options.length-1
			if Element.options(u).selected then
				MinTurtype= Element.options(u).text
				exit for
			end if
		next
	
		If MinTurtype="Instruktion" then
			set Element=document.getElementById("DivDestinationI" & p)
			Duration=Element.getAttribute("Value")
		Else
			set Element=document.getElementById("DivDestination" & p)
			Duration=Element.getAttribute("Value")
		End if
	
		timForventetind=now()+Duration/24
	
		set Element=document.getElementById("minforvtimind")
		set txtRange=element.createTextRange()
		txtRange.text=minute(timForventetind)
	
		set Element=document.getElementById("hrtimforvind")
		set txtRange=element.createTextRange()
		txtRange.text=hour(timForventetind)
	
		set Element=document.getElementById("dateforvtimind")
		set txtRange=element.createTextRange()
		txtRange.text=cdate(day(timForventetind)&"-"&month(timForventetind)&"-"&year(timForventetind))
	
		set Element=document.getElementById("Varighed")
		set txtRange=element.createTextRange()
		txtRange.text="Forventet varighed: " & duration & " timer"
	else
	
	end if

end sub

</SCRIPT>


<TR>
<TD colspan="2" height="15">

</TD>
</TR>


<TR>
<TD colspan="2">

<?php echo $s;?>

</TD>
</TR>
<TR>
<TD colspan="2">

</TD>
</TR>
<TR>
<TD colspan="2">

</TD>
</TR>
</table>

	<span id="arrayblockspan">
	</span>
	

	
<script language="JavaScript">

function ValidateRowers()
	{
	AntalRoere=<?php echo $AntalPladser;?><%;
	for (i=1; i<=AntalRoere; i++)
		{
		if (DoValidateRower(i)=='valid')	
			{
			document.getElementById('roerstatus' + i).src ="images/traflight_green.gif";			
			}
		}

	}
	
function DoValidateRower(rower)
	{
	Name = document.getElementById('Membername' + rower).value;
	MID = document.getElementById('MemberID' + rower).value;
	Validate="invalid";
	for (var i=0;i<MIDAry.length;i++)
		{
		if (MID==MIDAry[i])
			{
			if (Name==MnAry[i])
				{
				Validate = "valid";
				}
			}
		}	
	return Validate;
	}

</script>


</BODY>
</html>
