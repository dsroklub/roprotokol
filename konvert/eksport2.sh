#!/bin/bash

for tb in tblMembers; do
echo DO $tb
echo
mdb-export  Members.mdb "$tb" > konvert/"$tb.csv"
mdb-export -D '%F %T' -I mysql Members.mdb "$tb" > konvert/"$tb.sql"
done

perl -p -i -e "s/E-mail+/E_mail/g" konvert/tblMembers.sql

