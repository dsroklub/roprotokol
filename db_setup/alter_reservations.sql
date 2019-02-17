
ALTER TABLE status ADD COLUMN reservation_configuration VARCHAR(20) NOT NULL DEFAULT "sommer";

ALTER TABLE reservation DROP PRIMARY KEY;
ALTER TABLE reservation ADD PRIMARY KEY (boat,start_time,start_date,dayofweek,configuration);


alter table reservation rename foo;


INSERT INTO reservation SELECT boat,start_time,end_time,start_date,end_date,member,dayofweek,description,triptype,CancelledBy,Purpose,Created,Updated,created_by,'efterår' FROM reservation WHERE configuration="sommer" AND dayofweek >0;
