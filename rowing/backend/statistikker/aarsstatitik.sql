-- Tabel 1 - Indmeldelser, udmeldelser og medlemsomsætning
select count(*) FROM tblMembers where RemoveDate IS NULL or YEAR(RemoveDate)=2015;
select count(*) FROM tblMembers where YEAR(JoinDate)=2015;

select count(*) FROM tblMembers where YEAR(RemoveDate)=2015;


-- Tabel 2 - Nye medlemmer og kønsfordeling 1998-2015

select Sex as koen, count(*) as antal FROM tblMembers where YEAR(JoinDate)=2015 group by koen;

select avg(datediff(JoinDate, Birthdate)/365) as alder, Sex as koen FROM tblMembers where YEAR(JoinDate)=2015 group by koen;

-- Tabel 3 - Nye medlemmer og deres frafald

select count(*) as antal, YEAR(JoinDate) as aargang FROM tblMembers where  YEAR(JoinDate) >= 2004 and RemoveDate IS NULL group by aargang order by aargang desc;

select count(*) FROM tblMembers where  YEAR(JoinDate)=2015 and RemoveDate < '2015-11-10';


select count(*) FROM tblMembers where  YEAR(JoinDate)=2004 and RemoveDate IS NOT NULL AND RemoveDate <= '2006-01-01';


-- Opdater ture fra Kanin-numre til rigtige medlemsnumre

UPDATE TripMember, Member a
SET TripMember.member_id = IFNULL(
     (SELECT MAX(id) FROM Member b
      WHERE b.FirstName = a.FirstName
        AND b.LastName = a.LastName
        AND b.MemberID > 0
     ), a.id)
WHERE TripMember.Season = 2015
  AND TripMember.member_id = a.id
  AND a.MemberID = 0;




-- Tabel 4 - Samlet aktivitetsniveau
select sum(Trip.Meter)/1000 as km, BoatType.Category as bådkat  FROM Trip  INNER JOIN Boat ON (Boat.id = Trip.BoatID) INNER JOIN BoatType ON (BoatType.id = Boat.BoatType) where Trip.season='2015' GROUP BY BoatType.Category;


-- Tabel 5 - Bådture og personture, robåd og kajak
select count(distinct Trip.TripID) as baadture, COUNT(TripMember.member_id) as personture, ROUND(COUNT(TripMember.member_id)/COUNT(distinct Trip.TripID), 1) as personer_pr_tur, TripType.Name as turtype, BoatType.Category as bådkat  FROM Trip JOIN TripMember ON (TripMember.TripID = Trip.TripID) INNER JOIN TripType on Trip.TripTypeID = TripType.id INNER JOIN Boat ON (Boat.id = Trip.BoatID) INNER JOIN BoatType ON (BoatType.id = Boat.BoatType) where Trip.season='2015' GROUP BY BoatType.Category, TripType.id order by bådkat, turtype;



-- Tabel 6 - Aktivitetsniveau 2014 og 2015 opdelt på turtyper (robåde)
select TripType.Name as turtype, sum(Trip.Meter)/1000 as Afstand, count(Trip.TripID) as baadture, sum(Trip.Meter)/1000/count(Trip.TripID) as km_pr_tur FROM Trip INNER JOIN TripType on Trip.TripTypeID = TripType.id INNER JOIN Boat ON (Boat.id = Trip.BoatID) INNER JOIN BoatType ON (BoatType.id = Boat.BoatType) where Trip.season='2015' AND BoatType.Category = 2  GROUP BY TripType.id order by turtype;


-- Tabel 7 - Aktivitetsprofil for medlemmerne
select count(distinct TripMember.member_id) from Trip INNER JOIN TripMember ON (Trip.id = TripMember.TripID) WHERE Trip.Season = 2015;

select floor(meh.km/100), count(meh.medlemsnr) as antal, count(meh.medlemsnr)/702*100 as andel FROM (select TripMember.member_id as medlemsnr, sum(Trip.Meter)/1000 as km from Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID WHERE Trip.Season=2015 GROUP BY medlemsnr) as meh group by floor(meh.km/100);


-- Tabel 8 - Aktive instruktører 2015
select TripMember.member_id as medlemsnr, TripMember.MemberName as navn, COUNT(TripMember.member_id)  as ture FROM Trip  JOIN TripMember ON (TripMember.TripID = Trip.id) INNER JOIN Boat ON (Boat.id = Trip.BoatID) where Trip.season='2015' AND Boat.BoatType IN (1, 2) AND TripMember.Seat=0 AND Trip.TripTypeID IN (5) GROUP BY TripMember.member_id order by ture desc, navn asc;


-- Tabel 9 - Udmeldte kaniner efter aktivitet
select count(*)  from tblMembers where Year(JoinDate) = 2015;

