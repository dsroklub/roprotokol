
DELIMITER //
create procedure RedKey2_GiveAllRedKey(in aar int)  begin  
UPDATE Medlem LEFT JOIN Vintervedligehold ON Medlem.Medlemsnr = Vintervedligehold.Medlemsnr SET Vintervedligehold.HasRedKey = True, Vintervedligehold.Season = aar; end//

CREATE PROCEDURE SLET_poster_fra_TurDeltager_hvor_turen_er_slettet()
begin
DELETE TurDeltager.*
FROM TurDeltager LEFT JOIN Tur ON TurDeltager.FK_TurID = Tur.TurID
WHERE (((Tur.TurID) Is Null));
end//


CREATE PROCEDURE RedKey1_AddMissingMembersToVintervedligehold()
BEGIN
  INSERT INTO Vintervedligehold ( Medlemsnr )
  SELECT Medlem.Medlemsnr
  FROM Medlem LEFT JOIN Vintervedligehold ON Medlem.Medlemsnr = Vintervedligehold.Medlemsnr
  WHERE (((Vintervedligehold.Id) Is Null))
  GROUP BY Medlem.Medlemsnr;
END//


CREATE PROCEDURE Antal_ture()
BEGIN
  SELECT Tur.TurID, Tur.Meter, TurDeltager.FK_MedlemID, TurDeltager.Navn, TurType.Navn
  FROM TurType INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON TurType.TurTypeID = Tur.FK_TurTypeID;
END//

--CREATE PROCEDURE Baad_without_matching_kajakker
--BEGIN
--SELECT B�d.Navn
--FROM B�d LEFT JOIN kajakker ON B�d.Navn = [kajakker].[Kajakker]
--WHERE ([kajakker].[Kajakker] Is Null);
--END//

--CREATE PROCEDURE
--BEGIN
--END//

DELIMITER ;


CREATE VIEW qBoatsReserveret AS
SELECT B�d.Navn, Reservation.Start, Reservation.Slut, Reservation.FK_B�dID, Reservation.Beskrivelse
FROM B�d INNER JOIN Reservation ON B�d.B�dID = Reservation.FK_B�dID
WHERE (((Reservation.Start)<=Now()) AND ((Reservation.Slut)>=Now()))
ORDER BY B�d.Navn;

CREATE VIEW qBoatsSkadet AS
SELECT B�d.Navn, Skade.FK_B�dID, Max(Skade.Grad) AS grad
FROM B�d INNER JOIN Skade ON B�d.B�dID = Skade.FK_B�dID
WHERE (((Skade.Repareret) Is Null))
GROUP BY B�d.Navn, Skade.FK_B�dID
ORDER BY B�d.Navn;


-- FORMAT(Ud,"dd"". ""mmm hh:nn")
CREATE VIEW qBoatsOnWater AS
SELECT B�d.Navn AS B�d, DATE_FORMAT(Ud,"%d %b %H:%i") AS Tur_start, DATE_FORMAT(forvind,"%d %b %H:%i") AS Forventet_inde, Tur.Destination, Tur.TurID, TurType.Navn AS Turtype
FROM TurType RIGHT JOIN (B�d RIGHT JOIN Tur ON B�d.B�dID = Tur.FK_B�dID) ON TurType.TurTypeID = Tur.FK_TurTypeID
WHERE (((Tur.Ind) Is Null))
ORDER BY forvind;

CREATE VIEW qBoatsOnWater2 AS
SELECT B�d.Navn as baad_navn, Tur.Ud, Tur.ForvInd, Tur.Ind, Tur.Destination, Tur.FK_B�dID, Tur.TurID, TurType.Navn as TurType_Navn
FROM TurType RIGHT JOIN (B�d RIGHT JOIN Tur ON B�d.B�dID = Tur.FK_B�dID) ON TurType.TurTypeID = Tur.FK_TurTypeID
WHERE (((Tur.Ind) Is Null))
ORDER BY Tur.ForvInd;

