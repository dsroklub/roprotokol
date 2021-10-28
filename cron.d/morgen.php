#!/usr/bin/php
<?php
define( 'ROOT_DIR', dirname(__FILE__) );
$config = parse_ini_file(ROOT_DIR . '/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);
require_once("Mail.php");
$smtp = Mail::factory('sendmail', array ());

function dbErr(&$db, &$res, $err="") {
    $res["status"]="error";
    $res["error"]="DB ". $err . ": " .$db->error. " FILE: ". $_SERVER['PHP_SELF'];
    error_log("Database error: $db->error $err :". $_SERVER['PHP_SELF']);
    http_response_code(500);
    echo json_encode($res);
    $db->rollback();
    $db->close();
    exit(1);
}

foreach (['7843'] as $mid) {
    echo "do $mid\n";


    $ranksql=
   "SELECT 1+COUNT('x')  as rank,rs.summer FROM 
     (
       SELECT Member.Email,CAST(Sum(Meter) AS UNSIGNED) AS distance,Member.MemberID as id, Member.FirstName as firstname, Member.LastName as lastname,
       CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer
    FROM Member,season,BoatType,Trip,TripMember,Boat
    WHERE
      Trip.id = TripMember.TripID AND
      Member.id = TripMember.member_id AND
      Boat.id = Trip.BoatID AND
      BoatType.Name = Boat.boat_type AND
      season.season=Year(OutTime) AND
      ((Year(OutTime))=YEAR(NOW())) AND BoatType.Category=2
      GROUP BY Member.id,Member.MemberID, firstname, lastname
    ORDER BY summer desc
    ) as s,
     (SELECT CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer FROM season,BoatType,Trip,TripMember,Boat,Member
              WHERE
              Trip.id = TripMember.TripID AND
              Member.id = TripMember.member_id AND
              Member.MemberID = ? AND
              Boat.id = Trip.BoatID AND
              BoatType.Name = Boat.boat_type AND
              season.season=Year(OutTime) AND
              ((Year(OutTime))=YEAR(NOW())) AND BoatType.Category=2
   ) as rs
  WHERE s.summer >rs.summer
";

    $ustmt = $rodb->prepare("SELECT Email from Member WHERE MemberID=?") or dbErr($rodb,$res,"user id rankactivities");
    $ustmt->bind_param("s", $mid);
    $ustmt->execute() || dbErr($rodb,$res,"user rant exe");
    $result= $ustmt->get_result() or die("Error in user rank query: " . mysqli_error($rodb));
    if ($urow = $result->fetch_assoc()) {
        echo "GOT ".print_r($urow,true);
        $emails=[$urow["Email"]];
    }
    
    $rankstmt = $rodb->prepare($ranksql) or dbErr($rodb,$res,"user rankactivities");
    $rankstmt->bind_param("s", $mid);
    $rankstmt->execute() || dbErr($rodb,$res,"user rant exe");
    $result= $rankstmt->get_result() or die("Error in user rank query: " . mysqli_error($rodb));
    $ranktxt="";
    if ($rankrow = $result->fetch_assoc()) {
        $name=$rankrow["name"];
        $ranktxt="Du har roet ". number_format($rankrow["summer"]/1000,1). " km og er nummer ". $rankrow["rank"] ." i rostatistikken (sommer)";
    }
    $body="Din daglige morgenorientering fra DSR roprotokol:\n$ranktxt";
    error_log("morgen: $body");
         $mail_headers = [
         'From'                      => "Roprotokollen i Danske Studenters Roklub <elgaard@agol.dk>",
         'To'                        => $emails,
         'Reply-To'                  => "Niels Elgaard Larsen <elgaard@agol.dk>",
         'Subject'                   => mb_encode_mimeheader("DSR roprotokol morgenorientering"),
         'Content-Transfer-Encoding' => "8bit",
         'Content-Type'              => 'text/plain; charset="utf8"',
         'Date'                      => date('r'),
         'Message-ID'                => "<".sha1(microtime(true))."@aftaler.danskestudentersroklub.dk>",
         'MIME-Version'              => "1.0",
         'X-Mailer'                  => "DSRroprotokol",
    ];

    $mail_status = $smtp->send($emails, $mail_headers, $body);
    echo $body;
    if (PEAR::isError($mail_status)) {
        $warning="Kunne ikke sende besked som email: " . $mail_status->getMessage() . " headers=".print_r($mail_headers,true)." $body";
        //echo $warning;
        error_log(" $warning ");
    }
}

$rodb->close();
