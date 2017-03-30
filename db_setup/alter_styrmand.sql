DROP TABLE IF EXISTS instruction_team_participation;

DROP TABLE IF EXISTS instruction_team;
CREATE TABLE instruction_team (
  name            VARCHAR(30) PRIMARY KEY, 
  description      VARCHAR(2000),
  instructor      INTEGER,
  FOREIGN KEY (instructor) REFERENCES Member(id)
);


CREATE TABLE instruction_team_member (
  team varchar(30),
  member_id       INTEGER
);

CREATE TABLE instruction_team_participation (
  team varchar(30),
  member_id       INTEGER
);



DROP TABLE IF EXISTS team_requests;
CREATE TABLE team_requests (
  date_enter            date,
  member_id             INTEGER PRIMARY KEY,
  preferred_time        VARCHAR(30), -- e.g, season, week, weekday,
  team                  varchar(300),
  wish                  varchar(300),
  activities            varchar(3000),
  preferred_intensity   varchar(300),
  comment               varchar(5000),
  phone                 varchar(40),
  email                 varchar(500),    
  FOREIGN KEY (member_id) REFERENCES Member(id)

);

DROP TABLE IF EXISTS course_requirement;
CREATE TABLE course_requirement (
       name varchar(200),
       description varchar(2000),
       expiry    INTEGER, -- months, NULL for non expiery
       dispensation BOOL
);


INSERT INTO course_requirement VALUES ("landgang","Landgang på åben kyst",12,false);
INSERT INTO course_requirement VALUES ("tillægning","Tillægning ved ponton",12,false);
INSERT INTO course_requirement VALUES ("entring","entringsøvelse",12,true);
INSERT INTO course_requirement VALUES ("kanal","Kanaltur i Københavns havn",12,true);

DROP TABLE IF EXISTS course_requirement_pass;
CREATE TABLE course_requirement_pass (
       requirement  varchar(30),
       member_id    INTEGER
);
