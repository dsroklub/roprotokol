<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$forum=$_REQUEST['forum'];
$filename=$_REQUEST['file'];

$s="SELECT mime_type as mt,filename,file FROM forum_file WHERE forum=? AND filename=?";

if ($stmt = $rodb->prepare($s)) {
    $stmt->bind_param("ss", $forum,$filename);
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

