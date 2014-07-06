
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


DELIMITER ;

--CREATE PROCEDURE Baad_without_matching_kajakker
--BEGIN
--SELECT Båd.Navn
--FROM Båd LEFT JOIN kajakker ON Båd.Navn = [kajakker].[Kajakker]
--WHERE ([kajakker].[Kajakker] Is Null);
--END//

--CREATE PROCEDURE
--BEGIN
--END//


CREATE VIEW qBoatsReserveret AS
SELECT Båd.Navn, Reservation.Start, Reservation.Slut, Reservation.FK_BådID, Reservation.Beskrivelse
FROM Båd INNER JOIN Reservation ON Båd.BådID = Reservation.FK_BådID
WHERE (((Reservation.Start)<=Now()) AND ((Reservation.Slut)>=Now()))
ORDER BY Båd.Navn;

CREATE VIEW qBoatsSkadet AS
SELECT Båd.Navn, Skade.FK_BådID, Max(Skade.Grad) AS grad
FROM Båd INNER JOIN Skade ON Båd.BådID = Skade.FK_BådID
WHERE (((Skade.Repareret) Is Null))
GROUP BY Båd.Navn, Skade.FK_BådID
ORDER BY Båd.Navn;


-- FORMAT(Ud,"dd"". ""mmm hh:nn")
CREATE VIEW qBoatsOnWater AS
SELECT Båd.Navn AS Båd, DATE_FORMAT(Ud,"%d %b %H:%i") AS Tur_start, DATE_FORMAT(forvind,"%d %b %H:%i") AS Forventet_inde, Tur.Destination, Tur.TurID, TurType.Navn AS Turtype
FROM TurType RIGHT JOIN (Båd RIGHT JOIN Tur ON Båd.BådID = Tur.FK_BådID) ON TurType.TurTypeID = Tur.FK_TurTypeID
WHERE (((Tur.Ind) Is Null))
ORDER BY forvind;

CREATE VIEW qBoatsOnWater2 AS
SELECT Båd.Navn as baad_navn, Tur.Ud, Tur.ForvInd, Tur.Ind, Tur.Destination, Tur.FK_BådID, Tur.TurID, TurType.Navn as TurType_Navn
FROM TurType RIGHT JOIN (Båd RIGHT JOIN Tur ON Båd.BådID = Tur.FK_BådID) ON TurType.TurTypeID = Tur.FK_TurTypeID
WHERE (((Tur.Ind) Is Null))
ORDER BY Tur.ForvInd;

CREATE VIEW qBoatsOnWater3 AS
SELECT Båd.Navn as Baad_Navn, TurDeltager.Navn AS Styrmand, Tur.Ud, Tur.ForvInd, Tur.Ind, Tur.Destination, Tur.FK_BådID, Tur.TurID, TurType.Navn as TurType_Navn
FROM (TurType RIGHT JOIN (Båd RIGHT JOIN Tur ON Båd.BådID = Tur.FK_BådID) ON TurType.TurTypeID = Tur.FK_TurTypeID) LEFT JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID
WHERE (((TurDeltager.Plads)=0) AND ((Tur.Ind) Is Null))
ORDER BY Tur.ForvInd;


CREATE VIEW qAvalableboats AS
       SELECT Båd.BådID, Båd.Navn, Båd.FK_GruppeID, Båd.Pladser, qBoatsReserveret.FK_BådID as reserved_baadID, qBoatsOnWater2.FK_BådID as onWater_baadID, qBoatsSkadet.FK_BådID as skade_baadID, qBoatsSkadet.grad, LåsteBåde.locktimeout, qBoatsOnWater2.TurType_Navn AS TurType_navn
       FROM ((qBoatsReserveret RIGHT JOIN (qBoatsSkadet RIGHT JOIN Båd ON qBoatsSkadet.FK_BådID = Båd.BådID) ON qBoatsReserveret.FK_BådID = Båd.BådID) LEFT JOIN LåsteBåde ON Båd.BådID = LåsteBåde.BoatID) LEFT JOIN qBoatsOnWater2 ON Båd.BådID = qBoatsOnWater2.FK_BådID;



