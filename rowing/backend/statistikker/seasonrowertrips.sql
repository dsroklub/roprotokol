SELECT FirstName,LastName, Trip.OutTime, Trip.InTime,TIMEDIFF(Trip.InTime,Trip.OutTime) as tid,  Trip.Destination, Meter/1000 AS Km,TripType.Name AS turtype, Boat.Name,Trip.id as tur
FROM Trip,TripType,Member,TripMember,Boat
WHERE YEAR(Trip.outTime) = YEAR(NOW()) AND
TripType.id=Trip.TripTypeID AND
TripMember.TripID=Trip.id AND
Boat.id=BoatID AND
Member.id=TripMember.member_id
ORDER BY FirstName,LastName
;
