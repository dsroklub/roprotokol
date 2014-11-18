#!/bin/bash

BASEDIR=$(dirname $0)
SCRIPT_PATH=$(readlink -f $BASEDIR)

RODB=$SCRIPT_PATH/Roprotokol_sommer.mdb

echo using $SCRIPT_PATH  $RODB
for tb in Båd Bådindstilling BådKategori Fejl_system Fejl_tblMembersSportData Fejl_tur Gruppe Hitcounter Kajak_typer Kommentar LåsteBåde Medlem Motion+status Opsætning Postnr Reservation Skade TurDeltager TurType Version Vintervedligehold Destination Kajak_anvendelser Tur; do
    echo DO $tb
    mdb-export -D '%F %T' -I mysql "$RODB" "$tb" > "$SCRIPT_PATH/data/$tb.sql"
done


## use 2013 with newer RODB
for SEASON in $(seq 2010 2013); do
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
