
DELIMITER //
create procedure RedKey2_GiveAllRedKey(in aar int)  begin  
UPDATE Member LEFT JOIN Vintervedligehold ON Member.id = Vintervedligehold.Medlemsnr SET Vintervedligehold.HasRedKey = True, Vintervedligehold.Season = aar; end//

CREATE PROCEDURE SLET_poster_fra_TurDeltager_hvor_turen_er_slettet()
begin
DELETE TripMember.*
FROM TripMember LEFT JOIN Tur ON TripMember.TripID = Trip.TripID
WHERE (((Trip.TripID) Is Null));
end//


-- CREATE PROCEDURE RedKey1_AddMissingMembersToVintervedligehold()
-- BEGIN
--   INSERT INTO Vintervedligehold ( Medlemsnr )
--   SELECT Medlem.Medlemsnr
--   FROM Medlem LEFT JOIN Vintervedligehold ON Medlem.Medlemsnr = Vintervedligehold.Medlemsnr
--   WHERE (((Vintervedligehold.Id) Is Null))
--   GROUP BY Medlem.Medlemsnr;
-- END//


CREATE PROCEDURE Antal_ture()
BEGIN
  SELECT Trip.TripID, Trip.Meter, TripMember.MemberID, TripMember.Name, TripType.Name
  FROM TripType INNER JOIN (Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID) ON TripType.id = Trip.TripTypeID;
END//


DELIMITER ;

-- CREATE PROCEDURE Baad_without_matching_kajakker
-- BEGIN
-- SELECT Båd.Name
-- FROM Båd LEFT JOIN kajakker ON Båd.Name = [kajakker].[Kajakker]
-- WHERE ([kajakker].[Kajakker] Is Null);
-- END//

-- CREATE PROCEDURE
-- BEGIN
-- END//


-- CREATE VIEW qBoatsReserveret AS
-- SELECT Båd.Name, Reservation.Start, Reservation.Slut, Reservation.BådID, Reservation.Beskrivelse
-- FROM Båd INNER JOIN Reservation ON Båd.BådID = Reservation.BådID
-- WHERE (((Reservation.Start)<=Now()) AND ((Reservation.Slut)>=Now()))
-- ORDER BY Båd.Name;

-- CREATE VIEW qBoatsSkadet AS
-- SELECT Båd.Name, Skade.BådID, Max(Skade.Grad) AS grad
-- FROM Båd INNER JOIN Skade ON Båd.BådID = Skade.BådID
-- WHERE (((Skade.Repareret) Is Null))
-- GROUP BY Båd.Name, Skade.BådID
-- ORDER BY Båd.Name;


-- FORMAT(Ud,"dd"". ""mmm hh:nn")
CREATE VIEW qBoatsOnWater AS
SELECT Boat.Name AS Boat, DATE_FORMAT(Ud,"%d %b %H:%i") AS Trip_start, DATE_FORMAT(forvind,"%d %b %H:%i") AS Forventet_inde, Trip.Destination, Trip.TripID, TripType.Name AS Triptype
FROM TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.BoatID = Trip.BoatID) ON TripType.TripTypeID = Trip.TripTypeID
WHERE (((Trip.Ind) Is Null))
ORDER BY forvind;

CREATE VIEW qBoatsOnWater2 AS
SELECT Boat.Name as baad_navn, Trip.Ud, Trip.ForvInd, Trip.Ind, Trip.Destination, Trip.BoatID, Trip.TripID, TripType.Name as TripType_Name
FROM TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.BoatID = Trip.BoatID) ON TripType.TripTypeID = Trip.TripTypeID
WHERE (((Trip.Ind) Is Null))
ORDER BY Trip.ForvInd;

CREATE VIEW qBoatsOnWater3 AS
SELECT Boat.Name as Baad_Name, TripMember.Name AS Styrmand, Trip.Ud, Trip.ForvInd, Trip.Ind, Trip.Destination, Trip.BoatID, Trip.TripID, TripType.Name as TripType_Name
FROM (TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.BoatID = Trip.BoatID) ON TripType.TripTypeID = Trip.TripTypeID) LEFT JOIN TripMember ON Trip.TripID = TripMember.TripID
WHERE (((TripMember.Plads)=0) AND ((Trip.Ind) Is Null))
ORDER BY Trip.ForvInd;

