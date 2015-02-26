-- Make data more interesting for testing
-- Never use in production

UPDATE Trip set InTime=NULL WHERE TripID IN (49328, 49223,49141, 49058, 49009 );
