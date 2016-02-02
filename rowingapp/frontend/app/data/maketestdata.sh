for un in boatdamages.php  boatreservations.php  boats.php  boatstatus.php  destinations.php  rowers.php  boat_statistics.php?season=2014 boat_statistics.php?boattype=kayak boat_statistics.php?boattype=rowboat  rower_statistics.php?boattype=kayak rower_statistics.php?boattype=rowboat rower_statistics.php?season=2014  triptypes.php boat_status.php; do
    uns=${un/\?/Q}
    une=${uns/=/}
    wget -O - "http://localhost/DSR-roprotokol/backend/$un"|json_pp -json_opt pretty,utf8,canonical > "${une/.php/}.json"
done



cp boat_statisticsQseason2014.json boat_statistics.json
cp rower_statisticsQseason2014.json rower_statistics.json
