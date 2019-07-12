<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$forum=sanestring($_REQUEST['forum']);
$filename=sanestring($_REQUEST['file']);
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$s="SELECT mime_type as mt,filename,file
    FROM forum_file, Member, forum_subscription
    WHERE
    Member.MemberID=? AND forum_subscription.member=Member.id AND forum_subscription.forum=forum_file.forum AND
    forum_file.forum=? AND filename=? AND expire>NOW()";

if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("sss", $cuser,$forum,$filename);
    $stmt->execute();
    $result= $stmt->get_result() or die("Error in file query: " . mysqli_error($rodb));
    if ($result) {
        $file=$result->fetch_assoc();
        error_log("RES $forum, $filename:  ". print_r($file,true));
        header("Content-length: ".strlen($file['file']));
        header("Content-type: ".$file['mt']);
//        header("Content-Disposition: attachement; filename=\"".$file["filename"]."\"");
        header("Content-Disposition: inline; filename=\"".$file["filename"]."\"");
        echo $file['file'];
    } else {
        error_log("File not fould $filename");
    }
} else {
    dbErr($rodb,$res,"forum file");
}
