delete From Error_TripMember where member_id=0;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 1 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember0 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember0
GROUP BY Error_Trip.id;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 2 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember1 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember1
GROUP BY Error_Trip.id;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 3 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember2 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember2
GROUP BY Error_Trip.id;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 4 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember3 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember3
GROUP BY Error_Trip.id;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 5 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember4 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember4
GROUP BY Error_Trip.id;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 6 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember5 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember5
GROUP BY Error_Trip.id;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 7 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember6 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember6
GROUP BY Error_Trip.id;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 8 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember7 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember7
GROUP BY Error_Trip.id;

Insert INTO Error_TripMember (ErrorTripID, Seat, member_id)
SELECT Error_Trip.id as ErrorTripID, 9 as Seat, MAX(Member.id) as member_id 
FROM Error_Trip,Member WHERE TripMember8 is NOT NULL AND Concat(FirstName," ",LastName)=TripMember8
GROUP BY Error_Trip.id;


UPDATE Error_Trip SET TripTypeID = (SELECT id FROM TripType WHERE name=TripType);
