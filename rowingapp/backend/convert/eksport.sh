#!/bin/bash

BASEDIR=$(dirname $0)
SCRIPT_PATH=$(readlink -f $BASEDIR)

RODB=$SCRIPT_PATH/Roprotokol.mdb

echo using $SCRIPT_PATH  $RODB
for tb in Båd Bådindstilling BådKategori Fejl_system Fejl_tblMembersSportData Fejl_tur Gruppe Kajak_typer Kommentar Medlem Motion+status Opsætning Reservation Skade TurDeltager TurType Version Vintervedligehold Destination Kajak_anvendelser Tur; do
    echo DO $tb
    mdb-export -D '%F %T' -I mysql "$RODB" "$tb" > "$SCRIPT_PATH/data/$tb.sql"
done


## use 2013 with newer RODB
for SEASON in $(seq 2010 2014); do
    echo SEASON $SEASON; 
    for ST in Tur Turdeltager; do
	tb=${ST}_backup${SEASON}
	echo make XXXXXX $tb
	mdb-export -D '%F %T' -I mysql "$RODB" "$tb" > "$SCRIPT_PATH/data/$tb.sql"
    done
done

mv "$SCRIPT_PATH/data/Motion+status.sql" "$SCRIPT_PATH/data/Motionstatus.sql"
# ret Årsag til rettelsen i Fejl_tur.sql
# ret Motion+->MotionPlus i Baad

perl -p -i -e "s/Årsag til rettelsen/Årsagtilrettelsen/g" "$SCRIPT_PATH/data/Fejl_tur.sql"
perl -p -i -e "s/Motion\+/MotionPlus/g" "$SCRIPT_PATH/data/Båd.sql"
perl -p -i -e "s/MotionPlus\+/MotionPlus/g" "$SCRIPT_PATH/data/Båd.sql"
perl -p -i -e "s/Motion\+/Motion/g" "$SCRIPT_PATH/data/Motionstatus.sql"

for tb in tblMembers; do
    echo DO Members $tb
    mdb-export  $SCRIPT_PATH/Members.mdb "$tb" > "$SCRIPT_PATH/data/$tb.csv"
    mdb-export -D '%F %T' -I mysql $SCRIPT_PATH/Members.mdb "$tb" > "$SCRIPT_PATH/data/$tb.sql"
done
perl -p -i -e "s/E-mail+/E_mail/g" "$SCRIPT_PATH/data/tblMembers.sql"


RODB=$SCRIPT_PATH/Sportdat.mdb
echo using $SCRIPT_PATH  $RODB
for tb in tblMembersSportData; do
    echo DO Sportdat $tb
    mdb-export -D '%F %T' -I mysql "$RODB" "$tb" > "$SCRIPT_PATH/data/$tb.sql"
done

echo "INSERT INTO Location (Name) VALUES ('DSR');" > "$SCRIPT_PATH/data/Location.sql"
echo "INSERT INTO Location (Name) VALUES ('Nordhavn');" >> "$SCRIPT_PATH/data/Location.sql"
