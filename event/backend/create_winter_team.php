<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");

$data = file_get_contents("php://input");
$in=json_decode($data);

$boatName=$in->boat->name;
$forumName="$boatName vedligehold";
$forumDescription="Vintervedligehold af $boatName ";

$stmt = $rodb->prepare("INSERT INTO forum (forumtype,boat,name,description,is_open,is_public,owner,created_by) 
                        SELECT 'maintenance',?,?,?,TRUE,TRUE,Member.id,Member.id FROM Member,dsrvinter.baadformand,dsrvinter.baad
                        WHERE baad.id=baadformand.baad AND baad.navn=? AND CAST(baadformand.formand AS CHAR)=Member.MemberID LIMIT 1") or dbErr($rodb,$res,"vinterteam");
$stmt->bind_param("ssss",$boatName,$forumName,$forumDescription,$boatName)  || dbErr($rodb,$res,"vinterteam bind");
$stmt->execute() || dbErr($rodb,$res,"vinterteam exe");

$stmt = $rodb->prepare("INSERT INTO forum_subscription(member,forum,role)  
   SELECT Member.id, ?,IF(baadformand.formand IS NULL,'member','owner') FROM dsrvinter.baad,dsrvinter.person LEFT JOIN dsrvinter.baadformand ON baadformand.formand=person.ID,Member WHERE Member.MemberID=CAST(person.ID AS CHAR) AND person.baad=dsrvinter.baad.id AND baad.navn=?")  or dbErr($rodb,$res,"vinterteam members");
$stmt->bind_param("ss",$forumName,$boatName)  or dbErr($rodb,$res,"vinterteam mem bind");
$stmt->execute() || dbErr($rodb,$res,"vinterteam memb exe");

invalidate("fora");
echo json_encode($res);

