--#!/usr/bin/mysql -u root -p --password=xxx roprotokol
CREATE TABLE IF NOT EXISTS Locations (
       name VARCHAR(30) PRIMARY KEY,
       description VARCHAR(100) 
);

CREATE TABLE IF NOT EXISTS Tur (
       TurID INT PRIMARY KEY,
       FK_BådID INT NOT NULL,
       Ud DATETIME,
       Ind DATETIME,
       ForvInd DATETIME,
       Destination VARCHAR(100),
       Meter INT,
       FK_TurTypeID INT,
       Kommentar VARCHAR(1000),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer VARCHAR(10),
       DESTID INT
);

CREATE INDEX turfk on Tur(FK_BådID);
CREATE INDEX turud on Tur(ud);

CREATE TABLE IF NOT EXISTS Trip (
       id INT AUTO_INCREMENT,
       Season INT,
       BoatID INT NOT NULL,
       OutTime DATETIME,
       InTime DATETIME,
       ExpectedIn DATETIME,
       Destination VARCHAR(100),
       Meter INT,
       TripTypeID INT,
       Comment VARCHAR(1000),
       CreatedDate DATE,
       EditDate DATE,
       Initials VARCHAR(10),
       DESTID INT,
       PRIMARY KEY   (id)
       -- FIXME Season,TripID is the primary key in DSR DB, but then AUTO_INCREMENT does not work in MYSQL
);

CREATE INDEX  tripfk on Trip(BoatID);
CREATE INDEX tripout on Trip(OutTime);


CREATE TABLE IF NOT EXISTS Location (
    Name VARCHAR(10) NOT NULL,
    Description VARCHAR(500),
    Latitude FLOAT,
    Longitude FLOAT,
    PRIMARY KEY(Name)
);


CREATE TABLE IF NOT EXISTS Båd (
    BådID INT AUTO_INCREMENT,
    Navn VARCHAR(100) NOT NULL, -- FIXME should be unique: Balder
    FK_GruppeID INT,
    Pladser INT,
    brand VARCHAR(30),
    modelid INT,
--    level INT,
    Beskrivelse VARCHAR(100),
    OprettetDato DATETIME,
    RedigeretDato DATETIME,
    Initialer VARCHAR(10),
    MotionPlus VARCHAR(100),
    Type VARCHAR(100), -- FIXME was TYPE
    Anvendelse VARCHAR(100),
    Niveau VARCHAR(100),
    Location VARCHAR(100),
    Placement VARCHAR(100),
    Decommissioned DATETIME,
    PRIMARY KEY (BådID)
);

-- CREATE INDEX boat on Båd(FK_GruppeID);

CREATE TABLE IF NOT EXISTS Bådindstilling (
    BådID Int, -- FIXME should be Unique
    Navn VARCHAR(100) NOT NULL,
    Plads Int,
    Åretype VARCHAR(100),
    Righøjde FLOAT,
    Svirvelafstand FLOAT,
    Svirveltype VARCHAR(100),
    Åresmig FLOAT,
    Stammevinkel FLOAT,
    Årelængde FLOAT,
    ÅrelængdeIndvendig FLOAT,
    Håndtagslængde FLOAT,
    Sædetype VARCHAR(100),
    Skinnelængde FLOAT,
    SkinneForanSæde FLOAT,
    Bensparksdybde FLOAT,
    Sparkevinkel FLOAT,
    Spændholttype VARCHAR(100),
    Omsætningsforhold FLOAT,
    Gearingsforhold FLOAT,
    ØnsketOmsætningsforhold FLOAT,
    ØnsketGearingsforhold FLOAT,
    NyÅrelængde FLOAT,
    NyIndvendiglængde FLOAT,
    OprettetDato DATETIME,
    RedigeretDato DATETIME,
    Kommentar VARCHAR(1000),
    Initialer VARCHAR(10)
);


CREATE TABLE IF NOT EXISTS BådKategori (
       BådKategoriID INT PRIMARY KEY,
       Navn VARCHAR(100) UNIQUE NOT NULL,
       Beskrivelse VARCHAR(1000),
       OprettetDato DATETIME,
       RedigeretDato DATETIME,
       Initialer VARCHAR(10)
);

