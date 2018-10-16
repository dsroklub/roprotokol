ALTER TABLE BoatConfiguration DROP COLUMN Initialer;
ALTER TABLE BoatCategory DROP COLUMN Initials;
ALTER TABLE BoatType DROP COLUMN Initials;
ALTER TABLE Boat DROP COLUMN Initials;
ALTER TABLE Damage DROP COLUMN Initials;
ALTER TABLE Member DROP COLUMN Initials;
ALTER TABLE Destination DROP COLUMN Initials;

ALTER TABLE Destination ADD column created_by INT;
ALTER TABLE Destination ADD FOREIGN KEY (created_by) REFERENCES Member(id) ON DELETE SET NULL;

ALTER TABLE reservation MODIFY COLUMN start_date date DEFAULT "1867-07-01";
ALTER TABLE reservation DROP COLUMN Initials;
ALTER TABLE reservation ADD COLUMN created_by INT;
ALTER TABLE reservation ADD FOREIGN KEY (created_by) REFERENCES Member(id) ON DELETE RESTRICT;



ALTER TABLE TripRights ADD FOREIGN KEY (required_right) REFERENCES MemberRightType(member_right) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE BoatRights ADD FOREIGN KEY (required_right) REFERENCES MemberRightType(member_right) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE Damage ADD FOREIGN KEY (Boat) REFERENCES Boat(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- UPDATE Damage SET ResponsibleMember=NULL WHERE ResponsibleMember=0;

-- ALTER TABLE Damage ADD FOREIGN KEY (ResponsibleMember) REFERENCES Member(id) ON DELETE SET NULL;

-- ALTER TABLE reservation ADD FOREIGN KEY (boat) REFERENCES Boat(id) ON DELETE RESTRICT ON UPDATE CASCADE;


ALTER TABLE TripMember DROP COLUMN Initials;
ALTER TABLE TripType DROP COLUMN Initials;



ALTER TABLE MemberRights ADD column created_by INT;
ALTER TABLE MemberRights ADD FOREIGN KEY (created_by) REFERENCES Member(id) ON DELETE SET NULL;
-- TO HERE


DELETE FROM TripMember where TripID NOT IN (SELECT id FROM Trip);

ALTER TABLE TripMember ADD FOREIGN KEY (member_id) REFERENCES Member(id) ON UPDATE CASCADE ON DELETE RESTRICT;


ALTER TABLE TripMember DROP COLUMN Season;
ALTER TABLE TripMember DROP COLUMN MemberName;


ALTER TABLE TripMember ADD FOREIGN KEY (TripID) REFERENCES Trip(id) ON DELETE CASCADE;

ALTER TABLE Boat ADD COLUMN boat_type VARCHAR(100) REFERENCES BoatTypes(Name) ON DELETE Restrict ON UPDATE CASCADE;
UPDATE Boat SET boat_type = (SELECT Name From BoatType WHERE id=BoatType);

ALTER TABLE Error_Trip ADD FOREIGN KEY (Destination) REFERENCES Destination(name) ON UPDATE CASCADE ON DELETE SET NULL;
ALTER TABLE Boat ADD FOREIGN KEY (boat_type) REFERENCES BoatType(Name) ON DELETE Restrict ON UPDATE CASCADE,
DROP Trip.DESTID

-- TO HERE


ALTER TABLE BoatRights CHANGE COLUMN boat_type  bt INT;
ALTER TABLE BoatRights ADD COLUMN boat_type VARCHAR(100) NOT NULL REFERENCES BoatTypes(Name) ON DELETE Restrict ON UPDATE CASCADE;
UPDATE BoatRights SET boat_type = (SELECT Name From BoatType WHERE id=bt);
ALTER TABLE BoatRights DROP PRIMARY KEY;
ALTER TABLE BoatRights DROP COLUMN bt;
ALTER TABLE BoatRights ADD PRIMARY KEY (boat_type,requirement,required_right);


ALTER TABLE event_boat_type CHANGE COLUMN boat_type  bt INT;
ALTER TABLE event_boat_type ADD COLUMN boat_type VARCHAR(100) NOT NULL REFERENCES BoatTypes(Name) ON DELETE Restrict ON UPDATE CASCADE;
UPDATE event_boat_type SET boat_type = (SELECT Name From BoatType WHERE id=bt);
ALTER TABLE event_boat_type DROP FOREIGN KEY event_boat_type_ibfk_1;
ALTER TABLE event_boat_type DROP COLUMN bt;

ALTER TABLE BoatType CHANGE COLUMN id id INT;
ALTER TABLE BoatType DROP PRIMARY KEY;
ALTER TABLE BoatType ADD PRIMARY KEY (Name);
ALTER TABLE BoatType DROP COLUMN id;

ALTER TABLE Boat DROP COLUMN BoatType;
ALTER TABLE Boat DROP COLUMN MotionPlus;




--TODO
--  Trip.TripTypeID to Trip.trip_type referencing Triptype.Name, update PHP;
-- Affects TripRights



