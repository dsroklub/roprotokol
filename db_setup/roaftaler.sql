CREATE TABLE event_category (
  name                   VARCHAR(255),
  description            VARCHAR(255),
  priority               INTEGER
);

INSERT INTO event_category VALUE('rotur','rotur',1);
INSERT INTO event_category VALUE('langtur','langtur i Danmark eller udlandet',2);
INSERT INTO event_category VALUE('fest','vilde fester i DSR',10);

--    DROP TABLE event;
CREATE TABLE event (
  id                     INTEGER  NOT NULL AUTO_INCREMENT,
  owner                  INTEGER,  
  boat_category          INTEGER,
  start_time             DATETIME,
  end_time               DATETIME,
  distance               INTEGER, -- Planned distance
  trip_type              INTEGER,
  last_email             DATETIME,
  max_participants       INTEGER,
  location               VARCHAR(255),
  name                   VARCHAR(255),
  category               VARCHAR(255),
  preferred_intensity    VARCHAR(300),
  comment                VARCHAR(5000),
  FOREIGN KEY (owner) REFERENCES Member(id), 
  FOREIGN KEY (boat_category) REFERENCES BoatCategory(id),
  FOREIGN KEY (trip_type) REFERENCES TripType(id),
  PRIMARY KEY(id)
);

CREATE TABLE event_role (
  name       VARCHAR(255),
  can_post   BOOLEAN,
  is_leader  BOOLEAN,
  is_cox     BOOLEAN  
);

CREATE TABLE event_boat_type (
  event      INTEGER,
  boat_type  INTEGER,
  FOREIGN KEY (boat_type) REFERENCES BoatType(id)
);

CREATE TABLE event_member (
  member     INTEGER,
  event      INTEGER,
  enter_time DATETIME  DEFAULT NOW(),
  role       VARCHAR(255), -- waiting, cox, any, leader, admin
  FOREIGN KEY (member) REFERENCES Member(id),
  FOREIGN KEY (event) REFERENCES event(id)
);

CREATE TABLE event_invitees (
  member     INTEGER,
  event      INTEGER,
  comment    VARCHAR(255),
  role       VARCHAR(255), -- waiting, cox, any, leader, admin
  FOREIGN KEY (member) REFERENCES Member(id),
  FOREIGN KEY (event) REFERENCES event(id)
);
   
CREATE TABLE forum (
  name   VARCHAR(255) PRIMARY KEY NOT NULL,
  description VARCHAR(255)
);

INSERT INTO forum VALUE('roaftaler','generelle roaftaler');
INSERT INTO forum VALUE('kaproning','for kaproere');


CREATE TABLE forum_subscription (
  member     INTEGER,
  forum      VARCHAR(255),
  role       VARCHAR(255), -- waiting, cox, any, leader, admin
  FOREIGN KEY (forum) REFERENCES interest(name),
  FOREIGN KEY (member) REFERENCES Member(id)
);
