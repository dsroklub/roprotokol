<?php
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'Off');
$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

function invalidate($tp) {
    $mem  = new Memcached();
    $mem->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
    $mem->addServer('127.0.0.1',11211);
    $mem->increment($tp, 1, time());
}


$target_dir = "uploads/";
// $target_file = $target_dir . "members";
$target_file = $target_dir . preg_replace('/[^a-zA-Z0-9-_\.]/','',basename($_FILES["fileToUpload"]["name"]));
$uploadOk = 1;
$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
echo ("uploader: " .$fileType." file: ".$target_file);
error_log("uploading type: " .$fileType." file: ". $_FILES["fileToUpload"]["size"]);

if ($_FILES["fileToUpload"]["size"] > 7000000) {
    echo "<br>Filen er for stor";
    $uploadOk = 0;
}
if ($_FILES["fileToUpload"]["size"] < 100000) {
    echo "<br>Filen er for lille: ". $_FILES["fileToUpload"]["size"]." bytes\n";
    $uploadOk = 0;
}

if($fileType != "mdb" && $fileType != "accdb") {
    echo "<br>Det skal være en mdb eller en accdb file, ikke: ". $fileType. "<br>";
    $uploadOk = 0;
}


if (!$uploadOk) {
    echo "Filen kunne ikke uploades";
    exit(0);
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "<br> filen ". basename( $_FILES["fileToUpload"]["name"]). " blev uploaded.";
    } else {
        echo "Filen blev ikke uploaded.";
        exit(0);
    }

    $updateOk = 1;

    $extract="mdb-export -D '%F %T' -I mysql ". $target_file ." tblMembersToRoprotokol > uploads/tblMembers.sql";
#    echo "<br> extract <pre>".$extract."</pre><br>";
    echo "<br> pakker medlemsdata ud</pre><br>";
    system($extract) && die (" member tbl extract failed");
    echo "<br> Importerer til databasen<br>";
    $pw="";
    if ($config["dbpassword"]) {
        $pw=" -p --password=".$config["dbpassword"]." ";
    }
    $sl="mysql -f --default-character-set=utf8 -u ".$config["dbuser"] ." ".$pw . $config["database"]." < uploads/tblMembers.sql > /tmp/mlog 2>&1";
    error_log("load members:".$sl);
    system($sl) && die (" member sql inport");;

    echo "<br>Opdaterer roprotokollen<br>";

    $s="
UPDATE Member m JOIN tblMembersToRoprotokol tm ON (m.MemberID = tm.MemberID)
SET m.FirstName = tm.FirstName,
    m.LastName = tm.LastName,
    m.Email = tm.E_mail,
    m.ShowEmail = tm.OnAddressList,
    m.JoinDate = tm.JoinDate,
    m.RemoveDate = tm.RemoveDate,
    m.Birthday = tm.Birthdate,
    m.Gender = CASE tm.Sex WHEN 'm' THEN 0 WHEN 'f' THEN 1 ELSE NULL END;
";

    error_log("SQL :\n".$s."\n");
    if ($stmt = $rodb->prepare($s)) {
        if (!$stmt->execute()) {
            error_log("SQL stmt error: ".$rodb->error);
            die($rodb->error);
        }
    } else {
        error_log("SQL stmt error: ".$rodb->error);
        echo " FEJL i opdatering: ".$rodb->error;
        $updateOk = 0;
    }

    echo "<br>Indsætter eventuelle nye medlemmer i roprotokollen<br>";

    $s="
INSERT INTO Member ( MemberID, LastName, FirstName,JoinDate,RemoveDate, Email, ShowEmail, Birthday, KommuneKode,CprNo,Gender )
  SELECT DISTINCTROW 
  tMem.MemberID AS mid,
  tMem.LastName,
  tMem.FirstName,
  tMem.JoinDate,
  tMem.RemoveDate,
  tMem.E_mail,
  tMem.OnAddressList,
  tMem.Birthdate,
  tMem.KommuneKode,
  tMem.CprNo,
  CASE tMem.Sex WHEN 'm' THEN 0 WHEN 'f' THEN 1 ELSE NULL END
  FROM tblMembersToRoprotokol tMem
  WHERE (((tMem.RemoveDate) IS NULL) AND MemberID NOT IN (SELECT MemberID From Member))
  ORDER BY mid;
";
    error_log("SQL :\n".$s."\n");
    if ($stmt = $rodb->prepare($s)) { 
        $stmt->execute() || die($rodb->error);
    }  else {
        error_log("SQL stmt error: ".$rodb->error);
        echo " FEJL i indsættelse: ".$rodb->error;
        $updateOk = 0;
    }



    if ($stmt = $rodb->prepare('
UPDATE Member,tblMembersToRoprotokol
SET
Member.KommuneKode=tblMembersToRoprotokol.KommuneKode,
Member.CprNo=tblMembersToRoprotokol.CprNo
    WHERE tblMembersToRoprotokol.MemberID=Member.MemberID;
')){ 
        $stmt->execute() || die($rodb->error);
    }  else {
        error_log("SQL kommunecpr error: ".$rodb->error);
        echo " FEJL i kommune/cpr upload ".$rodb->error;
    }
    
    if ($stmt = $rodb->prepare("DELETE FROM tblMembersToRoprotokol")) { 
        $stmt->execute() || die($rodb->error);
    }  else {
        error_log("SQL stmt error: ".$rodb->error);
        echo " FEJL i oprydning: ".$rodb->error;
        $updateOk = 0;
    }
    invalidate('member');
    if ($updateOk) {
       echo "\n<h2>Medlemmer blev importeret</h2>\n";
    } else {
       echo "\n<h1>Der var fejl i opdateringen!!!</h1>\n<h2>Medlemmer blev <u>ikke</u> opdateret korrekt!</h2>\n";
    }
}

?>

<?php

