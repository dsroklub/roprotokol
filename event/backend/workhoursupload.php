<?php
include("inc/common.php");
require("inc/utils.php");

verify_right(["admin"=>["vedligehold"]]);
$data = file_get_contents("php://input");
$tn=tempnam(sys_get_temp_dir(), "whu");
error_log("tn=$tn");
$fh=fopen($tn, "w+");
fwrite($fh, $data);
$contentType = mime_content_type($fh);
fclose($fh);
error_log("WHU $contentType");


if ($contentType=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($tn);
unlink($tn);


$ws=$spreadsheet->getSheet(0);
$wd=$ws->toArray();
$i=0;
$rodb->query("BEGIN");
$stmt = $rodb->prepare(
    "INSERT INTO worker(member_id,assigner,requirement,description,workertype,season)
     SELECT id,?,?,'vintervedligehold',?,$workyear FROM Member WHERE Member.MemberId=?") or dbErr($rodb,$res,"arbejdstimer prep");

foreach($wd as $wr) {
    if ($i<1) {
        if (strtolower($wr[0]!="medlemsnummer")) {
                roErr("Den første kolonne skal være medlemsnummer, ikke: ".$wr[0]);
            }
        if (strtolower($wr[1]!="timer")) {
                roErr("Den anden kolonne skal være timer, ikke: ".$wr[1]);
            }
        $rodb->query("DELETE FROM worker WHERE season=$workyear AND (assigner='vedligehold' OR description='vintervedligehold')") || dnErr($rodb,$res,"clear workers");
    } else {
        $wt=null;
        if (count($wr)>2) {
            $wt=$wr[2];
        }
        $stmt->bind_param('idss', $cuser,$wr[1],$wt,$wr[0]) || dbErr($rodb,$res,"arbejdstimer");
        $stmt->execute() || dbErr($rodb,$res,"arbejdstimer");
    }
    $i++;
}
$rodb->query("COMMIT");

} else {
    roErr("unknown content type: $contentType");
}
invalidate("work");
