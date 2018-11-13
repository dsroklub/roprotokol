SELECT YEAR(OutTime) aar, SUM(Meter)/1000 km, FirstName, Lastname
FROM Trip,TripMember,Member
WHERE Member.id=TripMember.member_id AND
TripMember.TripID=Trip.id AND
Trip.BoatID IN (SELECT id FROM Boat WHERE boat_type LIKE "Inrigger%")
Group BY Member.id,aar
HAVING aar>2014 AND km>=100
ORDER by aar,km;
