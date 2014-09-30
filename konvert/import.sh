#!/bin/bash
SCRIPT_PATH=$(cd `dirname ${0}`; pwd)

# ignore Version  Hitcounter Opsætning
for tb in Båd Bådindstilling BådKategori Fejl_system Fejl_tblMembersSportData Fejl_tur Gruppe  Kajak_typer Kommentar LåsteBåde Medlem Motionstatus Postnr Reservation Skade TurDeltager TurType  Vintervedligehold Destination Kajak_anvendelser Tur; do
    echo DO IMPORT $tb
    echo
    mysql -u roprotokol -proprotokol roprotokol -e "TRUNCATE TABLE $tb;"
    mysql -u roprotokol -proprotokol roprotokol < $SCRIPT_PATH/data/$tb.sql
done
