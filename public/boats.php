<!DOCTYPE html lang="da-DK">
<html>
  <head>
    <link rel="stylesheet" href="basic.css">
    <meta charset="utf-8">
  </head>
  <body>
<?php
    include("../rowingapp/backend/inc/backheader.php");
?>
    <table>
<caption>Inriggere</caption>
      <tr>
<th>B&Aring;d</th>
        <th>turtype</th>
        <th>distance</th>
        <th>Antal Ture</th>
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

$s="SELECT Boat.id,Boat.Name AS boatname, BoatType.Name AS boat_type, COALESCE(MAX(Damage.Degree),0) as damage,
FROM (BoatType INNER JOIN Boat ON BoatType.Name = Boat.boat_type) LEFT JOIN Trip ON Boat.id = Trip.BoatID
 LEFT OUTER JOIN Damage ON (Damage.Boat=Boat.id AND Damage.Repaired IS NULL)
 LEFT OUTER JOIN Trip ON (Trip.BoatID = Boat.id AND Trip.Intime IS NULL)
WHERE 
         Boat.Decommissioned IS NULL
  ORDER BY Boat.id";


         error_log("SQL :\n".$s."\n");
         if ($stmt = $rodb->prepare($s)) {
      $result=$rodb->query($s) or die("Error in instruktion stat query: " . mysqli_error($rodb));;
      while ($row = $result->fetch_assoc()) {
      print("<tr><td>".$row['boatname']."</td><td>".$row['triptypename']."</td><td class='nr'>".$row['distance']."</td><td class='nr'>".$row['num_trips']."</td></tr>\n");
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
