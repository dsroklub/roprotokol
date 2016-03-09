
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
