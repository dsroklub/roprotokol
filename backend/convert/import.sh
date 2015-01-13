#!/bin/bash
BASEDIR=$(dirname $0)
SCRIPT_PATH=$(readlink -f $BASEDIR)

CURRENTSEASON=2014
echo CURRENTSEASON=$CURRENTSEASON
arg=$1


if [[ "x$arg" = "x" ]]
then
   echo usage:
   echo   import.sh fake
   echo   import.sh real
   echo fake use DSR data for boats, destinations, etc but generate fake informations about rowers
   echo real is for importing real DSR data, it requires that you have SQL dumps from the database
   exit 0
fi
DBCMD="mysql -f -u roprotokol roprotokol"

#if you you a password, put DBCMD="mysql -u roprotokol -p password roprotokol" in secret.sh
if [ -f $SCRIPT_PATH/secret.sh ];
then
. $SCRIPT_PATH/secret.sh
fi

for tb in Båd Bådindstilling BådKategori Gruppe  Kajak_typer LockedBoats Zipcode Reservation Skade TurType Destination Kajak_anvendelser; do
    echo DO IMPORT $tb
    echo
    $DBCMD -e "TRUNCATE TABLE $tb;"
    $DBCMD < $SCRIPT_PATH/testdata/$tb.sql
done
echo do trip rights
    $DBCMD < $SCRIPT_PATH/TripRights.sql

if [[ $arg = "fake" ]]; then
    echo "Generating fake data..."
    $DBCMD < $SCRIPT_PATH/rename.sql
    $SCRIPT_PATH/../tests/fakedata.py
elif [[ $arg = "real" ]]; then
    echo "Using real data..."
    for tb in Fejl_tblMembersSportData Fejl_system Fejl_tur TurDeltager Vintervedligehold Medlem Tur tblMembersSportData; do
	echo DO IMPORT $tb
	echo
	$DBCMD -e "TRUNCATE TABLE $tb;"
	$DBCMD < $SCRIPT_PATH/data/$tb.sql
    done
    for SEASON in $(seq 2010 2013); do
	echo SEASON $SEASON; 
	for ST in Tur Turdeltager; do
	    tb=${ST}_backup${SEASON}
	    echo make table $tb
	    $DBCMD -e "DROP TABLE IF EXISTS $tb" 
	    $DBCMD -e "CREATE TABLE $tb AS SELECT * from ${ST/deltager/Deltager} WHERE 1=2"
	    $DBCMD < $SCRIPT_PATH/data/$tb.sql
	done
    done

    for SEASON in $(seq 2010 2013); do
	$DBCMD -e "INSERT INTO Trip (TripID,Season,BoatID,OutTime,InTime,ExpectedIn,Destination,Meter,TripTypeID,Comment,CreatedDate,EditDate,Initials,DESTID) \
     SELECT TurID,${SEASON},FK_BådID,Ud,Ind,ForvInd,Destination,Meter,FK_TurTypeID,Kommentar,OprettetDato,RedigeretDato,Initialer,DESTID FROM Tur_backup${SEASON}"

	$DBCMD -e "INSERT INTO TripMember (TripID, Season, Seat, MemberID,MemberName,CreatedDate,EditDate,Initials) \
        SELECT FK_TurID, ${SEASON}, Plads, FK_MedlemID,Navn,OprettetDato,RedigeretDato,Initialer FROM Turdeltager_backup${SEASON}"
	$DBCMD -e "DROP TABLE Tur_backup${SEASON}"
	$DBCMD -e "DROP TABLE Turdeltager_backup${SEASON}"
    done
 echo now season $CURRENTSEASON
    SEASON=$CURRENTSEASON
    $DBCMD -e "INSERT INTO Trip (TripID,Season,BoatID,OutTime,InTime,ExpectedIn,Destination,Meter,TripTypeID,Comment,CreatedDate,EditDate,Initials,DESTID) \
     SELECT TurID,${SEASON},FK_BådID,Ud,Ind,ForvInd,Destination,Meter,FK_TurTypeID,Kommentar,OprettetDato,RedigeretDato,Initialer,DESTID FROM Tur"

    $DBCMD -e "INSERT INTO TripMember (TripID, Season,Seat, MemberID,MemberName,CreatedDate,EditDate,Initials) \
    SELECT   FK_TurID, ${SEASON}, Plads, FK_MedlemID,Navn,OprettetDato,RedigeretDato,Initialer FROM TurDeltager"
#    $DBCMD -e "DROP TABLE Tur"
#    $DBCMD -e "DROP TABLE TurDeltager"
    $DBCMD < $SCRIPT_PATH/konvertRights.sql
    $DBCMD < $SCRIPT_PATH/rename.sql

elif [[ $arg = "empty" ]]; then
    echo no rower data
else
    echo unknown argument
fi
