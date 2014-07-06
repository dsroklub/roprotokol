<?php // asp2php (vbscript) converted on Sun Aug 11 21:17:47 2013
 ?>
<!-- #include file="databaseINC.php" -->

<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</HEAD>
<BODY bgproperties="fixed" background="images/baggrund.jpg">
<?php 
// Kommer ind med boatid

$postback=${"Postback"};
$Beskrivelse=${"TxtBeskrivelse"};
$Origin=${"Origin"};
$BoatID=${"BoatID"};
$TurID=${"TurID"};
$SkadeID=${"skadeid"};
$bIsKlarMeld=($skadeID<>"");

$Opendatabase;

if (${"SlaaOpKnap"}!="")
{

  $StyrmandNavn=${"StyrmandNavn"};

  $StyrmandID=CheckIfMedlemsnr($styrmandnavn);

  if (strlen($styrmandID)>0)
  {

    $Options="<option>".$StyrmandID." - ".GetMemberName($StyrmandID)."</option>";
    $Styrmandnavn=GetMemberName($StyrmandID);
  }
    else
  {

    $Options=FillOptionForMember($Styrmandnavn);
  } 


}
  else
{

  if ($postback=="")
  {

    // $WriteHit"Rapporter skade"    $BoatID."'";
    if ((!$bisklarmeld))
    {

      if (($turid!=""))
      {

// Lad os se om vi har en styrmand
        $rsTur=$db->query("SELECT * FROM QturDeltagere WHERE Plads=0 and FK_Turid=" . $turid);
        if (!$rstur->eof)
        {

          $StyrmandID=$rsTur["FK_MedlemID"];
          $StyrmandNavn=$rsTur["Navn"];
        } 

      } 

    } 


    $BoatName=$getboatNameID[$BoatID];
    $rstur=null;


  } else if ($postback==1) {
    $StyrmandNavn=${"StyrmandNavn"};
    $rs=$db->query("SELECT * FROM Skademeld_MedlemID_og_Navn WHERE Navn='$StyrmandNavn'");
    if (!$rs->eof) {
      $StyrmandID=$rs["MedlemID"];
    } 
    if ($StyrmandID=="")  {
      $StyrmandID=0;
    } 

    $tempstore=${"TxtBeskrivelse"};

    for ($c1=1; $c1<=strlen($tempstore); $c1=$c1+1)
    {
      if (substr($tempstore,$c1-1,1)!="'")
      {
        $tempstore2=$tempstore2+substr($tempstore,$c1-1,1);
      } 

    }



    $sql="insert into Skade (FK_BådID,FK_Ansvarlig,Ødelagt,Grad,Beskrivelse,OprettetDato) values";
    $sql=$sql."(".$boatid.",".$styrmandid.",#".${"KonstateretDato"}."#,".${"SelGrad"}.",'".$tempstore2."',now)";

    $db->execute($sql);

    if ($origin=="topmenu")
    {

      header("Location: "."ud_ind_skriv.html");
    }
      else
    {

      header("Location: "."dsrbookboat.php?boatid=".$boatid);
    } 

  }
    else
  {
//Postback er nu 2, og det betyder Klarmeld båd
    $sql="Update Skade set repareret=Now() where skadeID=".$skadeid;
    $db->execute($sql);
    print $SQL;

    header("Location: "."dsrboats.php");
  } 

} 


$closedatabase;

?>
<table align="center"><tr><td>
<H2>Rapportering af skade</H2>
<form method="post" action="rapskade.php?Origin=<?php echo $origin;?>" id=form1 name=form1>
<TABLE cellSpacing=1 cellPadding=1 width="100%" border=0>
  <TR>
    <TD>BÃ¥dnavn:</TD>
    <TD>
    <select size="1" name="BoatID">
    <?php 
$opendatabase;
$sql="select BÃ¥dId, Navn from BÃ¥d order by Navn";
$rs=$db->query($sql);
while(!($rs->eof))
{
  if (intval($rs["bådid"])==intval($boatid))
  {

    print "<option selected value=".$rs["bÃ¥did"].">".$rs["navn"]."</option>";
  } else {
    print "<option value=".$rs["bÃ¥did"].">".$rs["navn"]."</option>";
  } 

  $rs->movenext;
} 
CloseDatabase();
?>
    </select> </TD>
    </TR>
  <TR>
    <TD>Skademelder:</TD>
    <TD>
    <INPUT type=locked id=StyrmandNavn name=StyrmandNavn Value="<?php echo $StyrmandNavn;?>" size="20">
    <INPUT Type=Hidden id=StyrmandID Name=StyrmandID Value="<?php echo $StyrmandID;?>">
    <input type=submit value="SlÃ¥ op" name="SlaaOpKnap">
    <select name=Medlemsforslag id=Medlemsforslag onchange="Pressed()" LANGUAGE="VBScript">
    <?php echo $Options;?>
    </select></TD>
    </TR>
  <TR>
    <TD>Dato for konstatering: </TD>
		<TD>
			<INPUT Type=locked id=KonstateretDato Name=KonstateretDato Value="<?php echo time();?>">
		</TD>
  </TR>
  <TR>
    <TD>Grad:</TD>
    <TD>
    <SELECT id=SelGrad name=SelGrad>
		<OPTION value=1 selected>1 Let</OPTION>
		<OPTION value=2>2 Middel</OPTION>
		<OPTION value=3>3 SvÃ¦r</OPTION>
	</SELECT>
	</TD>
    </TR>
  <TR>
    <TD>Beskrivelse:</TD>
    <TD>
    <textarea rows="9" id=TxtBeskrivelse name=TxtBeskrivelse cols="55"><?php echo $Beskrivelse;?></textarea>
    </TD>
    </TR>
		<tr>
			<td>
			</td>
			<td>
			<?php if ($bIsKlarMeld)
{
?>
			<INPUT id=submit1 type=submit value="Klarmeld skade" name=submit1>&nbsp;<a href="dsrbookboat.php?boatid=" <?php   echo $boatid;?>>Til
			<INPUT type=hidden Name=Postback Value=2>
			<INPUT type=hidden Name=SkadeID Value=<?php   echo $SkadeID;?>>
			oversigt</a> <?php }
  else
{
?>
			<INPUT id=submit1 type=submit value="Rapporter skade" name=submit1> 
			<INPUT type=hidden Name=Postback Value=1>
			<?php } ?>
			</td>
		</tr>
    </TABLE>
