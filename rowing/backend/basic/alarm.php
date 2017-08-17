<?php
$config = parse_ini_file('../../../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

$s="SELECT Trip.Destination as destination, Trip.id as trip FROM Trip  WHERE Trip.InTime IS NULL AND Trip.ExpectedIn <= NOW() ";

$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
if ($row = $result->fetch_assoc()) {
    $rodb->close();
    system("ssh port4 bin/alarm");
} else {
    $rodb->close();
}
