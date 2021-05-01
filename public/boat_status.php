<!DOCTYPE html lang="da-DK">
<html>
  <head>
    <link rel="stylesheet" href="basic.css">
    <meta charset="utf-8">
  </head>
  <body>
    <table>
<caption>Inriggere</caption>
      <tr>
<th>B&aring;d</th>
<th>B&aring;dtype</th>
<th>placering</th>
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
SELECT b.boat_name, b.boat_type, IFNULL(DamageType.name,'') as damage,out_time,location,expected
  FROM (
  SELECT Boat.id,Boat.Name AS boat_name, Boat.Location as location, Boat.boat_type, COALESCE(MAX(Damage.Degree),0) as damage,Trip.OutTime as out_time, DATE_FORMAT(Trip.ExpectedIn,'%Y-%m-%d %H:%i') as expected
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
      print("<tr><td>".$row['boat_name']."</td><td>".$row['boat_type']."</td><td class='nr'>".$row['location']."</td><td>".$row['damage']."</td><td>".$row['expected']."</td></tr>\n");
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
