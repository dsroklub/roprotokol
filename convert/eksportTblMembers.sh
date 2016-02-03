#!/bin/bash
SCRIPT_PATH=$(cd `dirname ${0}`; pwd)

for tb in tblMembers tblMembersSportData; do
    echo DO $tb
    echo
    mdb-export -D '%F %T' -I mysql $SCRIPT_PATH/Members.mdb "$tb" > "$SCRIPT_PATH/data/$tb.sql"
done

