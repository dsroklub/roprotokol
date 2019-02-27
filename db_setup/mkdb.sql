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
  MotionPlus varchar(100),
  boat_usage int(11),
  level int(11),
  Location varchar(100),
  placement_aisle INT, -- doors in DSR, Containers from left in Nordhavn
  placement_row INT, -- 1 is toward port, 2 is torwards Strandvænget
  placement_level INT, -- 0=ground, 1 .. shelves
  placement_side Char(6), -- -left, right,center
  Decommissioned datetime,
  -- TODO FOREIGN KEY (BoatType) REFERENCES BoatTypes(Name) ON DELETE Restrict ON UPDATE CASCADE,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS BoatCategory;
CREATE TABLE BoatCategory (
  id int(11) NOT NULL,
  Name varchar(100),
  Description varchar(1000),
  Created datetime,
  Updated datetime,
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
);

DROP TABLE IF EXISTS BoatRights;
CREATE TABLE BoatRights (
  boat_type int(11) NOT NULL,
  required_right varchar(30) NOT NULL,
  requirement varchar(10),
  FOREIGN KEY (required_right) REFERENCES MemberRightType(member_right) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (boat_type,required_right)
);

DROP TABLE IF EXISTS BoatType;
CREATE TABLE BoatType (
  Name varchar(100),
  Seatcount int(11),
  Description varchar(1000),
  Category int(11),
  Created datetime,
  Updated datetime,
  rights_subtype CHAR(20),
  PRIMARY KEY (`Name`)
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
  FOREIGN KEY (ResponsibleMember) REFERENCES Member(id) ON DELETE SET NULL,
  FOREIGN KEY (Boat) REFERENCES Boat(id) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS Destination;
CREATE TABLE Destination (
  id int(11),
  created_by int,
  Location varchar(10) NOT NULL DEFAULT 'DSR',
  Name varchar(100) NOT NULL,
  Meter int(11),
  Description varchar(1000),
  Created datetime,
  Updated datetime,
  ExpectedDurationNormal float,
  ExpectedDurationInstruction float,
  FOREIGN KEY (created_by) REFERENCES Member(id) ON DELETE SET NULL,
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
  Comment varchar(1000),
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
  FOREIGN KEY (Destination) REFERENCES Destination(name) ON UPDATE CASCADE ON DELETE NO ACTION,
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
  lat                DOUBLE,
  lon                DOUBLE,
  description varchar(100),
  PRIMARY KEY (`name`)
);


DROP TABLE IF EXISTS Member;
CREATE TABLE Member (
  id int(11) NOT NULL AUTO_INCREMENT,
  MemberID varchar(10) UNIQUE,
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
  JoinDate DateTime,
  RemoveDate DateTime,
  Email VARCHAR(255),
  ShowEmail VARCHAR(255),
  Gender       INTEGER,
  KommuneKode INTEGER,
  CprNo Boolean,
  PRIMARY KEY (id),
  KEY medlemnrix (MemberID)
);

DROP TABLE IF EXISTS MemberRightType;
CREATE TABLE MemberRightType (
  member_right varchar(50) NOT NULL,
  arg varchar(200) NOT NULL DEFAULT "",
  description varchar(200),
  PRIMARY KEY (member_right,arg)
);

DROP TABLE IF EXISTS MemberRights;
CREATE TABLE MemberRights (
  member_id int(11) NOT NULL,
  created_by int,
  MemberRight varchar(50) NOT NULL REFERENCES MemberRightType (member_right) ON DELETE CASCADE ON UPDATE CASCADE,
  Acquired datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  argument varchar(100) NOT NULL DEFAULT '',
  FOREIGN KEY (created_by) REFERENCES Member(id) ON DELETE SET NULL,
  FOREIGN KEY (member_id) REFERENCES Member(id) ON DELETE CASCADE,
  PRIMARY KEY (member_id,MemberRight,Acquired,argument)
);

DROP TABLE IF EXISTS reservation;
CREATE TABLE reservation (
  boat INT REFERENCES Boat(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  start_time time,
  end_time time,
  start_date date DEFAULT "1867-07-01",
  end_date date,
  member INT,
  dayofweek INT,
  description varchar(1000),
  triptype INT,
  CancelledBy INT,
  Purpose varchar(100),
  Created datetime,
  Updated datetime,
  created_by int,
  configuration VARCHAR(20) NOT NULL DEFAULT "sommer",
  FOREIGN KEY (created_by) REFERENCES Member(id) ON DELETE NO ACTION,
  PRIMARY KEY (boat,start_time,start_date,dayofweek,configuration)
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
  tripstat_name CHAR(20),
  DESTID int(11),
  info varchar(20),
  team varchar(200),
  PRIMARY KEY (id),
  FOREIGN KEY (Destination) REFERENCES Destination(name) ON UPDATE CASCADE ON DELETE NO ACTION,
  KEY tripfk (BoatID),
  KEY tripout (OutTime)
);

DROP TABLE IF EXISTS TripMember;
CREATE TABLE TripMember (
  TripID int(11) NOT NULL,
  Seat int(11) NOT NULL,
  member_id int(11) REFERENCES Member(id),
  CreatedDate date,
  EditDate date,
  FOREIGN KEY (member_id) REFERENCES Member(id) ON DELETE RESTRICT,
  FOREIGN KEY (TripID) REFERENCES Trip(id) ON DELETE CASCADE,
  PRIMARY KEY (TripID,Seat)
);

DROP TABLE IF EXISTS TripRights;
CREATE TABLE TripRights (
  trip_type int(11) NOT NULL,
  required_right varchar(30) REFERENCES MemberRightType(member_right) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
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
  MemberID           varchar(10),
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

DROP TABLE IF EXISTS worklog;
CREATE TABLE worklog (
  member_id        int REFERENCES Member(id) ON DELETE SET NULL,
  created          datetime NOT NULL default NOW(),
  workdate         datetime NOT NULL,
  work             varchar(1000),
  hours            NUMERIC(6,2) NOT NULL,
  forum            VARCHAR(255) REFERENCES forum(name) ON UPDATE CASCADE ON DELETE CASCADE,
  created_by       int REFERENCES Member(id) ON DELETE SET NULL
);

DROP TABLE IF EXISTS Configuration;
CREATE TABLE Configuration (
  id varchar(50) NOT NULL PRIMARY KEY,
  value varchar(400) DEFAULT NULL
);

INSERT INTO Configuration (id, value) VALUES ('db_version', '1');

DROP TABLE IF EXISTS status;
CREATE TABLE status (
  sculler_open INTEGER NOT NULL DEFAULT 0
  reservation_configuration VARCHAR(20) NOT NULL DEFAULT "sommer";
);
INSERT INTO status (sculler_open) VALUES (0);


CREATE INDEX tripmembermemberix ON TripMember(member_id);

CREATE INDEX damageresponsible ON Damage(ResponsibleMember);

CREATE INDEX damagerepairer ON Damage(RepairerMember);

CREATE INDEX reservationmember ON reservation(member);

CREATE INDEX rightsmember ON MemberRights(member_id);

CREATE INDEX membername ON Member(FirstName,LastName);



-- Styrmandinstruktion
CREATE TABLE instruction_team (
  name            VARCHAR(30) PRIMARY KEY,
  description      VARCHAR(2000),
  instructor      INTEGER,
  FOREIGN KEY (instructor) REFERENCES Member(id)
);


CREATE TABLE instruction_team_member (
  team varchar(30),
  member_id       INTEGER
);

CREATE TABLE instruction_team_participation (
  team varchar(30),
  member_id       INTEGER
);

DROP TABLE IF EXISTS team_requests;
CREATE TABLE team_requests (
  date_enter            date,
  member_id             INTEGER PRIMARY KEY,
  preferred_time        VARCHAR(30), -- e.g, season, week, weekday,
  team                  varchar(300),
  wish                  varchar(300),
  activities            varchar(3000),
  preferred_intensity   varchar(300),
  comment               varchar(5000),
  phone                 varchar(40),
  email                 varchar(500),
  FOREIGN KEY (member_id) REFERENCES Member(id)

);

DROP TABLE IF EXISTS course_requirement;
CREATE TABLE course_requirement (
       name varchar(200),
       description varchar(2000),
       expiry    INTEGER, -- months, NULL for non expiery
       dispensation BOOL
);


INSERT INTO course_requirement VALUES ("landgang","Landgang på åben kyst",12,false);
INSERT INTO course_requirement VALUES ("tillægning","Tillægning ved ponton",12,false);
INSERT INTO course_requirement VALUES ("entring","entringsøvelse",12,true);
INSERT INTO course_requirement VALUES ("kanal","Kanaltur i Københavns havn",12,true);

DROP TABLE IF EXISTS course_requirement_pass;
CREATE TABLE course_requirement_pass (
       requirement  varchar(300),
       member_id    INTEGER,
       passed       DATE,
       PRIMARY KEY (member_id,requirement)
);

-- INSERT INTO course_requirement_pass VALUE ('landgang',6784,'2017-03-31');

CREATE TABLE authentication (
  member_id             INTEGER NOT NULL PRIMARY KEY,
  password              VARCHAR(255) NOT NULL,
  newpassword           VARCHAR(255),
  role                  VARCHAR(255),
  FOREIGN KEY (member_id) REFERENCES Member(id)
);

CREATE TABLE cox_log (
  timestamp             DATETIME,
  member_id           VARCHAR(10),
  action              VARCHAR(255),
 entry               VARCHAR(20000) NOT NULL
 );


--INSERT INTO authentication(6270,"hest","coxaspirant");

--- Events

CREATE TABLE event_category (
  name                   VARCHAR(255) PRIMARY KEY,
  description            VARCHAR(255),
  priority               INTEGER
);

INSERT INTO event_category VALUE('rotur','rotur',1);
INSERT INTO event_category VALUE('langtur','langtur i Danmark eller udlandet',2);
INSERT INTO event_category VALUE('fest','vilde fester i DSR',10);

--    DROP TABLE event;
CREATE TABLE event (
  id                     INTEGER  NOT NULL AUTO_INCREMENT,
  owner                  INTEGER,
  auto_administer        BOOLEAN default false,
  boat_category          INTGER,
  boats                  VARCHAR(255),
  start_time             DATETIME,
  end_time               DATETIME,
  distance               INTEGER, -- Planned distance
  trip_type              INTEGER,
  open                   BOOLEAN default true,
  last_email             DATETIME,
  max_participants       INTEGER,
  location               VARCHAR(255),
  status                 VARCHAR(255) DEFAULT "on",
  destination            VARCHAR(255),
  name                   VARCHAR(255),
  category               VARCHAR(255),
  preferred_intensity    VARCHAR(300),
  comment                VARCHAR(5000),
  FOREIGN KEY (owner) REFERENCES Member(id),
  FOREIGN KEY (trip_type) REFERENCES TripType(id),
  PRIMARY KEY(id)
);

CREATE TABLE event_role (
  name       VARCHAR(255) PRIMARY KEY,
  description VARCHAR(5000),
  can_post   BOOLEAN,
  is_leader  BOOLEAN,
  is_cox     BOOLEAN
);

INSERT INTO event_role (name, description,can_post,is_leader,is_cox) VALUE ('member','deltager',1,0,0);
INSERT INTO event_role (name, description,can_post,is_leader,is_cox) VALUE ('owner','ejer',1,1,0);
INSERT INTO event_role (name, description,can_post,is_leader,is_cox) VALUE ('wait','venteliste',0,0,0);
INSERT INTO event_role (name, description,can_post,is_leader,is_cox) VALUE ('supplicant','ansøger',0,0,0);

CREATE TABLE event_boat_type (
  event      INTEGER,
  boat_type  INTEGER,
  FOREIGN KEY (boat_type) REFERENCES BoatType(id)
);

CREATE TABLE event_member (
  member     INTEGER,
  event      INTEGER,
  comment    VARCHAR(4096),
  value      FLOAT,
  enter_time DATETIME, -- default NOW(),
  role       VARCHAR(255), -- waiting, cox, any, leader, admin
  FOREIGN KEY (member) REFERENCES Member(id),
  FOREIGN KEY (event) REFERENCES event(id),
  PRIMARY KEY (member,event)
);

CREATE TABLE event_invitees (
  member     INTEGER,
  event      INTEGER,
  comment    VARCHAR(255),
  role       VARCHAR(255), -- waiting, cox, any, leader, admin
  FOREIGN KEY (member) REFERENCES Member(id),
  FOREIGN KEY (event) REFERENCES event(id)
);


CREATE TABLE forum (
  name   VARCHAR(255) PRIMARY KEY NOT NULL,
  description VARCHAR(255),
  owner     INTEGER,
  is_open      BOOLEAN DEFAULT TRUE,
  is_public     BOOLEAN DEFAULT TRUE,
  boat          VARCHAR(30) REFERENCES Boat(Name) ON DELETE SET NULL,
  created_by int REFERENCES Member(id),
  forumtype       VARCHAR(50) DEFAULT "generic",  // generic, vedligehold, tur
  FOREIGN KEY (owner) REFERENCES Member(id) NOT NULL
);

-- INSERT INTO forum VALUE('roaftaler','generelle roaftaler');
-- INSERT INTO forum VALUE('kaproning','for kaproere');


CREATE TABLE forum_subscription (
  member     INTEGER,
  forum      VARCHAR(255),
  role       VARCHAR(255) NOT NULL, -- waiting, cox, any, leader, admin
  comment    VARCHAR(4096),
  value      FLOAT,
  FOREIGN KEY (forum) REFERENCES forum(name) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (member) REFERENCES Member(id),
  PRIMARY KEY(member,forum)
);

CREATE TABLE forum_message (
  id         INTEGER  NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_from  INTEGER,
  created    DATETIME,
  forum      VARCHAR(255),
  subject    VARCHAR(1000),
  message    VARCHAR(10000),
  FOREIGN KEY (forum) REFERENCES forum(name) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (member_from) REFERENCES Member(id)
);

CREATE TABLE forum_file (
  member_from     INTEGER,
  created         DATETIME,
  forum           VARCHAR(255) NOT NULL,
  filename        VARCHAR(255) NOT NULL,
  mime_type       VARCHAR(255) NOT NULL,
  file            MEDIUMBLOB,
  folder          VARCHAR(255),
  expire          DATETIME,
  PRIMARY KEY (forum,filename),
  CONSTRAINT forum_file_fk FOREIGN KEY (member_from) REFERENCES Member (id)
);

CREATE TABLE event_message (
  id         INTEGER  NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_from  INTEGER,
  created    DATETIME,
  event      INTEGER NULL,
  subject    VARCHAR(1000),
  message    VARCHAR(10000),
  FOREIGN KEY (member_from) REFERENCES Member(id),
  FOREIGN KEY (event)       REFERENCES event(id),
--  PRIMARY KEY (id)
);

CREATE TABLE private_message (
  id         INTEGER  NOT NULL AUTO_INCREMENT PRIMARY KEY,
  member_from  INTEGER,
  created    DATETIME,
  subject    VARCHAR(1000),
  message    VARCHAR(10000),
  FOREIGN KEY (member_from) REFERENCES Member(id)
);

CREATE TABLE event_forum (
  event      INTEGER,
  forum      VARCHAR(255) NOT NULL,
  FOREIGN KEY (forum)       REFERENCES forum(name) ON UPDATE CASCADE,
  FOREIGN KEY (event)       REFERENCES event(id),
  PRIMARY KEY (event,forum)
);


CREATE TABLE member_message (
 member  INTEGER,
 message INTEGER,
  FOREIGN KEY (message) REFERENCES event_message(id),
  FOREIGN KEY (member) REFERENCES Member(id),
  PRIMARY KEY(member,message)
 );


CREATE TABLE member_setting (
 member  INTEGER,
  is_public BOOLEAN NOT NULL DEFAULT FALSE,
  show_status BOOLEAN NOT NULL DEFAULT FALSE,
  show_activities BOOLEAN NOT NULL DEFAULT FALSE,
  notification_email VARCHAR(255),
  email_shared VARCHAR(255),
  phone VARCHAR(20),
  FOREIGN KEY (member) REFERENCES Member(id),
  PRIMARY KEY(member)
 );


-- GYM

CREATE TABLE team (
  name varchar(30),
  description varchar(200),
  dayofweek     varchar(20),
  timeofday   char(5),
  teacher     varchar(200),
  teamkey     varchar(200),
  PRIMARY KEY (name,dayofweek,timeofday)
);

CREATE TABLE team_participation (
  team            varchar(30),
  member_id       int(11),
  start_time      datetime,
  classdate       date,
  dayofweek       varchar(20),
  timeofday       char(5),
  PRIMARY KEY (team, member_id, classdate,dayofweek)
);

DROP TABLE IF EXISTS weekday;
CREATE TABLE weekday (
  name varchar(10),
  no   INTEGER,
  language CHAR(2)
  );

INSERT INTO weekday (name,no,language) VALUES
  ("Mandag","1","da"),
  ("Tirsdag","2","da"),
  ("Onsdag","3","da"),
  ("Torsdag","4","da"),
  ("Fredag","5","da"),
  ("Lørdag","6","da"),
  ("Søndag","7","da");


CREATE TABLE season (
   season       INTEGER,
   summer_start DATETIME,
   summer_end   DATETIME
);

DELETE FROM season;
INSERT INTO season (season,summer_start,summer_end) VALUES
 (2006,"2006-03-26","2006-10-29"),
 (2007,"2007-03-25","2007-10-28"),
 (2008,"2008-03-30","2008-10-26"),
 (2009,"2009-03-29","2009-10-25"),
 (2010,"2010-03-28","2011-10-31"),
 (2011,"2011-03-27","2011-10-30"),
 (2012,"2012-03-25","2012-10-28"),
 (2013,"2013-03-31","2013-10-27"),
 (2014,"2014-03-30","2014-10-26"),
 (2015,"2015-03-29","2015-10-25"),
 (2016,"2016-03-27","2016-10-30"),
 (2017,"2017-03-26","2017-10-29"),
 (2018,"2018-03-25","2018-10-28"),
 (2019,"2019-03-31","2019-10-27"),
 (2020,"2020-03-29","2020-10-25"),
 (2021,"2020-03-28","2021-10-31"),
 (2022,"2022-03-27","2022-10-30"),
 (2023,"2023-03-26","2023-10-29"),
 (2024,"2024-03-31","2024-10-27"),
 (2025,"2025-03-30","2025-10-26"),
 (2026,"2026-03-29","2026-10-25"),
 (2027,"2027-03-28","2027-10-31"),
 (2028,"2028-03-26","2028-10-29"),
 (2029,"2029-03-25","2029-10-28"),
 (2030,"2030-03-31","2030-10-27"),
 (2031,"2031-03-30","2031-10-26"),
 (2032,"2032-03-28","2032-10-31"),
 (2033,"2033-03-27","2033-10-30"),
 (2034,"2034-03-26","2034-10-29"),
 (2035,"2035-03-25","2035-10-28"),
 (2036,"2036-03-30","2036-10-26"),
 (2037,"2037-03-29","2037-10-25"),
 (2038,"2038-03-28","2038-10-31"),
 (2039,"2039-03-27","2039-10-30");


INSERT INTO Member (id,MemberId,FirstName,LastName) VALUES (-1,"baadhal","Bådhallen","DSR");
