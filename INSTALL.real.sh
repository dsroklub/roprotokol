#!/bin/bash
#apt-get install python-mysqldb  php5-mysqlnd mysql nodejs npm

DB=${1-$defaultDB}
dbpassword=${2-roprotokol}
echo "pw=$dbpassword"
if [ -z $DB ]
then
   echo usage:
   echo   INSTALL.debian.sh databasename
   exit 0
fi


echo "CREATE USER IF NOT EXISTS 'roprotokol'@'localhost' IDENTIFIED BY '$dbpassword'"|mysql -u root
echo GRANT ALL PRIVILEGES ON "$DB"".*" TO 'roprotokol'@'localhost'|mysql -u root
echo "FLUSH PRIVILEGES"|mysql -u root

echo "DROP DATABASE IF EXISTS $DB;"| mysql -u roprotokol --password="$dbpassword"
echo "CREATE DATABASE IF NOT EXISTS $DB;" | mysql -u roprotokol --password="$dbpassword"
echo created db

echo NOW "mkdb.sql mysql -u roprotokol password=$dbpassword" "$DB"
mysql -u roprotokol --password="$dbpassword" "$DB" < db_setup/mkdb.sql

echo NOW REAL
#./db_setup/import.sh $DB real $dbpassword

echo now configure your webserver to server DSR roprotokol
