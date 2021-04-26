<?php
include("inc/common.php");
$error=null;
$res=array ("status" => "ok");
$message="";
$data = file_get_contents("php://input");
$closedtrip=json_decode($data);

$distance = $closedtrip->distance;
if (!empty($closedtrip->corrected_distance)) {
    $distance=$closedtrip->corrected_distance;
}

$rodb->begin_transaction();
$tripId=$closedtrip->trip_id;

$stmt = $rodb->prepare("SELECT 'x' FROM Trip WHERE id=? AND InTime IS NULL") or dbErr($rodb,$res,"closetrip onwater check Prep");
$stmt->bind_param('i', $tripId) || dbErr($rodb,$res,"closetrip onwater check");
$stmt->execute() || dbErr($rodb,$res,"closetrip onwater check exe");
$result= $stmt->get_result();
if (!$result->fetch_assoc()) {
    $message="trip $tripId already closed or not on water: ". json_encode($closedtrip,JSON_PRETTY_PRINT);
    error_log($message);
    $rodb->close();
    $res['status']='error';
    $res['error']='notonwater';
    echo json_encode($res);
    exit(0);
}

$stmt = $rodb->prepare("UPDATE Trip SET InTime = NOW(),Meter=?, Destination=?,Comment=? WHERE id=?;") or dbErr($rodb,$res,"closetrip set intime Prep");
$stmt->bind_param('issi', $distance,$closedtrip->destination ,$closedtrip->comment,$closedtrip->trip_id) || dbErr($rodb,$res,"closetrip set intime");
$stmt->execute() || dbErr($rodb,$res,"closetrip SET intime");
$rodb->commit();

$res['message']=$message;
$res['boat']=$closedtrip->boat;
invalidate('trip');
invalidate('stats');


$countStmt = $rodb->prepare("SELECT count('x') as year_boat_trips FROM Trip WHERE BoatID=? AND YEAR(OutTime)=YEAR(NOW()) ") or dbErr($rodb,$res,"closetrip count trips");
$countStmt->bind_param('i', $closedtrip->boat_id) || dbErr($rodb,$res,"close trip cnt");
$countStmt->execute() || dbErr($rodb,$res,"close trip COUNT");
if ($countRow=$countStmt->get_result()->fetch_assoc()) {
    $res['boattrips']=$countRow['year_boat_trips'];
}


$stmt = $rodb->prepare(
    "SELECT member_setting.notification_email as email, CONCAT(FirstName,' ',LastName) as rower, Trip.Destination as destination
    FROM Member,Trip,TripMember,member_setting
    WHERE Trip.id=? AND TripMember.TripId=Trip.id AND member_setting.member=Member.id AND Member.id=TripMember.member_id AND notification_email IS NOT NULL" ) or dbErr($rodb,$res,"close trip perr");
$stmt->bind_param('i', $tripId) || dbErr($rodb,$res,"close trip berr");
$stmt->execute() || dbErr($rodb,$res,"close trip xerr");
$result= $stmt->get_result();
require_once("Mail.php");
$smtp = Mail::factory('sendmail', array ());

$mail_headers = array(
    'From'                      => "Roprotokollen i Danske Studenters Roklub <elgaard@agol.dk>",
    'Reply-To'                  => "Niels Elgaard Larsen <elgaard@agol.dk>",
      'Content-Transfer-Encoding' => "8bit",
    'Content-Type'              => 'text/plain; charset="utf8"',
    'Date'                      => date('r'),
    'Message-ID'                => "<".sha1(microtime(true))."@aftaler.danskestudentersroklub.dk>",
    'MIME-Version'              => "1.0",
    'X-Mailer'                  => "DSRroprotokol",
);

while ($row = $result->fetch_assoc()) {
    $email=$row['email'];
    $mail_headers['Subject'] = mb_encode_mimeheader($row['rower']." er gået i land");
    $mail_headers['To']=$email;
      $body=$row['rower'] . " er kommet tilbage fra ". $row['destination'].", " .number_format($distance/1000,1,",","")." km";
      if (!empty($closedtrip->comment)) {
          $body .= "\r\n\r\nKommentar til turen: ".$closedtrip->comment;
      }
      $mail_status = $smtp->send(array($email), $mail_headers, $body);
      if (PEAR::isError($mail_status)) {
          $warning="Kunne ikke sende besked som email: " . $mail_status->getMessage() . " headers=".print_r($mail_headers,true)." $body";
          error_log(" $warning ");
      } else {
          //  error_log(" SENT EMAIL " . $mail_status->getMessage());
      }
}

$rodb->close();
echo json_encode($res);