CREATE VIEW qBoatsOnWaterRoere AS
SELECT Boat.Name as Baad_Name, TripMember.Name AS roer, Trip.Ud, Trip.ForvInd, Trip.Ind, Trip.Destination, Trip.BoatID, Trip.TripID, TripType.Name as TripType_Name
FROM (TripType RIGHT JOIN (Boat RIGHT JOIN Trip ON Boat.BoatID = Trip.BoatID) ON TripType.TripTypeID = Trip.TripTypeID) LEFT JOIN TripMember ON Trip.TripID = TripMember.TripID
WHERE  Trip.Ind Is Null
ORDER BY Trip.ForvInd,TripID;


CREATE VIEW qAvailableboats AS
       SELECT Boat.BoatID, Boat.Name, Boat.GruppeID, Boat.Pladser, qBoatsReserveret.BoatID as reserved_baadID, qBoatsOnWater2.BoatID as onWater_baadID, qBoatsSkadet.BoatID as skade_baadID, qBoatsSkadet.grad, LockedBoats.locktimeout, qBoatsOnWater2.TripType_Name AS TripType_navn
       FROM ((qBoatsReserveret RIGHT JOIN (qBoatsSkadet RIGHT JOIN Boat ON qBoatsSkadet.BoatID = Boat.BoatID) ON qBoatsReserveret.BoatID = Boat.BoatID) LEFT JOIN LockedBoats ON Boat.BoatID = LockedBoats.BoatID) LEFT JOIN qBoatsOnWater2 ON Boat.BoatID = qBoatsOnWater2.BoatID;



CREATE VIEW qBoatsSkader AS
SELECT Skade.BoatID AS BoatID, Boat.Name, 
       CONCAT(Medlemsnr," ",fornavn," ",efternavn) AS Skademelder, 
       DATE_FORMAT(Ødelagt,"%d %b") AS Dato, Skade.Grad, Skade.Beskrivelse, Skade.SkadeID
FROM Boat INNER JOIN (Skade LEFT JOIN Medlem ON Skade.Ansvarlig = Medlem.MedlemID) ON Boat.BoatID = Skade.BoatID
WHERE (((Skade.Repareret) Is Null))
ORDER BY Boat.Name;


CREATE VIEW Active_Mp_og_8gp AS
       	    SELECT TripMember.Name as TripMember_Name, TripType.Name as TripType_Name
	    FROM TripType INNER JOIN (Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID) ON TripType.TripTypeID = Trip.TripTypeID
	    WHERE (((TripType.Name)="8gp")) OR (((TripType.Name) Like "*Motion+tur*"))
	    GROUP BY TripMember.Name, TripType.Name;





CREATE VIEW AlleTurRettelser AS
SELECT Medlem.Medlemsnr, TripMember.TripID, Trip.Destination AS Dest, Trip.OprettetDato, Fejl_tur.FejlID, Fejl_tur.SletTur, Fejl_tur.TripID, Fejl_tur.Boat, Fejl_tur.Ud, Fejl_tur.Ind, Fejl_tur.Destination, Fejl_tur.Distance, Fejl_tur.TripType, Fejl_tur.TurDeltager0, Fejl_tur.TurDeltager1, Fejl_tur.TurDeltager2, Fejl_tur.TurDeltager3, Fejl_tur.TurDeltager4, Fejl_tur.TurDeltager5, Fejl_tur.TurDeltager6, Fejl_tur.TurDeltager7, Fejl_tur.TurDeltager8, Fejl_tur.TurDeltager9, Fejl_tur.Årsagtilrettelsen, Fejl_tur.Indberetter, Fejl_tur.Fixed_comment
FROM Fejl_tur INNER JOIN (Trip INNER JOIN (Medlem INNER JOIN TripMember ON Medlem.MedlemID = TripMember.MedlemID) ON Trip.TripID = TripMember.TripID) ON Fejl_tur.TripID = Trip.TripID
GROUP BY Medlem.Medlemsnr, TripMember.TripID, Trip.Destination, Trip.OprettetDato, Fejl_tur.FejlID, Fejl_tur.SletTrip, Fejl_tur.TripID, Fejl_tur.Boat, Fejl_tur.Ud, Fejl_tur.Ind, Fejl_tur.Destination, Fejl_tur.Distance, Fejl_tur.TripType, Fejl_tur.TripMember0, Fejl_tur.TripMember1, Fejl_tur.TripMember2, Fejl_tur.TripMember3, Fejl_tur.TripMember4, Fejl_tur.TripMember5, Fejl_tur.TripMember6, Fejl_tur.TripMember7, Fejl_tur.TripMember8, Fejl_tur.TripMember9, Fejl_tur.Årsagtilrettelsen, Fejl_tur.Indberetter, Fejl_tur.Fixed_comment;


