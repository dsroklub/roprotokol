RENAME TABLE Båd TO Boat;
RENAME TABLE BådKategori TO BoatCategory;
RENAME TABLE Bådindstilling TO BoatConfiguration;
RENAME TABLE Fejl_tur TO Error_Trip;
RENAME TABLE Gruppe TO BoatType;
RENAME TABLE Kajak_anvendelser TO boat_usage;
RENAME TABLE Kajak_typer TO boat_brand;
RENAME TABLE Kommentar TO Comment;
RENAME TABLE LåsteBåde TO LockedBoat;
RENAME TABLE Medlem TO Member;
-- RENAME TABLE Postnr TO Zipcode;
DROP TABLES IF EXISTS  Damage;
RENAME TABLE Skade TO Damage;
RENAME TABLE TurType TO TripType;


ALTER TABLE Member CHANGE MedlemID id INT AUTO_INCREMENT;
ALTER TABLE Member CHANGE Medlemsnr MemberID VARCHAR(10);

ALTER TABLE Member CHANGE Fornavn FirstName VARCHAR(100);
ALTER TABLE Member CHANGE Efternavn LastName VARCHAR(100);
ALTER TABLE Member CHANGE Adresse  Address VARCHAR(100);
ALTER TABLE Member CHANGE Fødselsdag  Birthday DATETIME;
ALTER TABLE Member CHANGE Telefon1  phone1 CHAR(20);
ALTER TABLE Member CHANGE Telefon2  phone2 CHAR(20);
ALTER TABLE Member DROP COLUMN Rettigheder;
ALTER TABLE Member CHANGE OprettetDato  Created DATETIME;
ALTER TABLE Member CHANGE RedigeretDato  Updated DATETIME;
ALTER TABLE Member CHANGE Initialer  Initials CHAR(10);
-- ALTER TABLE Member CHANGE FK_Postnr  Zipcode CHAR(20);


ALTER TABLE BoatType CHANGE GruppeID  id INT;
ALTER TABLE BoatType CHANGE Navn Name VARCHAR(100);
ALTER TABLE BoatType CHANGE Pladser Seatcount INT;
ALTER TABLE BoatType CHANGE Beskrivelse Description VARCHAR(1000);
ALTER TABLE BoatType CHANGE FK_BådKategoriID Category INT;
ALTER TABLE BoatType CHANGE OprettetDato Created DATETIME;
ALTER TABLE BoatType CHANGE RedigeretDato Updated DATETIME;
ALTER TABLE BoatType CHANGE Initialer Initials VARCHAR(10);
ALTER TABLE BoatType DROP COLUMN GruppeNr;

ALTER TABLE Boat CHANGE BådID id INT AUTO_INCREMENT;
ALTER TABLE Boat CHANGE Navn Name VARCHAR(100);
ALTER TABLE Boat CHANGE FK_GruppeID BoatType INT;
ALTER TABLE Boat CHANGE Type KayakModel INT;
ALTER TABLE Boat CHANGE Beskrivelse Description VARCHAR(1000);
ALTER TABLE Boat CHANGE OprettetDato Created DATETIME;
ALTER TABLE Boat CHANGE RedigeretDato Updated DATETIME;
ALTER TABLE Boat CHANGE Initialer Initials VARCHAR(10);
ALTER TABLE Boat DROP COLUMN Pladser;

ALTER TABLE BoatCategory CHANGE BådKategoriID id INT;
ALTER TABLE BoatCategory CHANGE Navn Name VARCHAR(100);
ALTER TABLE BoatCategory CHANGE Beskrivelse Description VARCHAR(1000);
ALTER TABLE BoatCategory CHANGE OprettetDato Created DATETIME;
ALTER TABLE BoatCategory CHANGE RedigeretDato Updated DATETIME;
ALTER TABLE BoatCategory CHANGE Initialer Initials VARCHAR(10);

