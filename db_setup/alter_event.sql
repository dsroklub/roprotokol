CREATE TABLE event_message (
  id         INTEGER  NOT NULL AUTO_INCREMENT,
  member_from  INTEGER,
  created    DATETIME,
  event      INTEGER,
  subject    VARCHAR(1000),
  message    VARCHAR(10000),
  FOREIGN KEY (member_from) REFERENCES Member(id),
  FOREIGN KEY (event)       REFERENCES event(id),
  PRIMARY KEY (id)
);

CREATE TABLE member_message (
 member  INTEGER,
 message INTEGER,
  FOREIGN KEY (message) REFERENCES event_message(id),
  FOREIGN KEY (member) REFERENCES Member(id),
  PRIMARY KEY(member,message)
 );


CREATE TABLE member_setting (
 member  INTEGER,
  is_public BOOLEAN NOT NULL DEFAULT FALSE,
  show_status BOOLEAN NOT NULL DEFAULT FALSE,
  show_activities BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (member) REFERENCES Member(id),
  PRIMARY KEY(member)
 );


CREATE TABLE forum_file (
  member_from     INTEGER,
  created         DATETIME,
  forum           VARCHAR(255) NOT NULL,
  filename        VARCHAR(1000) NOT NULL,
  mime_type       VARCHAR(255) NOT NULL,
  file            MEDIUMBLOB,
  expire          DATETIME,
  PRIMARY KEY (forum,filename),
  CONSTRAINT forum_file_fk FOREIGN KEY (member_from) REFERENCES Member (id)
);
