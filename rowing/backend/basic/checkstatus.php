#!/usr/bin/php
<?php
$config = parse_ini_file('/data/roprotokol/config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);
//set_include_path(get_include_path().':..');

$emails=[];
$emailres=$rodb->query("SELECT value as emails from Configuration WHERE id='not_in_emails'") or die ("not in email failed");

if ($e = $emailres->fetch_assoc()) {

    if (!empty($e["emails"])){
        $emails=explode(",",$e["emails"]);
    }
}

$s="SELECT Boat.id as boatid, Boat.Name AS boat, Trip.Destination as destination,
   IF (DAYOFYEAR(NOW())=DAYOFYEAR(OutTime),Date_Format(OutTime,'%H:%i'), Date_Format(OutTime,'%e/%c %H:%i')) as outtime,
   IF (DAYOFYEAR(NOW())=DAYOFYEAR(InTime),Date_Format(InTime,'%H:%i'),Date_Format(InTime,'%e/%c %H:%i')) as intime,
   IF (DAYOFYEAR(NOW())=DAYOFYEAR(ExpectedIn),Date_Format(ExpectedIn,'%H:%i'), Date_Format(ExpectedIn,'%e/%c %H:%i')) as expectedintime,
   Trip.Destination as destination, Trip.id, TripType.Name AS triptype,
   CONCAT(
       '[',
        GROUP_CONCAT(JSON_OBJECT(
       'member_id',Member.MemberID,
       'email',Member.Email,
       'name', CONCAT(Member.FirstName,' ',Member.LastName))),
   ']') AS rowers
   FROM TripMember LEFT JOIN Member ON Member.id = TripMember.member_id, TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.id = Trip.BoatID) ON TripType.id = Trip.TripTypeID
   WHERE Trip.id=TripMember.TripID AND Trip.InTime IS Null AND NOW()>ExpectedIn
   GROUP BY Trip.id
   ORDER BY InTime,ExpectedIn";

$result=$rodb->query($s) or die("Error in stat query: " . mysqli_error($rodb));;
 while ($trip = $result->fetch_assoc()) {
     $names=[];
     $emails=[];
     $rowers=json_decode($trip['rowers']);
     foreach ($rowers as $rower) {
         if (!empty($rower->email)) {
             $emails[]=$rower->email;
         }
         $names[]= $rower->name;
     }
     $allnames=implode(", ",$names);
     $body="Kære $allnames\n ".$trip["boat"]." er stadig skrevet ud til ".$trip["destination"].", den skulle være inde ".$trip["expectedintime"]."\n Hvis ".(count($names)>1?"I":"du")." er kommet i land, så få båden skrevet ind\n".
         ' Du kan selv skrive båden in på https://aftaler.danskestudentersroklub.dk/ under: "Min Side", "mine ture"'.
         "\n\n--\nroprotokollen";

     $mail_headers = [
         'From'                      => "Roprotokollen i Danske Studenters Roklub <roprotokol@roprotokol.danskestudentersroklub.dk>",
         'To'                        => $emails,
         'Reply-To'                  => "Niels Elgaard Larsen <elgaard@agol.dk>",
         'Subject'                   => mb_encode_mimeheader($trip["boat"] ." er stadig skrevet ud"),
         'Content-Transfer-Encoding' => "8bit",
         'Content-Type'              => 'text/plain; charset="utf8"',
         'Date'                      => date('r'),
         'Message-ID'                => "<".sha1(microtime(true))."@aftaler.danskestudentersroklub.dk>",
         'MIME-Version'              => "1.0",
         'X-Mailer'                  => "DSRroprotokol",
    ];

     echo $body;
     require_once("Mail.php");
     $smtp = Mail::factory('sendmail', array ());
     $mail_status = $smtp->send($emails, $mail_headers, $body);
     if (PEAR::isError($mail_status)) {
         $warning="Kunne ikke sende besked som email: " . $mail_status->getMessage() . " headers=".print_r($mail_headers,true)." $body";
         //echo $warning;
         error_log(" $warning ");
      }
 }

$rodb->close();
