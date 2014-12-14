for un in boatdamages  boatreservations  boats  boatstatus  destinations  rostat  rowers  rower_statistics  triptypes; do
    wget -O - http://localhost/DSR-roprotokol/backend/$un.php|json_pp -json_opt utf8 > $un.json
    done
