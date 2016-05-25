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
      <caption>Inringger instruktion</caption>
      <tr>
        <th>Dato</th>
        <th>antal roere<br> ink instruktører</th>
        <th>Antal Ture</th>
        <th>Antal roere<br> minus styrmand</th>
        <th>både</th>
      </tr>
      <?php
         ini_set('default_charset', 'utf-8');
         ini_set('display_errors', 'On');

         $config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
         $rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

         if (!$rodb->set_charset("utf8")) {
             printf("Error loading character set utf8: %s\n", $rodb->error);
         }
         $s="SELECT daytrips.day,rowers,trips,boats,seats FROM
(SELECT Date(Trip.OutTime) as day, COUNT(TripMember.member_id) as rowers, COUNT(DISTINCT(Trip.id)) as trips, 
GROUP_CONCAT(DISTINCT(Boat.Name) ORDER BY Boat.Name) as boats
FROM TripType,TripMember,Boat,BoatType, Trip
WHERE Trip.TripTypeID=TripType.id AND TripType.Name='Instruktion' AND TripMember.TripID=Trip.id  AND Boat.id=Trip.BoatID AND Boat.BoatType=BoatType.id AND BoatType.Seatcount>2 AND Category=2 GROUP BY day) as daytrips
JOIN (SELECT Date(Trip.OutTime) as day, SUM(BoatType.Seatcount-1) as seats
     FROM TripType,Boat,BoatType, Trip
     WHERE Trip.TripTypeID=TripType.id AND TripType.Name='Instruktion' AND Boat.id=Trip.BoatID AND Boat.BoatType=BoatType.id AND BoatType.Seatcount>2 GROUP by day)
     as sr ON sr.day=daytrips.day GROUP BY daytrips.day
ORDER BY day DESC
";


         error_log("SQL :\n".$s."\n");
         if ($stmt = $rodb->prepare($s)) { 
      $result=$rodb->query($s) or die("Error in instruktion stat query: " . mysqli_error($rodb));;
      while ($row = $result->fetch_assoc()) {
      print("<tr><td>".$row['day']."</td><td class='nr'>".$row['rowers']."</td><td class='nr'>".$row['trips']."</td><td class='nr'>".($row['seats'])."</td><td>".$row['boats']."</td></tr>\n");
      }
      }  else {
      error_log("SQL instruction stat error: ".$rodb->error);
      echo " FEJL i instruktionsstatistik ".$rodb->error;      
      }      
      ?>
    </table>
    <?php
       $rodb->close();
    ?> 
  </body>
</html>


