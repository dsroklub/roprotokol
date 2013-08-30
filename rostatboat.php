<?
  session_start();
  session_register("nemesis_session");
  session_register("SorterEfter_Boat_session");
  session_register("SortOrder_Boat_session");
?>
<? // asp2php (vbscript) converted on Sun Aug 11 21:04:58 2013
 ?>
<!-- #include file="databaseINC.php" -->

<HTML>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>

<BODY bgproperties="fixed" background="images/baggrund.jpg">
<P>
<table align="center"><tr><td>
<? 
$RostatAction=${"RostatAction"};
$IDAction=${"ID"};
$subgroup=${"subgroup"};

$_SESSION['nemesis']=123;


switch ($subgroup)
{
  case "alle":

    $s="SELECT [QRY Rostat_BådstatistikMain].Båd, [QRY Rostat_BådstatistikMain].Bådtype, [QRY Rostat_BådstatistikMain].Rodistance & \" km\" as Afstand, [QRY Rostat_BådstatistikMain].[Antal ture], Int([rodistance]/[antal ture]*10)/10 & \" km\" as Gennemsnit ";
    $s=$s."FROM [QRY Rostat_BådstatistikMain] ";
    break;
  case "kajakker":

    $s="SELECT [QRY Rostat_BådstatistikMain].Båd, [QRY Rostat_BådstatistikMain].Bådtype, [QRY Rostat_BådstatistikMain].Rodistance & \" km\" as Afstand, [QRY Rostat_BådstatistikMain].[Antal ture], Int([rodistance]/[antal ture]*10)/10 & \" km\" as Gennemsnit ";
    $s=$s."FROM [QRY Rostat_BådstatistikMain] ";
    $s=$s."WHERE left(Bådtype,5) = 'Kajak' ";

    break;
  case "robåde":

    $s="SELECT [QRY Rostat_BådstatistikMain].Båd, [QRY Rostat_BådstatistikMain].Bådtype, [QRY Rostat_BådstatistikMain].Rodistance & \" km\" as Afstand, [QRY Rostat_BådstatistikMain].[Antal ture], Int([rodistance]/[antal ture]*10)/10 & \" km\" as Gennemsnit ";
    $s=$s."FROM [QRY Rostat_BådstatistikMain] ";
    $s=$s."WHERE left(Bådtype,5)<> 'Kajak'  ";

    break;
} 


switch ($RostatAction)
{
  case "RankB":

    if ($_SESSION['SorterEfter_Boat']=="RankB")
    {

      $_SESSION['SortOrder_Boat']=$_SESSION['SortOrder_Boat'] ^ 1;
    }
      else
    {

      $_SESSION['SorterEfter_Boat']="RankB";
      $_SESSION['SortOrder_Boat']=1;
    } 


    $Direction=";";
    if ($_SESSION['SortOrder_Boat']==1)
    {
      $Direction=" DESC;";
    } 

    $S=$s."Order by [QRY Rostat_BådstatistikMain].Rodistance".$Direction;

    break;
  case "TypeB":

    if ($_SESSION['SorterEfter_Boat']=="TypeB")
    {

      $_SESSION['SortOrder_Boat']=$_SESSION['SortOrder_Boat'] ^ 1;
    }
      else
    {

      $_SESSION['SorterEfter_Boat']="TypeB";
      $_SESSION['SortOrder_Boat']=1;
    } 


    $Direction=";";
    if ($_SESSION['SortOrder_Boat']==0)
    {
      $Direction=" DESC;";
    } 

    $S=$s."Order by [QRY Rostat_BådstatistikMain].Bådtype".$Direction;

    break;
  case "NameB":

    if ($_SESSION['SorterEfter_Boat']=="NameB")
    {

      $_SESSION['SortOrder_Boat']=$_SESSION['SortOrder_Boat'] ^ 1;
    }
      else
    {

      $_SESSION['SorterEfter_Boat']="NameB";
      $_SESSION['SortOrder_Boat']=1;
    } 


    $Direction=";";
    if ($_SESSION['SortOrder_Boat']==0)
    {
      $Direction=" DESC;";
    } 

    $S=$s."Order by [QRY Rostat_BådstatistikMain].Båd".$Direction;

    break;
  case "TypeB":

    if ($_SESSION['SorterEfter_Boat']=="TypeB")
    {

      $_SESSION['SortOrder_Boat']=$_SESSION['SortOrder_Boat'] ^ 1;
    }
      else
    {

      $_SESSION['SorterEfter_Boat']="TypeB";
      $_SESSION['SortOrder_Boat']=1;
    } 


    $Direction=";";
    if ($_SESSION['SortOrder_Boat']==0)
    {
      $Direction=" DESC;";
    } 

    $S=$s."Order by [QRY Rostat_BådstatistikMain].Bådtype".$Direction;

    break;
  case "TripsB":

    if ($_SESSION['SorterEfter_Boat']=="TripsB")
    {

      $_SESSION['SortOrder_Boat']=$_SESSION['SortOrder_Boat'] ^ 1;
    }
      else
    {

      $_SESSION['SorterEfter_Boat']="TripsB";
      $_SESSION['SortOrder_Boat']=1;
    } 


    $Direction=";";
    if ($_SESSION['SortOrder_Boat']==0)
    {
      $Direction=" DESC;";
    } 

    $S=$s."Order by [QRY Rostat_BådstatistikMain].[Antal ture]".$Direction;

    break;
  case "AvrLenB":

    if ($_SESSION['SorterEfter_Boat']=="AvrLenB")
    {

      $_SESSION['SortOrder_Boat']=$_SESSION['SortOrder_Boat'] ^ 1;
    }
      else
    {

      $_SESSION['SorterEfter_Boat']="AvrLenB";
      $_SESSION['SortOrder_Boat']=1;
    } 


    $Direction=";";
    if ($_SESSION['SortOrder_Boat']==0)
    {
      $Direction=" DESC;";
    } 

    $S=$s."Order by ([rodistance]/[antal ture])".$Direction;

    break;
  default:

    print "Forkert parameter (".$RostatAction.")";
    exit();

    break;
} 

$opendatabase;
$WriteHit"Bådstatistik"
$rs=$db->execute;$s);
if (!$rs->eof)
{

  $Baadstatistik$rs  $subgroup;
}
  else
{

?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

$rs->close;
$closedatabase;

?>
</td></tr></table>
</P>

</BODY>
</HTML>