ALTER TABLE Damage CHANGE SkadeID id INT;
ALTER TABLE Damage CHANGE FK_BådID Boat INT;
ALTER TABLE Damage CHANGE FK_Ansvarlig ResponsibleMember INT;
ALTER TABLE Damage CHANGE Ødelagt Damaged DATETIME;
ALTER TABLE Damage CHANGE FK_Reperatør RepairerMember INT;
ALTER TABLE Damage CHANGE Grad Degree INT;
ALTER TABLE Damage CHANGE Beskrivelse Description VARCHAR(1000);
ALTER TABLE Damage CHANGE OprettetDato Created DATETIME;
ALTER TABLE Damage CHANGE RedigeretDato Updated DATETIME;
ALTER TABLE Damage CHANGE Initialer Initials VARCHAR(10);
ALTER TABLE Damage CHANGE Repareret Repaired DATETIME;

ALTER TABLE Destination CHANGE DestID id INT;
ALTER TABLE Destination CHANGE Navn Name VARCHAR(100);
ALTER TABLE Destination CHANGE Beskrivelse Description VARCHAR(1000);
ALTER TABLE Destination CHANGE OprettetDato Created DATETIME;
ALTER TABLE Destination CHANGE RedigeretDato Updated DATETIME;
ALTER TABLE Destination CHANGE Initialer Initials VARCHAR(10);
ALTER TABLE Destination CHANGE Gennemsnitlig_varighed_Normal ExpectedDurationNormal FLOAT;
ALTER TABLE Destination CHANGE Gennemsnitlig_varighed_Instruktion ExpectedDurationInstruction FLOAT;

ALTER TABLE  Error_Trip CHANGE FejlID id INT;
ALTER TABLE  Error_Trip CHANGE SletTur DeleteTrip INT;
ALTER TABLE  Error_Trip CHANGE TurID Trip INT;
ALTER TABLE  Error_Trip CHANGE Båd Boat VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE Ud TimeOut DATETIME;
ALTER TABLE  Error_Trip CHANGE Ind TimeIn DATETIME;
ALTER TABLE  Error_Trip CHANGE TurType TripType VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager0 TripMember0 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager1 TripMember1 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager2 TripMember2 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager3 TripMember3 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager4 TripMember4 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager5 TripMember5 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager6 TripMember6 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager7 TripMember7 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager8 TripMember8 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE TurDeltager9 TripMember9 VARCHAR(100);
ALTER TABLE  Error_Trip CHANGE Årsagtilrettelsen ReasonForCorrection VARCHAR(1000);
ALTER TABLE  Error_Trip CHANGE Indberetter  Reporter VARCHAR(100);

ALTER TABLE  boat_brand CHANGE ID id INT;
ALTER TABLE  boat_brand CHANGE Typenavn name VARCHAR(100);

ALTER TABLE boat_usage CHANGE ID id INT;
ALTER TABLE boat_usage CHANGE Anvendelse name VARCHAR(100);
ALTER TABLE boat_usage CHANGE Beskrivelse Description VARCHAR(1000);

ALTER TABLE LockedBoat CHANGE BoatID Boat INT;
ALTER TABLE LockedBoat CHANGE KlientNavn ClientName VARCHAR(100);

ALTER TABLE Reservation CHANGE ID id INT;
ALTER TABLE Reservation CHANGE FK_BådID Boat INT;
ALTER TABLE Reservation CHANGE Start Begin DATETIME;
ALTER TABLE Reservation CHANGE Slut End DATETIME;
ALTER TABLE Reservation CHANGE FK_MedlemID Member INT;
ALTER TABLE Reservation CHANGE Beskrivelse Description VARCHAR(1000);
ALTER TABLE Reservation CHANGE FK_SlettetAf CancelledBy INT;
ALTER TABLE Reservation CHANGE Formål Purpose VARCHAR(100);
ALTER TABLE Reservation CHANGE OprettetDato  Created DATETIME;
ALTER TABLE Reservation CHANGE RedigeretDato  Updated DATETIME;
ALTER TABLE Reservation CHANGE Initialer  Initials VARCHAR(10);

ALTER TABLE TripType CHANGE TurTypeID id INT;
ALTER TABLE TripType CHANGE Navn Name VARCHAR(100);
ALTER TABLE TripType CHANGE Beskrivelse Description VARCHAR(1000);
ALTER TABLE TripType CHANGE OprettetDato Created DATETIME;
ALTER TABLE TripType CHANGE RedigeretDato Updated DATETIME;
ALTER TABLE TripType CHANGE Initialer Initials VARCHAR(10);
ALTER TABLE TripType CHANGE Aktiv Active INT;