CREATE VIEW FindFejlture_1 AS
SELECT Medlem.Medlemsnr, Medlem.MedlemID, Ud AS Tripdato, Count(Ud) AS TripdatoC
FROM Medlem INNER JOIN (Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID) ON Medlem.MedlemID = TripMember.MedlemID
GROUP BY Medlem.Medlemsnr, Medlem.MedlemID, Ud
HAVING (((Count(Ud))>1))
ORDER BY Ud;

CREATE VIEW FindFejlture_2 AS
SELECT Trip.TripID, Ud AS Tripdato, TripMember.MedlemID
FROM Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID;

CREATE VIEW FindFejlture_3 AS
SELECT Medlem.Medlemsnr, FindFejlture_1.Tripdato, FindFejlture_2.TripID, Trip.Meter
FROM ((FindFejlture_1 INNER JOIN Medlem ON FindFejlture_1.MedlemID = Medlem.MedlemID) INNER JOIN FindFejlture_2 ON (FindFejlture_1.Tripdato = FindFejlture_2.Tripdato) AND (FindFejlture_1.MedlemID = FindFejlture_2.MedlemID)) INNER JOIN Trip ON FindFejlture_2.TripID = Trip.TripID
WHERE (((Trip.Meter)<1000));


CREATE VIEW RetTrip_subquery AS
SELECT Trip.TripID, Trip.BoatID, Trip.Ud, Trip.Ind, Trip.ForvInd, Trip.Destination, Trip.Meter, Trip.TripTypeID, Trip.Kommentar, Trip.OprettetDato, Trip.RedigeretDato, Trip.Initialer, Trip.DESTID
FROM Trip;

CREATE VIEW RetTripMembere AS
SELECT TripMember.MedlemID AS MedlemID, Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, Medlem.Adresse, Medlem.Postnr, Medlem.Telefon1, Medlem.Telefon2, Medlem.Fødselsdag, Medlem.Password, Medlem.Aktiv, Medlem.Rettigheder, Medlem.OprettetDato as Medlem_Oprettelsedato, Medlem.RedigeretDato AS Medlem_RedirigeretDato, Medlem.Initialer AS Medlem_Initialer, TripMember.*
FROM Medlem INNER JOIN TripMember ON Medlem.MedlemID = TripMember.MedlemID
ORDER BY TripMember.TripID, TripMember.Plads;


CREATE VIEW RetTripMembere_subquery AS
SELECT TripMember.Plads, Medlem.Medlemsnr, TripMember.MedlemID, TripMember.Name
FROM (Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID) INNER JOIN Medlem ON TripMember.MedlemID = Medlem.MedlemID;

CREATE VIEW Rostat_BeregnRodistance AS
SELECT Medlem.MedlemID, ROUND(Sum(Meter/1000)) AS Rodistance, Count(Trip.TripID) AS Antal_ture
FROM Trip INNER JOIN (Medlem INNER JOIN TripMember ON Medlem.MedlemID = TripMember.MedlemID) ON Trip.TripID = TripMember.TripID
GROUP BY Medlem.MedlemID
HAVING (((ROUND(Sum(Meter/1000)))>0))
ORDER BY Sum(Meter/1000);

CREATE VIEW Rostat_BeregnRodistance_kajak AS
SELECT Medlem.MedlemID, Sum(Meter/1000) AS Rodistance, Count(Trip.TripID) AS Antal_ture
FROM Boat RIGHT JOIN (Trip INNER JOIN (Medlem INNER JOIN TripMember ON Medlem.MedlemID = TripMember.MedlemID) ON Trip.TripID = TripMember.TripID) ON Boat.BoatID = Trip.BoatID
WHERE (((Boat.GruppeID)=4 Or (Boat.GruppeID)=5))
GROUP BY Medlem.MedlemID
ORDER BY Sum(Meter/1000);


