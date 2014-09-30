#!/bin/bash
SCRIPT_PATH=$(cd `dirname ${0}`; pwd)

for tb in tblMembers; do
    echo DO $tb
    echo
    mdb-export  $SCRIPT_PATH/Members.mdb "$tb" > "$SCRIPT_PATH/data/$tb.csv"
    mdb-export -D '%F %T' -I mysql $SCRIPT_PATH/Members.mdb "$tb" > "$SCRIPT_PATH/data/$tb.sql"
done

perl -p -i -e "s/E-mail+/E_mail/g" "$SCRIPT_PATH/data/tblMembers.sql"

