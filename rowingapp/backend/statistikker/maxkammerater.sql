SELECT Member.MemberID,Concat(Member.FirstName," ",Member.LastName) as name,COUNT(distinct tmo.member_id) as nummates
FROM Member, Trip,TripMember tm, TripMember tmo
WHERE tm.TripID=Trip.id AND tm.member_id=Member.id AND tmo.TripID=Trip.id AND Member.id!=tmo.member_id 
GROUP By Member.id
ORDER BY nummates DESC
LIMIT 10;

SELECT Member.MemberID,Concat(Member.FirstName," ",Member.LastName) as name,COUNT(distinct tmo.member_id) as 2015nummates
FROM Member, Trip,TripMember tm, TripMember tmo
WHERE tm.TripID=Trip.id AND tm.member_id=Member.id AND tmo.TripID=Trip.id AND Member.id!=tmo.member_id AND YEAR(Trip.OutTime)="2015"
GROUP By Member.id
ORDER BY 2015nummates DESC 
LIMIT 10;

SELECT Member.MemberID,Concat(Member.FirstName," ",Member.LastName) as name,COUNT(distinct tmo.member_id) as 2015nummates
FROM Member, Trip,TripMember tm, TripMember tmo
WHERE tm.TripID=Trip.id AND tm.member_id=Member.id AND tmo.TripID=Trip.id AND Member.id!=tmo.member_id AND YEAR(Trip.OutTime)="2015" AND Trip.TripTypeID!=5
GROUP By Member.id
ORDER BY 2015nummates DESC 
LIMIT 10;
