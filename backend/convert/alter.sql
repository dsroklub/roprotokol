ALTER TABLE Damage change id id INT NOT NULL AUTO_INCREMENT;
ALTER TABLE BoatType CHANGE id id INT NOT NULL AUTO_INCREMENT;

UPDATE Trip set Destination = TRIM(Destination);
Alter table Error_Trip add column BoatID INT NOT NULL;
Alter table Error_Trip add column TripTypeID INT;
Alter table Error_Trip add column CreatedDate DATE;
Alter table Error_Trip add column EditDate DATE;

UPDATE TripRights SET required_right='instructor' where required_right="instruktør";
UPDATE TripRights SET required_right='longdistance' where required_right="langtursstyrmand";
UPDATE TripRights SET required_right='outrigger_instructor' where required_right="outriggerinstruktør";
UPDATE Destination SET Location="Nordhavn" WHERE Location="Forpost";

ALTER TABLE BoatType CHANGE Name Name varchar(100) NOT NULL UNIQUE;
ALTER TABLE Member CHANGE MemberID MemberID VARCHAR(10) NOT NULL;
