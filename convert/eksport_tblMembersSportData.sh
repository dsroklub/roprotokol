#!/bin/bash

BASEDIR=$(dirname $0)
SCRIPT_PATH=$(readlink -f $BASEDIR)

RODB=$SCRIPT_PATH/Sportdat.mdb

echo using $SCRIPT_PATH  $RODB
for tb in tblMembersSportData; do
    echo DO $tb
    mdb-export -D '%F %T' -I mysql "$RODB" "$tb" > "$SCRIPT_PATH/data/$tb.sql"
done
