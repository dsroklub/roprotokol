-- Tabel 1 - Indmeldelser, udmeldelser og medlemsomsætning
select count(*) FROM tblMembers where RemoveDate IS NULL or YEAR(RemoveDate)=2014;
select count(*) FROM tblMembers where YEAR(JoinDate)=2014;

select count(*) FROM tblMembers where YEAR(RemoveDate)=2014;


-- Tabel 2 - Nye medlemmer og kønsfordeling 1998-2014

select Sex as koen, count(*) as antal FROM tblMembers where YEAR(JoinDate)=2014 group by koen;

select avg(datediff(JoinDate, Birthdate)/365) as alder, Sex as koen FROM tblMembers where YEAR(JoinDate)=2014 group by koen;

-- Tabel 3 - Nye medlemmer og deres frafald



select count(*) FROM tblMembers where  YEAR(JoinDate)=2013 and RemoveDate IS NOT NULL;

select count(*) FROM tblMembers where  YEAR(JoinDate)=2014 and RemoveDate IS NOT NULL;

select count(*) FROM tblMembers where  YEAR(JoinDate)=2014 and RemoveDate < '2014-11-10';



-- Tabel 4 - Samlet aktivitetsniveau
select sum(Trip.Meter)/1000 as km, Gruppe.FK_BådKategoriID as bådkat  FROM Trip  INNER JOIN Båd ON (Båd.BådID = Trip.BoatID) INNER JOIN Gruppe ON (Gruppe.GruppeID = Båd.FK_GruppeID) where Trip.season='2014' GROUP BY Gruppe.FK_BådKategoriID;

-- Tabel 5 - Bådture og personture, robåd og kajak
select count(distinct Trip.id) as baadture, COUNT(TripMember.MemberID) as personture, TurType.Navn as turtype, Gruppe.FK_BådKategoriID as bådkat  FROM Trip JOIN TripMember ON (TripMember.TripID = Trip.id) INNER JOIN TurType on Trip.TripTypeID = TurType.TurTypeID INNER JOIN Båd ON (Båd.BådID = Trip.BoatID) INNER JOIN Gruppe ON (Gruppe.GruppeID = Båd.FK_GruppeID) where Trip.season='2014' GROUP BY Gruppe.FK_BådKategoriID, TurType.TurTypeID order by bådkat, turtype;


-- Tabel 6 - Aktivitetsniveau 2013 og 2014 opdelt på turtyper (robåde)
select TurType.Navn as turtype, sum(Trip.Meter)/1000 as Afstand, count(Trip.id) as baadture, sum(Trip.Meter)/1000/count(Trip.id) as km_pr_tur FROM Trip INNER JOIN TurType on Trip.TripTypeID = TurType.TurTypeID INNER JOIN Båd ON (Båd.BådID = Trip.BoatID) INNER JOIN Gruppe ON (Gruppe.GruppeID = Båd.FK_GruppeID) where Trip.season='2014' AND Gruppe.FK_BådKategoriID = 2  GROUP BY TurType.TurTypeID order by turtype;


-- Tabel 7 - Aktivitetsprofil for medlemmerne
select floor(meh.km/100), count(meh.medlemsnr) as antal, count(meh.medlemsnr)/673*100 as andel FROM (select TripMember.MemberID as medlemsnr, sum(Trip.Meter)/1000 as km from Trip INNER JOIN TripMember ON Trip.id = TripMember.TripID WHERE Trip.Season=2014 GROUP BY medlemsnr) as meh group by floor(meh.km/100);


-- Tabel 8 - Aktive instruktører 2015
select TripMember.member_id as medlemsnr, TripMember.MemberName as navn, COUNT(TripMember.member_id)  as ture FROM Trip  JOIN TripMember ON (TripMember.TripID = Trip.id) INNER JOIN Boat ON (Boat.id = Trip.BoatID) where Trip.season='2015' AND Boat.BoatType IN (1, 2) AND TripMember.Seat=0 AND Trip.TripTypeID IN (5) GROUP BY TripMember.member_id order by ture desc, navn asc;


-- Tabel 9 - Kaniners aktivitet efter roret
select TurType.Navn as turtype, COUNT(distinct TripMember.MemberID)  as forskellige, COUNT(TripMember.MemberID)  as ture, count(TripMember.MemberID)/count(distinct TripMember.MemberID) as ture_pr_person FROM Trip  JOIN TripMember ON (TripMember.TripID = Trip.id) INNER JOIN Båd ON (Båd.BådID = Trip.BoatID) INNER JOIN Gruppe ON (Gruppe.GruppeID = Båd.FK_GruppeID) INNER JOIN TurType on (Trip.TripTypeID = TurType.TurTypeID) INNER JOIN Medlem ON (Medlem.MedlemID = TripMember.MemberID) where Trip.season='2014' AND Gruppe.FK_BådKategoriID=2 AND YEAR(Medlem.OprettetDato) = 2014 AND Trip.TripTypeID NOT IN (5) GROUP BY turtype order by turtype;