select count(*)  from tblMembers where Year(JoinDate) = 2015 and RemoveDate IS NOT NULL;

select count(*)
  from tblMembers
       INNER JOIN Member ON (Member.MemberID = tblMembers.MemberID)
       INNER JOIN (
           select 
                  TripMember.member_id as member_id,
                  COUNT(TripMember.member_id) as ture
            FROM Trip
               INNER JOIN TripMember ON (TripMember.TripID = Trip.TripID)
               INNER JOIN Boat ON (Boat.id = Trip.BoatID)
               INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
          WHERE Trip.season='2015' AND BoatType.Category=2
                AND Trip.TripTypeID = 5
          GROUP BY member_id
          HAVING COUNT(TripMember.member_id) >= 1
        ) t
       ON (t.member_id = Member.id)
  WHERE YEAR(tblMembers.JoinDate) = 2015;



select count(*)
  from tblMembers
       INNER JOIN Member ON (Member.MemberID = tblMembers.MemberID)
       INNER JOIN (
           select 
                  TripMember.member_id as MemberID,
                  COUNT(TripMember.member_id) as ture
            FROM Trip
               INNER JOIN TripMember ON (TripMember.TripID = Trip.TripID)
               INNER JOIN Boat ON (Boat.id = Trip.BoatID)
               INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
          WHERE Trip.season='2015' AND BoatType.Category=2
                AND Trip.TripTypeID = 8
          GROUP BY MemberID
          HAVING COUNT(TripMember.member_id) >= 3
        ) t
       ON (t.MemberID = Member.id)
  WHERE YEAR(tblMembers.JoinDate) = 2015
  AND tblMembers.RemoveDate IS NOT NULL
  AND tblMembers.RemoveDate <= '2016-01-01';



select count(*)
  from tblMembers
       INNER JOIN Member ON (Member.MemberID = tblMembers.MemberID)
       INNER JOIN MemberRights ON (Member.MemberID = MemberRights.MemberID)
  WHERE
       MemberRights.MemberRight = 'rowright'
   AND YEAR(tblMembers.JoinDate) = 2015
   AND YEAR(MemberRight.Acquired) = 2015;


select count(*)
  from tblMembers
       INNER JOIN Member ON (Member.MemberID = tblMembers.MemberID)
       INNER JOIN MemberRights ON (Member.MemberID = MemberRights.MemberID)
  WHERE
       MemberRights.MemberRight = 'rowright'
   AND YEAR(tblMembers.JoinDate) = 2015
   AND YEAR(MemberRights.Acquired) = 2015
   AND tblMembers.RemoveDate IS NOT NULL;




select count(*)
  from tblMembers
       INNER JOIN Member ON (Member.MemberID = tblMembers.MemberID)
       INNER JOIN (
           SELECT 
                  TripMember.member_id as MemberID,
                  COUNT(TripMember.member_id) as ture
            FROM Trip
               INNER JOIN TripMember ON (TripMember.TripID = Trip.TripID)
               INNER JOIN Boat ON (Boat.id = Trip.BoatID)
               INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
          WHERE Trip.season='2015' AND BoatType.Category=2
                AND Trip.TripTypeID <> 5
          GROUP BY MemberID
          HAVING COUNT(TripMember.member_id) >= 3
        ) t
       ON (t.MemberID = Member.id)
  WHERE YEAR(tblMembers.JoinDate) = 2015;




-- Tabel 10 - Kaniners aktivitet efter roret

select TripType.Name as turtype,
       COUNT(distinct TripMember.member_id)  as forskellige,
       COUNT(TripMember.member_id)  as ture,
       count(TripMember.member_id)/count(distinct TripMember.member_id) as ture_pr_person
FROM Trip 
JOIN TripMember ON (TripMember.TripID = Trip.TripID)
INNER JOIN Boat ON (Boat.id = Trip.BoatID)
INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
INNER JOIN TripType on (Trip.TripTypeID = TripType.id)
INNER JOIN Member ON (Member.id = TripMember.member_id)
where Trip.Season='2015'
  AND BoatType.Category=2
  AND YEAR(Member.Created) = 2015
  AND Trip.TripTypeID NOT IN (5)
GROUP BY turtype
order by turtype;


select TripType.Name as turtype,
       COUNT(distinct TripMember.member_id)  as forskellige
FROM Trip 
JOIN TripMember ON (TripMember.TripID = Trip.TripID)
INNER JOIN Boat ON (Boat.id = Trip.BoatID)
INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
INNER JOIN TripType on (Trip.TripTypeID = TripType.id)
INNER JOIN Member ON (Member.id = TripMember.member_id)
LEFT JOIN tblMembers ON (Member.MemberID = tblMembers.MemberID)
where Trip.Season='2015'
  AND BoatType.Category=2
  AND YEAR(Member.Created) = 2015
  AND Trip.TripTypeID NOT IN (5)
  AND tblMembers.RemoveDate IS NOT NULL
  AND tblMembers.RemoveDate <= '2016-01-01'
