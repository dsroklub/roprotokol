DROP TABLE IF EXISTS team;
CREATE TABLE team (
  name varchar(30),
  description varchar(200)
);

DROP TABLE IF EXISTS team_participation;
CREATE TABLE team_participation (
  team varchar(30),
  member_id       int(11),
  start_time         datetime,
  classdate           date,
  PRIMARY KEY (team,member_id,start_time) -- FIXME date
);


DROP TABLE IF EXISTS instruction_team_participation;
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
  preferreded_intensity varchar(30),
  comment               varchar(5000),
  FOREIGN KEY (member_id) REFERENCES Member(id)

);

DROP TABLE IF EXISTS course_requirement;
CREATE TABLE course_requirement (
       name varchar(200),
       desciption varchar(2000),
       expiery    INTEGER -- months, NULL for non expiery
);

DROP TABLE IF EXISTS course_requirement_passes;
CREATE TABLE course_requirement_passes (
       requirement  varchar(30),
       member_id    INTEGER
);

insert into teams values ('gotved','Gotved Gymnastic');
