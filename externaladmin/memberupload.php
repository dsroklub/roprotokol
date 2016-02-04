<?php
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'Off');
$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
// FIXME rm? $rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

function invalidate($tp) {
    $mem  = new Memcached();
    $mem->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
    $mem->addServer('127.0.0.1',11211);
    $mem->increment($tp, 1, time());
}


$target_dir = "uploads/";
// $target_file = $target_dir . "members";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
echo ("uploader: " .$fileType." file: ".$target_file);
error_log("uploading type: " .$fileType." file: ". $_FILES["fileToUpload"]["size"]);

if ($_FILES["fileToUpload"]["size"] > 7000000) {
    echo "<br>File en for stor";
    $uploadOk = 0;
}
if ($_FILES["fileToUpload"]["size"] < 100000) {
    echo "<br>Filen en for lille: ". $_FILES["fileToUpload"]["size"]."\n";
    $uploadOk = 0;
}

$fileType = pathinfo($_FILES["fileToUpload"]["tmp_name"],PATHINFO_EXTENSION);
if($fileType != "mdb" && $fileType != "accdb") {
    echo "<br>Det skal være en mdb eller en accdb file, ikke: ". $fileType. "<br>";
    $uploadOk = 0;
}



if ($uploadOk == 02) {
    echo "Filen kunne ikke uploades";
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "<br> filen ". basename( $_FILES["fileToUpload"]["name"]). " blev uploaded.";
    } else {
        echo "Filen blev ikke uploaded.";
    }

    $extract="mdb-export -D '%F %T' -I mysql ". $target_file ." tblMembers > uploads/tblMembers.sql";
    echo "<br> extract <pre>".$extract."</pre><br>";
    system($extract) && die (" member tbl extract failed");

    
    $import="mysql < uploads/tblMembers.sql";

    $pw="";
    if ($config["dbpassword"]) {
        $pw=" -p --passwd=".$config["dbpassword"]." ";
    }
    $sl="mysql -f -u ".$config["dbuser"] ." ".$pw . $config["database"]."  < uploads/tblMembers.sql";
    error_log("load :".$sl);
    system($sl);

// ((tblMembers.JoinJournalDate)>?))

    $s="
INSERT INTO Member ( MemberID, LastName, FirstName )
SELECT DISTINCTROW tblMembers.MemberID, tblMembers.LastName, tblMembers.FirstName
FROM tblMembers
WHERE (((tblMembers.RemoveDate) Is Null) AND MemberID NOT IN (SELECT MemberID From Member))
ORDER BY tblMembers.MemberID;
";
    if ($stmt = $rodb->prepare($s)) { 
        $stmt->execute() || die($rodb->error);;
    } 
    invalidate('member');
}

?>

<?php

