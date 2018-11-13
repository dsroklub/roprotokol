SELECT "robåde", aar as "år",COUNT(id) plus100 FROM (
 SELECT YEAR(OutTime) as aar,id,SUM(Meter) as distance
   FROM TripMember,Trip
   WHERE
   TripMember.TripID=Trip.id AND
   Trip.BoatID IN (SELECT id FROM Boat,BoatType WHERE boat_type=BoatType.name AND BoatType.category=2)
   GROUP BY TripMember.member_id,aar
 ) as r
 WHERE r.distance>=100000
 GROUP by aar;

SELECT "inrigger",aar as "år",COUNT(id) plus100 FROM (
 SELECT YEAR(OutTime) as aar,id,SUM(Meter) as distance
   FROM TripMember,Trip
   WHERE
   TripMember.TripID=Trip.id AND
   Trip.BoatID IN (SELECT id FROM Boat WHERE boat_type LIKE "Inrigger%")
   GROUP BY TripMember.member_id,aar
 ) as r
 WHERE r.distance>=100000
 GROUP by aar;
