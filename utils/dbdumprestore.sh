#!/bin/sh

pw=$(grep dbpassword /data/config.ini|cut -d = -f 2)
mysql -u roprotokol --password="$pw" roprotokol < /home/roprotokol/ro.sql
