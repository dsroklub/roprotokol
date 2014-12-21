for un in boatdamages.php  boatreservations.php  boats.php  boatstatus.php  destinations.php  rostat.php  rowers.php  boat_statistics.php boat_statistics.php?boattype=kayak boat_statistics.php?boattype=rowboat  rower_statistics.php?boattype=kayak rower_statistics.php?boattype=rowboat rower_statistics.php  triptypes.php; do
    uns=${un/\?/Q}
    une=${uns/=/}
    wget -O - "http://localhost/DSR-roprotokol/backend/$un"|json_pp -json_opt pretty,utf8 > "${une/.php/}.json"
done

