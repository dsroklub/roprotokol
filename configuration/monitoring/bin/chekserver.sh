#!/bin/sh

mkdir -p /data/roprotokol/log
wget -q -O -  https://roprotokol.danskestudentersroklub.dk/public/serverstatus.json >> /data/roprotokol/log/serverstatus || echo "Gør noget ved det"|mail -s roprotokolErNede elgaard@agol.dk

