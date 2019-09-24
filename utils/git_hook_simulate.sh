#!/bin/sh
#

sts=$(date +"%s")
lts="long"$(date +"%s")

echo sts=$sts lts=$lts
echo $lts | awk  'END { print "<?php\n$gitrevision=\"" $1 "\";\n"}' > ../public/inc/gitrevision.php
echo $lts | awk  'END { print "var gitrevision=\"" $1 "\";\n"}' > ../public/js/gitrevision.js
echo $sts | awk  'END { print "  <base href=\"/front"$1"/app/\">"}'> ../public/rowbasetag.html
echo $sts | awk  'END { print "  <base href=\"/front"$1"/event/\">"}'> ../public/eventbasetag.html

