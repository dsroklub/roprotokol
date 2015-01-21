#!/bin/sh
#apt-get install python-mysqldb  php5-mysqlnd mysql

mysql -u roprotokol --password=roprotokol roprotokol<<EOSQL
  CREATE SCHEMA 'roprotokol' DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol';
  GRANT ALL PRIVILEGES ON 'roprotokol'.* TO 'roprotokol'@'localhost';
  FLUSH PRIVILEGES;
EOSQL


mysql -u roprotokol -p'roprotokol' roprotokol < backend/convert/mkdb.sql
mysql -u roprotokol -p'roprotokol' roprotokol <  backend/convert/queries.sql
echo 'DBCMD="mysql -u roprotokol --password=roprotokol roprotokol"' >  backend/convert/secret.sh
echo "roprotokol" > backend/tests/secret.db

./backend/convert/import.sh fake

echo now configure your webserver to server DSR roprotokol