CREATE VIEW Rostat_BeregnRodistance_robaad AS
SELECT Medlem.MedlemID, Sum(Meter/1000) AS Rodistance, Count(Trip.TripID) AS Antal_ture
FROM Boat RIGHT JOIN (Trip INNER JOIN (Medlem INNER JOIN TripMember ON Medlem.MedlemID = TripMember.MedlemID) ON Trip.TripID = TripMember.TripID) ON Boat.BoatID = Trip.BoatID
WHERE (((Boat.GruppeID)<>4 And (Boat.GruppeID)<>5))
GROUP BY Medlem.MedlemID
ORDER BY Sum(Meter/1000);


CREATE VIEW Rostat_Rangorden AS
SELECT Rostat_BeregnRodistance.MedlemID, Medlem.Medlemsnr AS Medlemsnr, CONCAT(fornavn," ",efternavn) AS Name, Rostat_BeregnRodistance.Rodistance, Rostat_BeregnRodistance.Antal_ture, ROUND(Rodistance/Antal_ture*10)/10 AS Gennemsnitslaengde
FROM Rostat_BeregnRodistance INNER JOIN Medlem ON Rostat_BeregnRodistance.MedlemID = Medlem.MedlemID;


CREATE VIEW Rostat_Rangorden_kajak AS
SELECT Rostat_BeregnRodistance_kajak.MedlemID, medlemsnr AS Medlemsnr_, CONCAT(fornavn," ",efternavn) AS Name, Rostat_BeregnRodistance_kajak.Rodistance, Rostat_BeregnRodistance_kajak.Antal_ture, ROUND(Rodistance/Antal_ture*10)/10 AS Gennemsnitslaengde
FROM Rostat_BeregnRodistance_kajak INNER JOIN Medlem ON Rostat_BeregnRodistance_kajak.MedlemID = Medlem.MedlemID;

CREATE VIEW Rostat_Rangorden_robaad AS
SELECT Rostat_BeregnRodistance_robaad.MedlemID, medlemsnr AS Medlemsnr_, CONCAT(fornavn," ",efternavn) AS Name, Rostat_BeregnRodistance_robaad.Rodistance, Rostat_BeregnRodistance_robaad.Antal_ture, ROUND(Rodistance/Antal_ture*10)/10 AS Gennemsnitslaengde
FROM Rostat_BeregnRodistance_robaad INNER JOIN Medlem ON Rostat_BeregnRodistance_robaad.MedlemID = Medlem.MedlemID;

CREATE VIEW Skadeadmin AS
SELECT Boat.Name, Skade.BoatID, Skade.Ansvarlig, Skade.Ødelagt, Skade.Reperatør, Skade.Grad, Skade.Repareret, Skade.Beskrivelse, Skade.OprettetDato, Skade.RedigeretDato, Skade.Initialer
FROM Boat INNER JOIN Skade ON Boat.BoatID = Skade.BoatID;

CREATE VIEW Skademeld_MedlemID_og_Name AS
SELECT Medlem.MedlemID, Medlem.Medlemsnr, CONCAT(fornavn," ",efternavn) AS Name
FROM Medlem
ORDER BY CONCAT(fornavn," ",efternavn);

CREATE VIEW Trip_og_ugenr AS
SELECT Trip.TripID, WEEK(Ud) AS Uge
FROM Trip
GROUP BY Trip.TripID, WEEK(Ud);

CREATE VIEW qTripMembere AS
SELECT Medlem.MedlemID, Medlem.Medlemsnr, fornavn,efternavn, Adresse, Postnr, Aktiv, Rettigheder, Medlem.Initialer, TripID, Plads, Name
FROM Medlem INNER JOIN TripMember ON Medlem.MedlemID = TripMember.MedlemID
ORDER BY TripMember.TripID, TripMember.Plads;


CREATE VIEW Rostat_BaadstatistikMain AS
SELECT Boat.Name AS Boat, Gruppe.Name AS Boattype, Sum(ROUND(Meter/100)/10) AS Rodistance, Count(Trip.TripID) AS Antal_ture
FROM (Gruppe INNER JOIN Boat ON Gruppe.GruppeID = Boat.GruppeID) LEFT JOIN Trip ON Boat.BoatID = Trip.BoatID
GROUP BY Boat.Name, Gruppe.Name;

