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
       requirement  VARCHAR(255),
       member_id    INTEGER,
       passed       DATE,
       PRIMARY KEY (member_id,requirement)
);

INSERT INTO course_requirement_pass VALUE ('landgang',6784,'2017-03-31');

CREATE TABLE authentication (
  member_id             INTEGER NOT NULL PRIMARY KEY,
  password              VARCHAR(255) NOT NULL,
  newpassword           VARCHAR(255),
  role                  VARCHAR(255),
  FOREIGN KEY (member_id) REFERENCES Member(id)
);



CREATE TABLE cox_log (
  timestamp             DATETIME,
  member_id           VARCHAR(10),
  action              VARCHAR(255),                      
 entry               VARCHAR(20000) NOT NULL
 );
  


INSERT INTO authentication(6270,"hest","coxaspirant");
