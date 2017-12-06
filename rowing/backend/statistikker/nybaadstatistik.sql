-- Bådstatistik for alle år
SELECT Båd.Navn AS Båd, Gruppe.Navn AS Bådtype, FORMAT(Sum(ROUND(Meter/100)/10),1) AS Rodistance, Count(Trip.TripID) AS Antal_ture
FROM (Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN Trip ON Båd.BådID = Trip.BoatID
GROUP BY Båd.Navn, Gruppe.Navn;

-- Bådstatistik for 2014
SELECT Båd.Navn AS Båd, Gruppe.Navn AS Bådtype, FORMAT(Sum(ROUND(Meter/100)/10),1) AS Rodistance, Count(Trip.TripID) AS Antal_ture
FROM (Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN Trip ON Båd.BådID = Trip.BoatID
WHERE YEAR(OutTime)='2014'
GROUP BY Båd.Navn, Gruppe.Navn;

-- Nye Kaniner
select * from MemberRights where MemberRight='rowright' AND Acquired>'2014';


SELECT MemberName, TurType.Navn as turtype, count('x') from TripMember,Trip,TurType, Medlem
WHERE Medlem.Medlemsnr=TripMember.MemberID AND Trip.TripTypeID=TurTypeID AND TripMember.TripID=Trip.TripID AND YEAR(Trip.OutTime)='2014' AND
  Medlem.MedlemID IN (SELECT  MemberID FROM MemberRights where MemberRight='rowright' AND Acquired>'2014')

GROUP By MemberName;


