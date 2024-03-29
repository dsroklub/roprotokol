<!DOCTYPE html lang="da-DK">
<html>
  <head>
    <link rel="stylesheet" href="basic.css">
    <meta charset="utf-8">
  </head>
  <body>
       <h1>B&aring;dstatus <?php echo date("Y-m-d H:i"); ?></h1>
<button onClick="window.location.reload();">Opdater side</button>
<table>
         <caption>Inriggere</caption>
      <tr>
<th>B&aring;d</th>
<th>B&aring;dtype</th>
<th>placering</th>
<th>hylde</th>
<th>skade</th>
<th>forventes ind</th>
      </tr>
      <?php
         ini_set('default_charset', 'utf-8');
         ini_set('display_errors', 'On');

         $config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
         $rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

         if (!$rodb->set_charset("utf8")) {
             printf("Error loading character set utf8: %s\n", $rodb->error);
         }
$boatclause=" ";

$s="
SELECT b.boat_name,placement_level, b.boat_type, IFNULL(DamageType.name,'') as damageName,damage,out_time,location,expected
  FROM (
  SELECT Boat.id,Boat.Name AS boat_name, placement_level,Boat.Location as location, Boat.boat_type, COALESCE(MAX(Damage.Degree),0) as damage,Trip.OutTime as out_time, DATE_FORMAT(Trip.ExpectedIn,'%Y-%m-%d %H:%i') as expected
FROM
 Boat
   LEFT OUTER JOIN Damage ON (Damage.Boat=Boat.id AND Damage.Repaired IS NULL) JOIN DamageType
   LEFT OUTER JOIN Trip ON (Trip.BoatID = Boat.id AND Trip.Intime IS NULL)
WHERE
 Boat.boat_type like 'Inrigger%' AND
 Boat.Location != 'Andre' AND
 Boat.Decommissioned IS NULL
 GROUP BY Boat.id
 ) as b LEFT JOIN DamageType ON DamageType.degree=b.damage
ORDER BY location, boat_type,boat_name
";


//         error_log("SQL :\n".$s."\n");
if ($stmt = $rodb->prepare($s)) {
      $result=$rodb->query($s) or die("Error in instruktion stat query: " . mysqli_error($rodb));;
      while ($row = $result->fetch_assoc()) {
          $lvl=$row['placement_level']??'';
          if (!$lvl) $lvl='';
          elseif ($lvl==4) $lvl='elevator';
          print("<tr><td ".(($row['damage']>2 || !empty($row['expected']))?"class=\"alert\"":"").">".$row['boat_name'].
                "</td><td ". ($row['boat_type']=='Inrigger 2+'?"class=\"inriggertwo\"":"").">".$row['boat_type'] .
                "</td><td class=\"". ($row['location']!='DSR'?" notdsr":"") ."\">".$row['location']."</td><td>$lvl</td><td>".$row['damageName']."</td><td>".$row['expected']."</td></tr>\n");
      }
      }  else {
      error_log("SQL boat stat error: ".$rodb->error);
      echo " FEJL i boat statistik ".$rodb->error;
      }
      ?>
    </table>
    <?php
       $rodb->close();
    ?>
  </body>
</html>
