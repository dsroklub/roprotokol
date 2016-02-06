#!/bin/sh
#apt-get install python-mysqldb  php5-mysqlnd mysql nodejs npm

DB=${1-$defaultDB}

if [ -z $DB ]
then
   echo usage:
   echo   INSTALL.debian.sh databasename
   exit 0
fi



echo using database $DB
if [ ! -f secret.sh ]; then
    echo 'DBCMD="mysql -u roprotokol --password=roprotokol ' $DB '"' >  secret.sh
    echo 'superDBCMD="mysql -u roprotokol --password=roprotokol"' >>  secret.sh
    echo 'dbpassword=roprotokol' >>  secret.sh
    echo "roprotokol" > secret.db    
fi

. ./secret.sh


echo using DBCMD $DBCMD $superDBCMD
echo "DROP DATABASE IF EXISTS $DB;"| $superDBCMD
echo "CREATE DATABASE IF NOT EXISTS $DB;" | $superDBCMD
echo created db

echo NOW mkdb.sql
$DBCMD < convert/mkdb.sql

echo NOW FAKE
./convert/import.sh $DB fake $dbpassword

echo now configure your webserver to server DSR roprotokol
