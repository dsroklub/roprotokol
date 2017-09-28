<?php
require_once("Mail.php");
include("../../rowing/backend/inc/common.php");
$res=array ("status" => "ok");
$json = file_get_contents("php://input");
$data=json_decode($json);
$smtp = Mail::factory('sendmail', array ());

error_log("invitations=".print_r($data,true));

$mail_headers = array(
    'From'                      => "Roaftaler i Danske Studenters Roklub <roaftaler_noreply@danskestudentersroklub.dk>",
    'Content-Transfer-Encoding' => "8bit",
    'Content-Type'              => 'text/plain; charset="utf8"',
    'Date'                      => date('r'),
    'Message-ID'                => "<".sha1(microtime(true))."@aftaler.danskestudentersroklub.dk>",
    'MIME-Version'              => "1.0",
    'X-Mailer'                  => "PHP-Custom",
    'Subject'                   => $data->subject
);



// require("inc/db.php");
require("../../public/inc/mail_sender.php");

$toEmails=array();
$qMarks = str_repeat('?,', count($data->members) - 1) . '?';
$stmt = $rodb->prepare("SELECT * FROM Member WHERE MemberId IN ($qMarks)");
$stmt->execute($data->members);
$result= $stmt->get_result() or die("Error in location query: " . mysqli_error($rodb));
while ($rower = $result->fetch_assoc()) {
    $toEmails[] = $rower->email;
}

$mail_status = $smtp->send($toEmails, $mail_headers, $data->message);

if (PEAR::isError($mail_status)) {
    $res["status"]="error";
    $res["message"] = "Kunne ikke sende invitationer til $toEmails: " . $mail_status->getMessage();
}

return $res;
echo json_encode($r);
?> 