CREATE VIEW Rostat_KunTripeMedStyrmand as
SELECT Trip.TripID, TripMember.Plads, Trip.BoatID, Trip.TripTypeID, Trip.Meter
FROM Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID
WHERE (((TripMember.Plads)=0));


CREATE VIEW Kaniner AS
SELECT Medlem.MedlemID
FROM (((Medlem INNER JOIN TripMember ON Medlem.MedlemID = TripMember.MedlemID) INNER JOIN Trip ON TripMember.TripID = Trip.TripID) INNER JOIN TripType ON Trip.TripTypeID = TripType.TripTypeID) INNER JOIN tblMembers ON Medlem.Medlemsnr = tblMembers.MemberID
WHERE (((TripType.Name)="Instruktion") AND ((Year(JoinDate))=YEAR(now())))
GROUP BY Medlem.MedlemID;


CREATE VIEW  Racerkaniner AS
SELECT Medlem.MedlemID
FROM (((Medlem INNER JOIN TripMember ON Medlem.MedlemID = TripMember.MedlemID) INNER JOIN Trip ON TripMember.TripID = Trip.TripID) INNER JOIN TripType ON Trip.TripTypeID = TripType.TripTypeID) INNER JOIN tblMembers ON Medlem.Medlemsnr = tblMembers.MemberID
WHERE (((TripType.Name)="Racerkanin") AND ((Year(JoinDate))=year(now())))
GROUP BY Medlem.MedlemID;


CREATE VIEW Rostat_Kaniner AS
SELECT Rostat_Rangorden.MedlemID, Rostat_Rangorden.Medlemsnr, Rostat_Rangorden.Name, Rostat_Rangorden.Rodistance, Rostat_Rangorden.Antal_ture, Rostat_Rangorden.Gennemsnitslaengde
FROM Kaniner INNER JOIN Rostat_Rangorden ON Kaniner.MedlemID = Rostat_Rangorden.MedlemID;

CREATE VIEW DagensRoere AS
SELECT Trip.TripID, Boat.Name AS Boat, CONCAT(Medlemsnr, " - ", TripMember.Name) AS Roer, Trip.Destination, DATE_FORMAT(Ud,"%T") AS Udtid, DATE_FORMAT(Ind,"%T") AS Indtid, DATE_FORMAT(ForvInd,"%T") AS Forv_inde
FROM (Boat RIGHT JOIN (Trip INNER JOIN TripMember ON Trip.TripID = TripMember.TripID) ON Boat.BoatID = Trip.BoatID) LEFT JOIN Medlem ON TripMember.MedlemID = Medlem.MedlemID
WHERE ud BETWEEN date(now()) AND date(now())+1
ORDER BY Trip.TripID, DATE_FORMAT(Ind,"%T") DESC;

CREATE VIEW alle_der_har_roet_p100_robaad AS
SELECT Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, tblMembers.Address1, tblMembers.Address2, tblMembers.Postnr, tblMembers.City, tblMembers.Telephone1, tblMembers.Telephone2, Sum(Trip.Meter) AS SumOfMeter
FROM (Gruppe RIGHT JOIN (Boat RIGHT JOIN (Medlem LEFT JOIN (Trip RIGHT JOIN TripMember ON Trip.TripID = TripMember.TripID) ON Medlem.MedlemID = TripMember.MedlemID) ON Boat.BoatID = Trip.BoatID) ON Gruppe.GruppeID = Boat.GruppeID) LEFT JOIN tblMembers ON Medlem.Medlemsnr = tblMembers.MemberID
WHERE (((Gruppe.Name) Not Like "*kajak*"))
GROUP BY Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, tblMembers.Address1, tblMembers.Address2, tblMembers.Postnr, tblMembers.City, tblMembers.Telephone1, tblMembers.Telephone2
HAVING (((Sum(Trip.Meter))>100000));

CREATE VIEW RovagtAndet AS
SELECT Boat.Name, Gruppe.Name AS Boattype, if(Ud>0,"Ja","Nej") AS Paa_vandet, qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja") AS Tilgaengelig
FROM (((Gruppe RIGHT JOIN Boat ON Gruppe.GruppeID = Boat.GruppeID) LEFT JOIN BoatKategori ON Gruppe.BoatKategoriID = BoatKategori.BoatKategoriID) LEFT JOIN qBoatsOnWater3 ON Boat.BoatID = qBoatsOnWater3.BoatID) LEFT JOIN qBoatsSkadet ON Boat.BoatID = qBoatsSkadet.BoatID
WHERE (((Gruppe.GruppeNr)>2) AND ((BoatKategori.BoatKategoriID)=2))
ORDER BY Boat.Name, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja");