CREATE VIEW qBoatsOnWater3 AS
SELECT B�d.Navn as Baad_Navn, TurDeltager.Navn AS Styrmand, Tur.Ud, Tur.ForvInd, Tur.Ind, Tur.Destination, Tur.FK_B�dID, Tur.TurID, TurType.Navn as TurType_Navn
FROM (TurType RIGHT JOIN (B�d RIGHT JOIN Tur ON B�d.B�dID = Tur.FK_B�dID) ON TurType.TurTypeID = Tur.FK_TurTypeID) LEFT JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID
WHERE (((TurDeltager.Plads)=0) AND ((Tur.Ind) Is Null))
ORDER BY Tur.ForvInd;


CREATE VIEW qAvailableboats AS
       SELECT B�d.B�dID, B�d.Navn, B�d.FK_GruppeID, B�d.Pladser, qBoatsReserveret.FK_B�dID as reserved_baadID, qBoatsOnWater2.FK_B�dID as onWater_baadID, qBoatsSkadet.FK_B�dID as skade_baadID, qBoatsSkadet.grad, L�steB�de.locktimeout, qBoatsOnWater2.TurType_Navn AS TurType_navn
       FROM ((qBoatsReserveret RIGHT JOIN (qBoatsSkadet RIGHT JOIN B�d ON qBoatsSkadet.FK_B�dID = B�d.B�dID) ON qBoatsReserveret.FK_B�dID = B�d.B�dID) LEFT JOIN L�steB�de ON B�d.B�dID = L�steB�de.BoatID) LEFT JOIN qBoatsOnWater2 ON B�d.B�dID = qBoatsOnWater2.FK_B�dID;



CREATE VIEW qBoatsSkader AS
SELECT Skade.FK_B�dID AS BoatID, B�d.Navn, 
       CONCAT(Medlemsnr," ",fornavn," ",efternavn) AS Skademelder, 
       DATE_FORMAT(�delagt,"%d %b") AS Dato, Skade.Grad, Skade.Beskrivelse, Skade.SkadeID
FROM B�d INNER JOIN (Skade LEFT JOIN Medlem ON Skade.FK_Ansvarlig = Medlem.MedlemID) ON B�d.B�dID = Skade.FK_B�dID
WHERE (((Skade.Repareret) Is Null))
ORDER BY B�d.Navn;


CREATE VIEW Active_Mp_og_8gp AS
       	    SELECT TurDeltager.Navn as TurDeltager_Navn, TurType.Navn as TurType_Navn
	    FROM TurType INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON TurType.TurTypeID = Tur.FK_TurTypeID
	    WHERE (((TurType.Navn)="8gp")) OR (((TurType.Navn) Like "*Motion+tur*"))
	    GROUP BY TurDeltager.Navn, TurType.Navn;





