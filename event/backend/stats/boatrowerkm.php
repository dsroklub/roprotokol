<?php
set_include_path(get_include_path().':..');
include("../../../rowing/backend/inc/common.php");
include("inc/utils.php");
$vr=verify_right(["admin"=>null,"data"=>"stat"]);

$s = "SELECT Baad as båd, rn as rang, MedlemsNr, Navn, km
      FROM (
        SELECT Baad,
               BaadID,
               MedlemsNr,
               Navn,
               km,
        @rn := IF(@prev = BaadID, @rn +1, 1) as rn,
               @prev := BaadID as prev
        FROM (
          SELECT b.Name as Baad,
                 m.MemberID as MedlemsNr,
                 CONCAT(m.FirstName, ' ', m.LastName) as Navn,
                 ROUND(SUM(t.Meter)/1000) as km,
                 b.id as BaadID
          FROM TripMember tm
        JOIN Trip t ON (t.id = tm.TripID)
        JOIN Boat b ON (b.id = t.BoatID)
        JOIN Member m ON (m.id = tm.member_id)
        WHERE YEAR(t.OutTime)=YEAR(NOW())
              GROUP BY b.id, tm.member_id
                ORDER BY b.Name ASC, b.id ASC, km DESC
           ) as kml
JOIN (SELECT @prev := NULL, @rn := 0) as vars
      ) as stat
      WHERE rn <= 10
      ORDER BY Baad, BaadID, rn";

$result=$rodb->query($s) or dbErr($rodb,$res,"10aktive");


process($result,"xlsx","boatrowerkm","_auto");
