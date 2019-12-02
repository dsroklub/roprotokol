export:
	mysqldump -u roprotokol roprotokol -p -e -c --no-create-info --tables reservation Boat BoatCategory BoatRights BoatType Damage Destination Locations MemberRightType TripRights TripType boat_brand boat_usage rights_subtype> data.sql

	mysqldump -u roprotokol roprotokol -p -e -c --no-create-info --tables MemberRightType > MemberRightTypes.sql

import:
	mysql -u fake -pfake fakeprotokol < data.sql

clean:
	echo "delete from Boat;delete from reservation;delete from BoatCategory;delete from BoatRights;delete from BoatType;Delete FROM Damage;Delete from Destination;DELETE FROM Locations;DELETE FROM MemberRightType;"|mysql -u fake -p fake fakeprotokol
