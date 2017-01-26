DROP TABLE IF EXISTS team;

CREATE TABLE team (
  name varchar(30),
  description varchar(200),
  dayofweek     varchar(20),
  timeofday   char(5),
  teacher     varchar(200),
  teamkey     varchar(200),
  PRIMARY KEY (name,dayofweek,timeofday)
);

INSERT INTO team (teamkey,dayofweek,timeofday,name,teacher) VALUES
("14","Mandag",  "16:30","Yoga","Petra Mrvikova"),
("15","Mandag",  "18:00","Yoga","Petra Mrvikova"),
("23","Tirsdag", "17:00","Core og cirkel","Jannik N"),
("24","Tirsdag", "18:30","Core og cirkel","Jannik N"),
("33","Onsdag",  "17:00","Gotved","Jeanette"),
("21","Tirsdag", "17:00","Morgengymnastik","Asbjørn"),
("42","Torsdag", "17:00","Core og cirkel","Mathilde"),
("43","Torsdag", "18:30","Core og cirkel","Mathilde"),
("45","Torsdag", "20:00","KS","Petter"),
("51","Fredag",  "07:00","Morgengymnastik","Asbjørn");



DROP TABLE IF EXISTS team_participation;
CREATE TABLE team_participation (
  team varchar(30),
  member_id       int(11),
  start_time         datetime,
  classdate           date,
  PRIMARY KEY (team, member_id, classdate)
);



