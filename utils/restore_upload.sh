#!/bin/sh -x

# Restore DSR backup to local database

mkdir -p  ~/t
scp roprotokol@roprotokol.danskestudentersroklub.dk:/data/roprotokol/externaladmin/uploads/tblMembers.sql ~/t/tblMembers.sql
$DBCMD < ~/t/tblMembers.sql
