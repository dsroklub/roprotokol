#!/bin/bash
SCRIPT_PATH=$(cd `dirname ${0}`; pwd)


for tb in Båd Bådindstilling BådKategori Fejl_system Fejl_tblMembersSportData Fejl_tur Gruppe Hitcounter Kajak_typer Kommentar LåsteBåde Medlem Motion+status Opsætning Postnr Reservation Skade TurDeltager TurType Version Vintervedligehold Destination Kajak_anvendelser Tur; do
    echo DO $tb
    echo
    mdb-export $SCRIPT_PATH/Roprotokol_sommer.mdb "$tb" > "$SCRIPT_PATH/data/$tb.csv"
    mdb-export -D '%F %T' -I mysql "$SCRIPT_PATH/Roprotokol_sommer.mdb" "$tb" > "$SCRIPT_PATH/data/$tb.sql"
done

mv "$SCRIPT_PATH/data/Motion+status.sql" "$SCRIPT_PATH/data/Motionstatus.sql"
# ret Årsag til rettelsen i Fejl_tur.sql
# ret Motion+->MotionPlus i Baad

perl -p -i -e "s/Årsag til rettelsen/Årsagtilrettelsen/g" "$SCRIPT_PATH/data/Fejl_tur.sql"
perl -p -i -e "s/Motion\+/MotionPlus/g" "$SCRIPT_PATH/data/Båd.sql"
perl -p -i -e "s/MotionPlus\+/MotionPlus/g" "$SCRIPT_PATH/data/Båd.sql"
perl -p -i -e "s/Motion\+/Motion/g" "$SCRIPT_PATH/data/Motionstatus.sql"
