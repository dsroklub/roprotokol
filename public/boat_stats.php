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
<caption>Inringger b&aring;dstatistik i &aring;r</caption>
      <tr>
<th>B&aring;d</th>
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

$s="SELECT Boat.id,Boat.Name AS boatname, BoatType.Name AS boat_type, CAST(Sum(Meter/1000.0) AS UNSIGNED) AS distance, Count(Trip.id) AS num_trips
FROM (BoatType INNER JOIN Boat ON BoatType.id = Boat.BoatType) LEFT JOIN Trip ON Boat.id = Trip.BoatID
WHERE Year(OutTime)=Year(NOW()) AND (BoatType.Category=2) GROUP BY Boat.Name, BoatType.Name, Boat.id ORDER BY distance desc";


//         error_log("SQL :\n".$s."\n");
         if ($stmt = $rodb->prepare($s)) { 
      $result=$rodb->query($s) or die("Error in instruktion stat query: " . mysqli_error($rodb));;
      while ($row = $result->fetch_assoc()) {
      print("<tr><td>".$row['boatname']."</td><td class='nr'>".$row['distance']."</td><td class='nr'>".$row['num_trips']."</td></tr>\n");
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


