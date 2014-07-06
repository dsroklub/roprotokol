#!/bin/bash

# ignore Version  Hitcounter Opsætning
for tb in Båd Bådindstilling BådKategori Fejl_system Fejl_tblMembersSportData Fejl_tur Gruppe  Kajak_typer Kommentar LåsteBåde Medlem Motionstatus Postnr Reservation Skade TurDeltager TurType  Vintervedligehold Destination Kajak_anvendelser Tur; do
echo DO IMPORT $tb
echo
#drop table if exist $tb;
mysql -u root -p  roprotokol < konvert/$tb.sql

done
