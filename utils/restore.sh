#!/bin/sh

# Restore DSR backup to local database

mkdir -p  ~/t
scp roprotokol@roprotokol.danskestudentersroklub.dk:/data/backup/automysqlbackup/latest/roprotokol_*sql.gz ~/t/ro.sql.gz
gunzip -f ~/t/ro.sql.gz

mysql -u roprotokol < ~/t/ro.sql
