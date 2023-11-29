#!/bin/bash
dbpassword=${2-roprotokol}

# Restore DSR backup to local database

mkdir -p  ~/t
scp roprotokol@roprotokol.danskestudentersroklub.dk:/data/backup/automysqlbackup/latest/roprotokol_*sql.gz ~/t/ro.sql.gz
gunzip -f ~/t/ro.sql.gz

if [ -z $dbpassword ]; then
   mysql -u roprotokol < ~/t/ro.sql
   else
   mysql -u roprotokol --password="$dbpassword" < ~/t/ro.sql
fi
