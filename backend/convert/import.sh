#!/bin/bash
BASEDIR=$(dirname $0)
SCRIPT_PATH=$(readlink -f $BASEDIR)

CURRENTSEASON=2015
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

echo CHECKING $SCRIPT_PATH/secret.sh
if [ -f $SCRIPT_PATH/secret.sh ];
then
    . $SCRIPT_PATH/secret.sh
    echo read secret $DBCMD
fi

if [[ $datatype = "real" ]]; then
    DATADIR=data
else
    DATADIR=testdata
fi

for tb in Location Båd Bådindstilling BådKategori Gruppe Kajak_typer Postnr Reservation Skade TurType Destination Kajak_anvendelser; do
    echo DO IMPORT $tb
    echo
    $DBCMD -e "TRUNCATE TABLE $tb;"
    $DBCMD < $SCRIPT_PATH/$DATADIR/$tb.sql
done
echo do trip rights
    $DBCMD < $SCRIPT_PATH/TripRights.sql

if [[ $datatype = "fake" ]]; then
    echo "Generating fake data..."
    $DBCMD < $SCRIPT_PATH/rename.sql
    $SCRIPT_PATH/../tests/fakedata.py $DB
elif [[ $datatype = "real" ]]; then
    echo "Using real data..."
    for tb in Fejl_tblMembersSportData Fejl_system Fejl_tur TurDeltager Vintervedligehold Medlem Tur tblMembersSportData; do
	echo DO IMPORT $tb
	echo
	$DBCMD -e "TRUNCATE TABLE $tb;"
	$DBCMD < $SCRIPT_PATH/data/$tb.sql
    done
    for SEASON in $(seq 2010 2014); do
	echo SEASON $SEASON; 
	for ST in Tur Turdeltager; do
	    tb=${ST}_backup${SEASON}
	    echo make table $tb
	    $DBCMD -e "DROP TABLE IF EXISTS $tb" 
	    $DBCMD -e "CREATE TABLE $tb AS SELECT * from ${ST/deltager/Deltager} WHERE 1=2"
	    $DBCMD < $SCRIPT_PATH/data/$tb.sql
	done
    done

    for SEASON in $(seq 2010 2014); do
	$DBCMD -e "INSERT INTO Trip (id,Season,BoatID,OutTime,InTime,ExpectedIn,Destination,Meter,TripTypeID,Comment,CreatedDate,EditDate,Initials,DESTID) \
     SELECT TurID,${SEASON},FK_BådID,Ud,Ind,ForvInd,Destination,Meter,FK_TurTypeID,Kommentar,OprettetDato,RedigeretDato,Initialer,DESTID FROM Tur_backup${SEASON}"

	$DBCMD -e "INSERT INTO TripMember (TripID, Season, Seat, member_id,MemberName,CreatedDate,EditDate,Initials) \
        SELECT FK_TurID, ${SEASON}, Plads, FK_MedlemID,Navn,OprettetDato,RedigeretDato,Initialer FROM Turdeltager_backup${SEASON}"
	$DBCMD -e "DROP TABLE Tur_backup${SEASON}"
	$DBCMD -e "DROP TABLE Turdeltager_backup${SEASON}"
    done
 echo now season $CURRENTSEASON
    SEASON=$CURRENTSEASON
    $DBCMD -e "INSERT INTO Trip (id,Season,BoatID,OutTime,InTime,ExpectedIn,Destination,Meter,TripTypeID,Comment,CreatedDate,EditDate,Initials,DESTID) \
     SELECT TurID,${SEASON},FK_BådID,Ud,Ind,ForvInd,Destination,Meter,FK_TurTypeID,Kommentar,OprettetDato,RedigeretDato,Initialer,DESTID FROM Tur"
    $DBCMD -e "INSERT INTO TripMember (TripID, Season,Seat, member_id, MemberName,CreatedDate,EditDate,Initials) \
    SELECT   FK_TurID, ${SEASON}, Plads, FK_MedlemID,Navn,OprettetDato,RedigeretDato,Initialer FROM TurDeltager"
#    $DBCMD -e "DROP TABLE Tur"
    #    $DBCMD -e "DROP TABLE TurDeltager"
    echo "konverting rights"
    $DBCMD < $SCRIPT_PATH/konvertRights.sql
    echo "renaming"
    $DBCMD < $SCRIPT_PATH/rename.sql

    $DBCMD < $SCRIPT_PATH/TripRights.sql
    $DBCMD < $SCRIPT_PATH/BoatRights.sql
    $DBCMD < $SCRIPT_PATH/memberrighttype.sql
    $DBCMD < $SCRIPT_PATH/Location.sql
elif [[ $datatype = "empty" ]]; then
    echo no rower data
else
    echo unknown argument
fi