CREATE VIEW RovagtAndetPrintervenlig AS
SELECT Boat.Name, Gruppe.GruppeNr, Gruppe.Name AS Boattype, IF(Ud>0,"Ja","Nej") AS Paa_vandet, qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja") AS Tilgaengelig
FROM (((Gruppe RIGHT JOIN Boat ON Gruppe.GruppeID = Boat.GruppeID) LEFT JOIN BoatKategori ON Gruppe.BoatKategoriID = BoatKategori.BoatKategoriID) LEFT JOIN qBoatsOnWater3 ON Boat.BoatID = qBoatsOnWater3.BoatID) LEFT JOIN qBoatsSkadet ON Boat.BoatID = qBoatsSkadet.BoatID
WHERE (((Gruppe.GruppeNr)<>6 And (Gruppe.GruppeNr)<>7 And (Gruppe.GruppeNr)<>12 And (Gruppe.GruppeNr)>2) AND ((BoatKategori.BoatKategoriID)=2))
ORDER BY Boat.Name, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja");


-- XXXXXXXXXXXXXXX
/* CREATE VIEW Medlemsrettigheder AS
-- SportsData Missing
SELECT tblMembersSportData.MemberID, tblMembersSportData.Motorboat, tblMembersSportData.Roret, tblMembersSportData.TeoretiskStyrmandKursus, tblMembersSportData.Styrmand, tblMembersSportData.Langtur, tblMembersSportData.Skaergaard, tblMembersSportData.Langtur_Oeresund, tblMembersSportData.Ormen, tblMembersSportData.Svava, tblMembersSportData.Sculler, tblMembersSportData.Kajak, tblMembersSportData.Kajak_2, tblMembersSportData.RoInstruktoer, tblMembersSportData.StyrmandInstruktoer, tblMembersSportData.ScullerInstruktoer, tblMembersSportData.KajakInstruktoer, tblMembersSportData.Kaproer, tblMembersSportData.KeyType, tblMembersSportData.KeyDate, tblMembersSportData.KeyFee, tblMembersSportData.Stilling, tblMembersSportData.Ordinaert, tblMembersSportData.diverse1, tblMembersSportData.diverse2
FROM tblMembersSportData; */

CREATE VIEW QRYRovagtInr2 AS
SELECT Boat.Name, IF(Ud>0,"Ja","Nej") AS Paa_vandet, qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja") AS Tilgaengelig
FROM (((Gruppe RIGHT JOIN Boat ON Gruppe.GruppeID = Boat.GruppeID) LEFT JOIN BoatKategori ON Gruppe.BoatKategoriID = BoatKategori.BoatKategoriID) LEFT JOIN qBoatsOnWater3 ON Boat.BoatID = qBoatsOnWater3.BoatID) LEFT JOIN qBoatsSkadet ON Boat.BoatID = qBoatsSkadet.BoatID
WHERE (((Gruppe.Name)="Inrigger 2+") AND ((BoatKategori.BoatKategoriID)=2))
ORDER BY Boat.Name, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja");


CREATE VIEW QRYRovagtInr4 AS
SELECT Boat.Name, If(Ud>0,"Ja","Nej") AS Paa_vandet, qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja") AS Tilgaengelig
FROM (((Gruppe RIGHT JOIN Boat ON Gruppe.GruppeID = Boat.GruppeID) LEFT JOIN BoatKategori ON Gruppe.BoatKategoriID = BoatKategori.BoatKategoriID) LEFT JOIN qBoatsOnWater3 ON Boat.BoatID = qBoatsOnWater3.BoatID) LEFT JOIN qBoatsSkadet ON Boat.BoatID = qBoatsSkadet.BoatID
WHERE (((Gruppe.Name)="Inrigger 4+") AND ((BoatKategori.BoatKategoriID)=2))
ORDER BY Boat.Name, IF(Grad>2 OR ud>0 OR MotionPlus=1,"Nej","Ja");



