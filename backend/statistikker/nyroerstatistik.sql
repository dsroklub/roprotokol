-- Nye Kaniner
-- select * from MemberRights where MemberRight='rowright' AND Acquired>'2014';


SELECT Medlem.Medlemsnr,MemberName, TurType.Navn as turtype, count('x') as antal from TripMember,Trip,TurType, Medlem
WHERE Medlem.MedlemID=TripMember.MemberID AND Trip.TripTypeID=TurTypeID AND TripMember.TripID=Trip.TripID AND Trip.Season='2014' AND
Medlem.Medlemsnr IN (SELECT  MemberID FROM MemberRights where MemberRight='rowright' AND Acquired>'2014') GROUP BY Medlem.Medlemsnr;

SELECT TurType.Navn as turtype, count('x') as antal from TripMember,Trip,TurType, Medlem
WHERE Medlem.MedlemID=TripMember.MemberID AND Trip.TripTypeID=TurTypeID AND TripMember.TripID=Trip.TripID AND Trip.Season='2014' AND
Medlem.Medlemsnr IN (SELECT  MemberID FROM MemberRights where MemberRight='rowright' AND Acquired>'2014') GROUP BY turtype ;


