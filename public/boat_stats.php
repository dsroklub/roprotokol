<?php
include("../rowing/backend/inc/backheader.php");
include(__DIR__."/publicutils.php");
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'On');
$format=$_GET["format"] ?? "text";

if ($format=="textx") {
    header('Content-type: text/html');
    echo '<html><head><link rel="stylesheet" href="stat.css"><meta charset="utf-8"></head><body>';
}

$y=date('Y');
if (isset($_GET["year"])) {
    if ($_GET["year"] <0) {
        $y=$y+(int)($_GET["year"]);
    } else {
        $y=((int)($_GET["year"]));
    }
}

//      echo "    <table><caption>Inringger b&aring;dstatistik $y</caption> <tr><th>B&aring;d</th> <th>distance</th> <th>Antal Ture</th></tr> ";

$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

if (!$rodb->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $rodb->error);
}
$boatclause=" ";

if ($y==5) {
    $captions=["år","båd","bådtype","distance","antal ture"];
    $s="SELECT Year(OutTime) as year,Boat.Name AS boatname, BoatType.Name AS boat_type, CAST(Sum(Meter/1000.0) AS UNSIGNED) AS distance, Count(Trip.id) AS num_trips
FROM (BoatType INNER JOIN Boat ON BoatType.Name = Boat.boat_type) LEFT JOIN Trip ON Boat.id = Trip.BoatID
WHERE Year(OutTime)>YEAR(NOW())-6 AND (BoatType.Category=2) GROUP BY year,Boat.Name, BoatType.Name, Boat.id ORDER BY year desc, distance desc";
    $stmt=$rodb->prepare($s) or dbErr($rodb,$res,"boststas prep 5");
} else {
    $captions=["båd","bådtype","distance","antal ture"];
    $s="SELECT Boat.Name AS boatname, BoatType.Name AS boat_type, CAST(Sum(Meter/1000.0) AS UNSIGNED) AS distance, Count(Trip.id) AS num_trips
FROM (BoatType INNER JOIN Boat ON BoatType.Name = Boat.boat_type) LEFT JOIN Trip ON Boat.id = Trip.BoatID
WHERE Year(OutTime)=? AND (BoatType.Category=2) GROUP BY Boat.Name, BoatType.Name, Boat.id ORDER BY distance desc";
    $stmt=$rodb->prepare($s) or dbErr($rodb,$res,"boststas prep");
    $stmt->bind_param("i", $y) || dbErr($rodb,$res,"boatstats");
}

$stmt->execute() || dbErr($rodb,$res,"boatstat exe");
$result=$stmt->get_result() or dbErr($rodb,$res,"Error in boat name stats query: ");
process($result,$format,"Inrigger bådstatistik $y",$captions);
//      while ($row = $result->fetch_assoc()) {
//	  print("<tr><td>".$row['boatname']."</td><td class='nr'>".$row['distance']."</td><td class='nr'>".$row['num_trips']."</td></tr>\n");
//      }
?>
    </table>
    <?php
       $rodb->close();
    ?>
  </body>
</html>