-- ALTER TABLE Zipcode CHANGE Postnr Zipcode CHAR(10);
-- ALTER TABLE Zipcode CHANGE Distrikt District VARCHAR(100);

-- TODO: BoatConfiguration, Comment, 
DELETE FROM Destination WHERE Meter=0;
UPDATE Destination SET Name = SUBSTRING_INDEX(Name,"(",1);

UPDATE Trip SET Destination = SUBSTRING_INDEX(Destination,"(",1);
UPDATE Destination set Name= TRIM(Name);


UPDATE Destination SET Location = 'DSR';

INSERT INTO Destination (Name,Meter,ExpectedDurationNormal,ExpectedDurationInstruction,Location)
SELECT Name,Meter+10000,ExpectedDurationNormal+2,ExpectedDurationInstruction+4,'Nordhavn'
FROM Destination WHERE Name IN ("Bellevue","Charlottenlund","Hellerup","Skovshoved","Tuborg havn","Tårbæk","Vedbæk","Strandmøllen","Rungsted","Skodsborg","Opfyldningen nord","Knud","Svanemøllehavnen");

INSERT INTO Destination (Name,Meter,ExpectedDurationNormal,ExpectedDurationInstruction,Location)
SELECT Name,Meter-10000,ExpectedDurationNormal-2,ExpectedDurationInstruction-3,'Nordhavn'
FROM Destination WHERE Name IN ("Kanalen","Margretheholms havn","Slusen");

INSERT INTO Destination (Name,Meter,ExpectedDurationNormal,ExpectedDurationInstruction,Location)
SELECT Name,Meter-2000,ExpectedDurationNormal-1,ExpectedDurationInstruction-2,'Nordhavn'
FROM Destination WHERE Name IN ("Flakfortet","Langelinie");

INSERT INTO Destination (Name,Meter,ExpectedDurationNormal,ExpectedDurationInstruction,Location)
SELECT Name,Meter,ExpectedDurationNormal,ExpectedDurationInstruction,'Nordhavn'
FROM Destination WHERE Name IN ("Øvrige [Skriv i kommentar]");

UPDATE Destination SET ExpectedDurationInstruction='DSR' WHERE ExpectedDurationInstruction <= 0;

UPDATE Boat set Location='DSR';
UPDATE Boat set Location='Nordhavn' WHERE Name in ("Freja","Tyr","Modi","Embla");
UPDATE Boat set Decommissioned = Now() WHERE Name in ("Dan");

-- ALTER TABLE Trip CHANGE TripID id INT AUTO_INCREMENT;
ALTER TABLE Error_Trip CHANGE  id id INT AUTO_INCREMENT;
-- ALTER TABLE MemberRights CHANGE MemberID member_id INT;
-- ALTER TABLE Boat CHANGE KayakModel modelid INT;
-- ALTER TABLE Boat ADD brand VARCHAR(30);
UPDATE Boat SET brand=(SELECT Name as model FROM boat_brand WHERE boat_brand.id = Boat.modelid);
ALTER TABLE Boat DROP COLUMN modelid;
ALTER TABLE Boat CHANGE Anvendelse boat_usage INT;
ALTER TABLE Boat CHANGE  Niveau level INT;
ALTER TABLE boat_usage CHANGE ID id INT AUTO_INCREMENT;
-- ALTER TABLE boat_usage CHANGE Anvendelse name VARCHAR(100);


-- These might not be necessary, but they are at least idempotent
ALTER TABLE Damage change id id INT NOT NULL AUTO_INCREMENT;
ALTER TABLE BoatType CHANGE id id INT NOT NULL AUTO_INCREMENT;
UPDATE Trip set Destination = TRIM(Destination);
UPDATE TripRights SET required_right='instructor' where required_right="instruktør";
UPDATE TripRights SET required_right='longdistance' where required_right="langtursstyrmand";
UPDATE TripRights SET required_right='outrigger_instructor' where required_right="outriggerinstruktør";
UPDATE Destination SET Location="Nordhavn" WHERE Location="Forpost";