GROUP BY turtype
order by turtype;



-- Tabel 11 - Kaniners aktivitet efter roret – mere end 2 ture

select TripType.Name as turtype,
       COUNT(distinct t.member_id) as forskellige,
       sum(t.ture)  as ture,
       sum(t.ture)/count(distinct t.member_id) as ture_pr_person
  FROM (select Trip.TripTypeID as TripTypeID,
               TripMember.member_id as member_id,
               COUNT(TripMember.member_id) as ture
          FROM Trip
               JOIN TripMember ON (TripMember.TripID = Trip.TripID)
               INNER JOIN Boat ON (Boat.id = Trip.BoatID)
               INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
          WHERE Trip.season='2015' AND BoatType.Category=2
          GROUP BY TripTypeID, member_id
          HAVING COUNT(TripMember.member_id) > 2
        ) t
       INNER JOIN TripType on (t.TripTypeID = TripType.id)
       INNER JOIN Member ON (Member.id = t.member_id)
  WHERE YEAR(Member.Created) = 2015 AND t.TripTypeID NOT IN (5)
  GROUP BY turtype order by turtype;



-- Tabel 12 - Frafald blandt medlemmer - efter aktivitet

SELECT TripType.Name as turtype,
       COUNT(t.member_id) as individer
FROM TripType
JOIN ( SELECT Trip.TripTypeID as TripTypeID,
              TripMember.member_id as member_id
       FROM Trip
       INNER JOIN TripMember ON (TripMember.TripID = Trip.TripID)
       INNER JOIN Boat ON (Boat.id = Trip.BoatID)
       INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
       WHERE Trip.Season = '2014'
         AND BoatType.Category=2
       GROUP BY Trip.TripTypeID, TripMember.member_id
       HAVING COUNT(TripMember.member_id) >= 3
    ) t ON (t.TripTypeID = TripType.id)
INNER JOIN Member ON (Member.id = t.member_id)
LEFT JOIN tblMembers ON (Member.MemberID = tblMembers.MemberID) 
WHERE tblMembers.RemoveDate IS NOT NULL
  AND tblMembers.RemoveDate <= '2016-01-01'
GROUP BY turtype
order by turtype;






-- Tabel 13 - Både efter turtype og samlet kilometertal
-- Tabel 14-16 bruger samme udtræk

SELECT Boat.Name AS Boat,
       BoatType.Name AS Bådtype,
       FORMAT(Sum(ROUND(Meter/100)/10),1) AS Rodistance,
       Count(Trip.TripID) AS Antal_ture,
       TripType.Name AS turtype
  FROM BoatType
       INNER JOIN Boat ON (BoatType.id = Boat.BoatType)
       LEFT OUTER JOIN Trip ON Boat.id = Trip.BoatID
       LEFT OUTER JOIN TripType ON (TripType.id = Trip.TripTypeID)
       WHERE Season='2015'
   GROUP BY Boat.Name, Boat.id, BoatType.Name, turtype

INTO OUTFILE '/tmp/baadstatistik.csv'
FIELDS  TERMINATED BY '\t'
LINES TERMINATED BY '\r\n';


-- Tabel 16 - Ikke roede både

SELECT Boat.Name AS Båd,
       BoatType.Name AS Bådtype
  FROM BoatType
       INNER JOIN Boat ON (BoatType.id = Boat.BoatType)
  WHERE Boat.id NOT IN (
            select Trip.BoatID
            FROM Trip
            WHERE Season='2015'
        )
   ORDER BY BoatType.Name, Boat.Name;




-- Kaniner aldrig til instruktion


SELECT tblMembers.MemberID as Medlemsnummer, CONCAT(tblMembers.FirstName, ' ', tblMembers.LastName) as Navn
  FROM tblMembers
  INNER JOIN Member ON (Member.MemberID = tblMembers.MemberID)
  WHERE YEAR(tblMembers.JoinDate) = 2015
    AND tblMembers.RemoveDate IS  NULL
    AND Member.id NOT IN (
    SELECT DISTINCT TripMember.member_id
      FROM Trip
      INNER JOIN TripMember ON (TripMember.TripID = Trip.TripID)
      INNER JOIN Boat ON (Boat.id = Trip.BoatID)
      INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
      WHERE Trip.season='2015'
        AND BoatType.Category=2
        AND Trip.TripTypeID = 5
    )
  ORDER BY Navn;