-- Tabel 10 - Kaniners aktivitet efter roret – mere end 2 ture

select TurType.Navn as turtype,
       COUNT(distinct t.MemberID)  as forskellige,
       sum(t.ture)  as ture,
       sum(t.ture)/count(distinct t.MemberID) as ture_pr_person
  FROM (select Trip.TripTypeID as TripTypeID,
               TripMember.MemberID as MemberID,
               COUNT(TripMember.MemberID) as ture
          FROM Trip
               JOIN TripMember ON (TripMember.TripID = Trip.id)
               INNER JOIN Båd ON (Båd.BådID = Trip.BoatID)
               INNER JOIN Gruppe ON (Gruppe.GruppeID = Båd.FK_GruppeID)
          WHERE Trip.season='2014' AND Gruppe.FK_BådKategoriID=2
          GROUP BY TripTypeID, MemberID
          HAVING COUNT(TripMember.MemberID) > 2
        ) t
       INNER JOIN TurType on (t.TripTypeID = TurType.TurTypeID)
       INNER JOIN Medlem ON (Medlem.MedlemID = t.MemberID)
  WHERE YEAR(Medlem.OprettetDato) = 2014 AND t.TripTypeID NOT IN (5)
  GROUP BY turtype order by turtype;



-- Tabel 11 - Udmeldte kaniner efter aktivitet

select count(*)  from tblMembers where Year(JoinDate) = 2014;

select count(*)  from tblMembers where Year(JoinDate) = 2014 and RemoveDate IS NOT NULL;


select count(*)
  from tblMembers
       INNER JOIN Medlem ON (Medlem.Medlemsnr = tblMembers.MemberID)
       INNER JOIN (
           select 
                  TripMember.MemberID as MemberID,
                  COUNT(TripMember.MemberID) as ture
            FROM Trip
               INNER JOIN TripMember ON (TripMember.TripID = Trip.id)
               INNER JOIN Båd ON (Båd.BådID = Trip.BoatID)
               INNER JOIN Gruppe ON (Gruppe.GruppeID = Båd.FK_GruppeID)
          WHERE Trip.season='2014' AND Gruppe.FK_BådKategoriID=2
                AND Trip.TripTypeID = 5
          GROUP BY MemberID
          HAVING COUNT(TripMember.MemberID) >= 3
        ) t
       ON (t.MemberID = Medlem.MedlemID)
  WHERE YEAR(tblMembers.JoinDate) = 2014;


select count(*)
  from tblMembers
       INNER JOIN Medlem ON (Medlem.Medlemsnr = tblMembers.MemberID)
       INNER JOIN MemberRights ON (Medlem.Medlemsnr = MemberRights.MemberID)
  WHERE
       MemberRights.MemberRight = 'rowright'
   AND YEAR(tblMembers.JoinDate) = 2014;






-- Tabel 12 - Både efter turtype og samlet kilometertal
-- Tabel 13-15 bruger samme udtræk

SELECT Båd.Navn AS Båd,
       Gruppe.Navn AS Bådtype,
       FORMAT(Sum(ROUND(Meter/100)/10),1) AS Rodistance,
       Count(Trip.id) AS Antal_ture,
       TurType.Navn AS turtype
  FROM Gruppe
       INNER JOIN Båd ON (Gruppe.GruppeID = Båd.FK_GruppeID)
       LEFT OUTER JOIN Trip ON Båd.BådID = Trip.BoatID
       LEFT OUTER JOIN TurType ON (TurType.TurTypeID = Trip.TripTypeID)
       WHERE Season='2014'
   GROUP BY Båd.Navn, Gruppe.Navn, turtype;


-- Tabel 16 - Ikke roede både

SELECT Båd.Navn AS Båd,
       Gruppe.Navn AS Bådtype
  FROM Gruppe
       INNER JOIN Båd ON (Gruppe.GruppeID = Båd.FK_GruppeID)
  WHERE Båd.BådID NOT IN (
            select Trip.BoatID
            FROM Trip
            WHERE Season='2014'
        )
   ORDER BY Gruppe.Navn, Båd.Navn;


