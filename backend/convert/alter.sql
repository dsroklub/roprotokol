alter table Damage change id id INT NOT NULL AUTO_INCREMENT;

UPDATE Trip set Destination = TRIM(Destination);
Alter table Error_Trip add column BoatID INT NOT NULL;
Alter table Error_Trip add column TripTypeID INT;
Alter table Error_Trip add column CreatedDate DATE;
Alter table Error_Trip add column EditDate DATE;
