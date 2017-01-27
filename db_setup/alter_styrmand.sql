DROP TABLE IF EXISTS instruction_team_participation;

DROP TABLE IF EXISTS instruction_team;
CREATE TABLE instruction_team (
  name            VARCHAR(30) PRIMARY KEY, 
  description      VARCHAR(2000),
  instructor      INTEGER,
  FOREIGN KEY (instructor) REFERENCES Member(id)
);


CREATE TABLE instruction_team_participation (
  team varchar(30),
  member_id       INTEGER,
  in_time         datetime
);


DROP TABLE IF EXISTS team_requests;
CREATE TABLE team_requests (
  date_enter            date,
  member_id             INTEGER,
  preferred_time        VARCHAR(30), -- e.g, season, week, weekday,
  team                  varchar(30),
  preferred_intensity   varchar(30),
  comment               varchar(5000),
  FOREIGN KEY (member_id) REFERENCES Member(id)

);

DROP TABLE IF EXISTS course_requirement;
CREATE TABLE course_requirement (
       name varchar(200),
       description varchar(2000),
       expiry    INTEGER -- months, NULL for non expiery
);

DROP TABLE IF EXISTS course_requirement_passes;
CREATE TABLE course_requirement_passes (
       requirement  varchar(30),
       member_id    INTEGER
);
