#!/bin/sh
pw=$(grep dbpassword /data/config.ini|cut -d = -f 2)
mysqldump roprotokol -u roprotokol -p --password="$pw" > /data/backup/ro.sql
scp /data/backup/ro.sql ro0:
