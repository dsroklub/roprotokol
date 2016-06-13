ALTER DATABASE roprotokol CHARACTER SET utf8 COLLATE utf8_general_ci;
show variables like 'char%';

ALTER TABLE MemberRightType DROP PRIMARY KEY;
ALTER TABLE MemberRightType ADD COLUMN arg CHAR(20);
ALTER TABLE TripType ADD COLUMN   tripstat_name VARCHAR(20);

DELETE FROM MemberRightType WHERE member_right="instructor";
INSERT INTO  MemberRightType (member_right,arg,description) VALUES ("instructor","row","instruktør inrigger");
INSERT INTO  MemberRightType (member_right,arg,description) VALUES ("instructor","outrigger","instruktør, sculler");
INSERT INTO  MemberRightType (member_right,arg,description) VALUES ("instructor","kajak","instruktør, kajak");

CREATE TABLE rights_subtype (
  name VARCHAR(100) KEY,
  Description VARCHAR(1000)
);

INSERT INTO rights_subtype VALUES ('row','inrigger, coastal, gig');
INSERT INTO rights_subtype VALUES ('kayak','kajakker');
INSERT INTO rights_subtype VALUES ('outrigger','outrigger,sculler');

ALTER TABLE BoatType ADD COLUMN rights_subtype CHAR(20);

ALTER TABLE Boat  DROP COLUMN Placement;
