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


$ranksql="
SELECT member_id,name,rank,year_rank,email,summer,distance FROM
(SELECT
  member_id,
  ROW_NUMBER() OVER ( ORDER BY summer DESC) rank,
  ROW_NUMBER() OVER ( ORDER BY distance DESC) year_rank,
  distance,summer,name,email
   FROM
   (SELECT
    CAST(Sum(Meter) AS UNSIGNED) AS distance,
    CAST(SUM(IF(season.summer_start<OutTime AND season.summer_end>OutTime,Meter,0)) AS UNSIGNED) AS summer,
    Member.id as member_id, Member.MemberID,CONCAT(Member.FirstName,' ',Member.LastName) as name,
    Member.Email as email
    FROM season,Trip,TripMember,Member
    WHERE
      Member.id = TripMember.member_id AND
      Trip.id = TripMember.TripID AND
      season.season=Year(OutTime) AND
      Year(OutTime)=YEAR(NOW())
     GROUP BY Member.MemberID, name,email
  )   as m
) as m,    member_setting
WHERE member_setting.member=m.member_id AND member_setting.morning_status=1
";
$mail_status=null;
echo "$ranksql\nm2\n";
$result = $rodb->query($ranksql) or dbErr($rodb,$res," rank prep");
while ($rankrow = $result->fetch_assoc()) {
    $ranktxt="Kære ".$rankrow["name"].
        " Du har roet ". number_format($rankrow["summer"]/1000,1). " km sommer, " .
        number_format($rankrow["distance"]/1000,1). " km hele året" .
        " og er nummer ". $rankrow["rank"] ." i rostatistikken (sommer)".
                ", nummer ". $rankrow["year_rank"] ." (hele året)" ;
    $body="\nDin daglige morgenorientering fra DSR roprotokol:\n$ranktxt";
    error_log("morgen: $body");
         $mail_headers = [
         'From'                      => "Roprotokollen i Danske Studenters Roklub <aftaler.danskestudentersroklub.dk>",
         'To'                        => [$rankrow["email"]],
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
    //    echo $body;
    if (PEAR::isError($mail_status)) {
        $warning="Kunne ikke sende besked som email: " . $mail_status->getMessage() . " headers=".print_r($mail_headers,true)." $body";
        //echo $warning;
        error_log(" $warning ");
    }
}
$rodb->close();
