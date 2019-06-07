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

if ($stmt = $rodb->prepare("SELECT 'x' FROM Trip WHERE id=? AND InTime IS NULL")) { 
  $stmt->bind_param('i', $tripId);
  $stmt->execute();
  $result= $stmt->get_result();
  if (!$result->fetch_assoc()) {
      $error='notonwater';
      $message="trip $tripId already closed or not on water: ". json_encode($closedtrip,JSON_PRETTY_PRINT);
      error_log($message);
  }
} 

if (!$error) {
    if ($stmt = $rodb->prepare(
        "UPDATE Trip SET InTime = NOW(),Meter=?, Destination=?,Comment=? WHERE id=?;"
    )) { 
        $stmt->bind_param('issi', $distance,$closedtrip->destination ,$closedtrip->comment,$closedtrip->trip_id);
        $stmt->execute(); 
        $rodb->commit();
    }
}

if ($error) {
    $res['status']='error';
    $res['error']=$error;
}
$res['message']=$message;
$res['boat']=$closedtrip->boat;
invalidate('trip');
invalidate('stats');


if ($stmt = $rodb->prepare(
    "SELECT member_setting.notification_email as email, CONCAT(FirstName,' ',LastName) as rower, Trip.Destination as destination  
    FROM Member,Trip,TripMember,member_setting
    WHERE Trip.id=? AND TripMember.TripId=Trip.id AND member_setting.member=Member.id AND Member.id=TripMember.member_id AND notification_email IS NOT NULL" )) { 
  $stmt->bind_param('i', $tripId);
  $stmt->execute();
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

      if (!empty($closedtrip->boat->comment)) {
          $body .= "\r\n\r\nKommentar til turen: ".$closedtrip->boat->comment;    
      }
      $mail_status = $smtp->send(array($email), $mail_headers, $body);

      if (PEAR::isError($mail_status)) {
          $warning="Kunne ikke sende besked som email: " . $mail_status->getMessage() . " headers=".print_r($mail_headers,true)." $body";
          error_log(" $warning ");
      } else {
          //  error_log(" SENT EMAIL " . $mail_status->getMessage());
      }
  } 
} else {
    error_log("close trip email error: ". $rodb->error);
} 

$rodb->close();
echo json_encode($res);
