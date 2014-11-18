#!/bin/bash
BASEDIR=$(dirname $0)
SCRIPT_PATH=$(readlink -f $BASEDIR)

CURRENTSEASON=2014
echo CURRENTSEASON=$CURRENTSEASON

#DBCMD=mysql -u roprotokol -proprotokol roprotokol
DBCMD="mysql -u roprotokol roprotokol"

for tb in Båd Bådindstilling BådKategori Fejl_system Fejl_tblMembersSportData Fejl_tur Gruppe  Kajak_typer Kommentar LåsteBåde Medlem Motionstatus Postnr Reservation Skade TurDeltager TurType  Vintervedligehold Destination Kajak_anvendelser Tur; do
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

SEASON=$CURRENTSEASON
$DBCMD -e "INSERT INTO Trip (TripID,Season,BoatID,OutTime,InTime,ExpectedIn,Destination,Meter,TripTypeID,Comment,CreatedDate,EditDate,Initials,DESTID) \
     SELECT TurID,${SEASON},FK_BådID,Ud,Ind,ForvInd,Destination,Meter,FK_TurTypeID,Kommentar,OprettetDato,RedigeretDato,Initialer,DESTID FROM Tur"

$DBCMD -e "INSERT INTO TripMember (TripID, Season,Seat, MemberID,MemberName,CreatedDate,EditDate,Initials) \
    SELECT   FK_TurID, ${SEASON}, Plads, FK_MedlemID,Navn,OprettetDato,RedigeretDato,Initialer FROM TurDeltager"
$DBCMD -e "DROP TABLE Tur"
$DBCMD -e "DROP TABLE TurDeltager"
