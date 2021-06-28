export:
	mysqldump -u roprotokol roprotokol -p -e -c --no-create-info --tables reservation Boat BoatCategory BoatRights BoatType Damage Destination Locations MemberRightType TripRights TripType boat_brand boat_usage rights_subtype  reservation_configuration zones> data.sql
	mysqldump -u roprotokol roprotokol -p -e -c --no-create-info --tables MemberRightType > MemberRightTypes.sql

cexport:
	mysqldump -u roprotokol roprotokol -p -e -c  --add-drop-table --tables Boat BoatCategory BoatRights BoatType Damage Destination Locations MemberRightType TripRights TripType boat_brand boat_usage rights_subtype reservation reservation_configuration zones > data.sql
	mysqldump -u roprotokol roprotokol -p -e -c  --add-drop-table --tables MemberRightType > MemberRightTypes.sql

db:
	mysql -v -u fake -pfake fakeprotokol < ../mkdb.sql
import:
	mysql -v -u fake -pfake fakeprotokol < data.sql

clean:
	echo "delete from Boat;delete from reservation;delete from BoatCategory;delete from BoatRights;delete from BoatType;Delete FROM Damage;Delete from Destination;DELETE FROM Locations;DELETE FROM MemberRightType;"|mysql -u fake -p fake fakeprotokol

fakedata:
	./fakedata.py
