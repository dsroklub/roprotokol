alter table forum_message add column  deleted    DATETIME;
ALTER TABLE worklog ADD COLUMN  boat VARCHAR(100)  REFERENCES Boat(Name) ON DELETE SET NULL ON UPDATE CASCADE;
