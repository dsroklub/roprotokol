#!/usr/bin/php
<?php
echo "morgenorientering";

$motd="";
$motdfile="/data/media/motd";
if (file_exists($motdfile)) {
    $motd = "\n\n".file_get_contents($motdfile);
}

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


$ranksql="
SELECT Member.id as member_id,CONCAT(Member.FirstName,' ',Member.LastName) as name,m.rank,m.year_rank,Member.Email as email,m.summer,m.distance,g.gymrank,g.gymcount,m.rowingtrips
FROM
Member LEFT JOIN
(SELECT
  member_id,
  ROW_NUMBER() OVER ( ORDER BY summer DESC) rank,
  ROW_NUMBER() OVER ( ORDER BY distance DESC) year_rank,
  distance,summer,rowingtrips
   FROM
   (SELECT
    COUNT('x') as rowingtrips,
    CAST(Sum(Meter) AS UNSIGNED) AS distance,
    CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer,
    rm.id as member_id, rm.MemberID
    FROM season,Trip,TripMember,Member rm
    WHERE
      rm.id = TripMember.member_id AND
      Trip.id = TripMember.TripID AND
      season.season=Year(OutTime) AND
      Year(OutTime)=YEAR(NOW())
     GROUP BY rm.MemberID
  )  as im
) as m ON m.member_id=Member.id LEFT JOIN
(
  SELECT COUNT('x') AS gymcount,gm.id as member_id,
  ROW_NUMBER() OVER ( ORDER BY gymcount DESC) gymrank
  FROM team_participation,Member gm
    WHERE  YEAR(start_time)=YEAR(NOW()) AND
       gm.id=team_participation.member_id
     GROUP BY gm.id
  ) as g ON g.member_id=Member.id,
    member_setting
WHERE member_setting.member=m.member_id AND member_setting.morning_status=1
";
$mail_status=null;
$result = $rodb->query($ranksql) or dbErr($rodb,$res," rank prep");
while ($rankrow = $result->fetch_assoc()) {
    $ranktxt="\nKære ".$rankrow["name"].
        "\nDu har roet ". number_format($rankrow["summer"]/1000,1). " km sommer, " .
        " og er nummer ". $rankrow["rank"] ." i sommerrostatistikken\n".
        "Du har roet ".number_format($rankrow["distance"]/1000,1). " på ".$rankrow["rowingtrips"]." ture km hele året" .
                " og er nummer ". $rankrow["year_rank"] ." (hele året)\n" ;

    if (!empty($rankrow["gymcount"])) {
        $ranktxt .= "\n\nDu har gået til gymnastik ". $rankrow["gymcount"] ." gang". ($rankrow["gymcount"]>1?"e":"")." \n ".
            "og er nummer ".$rankrow["gymrank"] ." i gymnastikstatistikken ";
    }
    $body="\nDin daglige morgenorientering fra DSR roprotokol:\n$ranktxt\n$motd";
    error_log("morgen: $body");
         $mail_headers = [
         'From'                      => "Roprotokollen i Danske Studenters Roklub <roprotokol@roprotokol.danskestudentersroklub.dk>",
         'To'                        => [$rankrow["email"]],
         'Reply-To'                  => "Niels Elgaard Larsen <elgaard@agol.dk>",
         'Subject'                   => mb_encode_mimeheader("DSR roprotokol morgenorientering"),
         'Content-Transfer-Encoding' => "8bit",
         'Content-Type'              => 'text/plain; charset="utf8"',
         'Date'                      => date('r'),
         'Message-ID'                => "<".sha1(microtime(true))."@roprotokol.danskestudentersroklub.dk>",
         'MIME-Version'              => "1.0",
         'X-Mailer'                  => "DSRroprotokol",
    ];
    $mail_status = $smtp->send($rankrow["email"], $mail_headers, $body);
    //    echo $body;
    if (PEAR::isError($mail_status)) {
        $warning="Kunne ikke sende besked som email: " . $mail_status->getMessage() . " headers=".print_r($mail_headers,true)." $body";
        //echo $warning;
        error_log("morgenstatus $warning ");
    }
}
$rodb->close();
