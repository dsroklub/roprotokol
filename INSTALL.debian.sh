#!/bin/sh
#apt-get install python-mysqldb  php5-mysqlnd mysql nodejs npm



defaultDB=roprotokolx

DB=${1-$defaultDB}

echo using database $DB
if [ ! -f backend/convert/secret.sh ]; then
    echo 'DBCMD="mysql -u roprotokol --password=roprotokol ' $DB '"' >  backend/convert/secret.sh
    echo 'superDBCMD="mysql -u roprotokol --password=roprotokol"' >  backend/convert/secret.sh
    echo 'dbpassword=roprotokol' >>  backend/convert/secret.sh
    echo "roprotokol" > backend/tests/secret.db    
fi


. backend/convert/secret.sh


echo using DBCMD $DBCMD $superDBCMD
echo "DROP DATABASE IF EXISTS $DB;"| $superDBCMD
echo "CREATE DATABASE IF NOT EXISTS $DB;" | $superDBCMD
echo created db

echo NOW mkdb.sql
$DBCMD < backend/convert/mkdb.sql

echo NOW FAKE
./backend/convert/import.sh $DB fake $dbpassword

echo now configure your webserver to server DSR roprotokol
