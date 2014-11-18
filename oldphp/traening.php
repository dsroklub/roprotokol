<? // asp2php (vbscript) converted on Wed Jul 30 12:05:01 2014
 ?>
<!-- #include file="databaseINC.php" -->
<HTML>
<HEAD>
<META NAME="GENERATOR" Content="Microsoft Visual Studio 6.0">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</HEAD>

<BODY bgproperties="fixed" background="images/baggrund.jpg">


<? 

$Time1=time();

$opendatabase;
$WriteHit"Racerkanin"$CInt[$RoerID];
$closedatabase;


//TurArray=Array(53)
$Startuge=${"Startuge"};
$Slutuge=${"Slutuge"};
$RoerID=${"RoerID"};
$Gruppe=${"Group"};
$Turtyper=${"Turtyper"}; //Values: Training, Alt
$MyYear=strftime("%Y",time());

if ($Gruppe=="")
{
  $Gruppe="LDK";
} 
if ($Turtyper=="")
{
  $Turtyper="Alt";
} 
if ($startuge<1)
{
  $startuge=14;
} 
if ($slutuge<1)
{
  $slutuge=44;
} 

if (${"Oversigt"}=="Oversigt")
{
  header("Location: "."traening.asp?Startuge=".$startuge."&Slutuge=".$Slutuge."&Show=".$Show);
} 

if ($Turtyper=="Alt")
{

  $Turtypealias="statistik for alle ture";
}
  else
{

  $Turtypealias="statistik for træningsture";
} 



?>

<? if (strlen($RoerID)<3)
{
?>
<h3>Viser <?   echo $TurtypeAlias;?> for gruppen <?   echo $gruppe;?></h3>

<? 
}
  else
{

?>
<h3>Viser roerens turoversigt</h3>

<? 
} 

?>

<form method="POST" action="traening.php">
  <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="800">
    <tr>
      <input type="hidden" name="RoerID" Value="<? echo $RoerID;?>">
      <td width="10%">Træningstilbud: <select size="1" name="Group">
	  <? 
if ($gruppe=="LDK")
{

  $ldkselected="selected";
}
  else
{

  $rkselected="selected";
} 

?>
      <option value="LDK" <? echo $ldkselected;?>>Langdistance</option>
      <option value="Racerkanin" <? echo $rkselected;?>>Ræserkanin</option>
      </select>
	  </td>
      <td width="10%">Vis fra uge <input type="text" name="Startuge" size="2" value="<? echo $Startuge;?>"> til uge
      <input type="text" name="Slutuge" size="2" value="<? echo $Slutuge;?>"></td>
      <td width="10%">Turtyper: <select size="1" name="turtyper">
	  <? 
if ($Turtyper=="Alt")
{

  $AltSelected="selected";
}
  else
{

  $TrSelected="Selected";
} 

?>
      <option value="Alt" <? echo $AltSelected;?>>Alle ture</option>
      <option value="Training" <? echo $TrSelected;?>>Træningsture</option>
      </select>
	  </td>
	 </tr>
	 <tr>
      <td colspan=4 align="right">
	  <input type="submit" value="Udfør" name="Udfør">
      <? if (strlen($RoerID)>1)
{
?>
      <input type="submit" value="Oversigt" name="Oversigt">
	  <? } ?>
	  </td>
    </tr>
  </table>
</form>

	<table class="rostat" width=1024>
		<tr>
			<th class="tablehead" WIDTH=200>Måned</th>
			<? 
//Find ud af, hvilken måned, den første dag i hver af årets uger er i...
//
for ($c1=2; $c1<=53; $c1=$c1+1)
{
  $UgeMaaned[$c1]=strftime("%m",$dateadd["d"][(7*($c1-2))+8-strftime("%w","01-01-".$myyear)+1]["01-01-".$myyear]);

}

$UgeMaaned[1]="01-01-".$myyear;

$MonthBlocks=1;
$WeeksThisMB=1;
for ($c1=$startuge; $c1<=$slutuge; $c1=$c1+1)
{
  $Monthblock[$MonthBlocks][1]=$Ugemaaned[$c1];
  $Monthblock[$MonthBlocks][2]=$WeeksThisMB;
  $WeeksThisMB=$WeeksThisMB+1;
  if ($Ugemaaned[$c1]!=$Ugemaaned[$c1+1])
  {

    $Monthblocks=$monthblocks+1;
    $WeeksThisMB=1;
  } 


}


