
Alter table Error_Trip add column BoatID INT NOT NULL;
Alter table Error_Trip add column TripTypeID INT;
Alter table Error_Trip add column CreatedDate DATE;
Alter table Error_Trip add column EditDate DATE;

ALTER TABLE Error_TripMember CHANGE TridID ErrorTripID INT;

ALTER TABLE BoatType CHANGE Name Name varchar(100) NOT NULL UNIQUE;
ALTER TABLE Member CHANGE MemberID MemberID VARCHAR(10) NOT NULL;
ALTER TABLE Member ADD COLUMN log VARCHAR(2000);

UPDATE Trip SET Trip.OutTime=Trip.InTime WHERE Trip.OutTime IS NULL;
UPDATE Trip SET Trip.OutTime="2025-05-03 12:00:00" WHERE Trip.OutTime="1025-05-03 12:00:00";

select * from Trip WHERE Trip.OutTime < '2000';

ALTER TABLE Error_TripMember CHANGE TripID ErrorTripID INT;

UPDATE Error_Trip SET Distance = Distance*1000 WHERE Distance < 400;

UPDATE Error_Trip SET TimeOut=(SELECT OutTime From Trip WHERE Trip.id=Error_Trip.Trip) WHERE TimeOut="0000-00-00 00:00:00";
UPDATE Error_Trip SET TimeIn=(SELECT InTime From Trip WHERE Trip.id=Error_Trip.Trip) WHERE TimeIn="0000-00-00 00:00:00";
UPDATE Error_Trip SET TimeIn=(SELECT InTime From Trip WHERE Trip.id=Error_Trip.Trip) WHERE TimeIn=IS NULL;
UPDATE Error_Trip SET BoatID=(SELECT BoatID From Trip WHERE Trip.id=Error_Trip.Trip) WHERE Boat IS NULL;
UPDATE Error_Trip SET BoatID=(SELECT BoatID From Trip WHERE Trip.id=Error_Trip.Trip) WHERE Boat=0;
UPDATE Error_Trip SET Distance=(SELECT Distance From Trip WHERE Trip.id=Error_Trip.Trip) WHERE Distance=0;

UPDATE Error_Trip SET BoatID=(SELECT id From Boat WHERE Boat.name=Boat) WHERE BoatID=0 AND Boat IS NOT NULL AND Boat!="";
UPDATE Error_Trip SET Fixed=3 WHERE Error_Trip.Trip NOT IN (Select id from Trip);