CREATE TABLE IF NOT EXISTS Fejl_system (
       FejlID INT PRIMARY KEY,
       Indberetter VARCHAR(100),
       Beskrivelse VARCHAR(1000),
       Dato DATETIME,
       Mail VARCHAR(300)
);


CREATE TABLE IF NOT EXISTS Fejl_tblMembersSportData (
       FejlID INT PRIMARY KEY,
       Navn VARCHAR(100),
       MemberID INT,
       Roret INT,
       TeoretiskStyrmandKursus INT,
       Styrmand INT,
       Langtur INT,
       Ormen INT,
       Svava INT,
       Sculler INT,
       Kajak INT,
       Kajak_2 INT,
       RoInstruktoer INT,
       StyrmandInstruktoer INT,
       ScullerInstruktoer INT,
       KajakInstruktoer INT,
       Kaproer INT,
       Motorboat INT,
       Indberetter VARCHAR(100),
       Mail VARCHAR(300),
       Kommentar VARCHAR(1000),
       Fixed_Comment VARCHAR(1000),
       Fixed INT
);

CREATE TABLE IF NOT EXISTS  Fejl_tur (
       FejlID INT PRIMARY KEY,
       SletTur INT,
       CreatedDate DATE,
       EditDate DATE,
       TurID INT,
       Season INT,
       Båd VARCHAR(100),
       BoatID INT NOT NULL,
       TripTypeID INT,
       Ud DATETIME,
       Ind DATETIME,
       Destination VARCHAR(100),
       Distance INT,
       TurType VARCHAR(100),
       TurDeltager0 VARCHAR(100),
       TurDeltager1 VARCHAR(100),
       TurDeltager2 VARCHAR(100),
       TurDeltager3 VARCHAR(100),
       TurDeltager4 VARCHAR(100),
       TurDeltager5 VARCHAR(100),
       TurDeltager6 VARCHAR(100),
       TurDeltager7 VARCHAR(100),
       TurDeltager8 VARCHAR(100),
       TurDeltager9 VARCHAR(100),
       Årsagtilrettelsen VARCHAR(1000), -- RM space
       Indberetter VARCHAR(100),
       Mail VARCHAR(300),
       Fixed_comment VARCHAR(1000),
       Fixed INT 
);

CREATE TABLE IF NOT EXISTS Gruppe (
       GruppeID INT PRIMARY KEY,
       GruppeNr INT UNIQUE NOT NULL,
       Navn VARCHAR(100),
       Pladser INT,
       Beskrivelse VARCHAR(1000),
       FK_BådKategoriID INT,
       OprettetDato DATETIME,
       RedigeretDato DATETIME,
       Initialer VARCHAR(10)
);
CREATE INDEX gruppenavn on Gruppe(Navn);


CREATE TABLE IF NOT EXISTS  Kajak_typer (
       ID INT PRIMARY KEY,
       Typenavn VARCHAR(100) UNIQUE NOT NULL
);


CREATE TABLE IF NOT EXISTS Kommentar (
    Art VARCHAR(100),
    FK_ID INT,
    Dato DATE,
    Tid TIME,
    Kommentar VARCHAR(100)
);


CREATE TABLE IF NOT EXISTS LåsteBåde (
       BoatID INT PRIMARY KEY,
       KlientNavn VARCHAR(100),
       locktimeout INT -- type guessed
);

CREATE TABLE IF NOT EXISTS  Medlem (
       MedlemID INT PRIMARY KEY,
       Medlemsnr VARCHAR(10) NOT NULL,  -- FIXME UNIQUE: 4419 Frederik Thuesen
       Fornavn VARCHAR(100),
       Efternavn VARCHAR(100),
       Adresse VARCHAR(100),
       FK_Postnr INT,
       Telefon1 VARCHAR(20),
       Telefon2 VARCHAR(20),
       Fødselsdag DATETIME,
       Password VARCHAR(100),
       Aktiv INT,
       Rettigheder VARCHAR(100),
       OprettetDato DATE,
       RedigeretDato DATE,
       log VARCHAR(2000),
       Initialer VARCHAR(10)
);
CREATE INDEX  medlemnrix on Medlem(Medlemsnr);