CREATE VIEW AlleTurRettelser AS
SELECT Medlem.Medlemsnr, TurDeltager.FK_TurID, Tur.Destination AS Dest, Tur.OprettetDato, Fejl_tur.FejlID, Fejl_tur.SletTur, Fejl_tur.TurID, Fejl_tur.B�d, Fejl_tur.Ud, Fejl_tur.Ind, Fejl_tur.Destination, Fejl_tur.Distance, Fejl_tur.TurType, Fejl_tur.TurDeltager0, Fejl_tur.TurDeltager1, Fejl_tur.TurDeltager2, Fejl_tur.TurDeltager3, Fejl_tur.TurDeltager4, Fejl_tur.TurDeltager5, Fejl_tur.TurDeltager6, Fejl_tur.TurDeltager7, Fejl_tur.TurDeltager8, Fejl_tur.TurDeltager9, Fejl_tur.�rsagtilrettelsen, Fejl_tur.Indberetter, Fejl_tur.Fixed_comment
FROM Fejl_tur INNER JOIN (Tur INNER JOIN (Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Tur.TurID = TurDeltager.FK_TurID) ON Fejl_tur.TurID = Tur.TurID
GROUP BY Medlem.Medlemsnr, TurDeltager.FK_TurID, Tur.Destination, Tur.OprettetDato, Fejl_tur.FejlID, Fejl_tur.SletTur, Fejl_tur.TurID, Fejl_tur.B�d, Fejl_tur.Ud, Fejl_tur.Ind, Fejl_tur.Destination, Fejl_tur.Distance, Fejl_tur.TurType, Fejl_tur.TurDeltager0, Fejl_tur.TurDeltager1, Fejl_tur.TurDeltager2, Fejl_tur.TurDeltager3, Fejl_tur.TurDeltager4, Fejl_tur.TurDeltager5, Fejl_tur.TurDeltager6, Fejl_tur.TurDeltager7, Fejl_tur.TurDeltager8, Fejl_tur.TurDeltager9, Fejl_tur.�rsagtilrettelsen, Fejl_tur.Indberetter, Fejl_tur.Fixed_comment;


CREATE VIEW FindFejlture_1 AS
SELECT Medlem.Medlemsnr, Medlem.MedlemID, Ud AS Turdato, Count(Ud) AS TurdatoC
FROM Medlem INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID
GROUP BY Medlem.Medlemsnr, Medlem.MedlemID, Ud
HAVING (((Count(Ud))>1))
ORDER BY Ud;

CREATE VIEW FindFejlture_2 AS
SELECT Tur.TurID, Ud AS Turdato, TurDeltager.FK_MedlemID
FROM Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID;

CREATE VIEW FindFejlture_3 AS
SELECT Medlem.Medlemsnr, FindFejlture_1.Turdato, FindFejlture_2.TurID, Tur.Meter
FROM ((FindFejlture_1 INNER JOIN Medlem ON FindFejlture_1.MedlemID = Medlem.MedlemID) INNER JOIN FindFejlture_2 ON (FindFejlture_1.Turdato = FindFejlture_2.Turdato) AND (FindFejlture_1.MedlemID = FindFejlture_2.FK_MedlemID)) INNER JOIN Tur ON FindFejlture_2.TurID = Tur.TurID
WHERE (((Tur.Meter)<1000));


CREATE VIEW RetTur_subquery AS
SELECT Tur.TurID, Tur.FK_B�dID, Tur.Ud, Tur.Ind, Tur.ForvInd, Tur.Destination, Tur.Meter, Tur.FK_TurTypeID, Tur.Kommentar, Tur.OprettetDato, Tur.RedigeretDato, Tur.Initialer, Tur.DESTID
FROM Tur;

CREATE VIEW RetTurDeltagere AS
SELECT TurDeltager.FK_MedlemID AS MedlemID, Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, Medlem.Adresse, Medlem.FK_Postnr, Medlem.Telefon1, Medlem.Telefon2, Medlem.F�dselsdag, Medlem.Password, Medlem.Aktiv, Medlem.Rettigheder, Medlem.OprettetDato as Medlem_Oprettelsedato, Medlem.RedigeretDato AS Medlem_RedirigeretDato, Medlem.Initialer AS Medlem_Initialer, TurDeltager.*
FROM Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID
ORDER BY TurDeltager.FK_TurID, TurDeltager.Plads;


CREATE VIEW RetTurDeltagere_subquery AS
SELECT TurDeltager.Plads, Medlem.Medlemsnr, TurDeltager.FK_MedlemID, TurDeltager.Navn
FROM (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) INNER JOIN Medlem ON TurDeltager.FK_MedlemID = Medlem.MedlemID;

CREATE VIEW Rostat_BeregnRodistance AS
SELECT Medlem.MedlemID, ROUND(Sum(Meter/1000)) AS Rodistance, Count(Tur.TurID) AS Antal_ture
FROM Tur INNER JOIN (Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Tur.TurID = TurDeltager.FK_TurID
GROUP BY Medlem.MedlemID
HAVING (((ROUND(Sum(Meter/1000)))>0))
ORDER BY Sum(Meter/1000);

CREATE VIEW Rostat_BeregnRodistance_kajak AS
SELECT Medlem.MedlemID, Sum(Meter/1000) AS Rodistance, Count(Tur.TurID) AS Antal_ture
FROM B�d RIGHT JOIN (Tur INNER JOIN (Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Tur.TurID = TurDeltager.FK_TurID) ON B�d.B�dID = Tur.FK_B�dID
WHERE (((B�d.FK_GruppeID)=4 Or (B�d.FK_GruppeID)=5))
GROUP BY Medlem.MedlemID
ORDER BY Sum(Meter/1000);


CREATE VIEW Rostat_BeregnRodistance_robaad AS
SELECT Medlem.MedlemID, Sum(Meter/1000) AS Rodistance, Count(Tur.TurID) AS Antal_ture
FROM B�d RIGHT JOIN (Tur INNER JOIN (Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Tur.TurID = TurDeltager.FK_TurID) ON B�d.B�dID = Tur.FK_B�dID
WHERE (((B�d.FK_GruppeID)<>4 And (B�d.FK_GruppeID)<>5))
GROUP BY Medlem.MedlemID
ORDER BY Sum(Meter/1000);


CREATE VIEW Rostat_Rangorden AS
SELECT Rostat_BeregnRodistance.MedlemID, Medlem.Medlemsnr AS Medlemsnr, CONCAT(fornavn," ",efternavn) AS Navn, Rostat_BeregnRodistance.Rodistance, Rostat_BeregnRodistance.Antal_ture, ROUND(Rodistance/Antal_ture*10)/10 AS Gennemsnitsl�ngde
FROM Rostat_BeregnRodistance INNER JOIN Medlem ON Rostat_BeregnRodistance.MedlemID = Medlem.MedlemID;


CREATE VIEW Rostat_Rangorden_kajak AS
SELECT Rostat_BeregnRodistance_kajak.MedlemID, medlemsnr AS Medlemsnr_, CONCAT(fornavn," ",efternavn) AS Navn, Rostat_BeregnRodistance_kajak.Rodistance, Rostat_BeregnRodistance_kajak.Antal_ture, ROUND(Rodistance/Antal_ture*10)/10 AS Gennemsnitsl�ngde
FROM Rostat_BeregnRodistance_kajak INNER JOIN Medlem ON Rostat_BeregnRodistance_kajak.MedlemID = Medlem.MedlemID;

CREATE VIEW Rostat_Rangorden_robaad AS
SELECT Rostat_BeregnRodistance_robaad.MedlemID, medlemsnr AS Medlemsnr_, CONCAT(fornavn," ",efternavn) AS Navn, Rostat_BeregnRodistance_robaad.Rodistance, Rostat_BeregnRodistance_robaad.Antal_ture, ROUND(Rodistance/Antal_ture*10)/10 AS Gennemsnitsl�ngde
FROM Rostat_BeregnRodistance_robaad INNER JOIN Medlem ON Rostat_BeregnRodistance_robaad.MedlemID = Medlem.MedlemID;

CREATE VIEW Skadeadmin AS
SELECT B�d.Navn, Skade.FK_B�dID, Skade.FK_Ansvarlig, Skade.�delagt, Skade.FK_Reperat�r, Skade.Grad, Skade.Repareret, Skade.Beskrivelse, Skade.OprettetDato, Skade.RedigeretDato, Skade.Initialer
FROM B�d INNER JOIN Skade ON B�d.B�dID = Skade.FK_B�dID;

CREATE VIEW Skademeld_MedlemID_og_Navn AS
SELECT Medlem.MedlemID, Medlem.Medlemsnr, CONCAT(fornavn," ",efternavn) AS Navn
FROM Medlem
ORDER BY CONCAT(fornavn," ",efternavn);

CREATE VIEW Tur_og_ugenr AS
SELECT Tur.TurID, WEEK(Ud) AS Uge
FROM Tur
GROUP BY Tur.TurID, WEEK(Ud);

CREATE VIEW qTurDeltagere AS
SELECT Medlem.MedlemID, Medlem.Medlemsnr, fornavn,efternavn, Adresse, FK_Postnr, Aktiv, Rettigheder, Medlem.Initialer, FK_TurID, Plads, Navn
FROM Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID
ORDER BY TurDeltager.FK_TurID, TurDeltager.Plads;


CREATE VIEW Rostat_B�dstatistikMain AS
SELECT B�d.Navn AS B�d, Gruppe.Navn AS B�dtype, Sum(ROUND(Meter/100)/10) AS Rodistance, Count(Tur.TurID) AS Antal_ture
FROM (Gruppe INNER JOIN B�d ON Gruppe.GruppeID = B�d.FK_GruppeID) LEFT JOIN Tur ON B�d.B�dID = Tur.FK_B�dID
GROUP BY B�d.Navn, Gruppe.Navn;

CREATE VIEW Rostat_KunTureMedStyrmand as
SELECT Tur.TurID, TurDeltager.Plads, Tur.FK_B�dID, Tur.FK_TurTypeID, Tur.Meter
FROM Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID
WHERE (((TurDeltager.Plads)=0));


CREATE VIEW Kaniner AS
SELECT Medlem.MedlemID
FROM (((Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) INNER JOIN Tur ON TurDeltager.FK_TurID = Tur.TurID) INNER JOIN TurType ON Tur.FK_TurTypeID = TurType.TurTypeID) INNER JOIN tblMembers ON Medlem.Medlemsnr = tblMembers.MemberID
WHERE (((TurType.Navn)="Instruktion") AND ((Year(JoinDate))=YEAR(now())))
GROUP BY Medlem.MedlemID;


CREATE VIEW  Racerkaniner AS
SELECT Medlem.MedlemID
FROM (((Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) INNER JOIN Tur ON TurDeltager.FK_TurID = Tur.TurID) INNER JOIN TurType ON Tur.FK_TurTypeID = TurType.TurTypeID) INNER JOIN tblMembers ON Medlem.Medlemsnr = tblMembers.MemberID
WHERE (((TurType.Navn)="Racerkanin") AND ((Year(JoinDate))=year(now())))
GROUP BY Medlem.MedlemID;


CREATE VIEW Rostat_Kaniner AS
SELECT Rostat_Rangorden.MedlemID, Rostat_Rangorden.Medlemsnr, Rostat_Rangorden.Navn, Rostat_Rangorden.Rodistance, Rostat_Rangorden.Antal_ture, Rostat_Rangorden.Gennemsnitsl�ngde
FROM Kaniner INNER JOIN Rostat_Rangorden ON Kaniner.MedlemID = Rostat_Rangorden.MedlemID;

CREATE VIEW DagensRoere AS
SELECT Tur.TurID, B�d.Navn AS B�d, CONCAT(Medlemsnr, " - ", TurDeltager.Navn) AS Roer, Tur.Destination, DATE_FORMAT(Ud,"%T") AS Udtid, DATE_FORMAT(Ind,"%T") AS Indtid, DATE_FORMAT(ForvInd,"%T") AS Forv_inde
FROM (B�d RIGHT JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON B�d.B�dID = Tur.FK_B�dID) LEFT JOIN Medlem ON TurDeltager.FK_MedlemID = Medlem.MedlemID
WHERE ud BETWEEN date(now()) AND date(now())+1
ORDER BY Tur.TurID, DATE_FORMAT(Ind,"%T") DESC;

CREATE VIEW alle_der_har_roet_p100_rob�d AS
SELECT Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, tblMembers.Address1, tblMembers.Address2, tblMembers.Postnr, tblMembers.City, tblMembers.Telephone1, tblMembers.Telephone2, Sum(Tur.Meter) AS SumOfMeter
FROM (Gruppe RIGHT JOIN (B�d RIGHT JOIN (Medlem LEFT JOIN (Tur RIGHT JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON B�d.B�dID = Tur.FK_B�dID) ON Gruppe.GruppeID = B�d.FK_GruppeID) LEFT JOIN tblMembers ON Medlem.Medlemsnr = tblMembers.MemberID
WHERE (((Gruppe.Navn) Not Like "*kajak*"))
GROUP BY Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, tblMembers.Address1, tblMembers.Address2, tblMembers.Postnr, tblMembers.City, tblMembers.Telephone1, tblMembers.Telephone2
HAVING (((Sum(Tur.Meter))>100000));

CREATE VIEW RovagtAndet AS
SELECT B�d.Navn, Gruppe.Navn AS B�dtype, if(Ud>0,"Ja","Nej") AS Paa_vandet, qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja") AS Tilg�ngelig
FROM (((Gruppe RIGHT JOIN B�d ON Gruppe.GruppeID = B�d.FK_GruppeID) LEFT JOIN B�dKategori ON Gruppe.FK_B�dKategoriID = B�dKategori.B�dKategoriID) LEFT JOIN qBoatsOnWater3 ON B�d.B�dID = qBoatsOnWater3.FK_B�dID) LEFT JOIN qBoatsSkadet ON B�d.B�dID = qBoatsSkadet.FK_B�dID
WHERE (((Gruppe.GruppeNr)>2) AND ((B�dKategori.B�dKategoriID)=2))
ORDER BY B�d.Navn, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja");

CREATE VIEW RovagtAndetPrintervenlig AS
SELECT B�d.Navn, Gruppe.GruppeNr, Gruppe.Navn AS B�dtype, IF(Ud>0,"Ja","Nej") AS Paa_vandet, qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja") AS Tilg�ngelig
FROM (((Gruppe RIGHT JOIN B�d ON Gruppe.GruppeID = B�d.FK_GruppeID) LEFT JOIN B�dKategori ON Gruppe.FK_B�dKategoriID = B�dKategori.B�dKategoriID) LEFT JOIN qBoatsOnWater3 ON B�d.B�dID = qBoatsOnWater3.FK_B�dID) LEFT JOIN qBoatsSkadet ON B�d.B�dID = qBoatsSkadet.FK_B�dID
WHERE (((Gruppe.GruppeNr)<>6 And (Gruppe.GruppeNr)<>7 And (Gruppe.GruppeNr)<>12 And (Gruppe.GruppeNr)>2) AND ((B�dKategori.B�dKategoriID)=2))
ORDER BY B�d.Navn, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja");


--- XXXXXXXXXXXXXXX
CREATE VIEW Medlemsrettigheder AS
-- SportsData Missing
SELECT tblMembersSportData.MemberID, tblMembersSportData.Motorboat, tblMembersSportData.Roret, tblMembersSportData.TeoretiskStyrmandKursus, tblMembersSportData.Styrmand, tblMembersSportData.Langtur, tblMembersSportData.Skaergaard, tblMembersSportData.Langtur_Oeresund, tblMembersSportData.Ormen, tblMembersSportData.Svava, tblMembersSportData.Sculler, tblMembersSportData.Kajak, tblMembersSportData.Kajak_2, tblMembersSportData.RoInstruktoer, tblMembersSportData.StyrmandInstruktoer, tblMembersSportData.ScullerInstruktoer, tblMembersSportData.KajakInstruktoer, tblMembersSportData.Kaproer, tblMembersSportData.KeyType, tblMembersSportData.KeyDate, tblMembersSportData.KeyFee, tblMembersSportData.Stilling, tblMembersSportData.Ordinaert, tblMembersSportData.diverse1, tblMembersSportData.diverse2
FROM tblMembersSportData;




CREATE VIEW QRYRovagtInr2 AS
SELECT B�d.Navn, IF(Ud>0,"Ja","Nej") AS Paa_vandet, qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja") AS Tilg�ngelig
FROM (((Gruppe RIGHT JOIN B�d ON Gruppe.GruppeID = B�d.FK_GruppeID) LEFT JOIN B�dKategori ON Gruppe.FK_B�dKategoriID = B�dKategori.B�dKategoriID) LEFT JOIN qBoatsOnWater3 ON B�d.B�dID = qBoatsOnWater3.FK_B�dID) LEFT JOIN qBoatsSkadet ON B�d.B�dID = qBoatsSkadet.FK_B�dID
WHERE (((Gruppe.Navn)="Inrigger 2+") AND ((B�dKategori.B�dKategoriID)=2))
ORDER BY B�d.Navn, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja");


CREATE VIEW QRYRovagtInr4 AS
SELECT B�d.Navn, If(Ud>0,"Ja","Nej") AS Paa_vandet, qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IF(Grad>2 Or ud>0 Or MotionPlus=1,"Nej","Ja") AS Tilg�ngelig
FROM (((Gruppe RIGHT JOIN B�d ON Gruppe.GruppeID = B�d.FK_GruppeID) LEFT JOIN B�dKategori ON Gruppe.FK_B�dKategoriID = B�dKategori.B�dKategoriID) LEFT JOIN qBoatsOnWater3 ON B�d.B�dID = qBoatsOnWater3.FK_B�dID) LEFT JOIN qBoatsSkadet ON B�d.B�dID = qBoatsSkadet.FK_B�dID
WHERE (((Gruppe.Navn)="Inrigger 4+") AND ((B�dKategori.B�dKategoriID)=2))
ORDER BY B�d.Navn, IF(Grad>2 OR ud>0 OR MotionPlus=1,"Nej","Ja");



