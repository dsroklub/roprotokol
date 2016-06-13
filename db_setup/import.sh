#!/bin/bash
BASEDIR=$(dirname $0)
SCRIPT_PATH=$(readlink -f $BASEDIR)

CURRENTSEASON=2016
echo CURRENTSEASON=$CURRENTSEASON
DB=$1
datatype=$2
password=$3

echo data=$datatype
echo DB=$DB


if [ -z $datatype ]
then
   echo usage:
   echo   import.sh database fake [database password]
   echo   import.sh database real [database password]
   echo fake use DSR data for boats, destinations, etc but generate fake informations about rowers
   echo real is for importing real DSR data, it requires that you have SQL dumps from the database
   exit 0
fi

DBCMD="mysql -f -u roprotokol $DB"

if [ ! -z $password ]
then
    DBCMD="mysql -f -u roprotokol -p$password $DB"
fi

#if you you a password, put DBCMD="mysql -u roprotokol -p password roprotokol" in secret.sh

echo CHECKING $SCRIPT_PATH/../secret.sh
if [ -f $SCRIPT_PATH/../secret.sh ];
then
    . $SCRIPT_PATH/../secret.sh
    echo read secret $DBCMD
fi

if [[ $datatype = "real" ]]; then
    DATADIR=$SCRIPT_PATH/data
else
    DATADIR=$SCRIPT_PATH/testdata
fi
echo import from $DATADIR/data.sql
$DBCMD < $DATADIR/data.sql

if [[ $datatype = "fake" ]]; then
    echo "Generating fake data..."
    $SCRIPT_PATH/fakedata.py $DB
elif [[ $datatype = "real" ]]; then
    echo "Using real data..."
elif [[ $datatype = "empty" ]]; then
    echo no rower data
else
    echo unknown argument datatype=$datatype
fi