for ($c1=1; $c1<=$monthblocks; $c1=$c1+1)
{
  if ($Monthblock[$c1][2]!=0)
  {
    print "<th class=\"tablehead\" colspan=".$Monthblock[$c1][2].">".$Monthblock[$c1][1]."</th>";
  } 

}


//Create Month-blocks


?>
		</tr>
			<th class="tablehead" WIDTH=200>Uge</th>
			<? for ($c1=$startuge; $c1<=$slutuge; $c1=$c1+1)
{?>
			<th class="tablehead" width=28><?   echo $c1;?></th>
			<? 
}?>
		</tr>
	<? 
$opendatabase;

//rs indeholder alle, der er racerkaniner / en specifik racerkanin

// $adoxConn is of type "ADOX.Catalog"
$adoxConn->activeConnection=$DB;
$found=false;
foreach ($adoxConn->tables as $table)
{
  if (strtolower($table->name)==strtolower("Temptable"))
  {

    $found=true;
    break;

  } 

}
$adoxConn=null;


if ($found)
{
  $db->execute("DROP TABLE Temptable;");
} 

//db.execute("CREATE TABLE Temptable (MedlemID INT)")

//Intet tidsforbrug hertil

switch ($gruppe)
{
  case "Racerkanin":

    $sql="SELECT Medlem.MedlemID ".
      "As MedlemID INTO Temptable ".
      "FROM (((Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) INNER JOIN Tur ON TurDeltager.FK_TurID = Tur.TurID) INNER JOIN TurType ON Tur.FK_TurTypeID = TurType.TurTypeID) INNER JOIN tblMembers ON cvar(Medlem.Medlemsnr) = Cvar(tblMembers.MemberID) ".
      "WHERE ((TurType.Navn=\"Racerkanin\") AND (Year([JoinDate])=".$MyYear.")) ".
      "GROUP BY Medlem.MedlemID;";
    $rs3=$db->execute;    $sql);
    $MyTurtype="Racerkanin";
    break;
  case "LDK":

    $sql="SELECT TurDeltager.FK_MedlemID As MedlemID INTO Temptable ".
      "FROM TurType INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON TurType.TurTypeID = Tur.FK_TurTypeID ".
      "WHERE (TurType.Navn=\"Motion+tur\" AND Year([Ud])=".$MyYear.") GROUP BY TurDeltager.FK_MedlemID;";
    $rs3=$db->execute;    $sql);
    $MyTurtype="Motion+tur";
    break;
} 

if (strlen($roerID)<3)
{

  $rs=$db->execute;
}
  else
{

  $rs=$db->execute;  $RoerID."';");
} 


if (!$rs->eof)
{

  $ArrayMedlemsoplysninger=$rs->getrows();
} 

$rs->close;
$db->execute("DROP TABLE Temptable;");


switch ($Turtyper)
{
  case "Alt":


    $rs2=$db->execute;    $MyYear.") ".
      "GROUP BY TurDeltager.FK_MedlemID, [QRY Tur_og_ugenr].Uge;");
    if (!$rs2->eof)
    {
      $StatsArray=$rs2->getrows();
    } 
    $rs2->close;
    $Fontcolor="Black";

    break;
  case "Training":


    $rs2=$db->execute;    $MyTurtype."' AND Year([ud])=".$MyYear.") ".
      "GROUP BY TurDeltager.FK_MedlemID, [QRY Tur_og_ugenr].Uge;");
    if (!$rs2->eof)
    {
      $StatsArray=$rs2->getrows();
    } 
    $rs2->close;
    $Fontcolor="Red";

    break;
} 
$closedatabase;

