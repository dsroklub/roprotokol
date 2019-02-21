
ALTER TABLE status ADD COLUMN reservation_configuration VARCHAR(20) NOT NULL DEFAULT "sommer";

ALTER table reservation ADD COLUMN configuration VARCHAR(20) NOT NULL DEFAULT "sommer";


ALTER TABLE reservation DROP PRIMARY KEY;

ALTER TABLE reservation ADD PRIMARY KEY (boat,start_time,start_date,dayofweek,configuration);


alter table reservation rename foo;
CREATE TABLE reservation (
  boat INT REFERENCES Boat(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  start_time time,
  end_time time,
  start_date date DEFAULT "1867-07-01",
  end_date date,
  member INT,
  dayofweek INT,
  description varchar(1000),
  triptype INT,
  CancelledBy INT,
  Purpose varchar(100),
  Created datetime,
  Updated datetime,
  created_by int,
  configuration VARCHAR(20) NOT NULL DEFAULT "sommer",
  FOREIGN KEY (created_by) REFERENCES Member(id) ON DELETE NO ACTION,
  PRIMARY KEY (boat,start_time,start_date,dayofweek,configuration)
);


INSERT INTO reservation select * from foo;

INSERT INTO reservation SELECT boat,start_time,end_time,start_date,end_date,member,dayofweek,description,triptype,CancelledBy,Purpose,Created,Updated,created_by,'efterår' FROM reservation WHERE configuration="sommer" AND dayofweek >0;
