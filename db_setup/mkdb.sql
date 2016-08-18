DROP TABLE IF EXISTS Boat;
CREATE TABLE Boat (
  id int(11) NOT NULL AUTO_INCREMENT,
  Name varchar(100),
  BoatType int(11),
  rights_subtype CHAR(20),
  brand varchar(30),
  modelid int(11),
  Description varchar(1000),
  Created datetime,
  Updated datetime,
  Initials varchar(10),
  MotionPlus varchar(100),
  boat_usage int(11),
  level int(11),
  Location varchar(100),
  placement_aisle INT, -- doors in DSR, Containers from left in Nordhavn
  placement_row INT, -- 1 is toward port, 2 is torwards Strandvænget
  placement_level INT, -- 0=ground, 1 .. shelves
  placement_side Char(6), -- -left, right,center
  Decommissioned datetime,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS BoatCategory;
CREATE TABLE BoatCategory (
  id int(11) NOT NULL,
  Name varchar(100),
  Description varchar(1000),
  Created datetime,
  Updated datetime,
  Initials varchar(10),
  PRIMARY KEY (id),
  UNIQUE KEY Navn (`Name`)
);

DROP TABLE IF EXISTS rights_subtype;
CREATE TABLE rights_subtype (
  name VARCHAR(100) KEY,
  Description VARCHAR(1000)
);

DROP TABLE IF EXISTS BoatConfiguration;
CREATE TABLE BoatConfiguration (
  BådID int(11),
  Navn varchar(100) NOT NULL,
  Plads int(11),
  Åretype varchar(100),
  Righøjde float,
  Svirvelafstand float,
  Svirveltype varchar(100),
  Åresmig float,
  Stammevinkel float,
  Årelængde float,
  ÅrelængdeIndvendig float,
  Håndtagslængde float,
  Sædetype varchar(100),
  Skinnelængde float,
  SkinneForanSæde float,
  Bensparksdybde float,
  Sparkevinkel float,
  Spændholttype varchar(100),
  Omsætningsforhold float,
  Gearingsforhold float,
  ØnsketOmsætningsforhold float,
  ØnsketGearingsforhold float,
  NyÅrelængde float,
  NyIndvendiglængde float,
  OprettetDato datetime,
  RedigeretDato datetime,
  Kommentar varchar(1000),
  Initialer varchar(10)
);

DROP TABLE IF EXISTS BoatRights;
CREATE TABLE BoatRights (
  boat_type int(11) NOT NULL,
  required_right varchar(30) NOT NULL,
  requirement varchar(10),
  PRIMARY KEY (boat_type,required_right)
);

DROP TABLE IF EXISTS BoatType;
CREATE TABLE BoatType (
  id int(11) NOT NULL AUTO_INCREMENT,
  Name varchar(100),
  Seatcount int(11),
  Description varchar(1000),
  Category int(11),
  Created datetime,
  Updated datetime,
  Initials varchar(10),
  rights_subtype CHAR(20),
  PRIMARY KEY (id),
  KEY gruppenavn (`Name`)
);


DROP TABLE IF EXISTS Damage;
CREATE TABLE Damage (
  id int(11) NOT NULL AUTO_INCREMENT,
  Boat int(11),
  ResponsibleMember int(11),
  Damaged datetime,
  RepairerMember int(11),
  Degree int(11),
  Repaired datetime,
  Description varchar(1000),
  Created datetime,
  Updated datetime,
  Initials varchar(10),
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS Destination;
CREATE TABLE Destination (
  id int(11),
  Location varchar(10) NOT NULL DEFAULT 'DSR',
  Name varchar(100) NOT NULL,
  Meter int(11),
  Description varchar(1000),
  Created datetime,
  Updated datetime,
  Initials varchar(10),
  ExpectedDurationNormal float,
  ExpectedDurationInstruction float,
  PRIMARY KEY (`Name`,Location)
);

DROP TABLE IF EXISTS Error_Trip;

CREATE TABLE Error_Trip (
  id int(11) NOT NULL AUTO_INCREMENT,
  DeleteTrip int(11),
  CreatedDate date,
  EditDate date,
  Trip int(11),
  Boat varchar(100),
  BoatID int(11) NOT NULL,
  TripTypeID int(11),
  TimeOut datetime,
  TimeIn datetime,
  Destination varchar(100),
  Distance int(11),
  TripType varchar(100),
  ReasonForCorrection varchar(1000),
  Reporter varchar(100),
  Mail varchar(300),
  Fixed_comment varchar(1000),
  `Fixed` int(11), -- 0=open,1=fixed,2=rejected,3=other
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS Error_TripMember;
CREATE TABLE Error_TripMember (
  ErrorTripID int(11) NOT NULL,
  Seat int(11) NOT NULL,
  member_id int(11),
  MemberName varchar(100),
  PRIMARY KEY (ErrorTripID,Seat)
);

DROP TABLE IF EXISTS Locations;
CREATE TABLE Locations (
  `name` varchar(30) NOT NULL,
  description varchar(100),
  PRIMARY KEY (`name`)
);


DROP TABLE IF EXISTS Member;
CREATE TABLE Member (
  id int(11) NOT NULL AUTO_INCREMENT,
  MemberID varchar(10),
  FirstName varchar(100),
  LastName varchar(100),
  Address varchar(100),
  FK_Postnr int(11),
  phone1 char(20),
  phone2 char(20),
  Birthday datetime,
  `Password` varchar(100),
  Aktiv int(11),
  Created datetime,
  Updated datetime,
  log varchar(2000),
  Initials char(10),
  JoinDate DateTime,
  RemoveDate DateTime,
  PRIMARY KEY (id),
  KEY medlemnrix (MemberID)
);

DROP TABLE IF EXISTS MemberRightType;
CREATE TABLE MemberRightType (
  member_right varchar(50) NOT NULL,
  arg varchar(200),
  description varchar(200),
  PRIMARY KEY (member_right,arg)
);

DROP TABLE IF EXISTS MemberRights;
CREATE TABLE MemberRights (
  member_id int(11) NOT NULL,
  MemberRight varchar(50) NOT NULL,
  Acquired datetime NOT NULL,
  argument varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (member_id,MemberRight,Acquired,argument)
);

DROP TABLE IF EXISTS reservation;
CREATE TABLE reservation (
  boat INT,
  start_time time,
  end_time time,
  start_date date,
  end_date date,
  member INT,
  dayofweek INT,
  description varchar(1000),
  triptype INT,
  CancelledBy INT,
  Purpose varchar(100),
  Created datetime,
  Updated datetime,
  Initials varchar(10),
  PRIMARY KEY (boat,start_time,start_date,dayofweek)
);

DROP TABLE IF EXISTS Trip;
CREATE TABLE Trip (
  id int(11) NOT NULL AUTO_INCREMENT,
  BoatID int(11) NOT NULL,
  OutTime datetime,
  InTime datetime,
  ExpectedIn datetime,
  Destination varchar(100),
  Meter int(11),
  TripTypeID int(11),
  Comment varchar(1000),
  CreatedDate date,
  EditDate date,
  Initials varchar(10),
  tripstat_name CHAR(20),
  DESTID int(11),
  info varchar(20),
  PRIMARY KEY (id),
  KEY tripfk (BoatID),
  KEY tripout (OutTime)
);

DROP TABLE IF EXISTS TripMember;
CREATE TABLE TripMember (
  TripID int(11) NOT NULL,
  Seat int(11) NOT NULL,
  member_id int(11),
  MemberName varchar(100),
  CreatedDate date,
  EditDate date,
  Initials varchar(10),
  PRIMARY KEY (TripID,Seat)
);

DROP TABLE IF EXISTS TripRights;
CREATE TABLE TripRights (
  trip_type int(11) NOT NULL,
  required_right varchar(30) NOT NULL,
  requirement varchar(10),
  PRIMARY KEY (trip_type,required_right)
);

DROP TABLE IF EXISTS TripType;
CREATE TABLE TripType (
  id int(11) NOT NULL AUTO_INCREMENT,
  Name varchar(100),
  tripstat_name VARCHAR(20),
  Description varchar(1000),
  Created datetime,
  Updated datetime,
  Initials varchar(10),
  Active int(11),
  PRIMARY KEY (id),
  UNIQUE KEY Navn (`Name`)
);

DROP TABLE IF EXISTS Vintervedligehold;
CREATE TABLE Vintervedligehold (
  Id int(11) NOT NULL,
  Medlemsnr varchar(8),
  Season int(11),
  HasRedKey int(11),
  DeletedReason varchar(100),
  PRIMARY KEY (Id),
  KEY vintermedlem (Medlemsnr)
);

DROP TABLE IF EXISTS boat_brand;
CREATE TABLE boat_brand (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100),
  PRIMARY KEY (id),
  UNIQUE KEY Typenavn (`name`)
);

DROP TABLE IF EXISTS boat_usage;
CREATE TABLE boat_usage (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100),
  Description varchar(1000),
  PRIMARY KEY (id),
  UNIQUE KEY Anvendelse (`name`)
);


DROP TABLE IF EXISTS event_log;
CREATE TABLE event_log (
  event varchar(500),
  event_time datetime,
  KEY eventtime (event_time)
);



DROP TABLE IF EXISTS tblMembersToRoprotokol;
CREATE TABLE tblMembersToRoprotokol (
  MemberID           INT, 
  LastName           Text (50), 
  FirstName          Text (50), 
  E_mail             Text (100), 
  MemberType         Integer, 
  JoinDate           DateTime, 
  RemoveDate         DateTime, 
  OnAddressList      Boolean NOT NULL, 
  Danish             Boolean NOT NULL
);

DROP TABLE IF EXISTS tblMembers;
CREATE TABLE tblMembers (
  MemberID int(11) NOT NULL,
  LastName varchar(50),
  FirstName varchar(50),
  Birthdate date,
  Sex varchar(2),
  Address1 varchar(70),
  Address2 varchar(70),
  Address3 varchar(70),
  Address4 varchar(70),
  Postnr varchar(8),
  City varchar(40),
  Country varchar(4),
  Telephone1 varchar(40),
  Telephone2 varchar(40),
  Fax varchar(40),
  E_mail varchar(100),
  MemberType int(11),
  Misc1 varchar(100),
  Misc2 varchar(140),
  DiverseMemo tinytext,
  Control int(11),
  OldBalance float,
  Subscription float,
  RefusedPayed float,
  Surcharge float,
  ExtraCharge float,
  ExtraChargeText varchar(100),
  AddSubscription char(1) NOT NULL,
  SendAbroad char(1) NOT NULL,
  SendInvoice char(1) NOT NULL,
  SendInvoiceExtraordinary char(1) NOT NULL,
  ReminderTextSurcharge char(1) NOT NULL,
  JoinDate date,
  JoinJournalDate date,
  RemoveDate date,
  RemoveJournaLDate date,
  SleepTo date,
  InvoiceText1 varchar(120),
  InvoiceText2 varchar(120),
  InvoiceText3 varchar(120),
  InvoiceText4 varchar(120),
  InvoiceText5 varchar(120),
  InvoiceText6 varchar(120),
  `E-mailText1` varchar(300),
  EraseTextNext char(1) NOT NULL,
  NewsletterStart char(1) NOT NULL,
  NewsletterStop char(1) NOT NULL,
  NewsletterChange char(1) NOT NULL,
  NewsletterReceives char(1) NOT NULL,
  `E-mail_News` char(1) NOT NULL,
  OnAddressList char(1) NOT NULL,
  OnTelList char(1) NOT NULL,
  Danish char(1) NOT NULL,
  CprNo char(1) NOT NULL,
  Marker char(1) NOT NULL,
  Parent int(11),
  Kundenr int(11)
);

DROP TABLE IF EXISTS tblMembersSportData;
CREATE TABLE tblMembersSportData (
  MemberID int(11),
  Roret datetime,
  TeoretiskStyrmandKursus datetime,
  Styrmand datetime,
  TeoretiskLangtursStyrmandKursus datetime,
  Langtur datetime,
  Skaergaard datetime,
  Langtur_Oeresund datetime,
  Ormen datetime,
  Svava datetime,
  Sculler datetime,
  Kajak datetime,
  Kajak_2 datetime,
  Swim_400 datetime,
  RoInstruktoer datetime,
  StyrmandInstruktoer datetime,
  ScullerInstruktoer datetime,
  KajakInstruktoer datetime,
  Kaproer datetime,
  Motorboat varchar(40),
  KeyType varchar(2),
  KeyDate datetime,
  KeyFee float,
  Stilling varchar(30),
  Ordinaert varchar(2),
  diverse1 varchar(140),
  diverse2 varchar(140)
);

DROP TABLE IF EXISTS volunteerwork;
CREATE TABLE volunteerwork (
  Medlemsnr varchar(8),
  Season int(11),
  worktype varchar(100)
);

DROP TABLE IF EXISTS Configuration;
CREATE TABLE Configuration (
  id varchar(50) NOT NULL PRIMARY KEY,
  value varchar(400) DEFAULT NULL
);
INSERT INTO Configuration (id, value) VALUES ('db_version', '1');

CREATE INDEX tripmembermemberix ON TripMember(member_id);

CREATE INDEX damageresponsible ON Damage(ResponsibleMember);

CREATE INDEX damagerepairer ON Damage(RepairerMember);

CREATE INDEX reservationmember ON reservation(member);

CREATE INDEX rightsmember ON MemberRights(member_id);

CREATE INDEX membername ON Member(FirstName,LastName);
