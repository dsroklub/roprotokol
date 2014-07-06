drop VIEW Rostat_BaadstatistikMain2010;
drop VIEW Rostat_BaadstatistikMain2011;
drop VIEW Rostat_BaadstatistikMain2012;
drop VIEW Rostat_BaadstatistikMain2013;

CREATE VIEW Rostat_BaadstatistikMain2013 AS
SELECT 2013, Båd.Navn AS Båd, Gruppe.Navn AS Bådtype, Sum(ROUND(Meter/100)/10) AS Rodistance, Count(Tur.TurID) AS Antal_ture
FROM (Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN Tur ON Båd.BådID = Tur.FK_BådID
GROUP BY Båd.Navn, Gruppe.Navn;

CREATE VIEW Rostat_BaadstatistikMain2010 AS
SELECT 2010, Båd.Navn AS Båd, Gruppe.Navn AS Bådtype, Sum(ROUND(Meter/100)/10) AS Rodistance, Count(Tur_backup2010.TurID) AS Antal_ture
FROM (Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN Tur_backup2010 ON Båd.BådID = Tur_backup2010.FK_BådID
GROUP BY Båd.Navn, Gruppe.Navn;

CREATE VIEW Rostat_BaadstatistikMain2011 AS
SELECT 2011, Båd.Navn AS Båd, Gruppe.Navn AS Bådtype, Sum(ROUND(Meter/100)/10) AS Rodistance, Count(Tur_backup2011.TurID) AS Antal_ture
FROM (Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN Tur_backup2011 ON Båd.BådID = Tur_backup2011.FK_BådID
GROUP BY Båd.Navn, Gruppe.Navn;

CREATE VIEW Rostat_BaadstatistikMain2012 AS
SELECT 2012, Båd.Navn AS Båd, Gruppe.Navn AS Bådtype, Sum(ROUND(Meter/100)/10) AS Rodistance, Count(Tur_backup2012.TurID) AS Antal_ture
FROM (Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN Tur_backup2012 ON Båd.BådID = Tur_backup2012.FK_BådID
GROUP BY Båd.Navn, Gruppe.Navn;


Create View baadstat AS SELECT
Rostat_BaadstatistikMain2010 UNION
Rostat_BaadstatistikMain2011 UNION
Rostat_BaadstatistikMain2012 UNION
Rostat_BaadstatistikMain2013; 

