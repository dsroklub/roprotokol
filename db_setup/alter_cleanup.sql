ALTER Table Trip drop column season;
ALTER Table TripMember drop column season;

ALTER TABLE TripMember  DROP COLUMN MemberName;


ALTER Table Boat Drop column MotionPlus;

DROP TABLE IF EXISTS volunteerwork;

DROP TABLE Vintervedligehold;

ALTER TABLE BoatCategory DROP COLUMN Initials;
ALTER TABLE Boat DROP COLUMN Initials;


ALTER TABLE BoatConfiguration DROP COLUMN Initialer;
ALTER TABLE BoatType DROP COLUMN Initials;
ALTER TABLE Damage DROP COLUMN Initials;
ALTER TABLE Destination DROP COLUMN Initials;

ALTER TABLE Member DROP COLUMN Initials;
ALTER TABLE reservation DROP COLUMN Initials;
ALTER TABLE Trip DROP COLUMN Initials;
ALTER TABLE TripMember DROP COLUMN Initials;
ALTER TABLE TripType DROP COLUMN Initials;





