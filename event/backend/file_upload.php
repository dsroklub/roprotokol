<?php
include("../../rowing/backend/inc/common.php");

$res=array ("status" => "ok");
$data = file_get_contents("php://input");
error_log("file data=".$data);
//error_log("POST=". print_r($_POST,true));

$forum=$_POST["forum"];
$filename=$_POST["filename"];
$expire=explode(".",$_POST["expire"])[0];
$file=$_POST["file"];

$file=$_FILES['file'];

$finfo = new finfo(FILEINFO_MIME_TYPE);


$ofilename=$file['name']['file'];
$tmpfilename=$file['tmp_name']['file'];
$otype=$file['type']['file'];

error_log("FORUM=$forum, filename=$filename, exp=$expire, ofilename=$ofilename, tmp_file=$tmpfilename,type=$otype");
$fileup=json_decode($data);
$message='';
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

$fp      = fopen($tmpfilename, 'r');
$content = fread($fp, filesize($tmpfilename));


$mimeType=$finfo->buffer($content);
fclose($fp);
if ($stmt = $rodb->prepare(
        "INSERT INTO forum_file(member_from,created,forum,filename,mime_type,file,expire)
         SELECT Member.id, NOW(), ?, ?,?,?, CONVERT_TZ(?,'+00:00','SYSTEM')
         FROM Member
         WHERE 
           MemberId=?
         ")) {

    $triptype="NULL";
    $stmt->bind_param(
        'ssssss',
        $forum,
        $filename,
        $mimeType,
        $content,
        $expire,
        $cuser) ||  die("forum file BIND errro ".mysqli_error($rodb));

    if (!$stmt->execute()) {
        $error=" event forumfileexe error ".mysqli_error($rodb);
        error_log($error);
        $message=$message."\n"."forumfile upload error: ".mysqli_error($rodb);
    } else {
        error_log($rodb->error);
    } 
} else {
    error_log($rodb->error);
}

if ($error) {
    error_log($error);
    $res['message']=$message;
    $res['status']='error';
    $res['error']=$error;
}
invalidate("event");
invalidate("message");
echo json_encode($res);
?> 
