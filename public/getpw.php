<?php
$r=array ("status" => "ok");
$memberId = trim(file_get_contents("php://input"));
//error_log("mid=$memberId");
require("inc/db.php");
require("inc/mail_sender.php");
$res = $link->query("SELECT * FROM Member WHERE RemoveDate IS NULL AND MemberId = '" . (int) $memberId."'");
if ($res) {
    error_log("got member $memberId");
    $person = $res->fetch_assoc();
    if ($person || $memberId=='kajakskur') {
        $pw=null;
        if ($stmt = $link->prepare("SELECT newpassword FROM authentication,Member WHERE member_id=Member.id AND Member.MemberId=?")) {
            $stmt->bind_param('s', $memberId);
            $stmt->execute();
            $result= $stmt->get_result();
            if ($result && $r=$result->fetch_assoc()) {
                $pw=$r['newpassword'];
                error_log("old pw= $pw");
            }
        } else {
            error_log("get pw error: ".$link->error);
        }
        if (empty($pw) or $pw="xxx" or ($pw[0]=='$')) { // transferred htpasswd hashes
            $pw = generate_password();
            $hpw= '{SHA}' . base64_encode(sha1($pw, TRUE));
            //            error_log("new  pw= $pw $hpw");
                if ($istmt = $link->prepare(
                    "REPLACE INTO authentication (password,newpassword,member_id)  SELECT ?,?,id FROM Member WHERE MemberId=?")) {
                    error_log("now Bind");
                    $istmt->bind_param('sss', $hpw,$pw,$memberId) || error_log($link->error);
                    error_log("now EXE");
                    $istmt->execute() || error_log("pw update error: ". $link->error);
                } else {
                    error_log("Prepare Error:". $link->error);
                }
            }
        $body = "Kode til DSR for $memberId \n Din kode er: $pw\nDit brugernavn er dit medlemsnummer";
        $mail_error = send_email("Kode til DSR roprotokol og aftaler", $body, $person, $pw);
        if ($mail_error) {
            echo "<p class=\"error\">Fejl: Kunne ikke afsende mail til aftaler og roprotokol: $mail_error</p>\n";
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
