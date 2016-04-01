<!DOCTYPE html lang="da-DK">
<html>
  <head>
    <link rel="stylesheet" href="basic.css">
  </head>
  <body>      
    
    <table>
      <caption>Inringger instruktion</caption>
      <tr>
        <th>Dato</th>
        <th>antal roere<br> ink instruktører</th>
        <th>Antal Ture</th>
        <th>Antal roere<br> minus styrman</th>
        <th>både</th>
      </tr>
      <?php
         ini_set('default_charset', 'utf-8');
         ini_set('display_errors', 'On');
         $config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
         $rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

         $s="SELECT Date(OutTime) as day, COUNT(TripMember.member_id) as rowers, COUNT(DISTINCT(Trip.id)) as trips, GROUP_CONCAT(DISTINCT(Boat.Name)) as boats 
             FROM Trip,TripType,TripMember,Boat,BoatType 
             WHERE Trip.TripTypeID=TripType.id AND TripType.Name='Instruktion' AND TripMember.TripID=Trip.id  AND Boat.id=Trip.BoatID AND Boat.BoatType=BoatType.id AND BoatType.Seatcount>2 AND Category=2 
             GROUP BY day
             ORDER BY day desc
             ";
         error_log("SQL :\n".$s."\n");
         if ($stmt = $rodb->prepare($s)) { 
      $result=$rodb->query($s) or die("Error in instruktion stat query: " . mysqli_error($rodb));;
      while ($row = $result->fetch_assoc()) {
      print("<tr><td>".$row['day']."</td><td class='nr'>".$row['rowers']."</td><td class='nr'>".$row['trips']."</td><td class='nr'>".($row['rowers']-$row['trips'])."</td><td>".$row['boats']."</td></tr>\n");
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


