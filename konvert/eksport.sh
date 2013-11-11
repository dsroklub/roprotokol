#!/bin/bash

for tb in Båd Bådindstilling BådKategori Fejl_system Fejl_tblMembersSportData Fejl_tur Gruppe Hitcounter Kajak_typer Kommentar LåsteBåde Medlem Motion+status Opsætning Postnr Reservation Skade TurDeltager TurType Version Vintervedligehold Destination Kajak_anvendelser Tur; do
echo DO $tb
echo
mdb-export  Roprotokol_sommer.mdb "$tb" > konvert/"$tb.csv"
mdb-export -D '%F %T' -I mysql Roprotokol_sommer.mdb "$tb" > konvert/"$tb.sql"
done

mv "konvert/Motion+status.sql" "konvert/Motionstatus.sql"
# ret Årsag til rettelsen i Fejl_tur.sql
# ret Motion+->MotionPlus i Baad

perl -p -i -e "s/Årsag til rettelsen/Årsagtilrettelsen/g" konvert/Fejl_tur.sql
perl -p -i -e "s/Motion\+/MotionPlus/g" konvert/Båd.sql
perl -p -i -e "s/MotionPlus\+/MotionPlus/g" konvert/Båd.sql
perl -p -i -e "s/Motion\+/Motion/g" konvert/Motionstatus.sql
