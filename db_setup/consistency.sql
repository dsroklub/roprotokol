-- select  * From Trip Where InTime<OutTime;
UPDATE Member SET Birthday=NULL where CAST(Birthday AS CHAR(20))='0000-00-00 00:00:00';
-- ALTER TABLE Member CONVERT TO CHARACTER SET utf8;
ALTER TABLE Member CONVERT TO CHARACTER SET utf8mb4;