CREATE VIEW qBoatsSkader AS
SELECT Skade.FK_BådID AS BoatID, Båd.Navn, 
       CONCAT(Medlemsnr," ",fornavn," ",efternavn) AS Skademelder, 
       DATE_FORMAT(Ødelagt,"%d %b") AS Dato, Skade.Grad, Skade.Beskrivelse, Skade.SkadeID
FROM Båd INNER JOIN (Skade LEFT JOIN Medlem ON Skade.FK_Ansvarlig = Medlem.MedlemID) ON Båd.BådID = Skade.FK_BådID
WHERE (((Skade.Repareret) Is Null))
ORDER BY Båd.Navn;


CREATE VIEW Active_Mp_og_8gp AS
       	    SELECT TurDeltager.Navn as TurDeltager_Navn, TurType.Navn as TurType_Navn
	    FROM TurType INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON TurType.TurTypeID = Tur.FK_TurTypeID
	    WHERE (((TurType.Navn)="8gp")) OR (((TurType.Navn) Like "*Motion+tur*"))
	    GROUP BY TurDeltager.Navn, TurType.Navn;





CREATE VIEW AlleTurRettelser AS
SELECT Medlem.Medlemsnr, TurDeltager.FK_TurID, Tur.Destination AS Dest, Tur.OprettetDato, Fejl_tur.FejlID, Fejl_tur.SletTur, Fejl_tur.TurID, Fejl_tur.Båd, Fejl_tur.Ud, Fejl_tur.Ind, Fejl_tur.Destination, Fejl_tur.Distance, Fejl_tur.TurType, Fejl_tur.TurDeltager0, Fejl_tur.TurDeltager1, Fejl_tur.TurDeltager2, Fejl_tur.TurDeltager3, Fejl_tur.TurDeltager4, Fejl_tur.TurDeltager5, Fejl_tur.TurDeltager6, Fejl_tur.TurDeltager7, Fejl_tur.TurDeltager8, Fejl_tur.TurDeltager9, Fejl_tur.Årsagtilrettelsen, Fejl_tur.Indberetter, Fejl_tur.Fixed_comment
FROM Fejl_tur INNER JOIN (Tur INNER JOIN (Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Tur.TurID = TurDeltager.FK_TurID) ON Fejl_tur.TurID = Tur.TurID
GROUP BY Medlem.Medlemsnr, TurDeltager.FK_TurID, Tur.Destination, Tur.OprettetDato, Fejl_tur.FejlID, Fejl_tur.SletTur, Fejl_tur.TurID, Fejl_tur.Båd, Fejl_tur.Ud, Fejl_tur.Ind, Fejl_tur.Destination, Fejl_tur.Distance, Fejl_tur.TurType, Fejl_tur.TurDeltager0, Fejl_tur.TurDeltager1, Fejl_tur.TurDeltager2, Fejl_tur.TurDeltager3, Fejl_tur.TurDeltager4, Fejl_tur.TurDeltager5, Fejl_tur.TurDeltager6, Fejl_tur.TurDeltager7, Fejl_tur.TurDeltager8, Fejl_tur.TurDeltager9, Fejl_tur.Årsagtilrettelsen, Fejl_tur.Indberetter, Fejl_tur.Fixed_comment;


CREATE VIEW FindFejlture_1 AS
SELECT Medlem.Medlemsnr, Medlem.MedlemID, Ud AS Turdato, Count(Ud) AS TurdatoC
FROM Medlem INNER JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID
GROUP BY Medlem.Medlemsnr, Medlem.MedlemID, Ud
HAVING (((Count(Ud))>1))
ORDER BY Ud;

CREATE VIEW FindFejlture_2 AS
SELECT Tur.TurID, Date(Ud) AS Turdato, TurDeltager.FK_MedlemID
FROM Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID;

CREATE VIEW FindFejlture_3 AS
SELECT Medlem.Medlemsnr, FindFejlture_1.Turdato, FindFejlture_2.TurID, Tur.Meter
FROM ((FindFejlture_1 INNER JOIN Medlem ON FindFejlture_1.MedlemID = Medlem.MedlemID) INNER JOIN FindFejlture_2 ON (FindFejlture_1.Turdato = FindFejlture_2.Turdato) AND (FindFejlture_1.MedlemID = FindFejlture_2.FK_MedlemID)) INNER JOIN Tur ON FindFejlture_2.TurID = Tur.TurID
WHERE (((Tur.Meter)<1000));

CREATE VIEW Kaniner AS
SELECT Medlem.MedlemID
FROM (((Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) INNER JOIN Tur ON TurDeltager.FK_TurID = Tur.TurID) INNER JOIN TurType ON Tur.FK_TurTypeID = TurType.TurTypeID) INNER JOIN tblMembers ON cvar(Medlem.Medlemsnr) = Cvar(tblMembers.MemberID)
WHERE (((TurType.Navn)="Instruktion") AND ((Year(JoinDate))=year(now())))
GROUP BY Medlem.MedlemID;


CREATE VIEW  Racerkaniner AS
SELECT Medlem.MedlemID
FROM (((Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) INNER JOIN Tur ON TurDeltager.FK_TurID = Tur.TurID) INNER JOIN TurType ON Tur.FK_TurTypeID = TurType.TurTypeID) INNER JOIN tblMembers ON cvar(Medlem.Medlemsnr) = Cvar(tblMembers.MemberID)
WHERE (((TurType.Navn)="Racerkanin") AND ((Year(JoinDate))=year(now())))
GROUP BY Medlem.MedlemID;

CREATE VIEW RetTur_subquery AS
SELECT Tur.TurID, Tur.FK_BådID, Tur.Ud, Tur.Ind, Tur.ForvInd, Tur.Destination, Tur.Meter, Tur.FK_TurTypeID, Tur.Kommentar, Tur.OprettetDato, Tur.RedigeretDato, Tur.Initialer, Tur.DESTID
FROM Tur;

CREATE VIEW RetTurDeltagere AS
SELECT TurDeltager.FK_MedlemID AS MedlemID, Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, Medlem.Adresse, Medlem.FK_Postnr, Medlem.Telefon1, Medlem.Telefon2, Medlem.Fødselsdag, Medlem.Password, Medlem.Aktiv, Medlem.Rettigheder, Medlem.OprettetDato, Medlem.RedigeretDato, Medlem.Initialer, TurDeltager.*
FROM Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID
ORDER BY TurDeltager.FK_TurID, TurDeltager.Plads;

CREATE VIEW RetTurDeltagere_subquery AS
SELECT TurDeltager.Plads, Medlem.Medlemsnr, TurDeltager.FK_MedlemID, TurDeltager.Navn
FROM (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) INNER JOIN Medlem ON TurDeltager.FK_MedlemID = Medlem.MedlemID;

CREATE VIEW Rostat_BeregnRodistance AS
SELECT Medlem.MedlemID, Round(Sum(Meter/1000)) AS Rodistance, Count(Tur.TurID) AS [Antal ture]
FROM Tur INNER JOIN (Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Tur.TurID = TurDeltager.FK_TurID
GROUP BY Medlem.MedlemID
HAVING (((Round(Sum(Meter/1000)))>0))
ORDER BY Round(Sum(Meter/1000));

CREATE VIEW Rostat_BeregnRodistance_kajak AS
SELECT Medlem.MedlemID, Sum(Meter/1000) AS Rodistance, Count(Tur.TurID) AS [Antal ture]
FROM Båd RIGHT JOIN (Tur INNER JOIN (Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Tur.TurID = TurDeltager.FK_TurID) ON Båd.BådID = Tur.FK_BådID
WHERE (((Båd.FK_GruppeID)=4 Or (Båd.FK_GruppeID)=5))
GROUP BY Medlem.MedlemID
ORDER BY Sum(Meter/1000);


CREATE VIEW Rostat_BeregnRodistance_robaad AS
SELECT Medlem.MedlemID, Sum(Meter/1000) AS Rodistance, Count(Tur.TurID) AS [Antal ture]
FROM Båd RIGHT JOIN (Tur INNER JOIN (Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Tur.TurID = TurDeltager.FK_TurID) ON Båd.BådID = Tur.FK_BådID
WHERE (((Båd.FK_GruppeID)<>4 And (Båd.FK_GruppeID)<>5))
GROUP BY Medlem.MedlemID
ORDER BY Sum(Meter/1000);

CREATE VIEW Rostat_BådstatistikMain AS
SELECT Båd.Navn AS Båd, Gruppe.Navn AS Bådtype, Sum(Int([Meter]/100)/10) AS Rodistance, Count(Tur.TurID) AS [Antal ture]
FROM (Gruppe INNER JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN Tur ON Båd.BådID = Tur.FK_BådID
GROUP BY Båd.Navn, Gruppe.Navn;

CREATE VIEW Rostat_Kaniner AS
SELECT Rostat_Rangorden].MedlemID, Rostat_Rangorden].Medlemsnr_, Rostat_Rangorden].Navn, Rostat_Rangorden].Rodistance, Rostat_Rangorden].[Antal ture], Rostat_Rangorden].Gennemsnitslængde
FROM Kaniner] INNER JOIN Rostat_Rangorden] ON Kaniner].MedlemID = Rostat_Rangorden].MedlemID;