if (is_array($ArrayMedlemsoplysninger))
{

  $i=0;
  for ($c1=0; $c1<=count($ArrayMedlemsoplysninger); $c1=$c1+1)
  {
    $i=$i+1;

    if (($i%2)==0)
    {

      $ThisRow="firstrow";
    }
      else
    {

      $ThisRow="secondrow";
    } 


    $MedlemID=$ArrayMedlemsoplysninger[0][$c1];
    $Medlemsnr=$ArrayMedlemsoplysninger[1][$c1];
    $MedlemsNavn=$ArrayMedlemsoplysninger[2][$c1]." ".$ArrayMedlemsoplysninger[3][$c1];

    for ($c3=1; $c3<=53; $c3=$c3+1)
    {
      $TurArray[$c3]=0;
      $KMArray[$c3]=0;

    }


//0 -- MedlemID
//1 -- Uge
//2 -- AntalTure
//3 -- KM		

    if (is_array($StatsArray))
    {

//0 -- MedlemID
//1 -- Uge
//2 -- AntalTure
//3 -- KM		

      for ($c2=0; $c2<=count($StatsArray); $c2=$c2+1)
      {
        if ($StatsArray[0][$c2]==$MedlemID)
        {

          $Turarray[$StatsArray[1][$c2]]=$StatsArray[2][$c2];
          $KMarray[$StatsArray[1][$c2]]=$StatsArray[3][$c2];
        } 


      }

    } 


?>
		<tr class="<?     echo $ThisRow;?>">
			<td><a href="traening.asp?RoerID=<?     echo $Medlemsnr;?>"><?     echo $Medlemsnr;?></a></td><? 
    for ($c3=$Startuge; $c3<=$slutuge; $c3=$c3+1)
    {
      $MyVar=$TurArray[$c3];
      if ($MyVar==0)
      {
        $Myvar="";
      } ?>
				<td align="right"><font color=<?       echo $FontColor;?>><?       echo $MyVar;?></font></td>
				<? 

    }

?>	
		</tr>
		<tr class="<?     echo $ThisRow;?>">
			<td><a href="traening.asp?RoerID=<?     echo $Medlemsnr;?>"><?     echo $Medlemsnavn;?></a></td>
			<? 
    for ($c3=$Startuge; $c3<=$slutuge; $c3=$c3+1)
    {
      $MyVar=$KMArray[$c3];
      if ($MyVar==0)
      {
        $Myvar="";
      } ?>
				<td align="right"><font color=<?       echo $FontColor;?>><?       echo $MyVar;?></font></td>
				<? 

    }

?>
		</tr>
	<? 

  }

} 


?>
	</table>

	<? 

if (strlen($RoerID)>1)
{


  $opendatabase;

  $s="SELECT TurType.Navn AS Turtype, Count([QRY Rostat_KunTureMedStyrmand].TurID) AS [Antal ture], Sum([Meter]\\1000) AS Rodistance, Int([Rodistance]/[Antal Ture]*10)/10 as Gennemsnit ";
  $s=$s."FROM ([QRY Rostat_KunTureMedStyrmand] LEFT JOIN (Medlem RIGHT JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON [QRY Rostat_KunTureMedStyrmand].TurID = TurDeltager.FK_TurID) LEFT JOIN TurType ON [QRY Rostat_KunTureMedStyrmand].FK_TurTypeID = TurType.TurTypeID ";
  $s=$s."GROUP BY TurType.Navn, Medlem.Medlemsnr HAVING (((Medlem.Medlemsnr)=\"".$RoerID."\"));";

  $rs=$db->execute;  $s);
  if (!$rs->eof)
  {

    $Turtypesummary[$RS];
  }
    else
  {

?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
  } 

  $rs->close;

?><br><? 

  $s="SELECT Tur.TurID, Båd.Navn AS Båd, Tur.Destination, Tur.OprettetDato as Oprettet, Int([Meter]*10)/10000 & \" km\" AS Turlængde, Medlem.Medlemsnr, [Fornavn] & \" \" & [Efternavn] AS Navn ";
  $s=$s."FROM Båd RIGHT JOIN (Medlem INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Båd.BådID = Tur.FK_BådID ";
  $s=$s."WHERE (((Medlem.Medlemsnr)=\"".$RoerID."\")) ORDER BY Tur.TurID DESC;";

  $rs=$db->execute;  $s);
  if (!$rs->eof)
  {

    $Turoversigt[$RS];
  }
    else
  {

?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
  } 

  $rs->close;
  $closedatabase;
} 


?>

</BODY>
</HTML>

