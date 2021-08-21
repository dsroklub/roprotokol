<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
include("inc/utils.php");
$vr=verify_right("admin");
$s="SELECT CONCAT(FirstName,' ',LastName) AS navn, MemberID AS medlemsnummer, TripType.Name as turtype,IFNULL(starting_place,'') as fra, Destination as til, ROUND(Meter/1000,2) as km, WEEK(OutTime) as uge,DATE_FORMAT(OutTime,'%Y-%m-%d %H:%i') as ud, DATE_FORMAT(InTime,'%Y-%m-%d %H:%i') as ind, Comment as kommentar
FROM Member,Trip,TripMember,TripType
WHERE
  TripType.id=Trip.TripTypeID AND
  YEAR(Trip.OutTime)=YEAR(NOW()) AND
  TripMember.member_id=Member.id AND
  Trip.id=TripMember.TripID AND
  EXISTS (SELECT 'x' FROM Trip t, TripMember tm, TripType tt WHERE t.TripTypeID=tt.id AND tm.member_id=Member.id AND tm.TripID=t.id AND (tt.Name='Inriggerkaproning' OR tt.Name='Coastalkaproning') AND YEAR(t.OutTime)=YEAR(NOW()))
  ORDER BY navn,ud";


$result=$rodb->query($s) or dbErr($rodb,$res,"inka stat");
$output='xlsx';
process($result,$output,"inkaroning","_auto");
$rodb->close();
