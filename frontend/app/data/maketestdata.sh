for un in boatdamages  boatreservations  boats  boatstatus  destinations  rostat  rowers  rower_statistics  triptypes; do
    wget -O - http://localhost/DSR-roprotokol/backend/$un.php|json_pp > $un.json
    done
