#!/bin/bash
#mysqldump --skip-comments --compatible=ansi -u roprotokol --no-data roprotokol > roprotokolschema.sql
mysqldump --skip-comments -u roprotokol --no-data roprotokol > roprotokolschema.sql
mysqldump --skip-comments -u roprotokol --no-data roprotokol --compatible=ansi | sed -e "s/AUTO_INCREMENT/IDENTITY/"> roprotokolschemalite.sql
../../utils/mysql2sqlite.sh -u roprotokol -B roprotokol --default-character-set=utf8 |grep -v "PRAGMA"|grep -v TRANSACTION|grep -v "CREATE DATABASE" | perl -i -p -e 's/([^;])\n/\1/g' > ~/src/dsr.lite.sql