CREATE VIEW Rostat_KunTureMedStyrmand as
SELECT Tur.TurID, TurDeltager.Plads, Tur.FK_BådID, Tur.FK_TurTypeID, Tur.Meter
FROM Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID
WHERE (((TurDeltager.Plads)=0));


CREATE VIEW Rostat_Rangorden AS
SELECT RoStat_BeregnRoDistance].MedlemID, Medlem.Medlemsnr AS Medlemsnr_, [Fornavn] & " " & [Efternavn] AS Navn, RoStat_BeregnRoDistance].Rodistance, RoStat_BeregnRoDistance].[Antal ture], Int([Rodistance]/[Antal Ture]*10)/10 AS Gennemsnitslængde
FROM RoStat_BeregnRoDistance] INNER JOIN Medlem ON RoStat_BeregnRoDistance].MedlemID = Medlem.MedlemID;

CREATE VIEW Rostat_Rangorden_kajak AS
SELECT RoStat_BeregnRoDistance_kajak].MedlemID, Val([medlemsnr]) AS Medlemsnr_, [Fornavn] & " " & [Efternavn] AS Navn, RoStat_BeregnRoDistance_kajak].Rodistance, RoStat_BeregnRoDistance_kajak.[Antal ture], Int([Rodistance]/[Antal Ture]*10)/10 AS Gennemsnitslængde
FROM RoStat_BeregnRoDistance_kajak INNER JOIN Medlem ON RoStat_BeregnRoDistance_kajak].MedlemID = Medlem.MedlemID;


