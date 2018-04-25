<!DOCTYPE html>

<html>
<head>
 <link rel="stylesheet" href="club.css"/>
<head>
<form method="post" action="index.php?action=mail">
  <input class="forumselect" name="memberid" pattern="[gk]?\d{1,5}" type="text" id="messagesto" placeholder='medlemsnummber' required><br>
  <input class="forumselect" name="subject" type="text" placeholder="emne" id="messagesubject" required><br>
  <textarea placeholder="besked" class="msgbody" name="body"  id="privatemessage_body" type="text" required>
  </textarea><br>
   <label>Send privat besked </label>
  <input type="submit"> 
<!--button class="green forumselect">Send privat besked </button-->
</form>
</html>
      

<?php
$cuser=-1;
include("../rowing/backend/inc/common.php");
include("../event/backend/messagelib.php");

if (isset($_REQUEST['action']) and ($_REQUEST['action']=="mail")) {
    error_log("klub private msg ".$_REQUEST['memberid']);
    $res=post_private_message($_REQUEST['memberid'], htmlspecialchars($_REQUEST['subject']),   htmlspecialchars($_REQUEST['body']));
    echo print_r($res);
}


header('Content-Type: text/html; charset=utf-8');

$s="SELECT CONCAT(Member.FirstName,' ',Member.LastName) AS owner_name, event.name, 
    BoatCategory.Name as boat_category, start_time, end_time, 
    distance, destination, TripType.Name as trip_type, max_participants, location, category, comment, event.open,event.status
    FROM Member, (event LEFT JOIN BoatCategory on BoatCategory.id=event.boat_category) LEFT JOIN TripType ON TripType.id=event.trip_type 
    WHERE Member.id=event.owner AND start_time >= NOW() ORDER BY start_time ASC LIMIT 50";
    $result=$rodb->query($s);

if ($result) {
    echo '<table class="tablelayout"> <caption>Begivenheder</caption>';
    echo "<thead>";
    echo "<th>Tur</th> <th>start</th> <th>distance</th> <th>Mål</th> <th>Mød</th>";
    echo "</thead>";
    $even=true;
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        $even=!$even;
        echo '<tr class="'. (($even)?"even":"odd") .'">';
        echo "<td>".$row["name"]."</td>";
        echo "<td>". date_format(date_create(($row["start_time"])),"H:i d. M")."</td>";
        echo "<td>".$row["distance"]."</td>";
        echo "<td>".$row["destination"]."</td> ";
        echo "<td>".$row["location"]."</td>";
        echo "<td>".$row["boat_category"]."</td>";
        echo "<td>".$row["category"]."</td>";
        echo "<td>".$row["trip_type"]."</td>";        
        echo "\n";
        echo "</tr>\n";
    }
    echo "</tbody>";
    echo "</table>";
}



    