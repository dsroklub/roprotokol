<?php
include("../../rowing/backend/inc/common.php");

require_once("Mail.php");

if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$res=array ("status" => "ok");
$data = file_get_contents("php://input");

error_log("data: $data");
$msg=json_decode($data);

$toEmails=array();
error_log("forum: " . $msg->forum->name);
$stmt = $rodb->prepare(
    "SELECT DISTINCT email 
     FROM Member,forum_subscription
     WHERE Member.id=forum_subscription.member AND forum_subscription.forum=?");

$stmt->bind_param('s',$msg->forum->name) or die("{\"status\":\"Error in message query bind: " . mysqli_error($rodb) ."\"}");
$stmt->execute() or die("{\"status\":'Error in message exe query: " . mysqli_error($rodb) ."\"}");
$result= $stmt->get_result() or die("{\"status\":'Error in message query: " . mysqli_error($rodb) ."\"}");

while ($rower = $result->fetch_assoc()) {
    error_log(print_r($rower,true));
    $toEmails[] = $rower['email'];
}
$result->free();

$smtp = Mail::factory('sendmail', array ());
$mail_headers = array(
    'From'                      => "Roaftaler i Danske Studenters Roklub <elgaard@agol.dk>",
    'Reply-To'                  => "Niels Elgaard Larsen <elgaard@agol.dk>",
    'Content-Transfer-Encoding' => "8bit",
    'Content-Type'              => 'text/plain; charset="utf8"',
    'Date'                      => date('r'),
    'Message-ID'                => "<".sha1(microtime(true))."@aftaler.danskestudentersroklub.dk>",
    'MIME-Version'              => "1.0",
    'X-Mailer'                  => "PHP-Custom",
    'Subject'                   => "$msg->subject"
);



if ($stmt = $rodb->prepare(
        "INSERT INTO forum_message(member_from, forum, created, subject, message)
         SELECT mf.id,?,NOW(),?,?
         FROM Member mf
         WHERE 
           mf.MemberId=?")) {

    $stmt->bind_param(
        'ssss',
        $msg->forum->name,
        $msg->subject,
        $msg->body,
        $cuser) ||  die("create forum message BIND errro ".mysqli_error($rodb));

    error_log("NOW EXE");
    if (!$stmt->execute()) {
        $error=" message forum error ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."forum message DB error: ".mysqli_error($rodb);
    } else {
        $error=$rodb->error;
        error_log("forum send insert db $error");
    } 
}
error_log("now send mail");
$mail_status = $smtp->send($toEmails, $mail_headers, $msg->body);

if (PEAR::isError($mail_status)) {
    $res["status"]="error";
    $res["message"] = "Kunne ikke sende besked" . $mail_status->getMessage();
}


if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
echo json_encode($res);
?> 