CREATE VIEW Rostat_Rangorden_robaad AS
SELECT RoStat_BeregnRoDistance_robåd].MedlemID, Val([medlemsnr]) AS Medlemsnr_, [Fornavn] & " " & [Efternavn] AS Navn, RoStat_BeregnRoDistance_robåd].Rodistance, RoStat_BeregnRoDistance_robåd].[Antal ture], Int([Rodistance]/[Antal Ture]*10)/10 AS Gennemsnitslængde
FROM RoStat_BeregnRoDistance_robåd] INNER JOIN Medlem ON RoStat_BeregnRoDistance_robåd].MedlemID = Medlem.MedlemID;

CREATE VIEW Skadeadmin AS
SELECT Båd.Navn, Skade.FK_BådID, Skade.FK_Ansvarlig, Skade.Ødelagt, Skade.FK_Reperatør, Skade.Grad, Skade.Repareret, Skade.Beskrivelse, Skade.OprettetDato, Skade.RedigeretDato, Skade.Initialer
FROM Båd INNER JOIN Skade ON Båd.BådID = Skade.FK_BådID;

CREATE VIEW Skademeld_MedlemID_og_Navn AS
SELECT Medlem.MedlemID, Medlem.Medlemsnr, [Fornavn] & " " & [Efternavn] AS Navn
FROM Medlem
ORDER BY [Fornavn] & " " & [Efternavn];

