<?php // asp2php (vbscript) converted on Sun Aug 11 21:05:59 2013
 ?>
<!-- #include file="databaseINC.php" -->

<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</HEAD>
<BODY bgproperties="fixed" background="images/baggrund.jpg">
<?php 
// Kommer ind med boatid

$Origin=${"Origin"};
$BoatID=${"BoatID"};
$TurID=${"TurID"};
$SkadeID=${"skadeid"};
$bIsKlarMeld=($skadeID<>"");

$postback=${"Postback"};

$Opendatabase;

$Beskrivelse=$GetSkadeBeskrivelse[$SkadeID];
$BoatName=$getboatNameID[$BoatID];

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


if (${"submit1"}=="Klarmeld skade")
{


  $StyrmandNavn=${"StyrmandNavn"};


  $rs=$db->execute;  $StyrmandNavn."'");
  if (!$rs->eof)
  {
    $StyrmandID=$rs["MedlemID"];
  } 

  if ($StyrmandID=="")
  {
    $StyrmandID=0;
  } 

  $sql="Update Skade set repareret=Now(), FK_Reperatør=".$StyrmandID.", Beskrivelse='".${"txtBeskrivelse"}."' where skadeID=".$skadeid;


  $db->execute($sql);

  switch ($Origin)
  {
    case "SkadedeBåde":

      header("Location: "."dsrlist.php?action=2");

      break;
    case "IndUdskriv":

      header("Location: "."dsrbookboat.php?boatid=".$boatid);

      break;
  } 

} 

$closedatabase;

?>
<H2>Klarmeld båd</H2>

<form method="post" action="Klarmeld.php?Origin=<?php echo $origin;?>" id=form1 name=form1>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TR>
    <TD>Bådnavn:</TD>
    <TD><INPUT id=Navn Value="<?php echo $BoatName;?>"><INPUT type=hidden id=BoatID name=BoatID Value="<?php echo $BoatID;?>"></TD>
    <TD></TD></TR>
  <TR>
    <TD>Klarmeldt af:</TD>
    <TD>
    <INPUT type=locked id=StyrmandNavn name=StyrmandNavn Value="<?php echo $StyrmandNavn;?>" size="20"><INPUT Type=Hidden id=StyrmandID Name=StyrmandID Value="<?php echo $StyrmandID;?>">
    <input type=submit value="Slå op" name="SlaaOpKnap">
    <select name=Medlemsforslag id=Medlemsforslag onchange="Pressed()" LANGUAGE="VBScript">
    <?php echo $Options;?>
    </select>
    </TD>
   </TR>
  <TR>
    <TD>Dato for klarmelding: </TD>
    <TD><?php echo time();?></TD>
    <TD></TD></TR>
  <TR>
    <TD>Beskrivelse af skaden:</TD>
    <TD>
    <textarea rows="9" id=TxtBeskrivelse name=TxtBeskrivelse cols="32"><?php echo $Beskrivelse;?></textarea>
    </TD>
    <TD></TD></TR></TABLE>
<P> </P>

<P>
<INPUT id=submit1 type=submit value="Klarmeld skade" name=submit1>
<INPUT type=hidden Name=SkadeID Value=<?php echo $SkadeID;?>>
</P>
</Form>

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
  } else {
    $sl="";
    for ($i=1; $i<=strlen($membername); $i=$i+1)
    {
      if (substr($membername,$i-1,1)==" ")
      {

        $sl=$sl+"%";
      } else {
        $sl=$sl+substr($membername,$i-1,1);
      } 
    }

    $s="Navn like ('%".$sl."%')";
    $rs=$db->execute;    $S);

    if (!$rs->eof) {
      $l=$rs["Medlemsnr"];
      $rs->movenext;
      if ($rs->eof) {
        $function_ret=$l;
      } 
    } else {
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

  $rs=$db->execute;  $MemberID."'");
  if (!$rs->eof)
  {

    $function_ret=$rs["Fornavn"]." ".$rs["Efternavn"];
  }
    else
  {

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

    $rs=$Db->execute;    $sql);
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


  return $function_ret;
} 
?>




