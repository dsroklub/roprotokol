UPDATE reservation SET configuration='racerkanin' WHERE triptype=8 and configuration='sommer';
UPDATE reservation SET configuration='instruktion' WHERE triptype=5 and configuration='sommer';
UPDATE reservation SET configuration='kajak' WHERE triptype=19 and configuration='sommer';

---- 2 ----
ALTER TABLE reservation DROP PRIMARY KEY;
ALTER TABLE reservation add id INT NOT NULL AUTO_INCREMENT KEY;