CREATE VIEW Tur_og_ugenr AS
SELECT Tur.TurID, Format([Ud],"ww") AS Uge
FROM Tur
GROUP BY Tur.TurID, Format([Ud],"ww");

CREATE VIEW QRYDagensRoere AS
SELECT Tur.TurID, Båd.Navn AS Båd, [Medlemsnr] & " - " & [tURDELTAGER].[Navn] AS Roer, Tur.Destination, Format([Ud],"hh:nn:ss") AS Udtid, Format([Ind],"hh:nn:ss") AS Indtid, Format([ForvInd],"hh:nn:ss") AS [Forv inde]
FROM (Båd RIGHT JOIN (Tur INNER JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Båd.BådID = Tur.FK_BådID) LEFT JOIN Medlem ON TurDeltager.FK_MedlemID = Medlem.MedlemID
WHERE (((IIf(Year(Now()) & Month(Now()) & Day(Now())=Year([ud]) & Month([ud]) & Day([ud]),1,0))=1))
ORDER BY Tur.TurID, Format([Ind],"hh:nn:ss") DESC;


CREATE VIEW QRYMedlemsrettigheder AS
-- SportsData Missing
SELECT tblMembersSportData.MemberID, tblMembersSportData.Motorboat, tblMembersSportData.Roret, tblMembersSportData.TeoretiskStyrmandKursus, tblMembersSportData.Styrmand, tblMembersSportData.Langtur, tblMembersSportData.Skaergaard, tblMembersSportData.Langtur_Oeresund, tblMembersSportData.Ormen, tblMembersSportData.Svava, tblMembersSportData.Sculler, tblMembersSportData.Kajak, tblMembersSportData.Kajak_2, tblMembersSportData.RoInstruktoer, tblMembersSportData.StyrmandInstruktoer, tblMembersSportData.ScullerInstruktoer, tblMembersSportData.KajakInstruktoer, tblMembersSportData.Kaproer, tblMembersSportData.KeyType, tblMembersSportData.KeyDate, tblMembersSportData.KeyFee, tblMembersSportData.Stilling, tblMembersSportData.Ordinaert, tblMembersSportData.diverse1, tblMembersSportData.diverse2
FROM tblMembersSportData;

CREATE VIEW RovagtAndet AS
SELECT Båd.Navn, Gruppe.Navn AS Bådtype, IIf([Ud]>0,"Ja","Nej") AS [På vandet], qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IIf([Grad]>2 Or [ud]>0 Or [Motion+]=1,"Nej","Ja") AS Tilgængelig
FROM (((Gruppe RIGHT JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN BådKategori ON Gruppe.FK_BådKategoriID = BådKategori.BådKategoriID) LEFT JOIN qBoatsOnWater3 ON Båd.BådID = qBoatsOnWater3.FK_BådID) LEFT JOIN qBoatsSkadet ON Båd.BådID = qBoatsSkadet.FK_BådID
WHERE (((Gruppe.GruppeNr)>2) AND ((BådKategori.BådKategoriID)=2))
ORDER BY Båd.Navn, IIf([Grad]>2 Or [ud]>0 Or [Motion+]=1,"Nej","Ja");


CREATE VIEW RovagtAndetPrintervenlig AS
SELECT Båd.Navn, Gruppe.GruppeNr, Gruppe.Navn AS Bådtype, IIf([Ud]>0,"Ja","Nej") AS [På vandet], qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IIf([Grad]>2 Or [ud]>0 Or [Motion+]=1,"Nej","Ja") AS Tilgængelig
FROM (((Gruppe RIGHT JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN BådKategori ON Gruppe.FK_BådKategoriID = BådKategori.BådKategoriID) LEFT JOIN qBoatsOnWater3 ON Båd.BådID = qBoatsOnWater3.FK_BådID) LEFT JOIN qBoatsSkadet ON Båd.BådID = qBoatsSkadet.FK_BådID
WHERE (((Gruppe.GruppeNr)<>6 And (Gruppe.GruppeNr)<>7 And (Gruppe.GruppeNr)<>12 And (Gruppe.GruppeNr)>2) AND ((BådKategori.BådKategoriID)=2))
ORDER BY Båd.Navn, IIf([Grad]>2 Or [ud]>0 Or [Motion+]=1,"Nej","Ja");


CREATE VIEW QRYRovagtlnr2 AS
SELECT Båd.Navn, IIf([Ud]>0,"Ja","Nej") AS [På vandet], qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IIf([Grad]>2 Or [ud]>0 Or [Motion+]=1,"Nej","Ja") AS Tilgængelig
FROM (((Gruppe RIGHT JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN BådKategori ON Gruppe.FK_BådKategoriID = BådKategori.BådKategoriID) LEFT JOIN qBoatsOnWater3 ON Båd.BådID = qBoatsOnWater3.FK_BådID) LEFT JOIN qBoatsSkadet ON Båd.BådID = qBoatsSkadet.FK_BådID
WHERE (((Gruppe.Navn)="Inrigger 2+") AND ((BådKategori.BådKategoriID)=2))
ORDER BY Båd.Navn, IIf([Grad]>2 Or [ud]>0 Or [Motion+]=1,"Nej","Ja");


CREATE VIEW QRYRovagtlnr4 AS
SELECT Båd.Navn, IIf([Ud]>0,"Ja","Nej") AS [På vandet], qBoatsOnWater3.ForvInd, qBoatsOnWater3.Styrmand, qBoatsSkadet.grad AS Skadet, IIf([Grad]>2 Or [ud]>0 Or [Motion+]=1,"Nej","Ja") AS Tilgængelig
FROM (((Gruppe RIGHT JOIN Båd ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN BådKategori ON Gruppe.FK_BådKategoriID = BådKategori.BådKategoriID) LEFT JOIN qBoatsOnWater3 ON Båd.BådID = qBoatsOnWater3.FK_BådID) LEFT JOIN qBoatsSkadet ON Båd.BådID = qBoatsSkadet.FK_BådID
WHERE (((Gruppe.Navn)="Inrigger 4+") AND ((BådKategori.BådKategoriID)=2))
ORDER BY Båd.Navn, IIf([Grad]>2 Or [ud]>0 Or [Motion+]=1,"Nej","Ja");

CREATE VIEW qTurDeltagere AS
SELECT Medlem.*, TurDeltager.*
FROM Medlem INNER JOIN TurDeltager ON Medlem.MedlemID = TurDeltager.FK_MedlemID
ORDER BY TurDeltager.FK_TurID, TurDeltager.Plads;

CREATE VIEW alle_der_har_roet_p100_robåd AS
SELECT Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, tblMembers.Address1, tblMembers.Address2, tblMembers.Postnr, tblMembers.City, tblMembers.Telephone1, tblMembers.Telephone2, Sum(Tur.Meter) AS SumOfMeter
FROM (Gruppe RIGHT JOIN (Båd RIGHT JOIN (Medlem LEFT JOIN (Tur RIGHT JOIN TurDeltager ON Tur.TurID = TurDeltager.FK_TurID) ON Medlem.MedlemID = TurDeltager.FK_MedlemID) ON Båd.BådID = Tur.FK_BådID) ON Gruppe.GruppeID = Båd.FK_GruppeID) LEFT JOIN tblMembers ON cstr(Medlem.Medlemsnr) = cstr(tblMembers.MemberID)
WHERE (((Gruppe.Navn) Not Like "*kajak*"))
GROUP BY Medlem.Medlemsnr, Medlem.Fornavn, Medlem.Efternavn, tblMembers.Address1, tblMembers.Address2, tblMembers.Postnr, tblMembers.City, tblMembers.Telephone1, tblMembers.Telephone2
HAVING (((Sum(Tur.Meter))>100000));
