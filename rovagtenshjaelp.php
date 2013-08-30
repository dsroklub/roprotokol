<!-- #include file="databaseINC.php" -->

<html>
<head>
<title>Rovagtens hjælper</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>

<body>

<h2>Hjælp til rovagten</h2>
<p>Ved at oplyse antallet af roere uden roret samt antallet af styrmænd beregner roprotokollen de kombinationer af firere og toere, du kan sætte.</p>
<form action="rovagtenshjaelp.php" method="post">
<table border=1 style="border-collapse: collapse">
	<tr>
		<td>
			<table border=0 width=300>
				<tr>
					<td>Antal styrmænd</td><td align="right"><input type="text" size="4" name="AntalStyrmaend"></td>
				</tr>
				<tr>
					<td>Antal menige roere</td><td align="right"><input type="text" size="4" name="AntalRoere"></td>
				</tr>
			</table>
			<input type="submit" value="Beregn">
		</td>
	</tr>
</table>
</form>

<? 
include "DatabaseINC.php";
if(!isset($_SESSION))  session_start();
$AntalRoere=0;
$AntalStyrmaend=0;
if (array_key_exists('AntalRoere',$_POST)) $AntalRoere=intval($_POST["AntalRoere"]);
if (array_key_exists('AntalStyrmaend',$_POST)) $AntalStyrmaend=intval($_POST["AntalStyrmaend"]);
$PladserIAlt=$AntalRoere+$AntalStyrmaend;

if ($PladserIAlt>0)
{


  $sql="SELECT Gruppe.Navn as Baadtype, Count(qAvailableboats.FK_GruppeID) AS Antal FROM qAvailableboats INNER JOIN Gruppe ON qAvailableboats.FK_GruppeID = Gruppe.GruppeID WHERE (((qAvailableboats.reserved_baadID) Is Null) AND ((qAvailableboats.onWater_baadID) Is Null) AND ((qAvailableboats.grad)<3)) GROUP BY Gruppe.Navn;";
  $db=OpenDatabase();
  $rr=$db->query($sql);
  error_log(" vDSRSQL=".$sql,0);
  $Available4ere=0;
  $Available2ere=0;
  while($rs = $rr->fetch_array(MYSQLI_ASSOC)) {

    if ($rs["Baadtype"]=="Inrigger 2+") {
      $Available2ere=$rs["Antal"];
    } 
    if ($rs["Baadtype"]=="Inrigger 4+") {
      $Available4ere=$rs["Antal"];
    } 
  } 
  // NEL  $closedatabase;
//
  $Max2ere=intval($PladserIAlt/3);
  if ($Max2ere>$AntalStyrmaend) {
    $Max2ere=$AntalStyrmaend;
  } 
  if ($Max2ere>$Available2ere) {
    $Max2ere=$Available2ere;
  } 


  $Max4ere=intval($PladserIAlt/5);
  if ($Max4ere>$AntalStyrmaend) {
    $Max4ere=$AntalStyrmaend;
  } 
  if ($Max4ere>$Available4ere) {
    $Max4ere=$Available4ere;
  } 

  $MinRest=999;
  $KombiCounter=0;

  for ($c1=0; $c1<=$Max2ere; $c1=$c1+1)
  {
    for ($c2=0; $c2<=$Max4ere; $c2=$c2+1) {
      if ($c1*3+$c2*5==$PladserIAlt) {
        $Kombinationer2ere[$KombiCounter]=$c1;
        $Kombinationer4ere[$KombiCounter]=$c2;
        $KombinationerRemarks[$KombiCounter]="";
        $KombiCounter=$KombiCounter+1;
      } 
    }
  }


//Hvis der ikke blev fundet nogen kombinationer, hvor det gik op
  $InfoText="";
  if ($KombiCounter==0) {
    $InfoText="<p>Viser kombinationsmuligheder med mindst mulig rest</p>";
    for ($c1=0; $c1<=$Max2ere; $c1=$c1+1) {
      for ($c2=0; $c2<=$Max4ere; $c2=$c2+1) {
        if ($c1*3+$c2*5<$PladserIAlt) {
          $Kombinationer2ere[$KombiCounter]=$c1;
          $Kombinationer4ere[$KombiCounter]=$c2;
          $KombinationerRemarks[$KombiCounter]=$PladserIAlt-($c1*3+$c2*5);
          if ($KombinationerRemarks[$KombiCounter]<$MinRest) {
            $MinRest=$KombinationerRemarks[$KombiCounter];
          } 
          $KombiCounter=$KombiCounter+1;
        } 
      }
    }
  } 

?>
<h2>Viser mulige kombinationer</h2>
<table border=1 style="border-collapse: collapse">
	<tr>		<td>
			<b>Forudsætninger</b>
			<table border=0 cellspacing="0">
				<tr><td width=200><p>Menige roere</p></td><td><?   echo $AntalRoere;?></td></tr>
				<tr><td><p>Styrmænd</p></td><td><?   echo $AntalStyrmaend;?></td></tr>
				<tr bgcolor="#99dddd"><td><p>Pladsbehov i alt</p></td><td><?   echo $PladserIAlt;?></td></tr>
				<tr><td colspan=2><hr size="1" color="#000000"></td></tr>
				<tr><td><p>Tilgængelige 2'ere</p></td><td><?   echo $Available2ere;?></td></tr>
				<tr><td><p>Tilgængelige 4'ere</p></td><td><?   echo $Available4ere;?></td></tr>
			</table>
		</td>
	</tr>
</table>

<p> </p>

<b>Kombinationsmuligheder</b>
<?   echo $InfoText;?>

<table border=1 style="border-collapse: collapse" width="200">
	<tr>
		<th class="tablehead">2'ere</th><th class="tablehead">4'ere</th><th class="tablehead">Rest</th>
	</tr>
	<? 

  $c2=0;
  while(!($c2==$KombiCounter))
  {

    if ($KombinationerRemarks[$c2]==$MinRest || $MinRest==999)
    {

      print "<tr><td align='right'><p>".$Kombinationer2ere[$c2]."</p></td><td align='right'><p>".$Kombinationer4ere[$c2]."</p></td><td align='right'><p>".$KombinationerRemarks[$c2]."</p></td></tr>";
    } 

    $c2=$c2+1;
  } 
?>
</table>

<? 
} 

?>

</body>
</html>




