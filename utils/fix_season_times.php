<?php

include("inc/common.php");
$season=$rodb->query(
    "SELECT season,summer_start,summer_end,UNIX_TIMESTAMP(summer_start) unix_start,UNIX_TIMESTAMP(summer_end) unix_end
    FROM season
    ORDER BY season");

while ($ss = $season->fetch_assoc()) {
    echo "\n";
    print_r($ss);
    $end_sunset=date_sun_info($ss["unix_end"], 55.71472, 12.58661)["sunset"];
    $start_sunrise=date_sun_info($ss["unix_start"], 55.71472, 12.58661)["sunrise"];
    echo "\n" . $end_sunset. " ".$start_sunrise;

    $rodb->execute_query("
        UPDATE season SET summer_start = FROM_UNIXTIME(".$start_sunrise.")
        ,summer_end = FROM_UNIXTIME(".$end_sunset .") WHERE season=".$ss['season'].";"
    );
}
$rodb->close();