CREATE TABLE IF NOT EXISTS  Motionstatus ( -- FIXME was motion+status
       MotionstatusID INT PRIMARY KEY,
       Motionstatus VARCHAR(100)

);

CREATE TABLE IF NOT EXISTS  Postnr (
       Postnr INT PRIMARY KEY,
       Distrikt VARCHAR(100),
       COUNTRY CHAR(2) DEFAULT 'DK'
);

CREATE TABLE IF NOT EXISTS Reservation (
       ID INT PRIMARY KEY,
       FK_BådID INT,
       Start DATETIME,
       Slut DATETIME,
       FK_MedlemID INT,
       Beskrivelse VARCHAR(1000),
       FK_SlettetAf INT,
       Formål VARCHAR(100),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer VARCHAR(10)
);

CREATE TABLE IF NOT EXISTS Skade (
       SkadeID INT NOT NULL AUTO_INCREMENT,
       FK_BådID INT NOT NULL,
       FK_Ansvarlig INT,
       Ødelagt DATETIME,
       FK_Reperatør INT,
       Grad INT,
       Repareret DATETIME,
       Beskrivelse VARCHAR(1000),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer VARCHAR(10),
       PRIMARY KEY(SkadeID)
);

CREATE TABLE IF NOT EXISTS TurDeltager (
       FK_TurID INT,
       Plads INT,
       FK_MedlemID INT,
       Navn VARCHAR(100),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer VARCHAR(10),
       PRIMARY KEY(FK_TurID,Plads)
);

CREATE TABLE IF NOT EXISTS TripMember (
       TripID INT,
       Season INT,
       Seat INT,  -- 1 is cox, 2 is stroke, etc
       member_id INT,
       MemberName VARCHAR(100),
       CreatedDate DATE,
       EditDate DATE,
       Initials VARCHAR(10),
       PRIMARY KEY(TripID,Season,Seat)
);

CREATE TABLE IF NOT EXISTS Error_TripMember (
       ErrorTripID INT,
       Seat INT,
       member_id INT,
       MemberName VARCHAR(100),
       CreatedDate DATE,
       EditDate DATE,
       Initials VARCHAR(10),
       PRIMARY KEY(TripID,Seat)
);

-- CREATE INDEX  triptripix on Trip(TripID);


CREATE TABLE IF NOT EXISTS TurType (
       TurTypeID INT PRIMARY KEY,
       Navn VARCHAR(100) UNIQUE NOT NULL,	
       Beskrivelse VARCHAR(1000),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer VARCHAR(10),
       Aktiv INT
);


-- Vintervedligehold to be removed
CREATE TABLE IF NOT EXISTS Vintervedligehold (
       Id INT PRIMARY KEY,
       Medlemsnr VARCHAR(8),
       Season INT,
       HasRedKey INT,
       DeletedReason VARCHAR(100)
);
CREATE INDEX vintermedlem on Vintervedligehold(Medlemsnr);

CREATE TABLE IF NOT EXISTS volunteerwork (
       Medlemsnr VARCHAR(8),
       Season INT,
       worktype VARCHAR(100)
);


CREATE TABLE IF NOT EXISTS Destination (
       DestID INT,
       Location VARCHAR(10),
       Navn VARCHAR(100) NOT NULL,
       Meter INT,
       Beskrivelse VARCHAR(1000),
       OprettetDato DATE,
       RedigeretDato DATE,
       Initialer VARCHAR(10),
       Gennemsnitlig_varighed_Normal FLOAT,
       Gennemsnitlig_varighed_Instruktion FLOAT,
       PRIMARY KEY(Navn,Location)
);


CREATE TABLE IF NOT EXISTS Kajak_anvendelser (
       ID INT PRIMARY KEY,
       Anvendelse VARCHAR(100) UNIQUE NOT NULL,
       Beskrivelse VARCHAR(1000)
);