</Form>
</table></tr></td>


<SCRIPT LANGUAGE="VBScript">
Sub Pressed()
dim s
Dim Element
Dim txtRange
Dim i
Dim stxt

set Element=document.getElementById("Medlemsforslag")
s=Element.getAttribute("Value")

for i=0 to Element.options.length-1
	if Element.options(i).selected then
		sTxt= Element.options(i).text
		exit for
	end if
next 

i=instr(stxt,"-")
if i<>0 then stxt=mid(stxt,i+1)

set Element=document.getElementById("Styrmandnavn")
set txtRange=element.createTextRange()
txtRange.text=stxt
end sub
</SCRIPT>

</BODY>
</HTML>

<?php 
function GetMemberBestMatch($MemberName)
{
  extract($GLOBALS);


  if ($val[$membername]>0)
  {


    $function_ret=$Membername;

  }
    else
  {


    $sl="";
    for ($i=1; $i<=strlen($membername); $i=$i+1)
    {
      if (substr($membername,$i-1,1)==" ")
      {

        $sl=$sl+"%";
      }
        else
      {

        $sl=$sl+substr($membername,$i-1,1);
      } 


    }


    $s="Navn like ('%".$sl."%')";

    $rs=$db->query($S);

    if (!$rs->eof)
    {

      $l=$rs["Medlemsnr"];
      $rs->movenext;
      if ($rs->eof)
      {

        $function_ret=$l;
      } 

    }
      else
    {

      $function_ret=0;
    } 


    $rs->close;
    $rs=null;

  } 



  return $function_ret;
} 

function GetMemberName($MemberID)
{
  extract($GLOBALS);

  $rs=$db->query("SELECT * FROM Medlem WHERE medlemsnr='".$MemberID."'");
  if (!$rs->eof) {
    $function_ret=$rs["Fornavn"]." ".$rs["Efternavn"];
  } else {

    $function_ret="-";
  } 

  $rs->close;
  $rs=null;


  return $function_ret;
} 

function FillOptionForMember($Membername)
{
  extract($GLOBALS);


  $s1=trim($MemberName);
  $sOption="<OPTION Selected>-Vælg-</Option>";
  if ($s1=="")
  {

    $sOption="<OPTION Selected>Ingen fundet</Option>";
  }
    else
  {


    $sl="";
    for ($i=1; $i<=strlen($membername); $i=$i+1)
    {
      if (substr($membername,$i-1,1)==" ")
      {

        $sl=$sl+"%";
      }
        else
      {

        $sl=$sl+substr($membername,$i-1,1);
      } 


    }


    $s="Navn like ('%".$sl."%')";

    $sql="select top 25 Medlemsnr, Navn from [QRY Skademeld_MedlemID_og_Navn] where ".$S;

    $rs=$Db->query($sql);
// opbyg Deltagere options
//Response.Write("IDest:" & iDestination & "<BR>")
    while(!$rs->eof)
    {

      $sOption=$sOption."<OPTION value=".$rs["Medlemsnr"].">";
//sOption= sOption & rs("Medlemsnr") & " - " & rs("fornavn") & " " & rs("efternavn") & "</OPTION>"
      $sOption=$sOption.$rs["Medlemsnr"]." - ".$rs["navn"]."</OPTION>";
      $rs->movenext;
    } 

    $rs->close;
    $rs=null;

  } 

  $function_ret=$Soption;
  return $function_ret;
} 

function CheckIfMedlemsnr($InString)
{
  extract($GLOBALS);


  if (!(!isset($instring) || $instring==""))
  {

    if (ord(substr($instring,0,1))>=48 && ord(substr($instring,0,1))<=57)
    {


      $c1=1;
      while(!($c1==strlen($instring)+1))
      {

        $function_ret=$CheckIfMedlemsnr*10;
        $function_ret=$CheckIfMedlemsnr+intval(substr($instring,$c1-1,1));
        $c1=$c1+1;
      } 

    } 

  } 


  return $function_ret;
} 
?>
