<?php
include("../../rowing/backend/inc/common.php");
require("utils.php");
$data = file_get_contents("php://input");
$tripId=json_decode($data);
$rodb->begin_transaction();

$stmt = $rodb->prepare("SELECT 'x' FROM Trip WHERE id=? AND InTime IS NULL") or dbErr($rodb,$res,"close trip check prep");
$stmt->bind_param('i', $tripId);
$stmt->execute() || dbErr($rodb,$res,"close trip check exe");
$result= $stmt->get_result();
if (!$result->fetch_assoc()) {
    error_log("trip $tripId already closed or not on water: ". json_encode($closedtrip,JSON_PRETTY_PRINT));
    dbErr($rodb,$res,"trip $tripId already closed or not on water");
}

$stmt = $rodb->prepare("SELECT 'x' FROM Trip,Member,TripMember WHERE Trip.id=? AND TripMember.member_id=Member.id AND TripMember.TripID=Trip.id AND Member.MemberID=?") or dbErr($rodb,$res,"close trip membercheck prep");
$stmt->bind_param('is', $tripId,$_SERVER['PHP_AUTH_USER']);
$stmt->execute() || dbErr($rodb,$res,"close trip membercheck exe");
$result= $stmt->get_result();
if (!$result->fetch_assoc()) {
    dbErr($rodb,$res,"rower not on trip $tripId");
}

$stmt = $rodb->prepare("UPDATE Trip SET InTime = NOW() WHERE id=?;") or dbErr($rodb,$res,"close trip prep");
$stmt->bind_param('i', $tripId) || dbErr($rodb,$res,"close trip $tripId bind");
$stmt->execute() || dbErr($rodb,$res,"close $tripId trip bind");
$rodb->commit();
echo json_encode($res);