CREATE TABLE tblMembers
 (
    MemberID            int NOT NULL, 
    LastName            varchar (50), 
    FirstName            varchar (50), 
    Birthdate            date, 
    Sex            varchar (2), 
    Address1            varchar (70), 
    Address2            varchar (70), 
    Address3            varchar (70), 
    Address4            varchar (70), 
    Postnr            varchar (8), 
    City            varchar (40), 
    Country            varchar (4), 
    Telephone1            varchar (40), 
    Telephone2            varchar (40), 
    Fax            varchar (40), 
    E_mail            varchar (100), 
    MemberType            int, 
    Misc1            varchar (100), 
    Misc2            varchar (140), 
    DiverseMemo            text (255), 
    Control            int, 
    OldBalance            float, 
    Subscription            float, 
    RefusedPayed            float, 
    Surcharge            float, 
    ExtraCharge            float, 
    ExtraChargeText            varchar (100), 
    AddSubscription            char NOT NULL, 
    SendAbroad            char NOT NULL, 
    SendInvoice            char NOT NULL, 
    SendInvoiceExtraordinary            char NOT NULL, 
    ReminderTextSurcharge            char NOT NULL, 
    JoinDate            date, 
    JoinJournalDate            date, 
    RemoveDate            date, 
    RemoveJournaLDate            date, 
    SleepTo            date, 
    InvoiceText1            varchar (120), 
    InvoiceText2            varchar (120), 
    InvoiceText3            varchar (120), 
    InvoiceText4            varchar (120), 
    InvoiceText5            varchar (120), 
    InvoiceText6            varchar (120), 
    `E-mailText1`            varchar (300), 
    EraseTextNext            char NOT NULL, 
    NewsletterStart            char NOT NULL, 
    NewsletterStop            char NOT NULL, 
    NewsletterChange            char NOT NULL, 
    NewsletterReceives            char NOT NULL, 
    `E-mail_News`            char NOT NULL, 
    OnAddressList            char NOT NULL, 
    OnTelList            char NOT NULL, 
    Danish            char NOT NULL, 
    CprNo            char NOT NULL, 
    Marker            char NOT NULL, 
    Parent            int, 
    Kundenr            int
);


CREATE TABLE `tblMembersSportData`
 (
    `MemberID`            int, 
    `Roret`            datetime, 
    `TeoretiskStyrmandKursus`            datetime, 
    `Styrmand`            datetime, 
    `TeoretiskLangtursStyrmandKursus`            datetime, 
    `Langtur`            datetime, 
    `Skaergaard`            datetime, 
    `Langtur_Oeresund`            datetime, 
    `Ormen`            datetime, 
    `Svava`            datetime, 
    `Sculler`            datetime, 
    `Kajak`            datetime, 
    `Kajak_2`            datetime, 
    `Swim_400`            datetime, 
    `RoInstruktoer`            datetime, 
    `StyrmandInstruktoer`            datetime, 
    `ScullerInstruktoer`            datetime, 
    `KajakInstruktoer`            datetime, 
    `Kaproer`            datetime, 
    `Motorboat`            varchar (40), 
    `KeyType`            varchar (2), 
    `KeyDate`            datetime, 
    `KeyFee`            float, 
    `Stilling`            varchar (30), 
    `Ordinaert`            varchar (2), 
    `diverse1`            varchar (140), 
    `diverse2`        	varchar (140)
);



CREATE TABLE IF NOT EXISTS MemberRights (
	member_id			INT, 
	MemberRight		 	VARCHAR(50),
        Acquired			DateTime,
	argument			VARCHAR(100),
       PRIMARY KEY(member_id, MemberRight,Acquired,Argument)
);

CREATE TABLE IF NOT EXISTS MemberRightType (
	member_right  VARCHAR(50) PRIMARY KEY,
        description  VARCHAR(200)
);


CREATE TABLE IF NOT EXISTS TripRights (
       trip_type INT NOT NULL,
       required_right VARCHAR(30) NOT NULL,
       requirement VARCHAR(10),
       PRIMARY KEY (trip_type,required_right)
       );

DROP TABLE IF EXISTS BoatRights ;
CREATE TABLE IF NOT EXISTS BoatRights (
       boat_type INT NOT NULL,
       required_right VARCHAR(30) NOT NULL,
       requirement VARCHAR(10),
       PRIMARY KEY (boat_type,required_right)
       );

CREATE TABLE IF NOT EXISTS event_log (
	event  VARCHAR(500),
        event_time   DATETIME
);
CREATE INDEX eventtime on event_log(event_time);
