DELETE FROM workimport;
LOAD DATA LOCAL INFILE '/data/import/vtimer.csv'
INTO TABLE workimport
FIELDS TERMINATED BY ',' ENCLOSED BY '"' ESCAPED BY '\\' 
LINES TERMINATED BY '\n';
--IGNORE 1 ROWS;
DELETE FROM workimport WHERE member_id="";
DELETE FROM worker;
INSERT INTO worker(member_id,assigner,requirement,description) SELECT id,6270,hours,'vintervedligehold' FROM workimport,Member WHERE Member.MemberId=workimport.member_id;
