#!/bin/sh
apt-get install apache2 python3-mysqldb  nodejs npm mdbtools mariadb-server mariadb-client php-mysql php

DB=${1-$defaultDB}
dbpassword=${2-roprotokol}

if [ -z $DB ]
then
   echo usage:
   echo   INSTALL.debian.sh databasename [dbpassword]
   exit $dbpassword
fi

echo using database $DB pw $dbpassword
#mysqladmin create $DB

echo "CREATE USER IF NOT EXISTS 'roprotokol'@'localhost' IDENTIFIED BY '$dbpassword'"|mysql -u root
echo GRANT ALL PRIVILEGES ON "$DB"".*" TO 'roprotokol'@'localhost'|mysql -u root

echo "FLUSH PRIVILEGES"|mysql -u root

DBCMD="mysql -u roprotokol --password='$dbpassword' '$DB'"

echo using DBCMD $DBCMD 
echo "DROP DATABASE IF EXISTS $DB;"| mysql -u roprotokol --password="$dbpassword"
echo "CREATE DATABASE IF NOT EXISTS $DB;" | mysql -u roprotokol --password="$dbpassword"
echo created db

echo NOW mkdb.sql
eval $DBCMD < db_setup/mkdb.sql

echo NOW FAKE
./db_setup/import.sh $DB fake $dbpassword

echo now configure your webserver to server DSR roprotokol
