<?php
$r=array ("status" => "ok");
$memberId = trim(file_get_contents("php://input"));
error_log("mid=$memberId");

require("inc/db.php");
require("inc/mail_sender.php");
$res = $link->query("SELECT * FROM Member WHERE MemberId = '" . (int) $memberId."'");
if ($res) {
    error_log("got member");
    $person = $res->fetch_assoc();
    if ($person) {
        $pw=null;
        if ($stmt = $link->prepare("SELECT newpassword FROM authentication,Member WHERE member_id=Member.id AND Member.MemberId=?")) {
            $stmt->bind_param('i', $memberId);
            $stmt->execute();
            $result= $stmt->get_result();
            if ($result) {
                $pw=$result->fetch_assoc()['newpassword'];
                error_log("old pw= $pw");
            }
        } else {
            error_log("get pw error: ".$link->error);
        }
        if (empty($pw) or ($pw[0]=='$')) { // transferred htpasswd hashes
            $pw = generate_password();
            $hpw= '{SHA}' . base64_encode(sha1($pw, TRUE));
            error_log("new  pw= $pw $hpw");
//            if ($stmt = $link->prepare("UPDATE authentication SET password=?, newpassword=? WHERE member_id=?")) {
            if ($istmt = $link->prepare(
                "INSERT INTO authentication (password,newpassword,member_id)  SELECT ?,?,id FROM Member WHERE MemberId=?")) {
                error_log("now Bind");
                $istmt->bind_param('sss', $hpw,$pw,$memberId) || error_log($link->error);

                error_log("now EXE");
                $istmt->execute() || error_log("pw update error: ". $link->error);                            
            } else {
                error_log("Prepare Error:". $link->error);
            }
        }
        $body = "Kode til DSR for $memberId \n Din kode er: $pw\nDit brugernavn er dit medlemsnummer";
        $mail_error = send_email("Kode til DSR styrmandsinstruktion og aftaler", $body, $person, $pw);
        if ($mail_error) {
            echo "<p class=\"error\">Fejl: Kunne ikke afsende mail til aftaler og styrmandsinstruktion: $mail_error</p>\n";
        } else {
            $sent = true;
        }
    } else {
        $r["status"]="not member";
        $r["message"]="medlemsnummeret findes ikke";
    }
    $res->close();
} else {
    $r["status"]="member login db error";
}
echo json_encode($r);
?> 
