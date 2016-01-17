DELETE FROM TripRights;
INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Lokaltur","rowright","all");
INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Lokaltur","cox","cox"); 

INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Langtur","langtursstyrmand","cox");
INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Langtur","rowright","all"); 

INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Instruktion","instruktør","any");

INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Sved på Panden","rowright","all");
INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Sved på Panden","cox","cox"); 

INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Puls og program","rowright","all");
INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Puls og program","cox","cox"); 

INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Teknik+","cox","cox");
INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Teknik+","rowright","all"); 

INSERT INTO TripRights (trip_type,required_right,requirement) VALUES ("Outrigger/ScullerInstruktion","outriggerinstruktør","cox");
