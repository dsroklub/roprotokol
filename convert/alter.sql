
Alter table Error_Trip add column BoatID INT NOT NULL;
Alter table Error_Trip add column TripTypeID INT;
Alter table Error_Trip add column CreatedDate DATE;
Alter table Error_Trip add column EditDate DATE;


ALTER TABLE BoatType CHANGE Name Name varchar(100) NOT NULL UNIQUE;
ALTER TABLE Member CHANGE MemberID MemberID VARCHAR(10) NOT NULL;
ALTER TABLE Member ADD COLUMN log VARCHAR(2000);
