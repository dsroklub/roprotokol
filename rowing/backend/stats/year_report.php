<?php
include("../inc/common.php");
// include("../inc/verify_user.php");

$messages = [];
$error=null;
$res=array ("status" => "ok");
$tripTypes = [];


$y = 0;
$now = getdate();
$to_year = isset($_GET['to_year']) ? (int) $_GET['to_year'] : $now['mon'] < 2 ? $now['year'] - 1 : $now['year'];
$from_year = isset($_GET['from_year']) ? (int) $_GET['from_year'] : 2010;
$cut_date = isset($_GET['cut_date']) ? $_GET['cut_date'] : '01-01';
$cut_year_offset = isset($_GET['cut_year_offset']) ? (int) $_GET['cut_year_offset'] : 1;

$res["parameters"] = [ 'from_year' => $from_year,
                       'to_year' => $to_year,
                       'cut_date' => $cut_date,
                       'cut_year_offset' => $cut_year_offset
                     ];

if (! preg_match("/^\d\d\-\d\d$/", $cut_date)) {
   $error = 'Invalid cut_date: Must be on the form mm-dd, e.g. 01-01';
   goto end;
}


function get_cut($year, $date=null, $offset=null) {
  global $cut_date, $cut_year_offset;
  if (!$date) {
    $date = $cut_date;
  }
  if (!$offset) {
    $offset = $cut_year_offset;
  }

  $year += $offset;
  return $year . '-' . $date;
} 

function make_error() {
  global $error, $table, $step, $y, $rodb;

  $error = 'Could not get ' . $table . ':' . $step . '/' . $y . ': ' . $rodb->error;
}


// Tabel 1 - Indmeldelser, udmeldelser og medlemsomsætning
$table = 'members';
$res[$table] = [];
$years = [];
for ($y = $from_year; $y <= $to_year; $y++) {
    $years[] = $y;
    $res[$table][$y] = [];
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);

    $step = 'during';
    $s = "select count(*) FROM Member where JoinDate <= '" . $to_cut . "' AND (RemoveDate IS NULL OR RemoveDate >= '" . $from_cut . "')";

    $r = $rodb->query($s);
    if ($r) {
       $res[$table][$y][$step] = $r->fetch_row()[0];
    } else {
      make_error();
      goto end;
    }

    $step = 'incoming';
    $s = "select count(1) FROM Member where JoinDate >= '" . $from_cut . "' AND JoinDate < '" . $to_cut . "'";

    $r = $rodb->query($s);
    if ($r) {
       $res[$table][$y][$step] = $r->fetch_row()[0];
    } else {
      make_error();
      goto end;
    }


    $step = 'outgoing';
    $s = "select count(*) FROM Member where RemoveDate > '" . $from_cut . "' AND RemoveDate <= '" . $to_cut . "'";

    $r = $rodb->query($s);
    if ($r) {
       $res[$table][$y][$step] = $r->fetch_row()[0];
    } else {
      make_error();
      goto end;
    }

    $res[$table][$y]['diff'] = $res[$table][$y]['incoming'] - $res[$table][$y]['outgoing'];
}    
$res['years'] = $years;


// Tabel 2 - Nye medlemmer og kønsfordeling
$table = 'new_members_by_gender';
$genders = [ 'male', 'female' ];
for ($y = $from_year; $y <= $to_year; $y++) {
    $res[$table][$y] = [];
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);
    $joindate_where = "JoinDate >= '" . $from_cut . "' AND JoinDate < '" . $to_cut . "'";

    $step = 'stats';
    $s = "select Gender as gender, count(*) as count, avg(datediff(JoinDate, Birthday)/365) as age FROM Member WHERE " . $joindate_where . " GROUP BY gender";
    $r = $rodb->query($s);
    if ($r) {
       $res[$table][$y]['total'] = ['count' => 0]; 
       while ($row = $r->fetch_assoc()) {
       	 $res[$table][$y]['total']['count'] += $row['count'];
	 $res[$table][$y][$genders[$row['gender']]] = []; 
	 $res[$table][$y][$genders[$row['gender']]]['count'] = $row['count'];
	 $res[$table][$y][$genders[$row['gender']]]['age'] = $row['age'];
       };

       $age_sum = 0;
       foreach ( $genders as $g) {
         $res[$table][$y][$g]['percentage'] = round(100 * $res[$table][$y][$g]['count'] / $res[$table][$y]['total']['count'], 0);
         $age_sum += $res[$table][$y][$g]['count'] * $res[$table][$y][$g]['age'];
	 $res[$table][$y][$g]['age'] = round($res[$table][$y][$g]['age'], 1);
       }
       $res[$table][$y]['total']['age'] = round( $age_sum / $res[$table][$y]['total']['count'], 1);
    } else {
      make_error();
      goto end;
    }
}

// Tabel 3 - Nye medlemmer og deres frafald
// Tabel 9 - Udmeldte kaniner efter aktivitet
$dropout_categories = [
  'after_q3' => ['11-01', 0],
  'after_q4' => ['01-01', 1],
  'after_y2' => ['01-01', 2],
  'after_y3' => ['01-01', 3],
  'after_y4' => ['01-01', 4]
];
 
$table = 'new_members_dropout';
$res[$table] = [];
for ($y = $from_year; $y <= $to_year; $y++) {
    $res[$table][$y] = [];
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);
    $joindate_where = "JoinDate >= '" . $from_cut . "' AND JoinDate < '" . $to_cut . "'";

    $incoming = $res['members'][$y]['incoming'];
    $res[$table][$y]['incoming'] = [ 'count' => $incoming ];

    $step = 'still';
    $s = "select count(1) as antal FROM Member WHERE " . $joindate_where . " AND RemoveDate IS NULL";
    $r = $rodb->query($s);
    if ($r) {
       $count = $r->fetch_row()[0];
       $res[$table][$y][$step] = [ 'count' => $count, 'percentage' => round(($incoming ? 100 * $count / $incoming : 0), 0) ];
    } else {
      make_error();
      goto end;
    }

    foreach ($dropout_categories as $st => $vals) {
      $step = $st;
      if ($y + $vals[1] > $now['year']) {
        $res[$table][$y][$step] = ['count' => null, 'percentage' => null];
        continue;
      }
      $s = "select count(1) as antal FROM Member WHERE " . $joindate_where . " AND RemoveDate <= '" . get_cut($y, $vals[0], $vals[1]) . "'";
      $r = $rodb->query($s);
      if ($r) {
        $count = $r->fetch_row()[0];
        $res[$table][$y][$step] = [ 'count' => $count, 'percentage' => round(($incoming ? 100 * $count / $incoming : 0), 0) ];
      } else {
        make_error();
        goto end;
      }
    }

    $step = 'after_rowright';
    $s = "SELECT count(1) as members,
          IF((Member.RemoveDate IS NOT NULL AND Member.RemoveDate <= '" . $to_cut . "'),
                    1,
                    0) as removed
          FROM Member
          INNER JOIN MemberRights ON (Member.id = MemberRights.member_id)
          WHERE MemberRights.MemberRight = 'rowright'
            AND DATE(Member.JoinDate) >= '" . $from_cut . "'
            AND DATE(Member.JoinDate) < '" . $to_cut . "'
            AND DATE(MemberRights.Acquired) >= '" . $from_cut . "'
            AND DATE(MemberRights.Acquired) < '" . $to_cut . "'
	  GROUP BY removed";

    $r = $rodb->query($s);
    if ($r) {
       $res[$table][$y][$step] = [ 'total' => 0];
       while ($row = $r->fetch_assoc()) {
         $res[$table][$y][$step]['total'] += $row['members'];
         if ($row['removed']) {
	   $res[$table][$y][$step]['dropped_out'] = $row['members'];
         }
       }
       $res[$table][$y][$step]['percentage'] = round(100 * $res[$table][$y][$step]['dropped_out']
                                                     / $res[$table][$y][$step]['total'], 0);
    } else {
      make_error();
      goto end;
    }


    $after_rowing_trip_types = [ 'after_instruction' => '= 5',
                                 'after_rowing' => '<> 5',
                                 'after_racer_rabbit' => '= 8'
                               ];
    foreach ($after_rowing_trip_types as $st => $condition) {
      $step = $st;
      $s = "SELECT count(1) as members,
            t.trips as trips,
            IF((Member.RemoveDate IS NOT NULL AND Member.RemoveDate <= '" . $to_cut . "'),
                      1,
                      0) as removed
            FROM Member
            INNER JOIN (SELECT TripMember.member_id as MemberID,
                        COUNT(TripMember.member_id) as trips
                        FROM Trip
                        INNER JOIN TripMember ON (TripMember.TripID = Trip.id)
                        INNER JOIN Boat ON (Boat.id = Trip.BoatID)
                        INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
                        WHERE DATE(Trip.OutTime) >= '" . $from_cut . "'
                          AND DATE(Trip.OutTime) < '" .  $to_cut . "'
                          AND Trip.TripTypeID " . $condition . "
                       GROUP BY MemberID
                      ) t ON (t.MemberID = Member.id)
            WHERE Member.JoinDate >= '" . $from_cut . "'
              AND Member.JoinDate < '" . $to_cut . "'
	    GROUP BY t.trips, removed
            ORDER BY t.trips DESC, removed ASC";

      $r = $rodb->query($s);
      if ($r) {
        $res[$table][$y][$step] = [];
	$last = [ 'total' => 0, 'dropped_out' => 0 ];
        $max_after_20 = 10;
        while (($row = $r->fetch_assoc())) {
          if ($row['trips'] > 20) {
	    $max_after_20 = $row['trips'];
          }
          if (! isset($res[$table][$y][$step][$row['trips']])) {
            // Cumulate
	    $res[$table][$y][$step][$row['trips']] = [ 'total' => $last['total'], 'dropped_out' => $last['dropped_out'] ];
          }
          $res[$table][$y][$step][$row['trips']]['total'] += $row['members'];
       	  if ($row['removed']) {
	    $res[$table][$y][$step][$row['trips']]['dropped_out'] += $row['members'];
          }
          // Will calculate too many times, but catches rows without dropouts
	  $res[$table][$y][$step][$row['trips']]['percentage'] = round(100 * $res[$table][$y][$step][$row['trips']]['dropped_out']
                                                                           / $res[$table][$y][$step][$row['trips']]['total'], 0) ;
	  $last['total'] = $res[$table][$y][$step][$row['trips']]['total'];
	  $last['dropped_out'] = $res[$table][$y][$step][$row['trips']]['dropped_out'];
        }

        // Fill holes - e.g. if no one have rowed 10 trips, use numbers from 11
        for ($i = $max_after_20; $i > 1; $i--) {
          if (!isset($res[$table][$y][$step][$i])) {
            $res[$table][$y][$step][$i] = $res[$table][$y][$step][$i + 1];
          }
        }
      } else {
        make_error();
        goto end;
      }
   }
}





// Tabel 4 - Samlet aktivitetsniveau
$table = 'rowed_kilometers';
$res[$table] = [];
$boat_types = ['none','kayak','rowboat', 'motorboat'];
for ($y = $from_year; $y <= $to_year; $y++) {
    $res[$table][$y] = [];
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);

    $step = 'kilometers';

    $s = "SELECT SUM(Trip.Meter)/1000 as km,
                 BoatType.Category as boatkat
          FROM Trip
          INNER JOIN Boat ON (Boat.id = Trip.BoatID)
          INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
          WHERE DATE(OutTime) >= '" . $from_cut . "' AND DATE(OutTime) < '" . $to_cut . "'
          GROUP BY BoatType.Category;";
    $r = $rodb->query($s);
    if ($r) {
       $total = 0;
       while ($row = $r->fetch_assoc()) {
         $res[$table][$y][$boat_types[$row['boatkat']]] = round($row['km'], 0);
	 if ($row['boatkat'] <= 2) {
	   $total += $row['km'];
         }
       }
       $res[$table][$y]['total'] = round($total, 0);
    } else {
      make_error();
      goto end;
    }
}





// Tabel 6+7 - Bådture og personture, robåd og kajak
$table = 'trips';
$res[$table] = [];
for ($y = $from_year; $y <= $to_year; $y++) {
    $res[$table][$y] = [];
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);
    $step = 'trips';
    $s = "SELECT COUNT(DISTINCT Trip.id) as boatTrips,
                 COUNT(TripMember.member_id) as personTrips,
                 COUNT(DISTINCT(TripMember.member_id)) as individuals,
		 COUNT(DISTINCT(Trip.BoatID)) as boatCount,
                 ROUND(COUNT(TripMember.member_id)/COUNT(distinct Trip.id), 1) as persons_per_trip,
                 TripType.Name as triptype,
                 BoatType.Category as boatCat
          FROM Trip
          INNER JOIN TripMember ON (TripMember.TripID = Trip.id)
          INNER JOIN TripType on Trip.TripTypeID = TripType.id
          INNER JOIN Boat ON (Boat.id = Trip.BoatID)
          INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
          WHERE DATE(OutTime) >= '" . $from_cut . "' AND DATE(OutTime) < '" . $to_cut . "'
          GROUP BY BoatType.Category, TripType.id
          ORDER BY boatCat, triptype";
    $r = $rodb->query($s);
    if ($r) {
       $total = 0;
       while ($row = $r->fetch_assoc()) {
         $res[$table][$y][$row['triptype']][$boat_types[$row['boatCat']]]['boat_trips'] = $row['boatTrips'];
         $res[$table][$y][$row['triptype']][$boat_types[$row['boatCat']]]['person_trips'] = $row['personTrips'];
         $res[$table][$y][$row['triptype']][$boat_types[$row['boatCat']]]['individuals'] = $row['individuals'];
         $res[$table][$y][$row['triptype']][$boat_types[$row['boatCat']]]['boats'] = $row['boatCount'];
         $res[$table][$y][$row['triptype']][$boat_types[$row['boatCat']]]['persons_per_trip'] = round($row['persons_per_trip'], 1);
	 $tripTypes[$row['triptype']] = 1;
       }
    } else {
      make_error();
      goto end;
    }
}


// Tabel 5 - Aktivitetsniveau 2014 og 2015 opdelt på turtyper (robåde)
$table = 'trips';
for ($y = $from_year; $y <= $to_year; $y++) {
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);

    $step = 'distances';
    $s = "SELECT TripType.Name as triptype,
                 sum(Trip.Meter)/1000 as distance,
                 count(Trip.id) as boat_trips,
                 sum(Trip.Meter)/1000/count(Trip.id) as km_per_trip,
                 BoatType.Category as boatCat		 
          FROM Trip
          INNER JOIN TripType on Trip.TripTypeID = TripType.id
          INNER JOIN Boat ON (Boat.id = Trip.BoatID)
          INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
          WHERE DATE(OutTime) >= '" . $from_cut . "' AND DATE(OutTime) < '" . $to_cut . "'
          GROUP BY BoatType.Category, TripType.id
          ORDER BY boatCat, triptype";
    $r = $rodb->query($s);
    if ($r) {
       $total = 0;
       while ($row = $r->fetch_assoc()) {
         $res[$table][$y][$row['triptype']][$boat_types[$row['boatCat']]]['distance'] = round($row['distance'], 0);
         $res[$table][$y][$row['triptype']][$boat_types[$row['boatCat']]]['km_per_trip'] = round($row['km_per_trip'], 1);
       }
    } else {
      make_error();
      goto end;
    }
}



// Tabel 8 - Aktivitetsprofil for medlemmerne
$table = 'rower_activity';
$res[$table] = [];
for ($y = $from_year; $y <= $to_year; $y++) {
    $res[$table][$y] = [];
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);

    $step = 'total_trips';
    $s = "SELECT COUNT(distinct TripMember.member_id)
          FROM Trip
          JOIN TripMember ON (Trip.id = TripMember.TripID)
          WHERE DATE(OutTime) >= '" . $from_cut . "' AND DATE(OutTime) < '" . $to_cut . "'";

    $r = $rodb->query($s);
    if ($r) {
       $res[$table][$y]['total'] = [];
       $res[$table][$y]['total']['count'] = $r->fetch_row()[0];
       if ($res['members'][$y]['during']) {
       	  $res[$table][$y]['total']['percentage'] = round(100 * $res[$table][$y]['total']['count'] / $res['members'][$y]['during'], 0);
       } else {
       	  $res[$table][$y]['total']['percentage'] = 0;
       }
    } else {
      make_error();
      goto end;
    }
    $s = "SELECT SUM(Meter)/1000
          FROM Trip,TripMember
          WHERE
          TripMember.TripID=Trip.id AND
          DATE(OutTime) >= '" . $from_cut . "' AND DATE(OutTime) < '" . $to_cut . "'";

    $r = $rodb->query($s);
    if ($r) {
       $res[$table][$y]['total']['distance'] = $r->fetch_row()[0];
    } else {
      make_error();
      goto end;
    }

    $step = 'by_category';
    $s = "SELECT FLOOR(meh.distance/100000) as hundreds,
                 COUNT(meh.member_no) as members, SUM(distance)/1000 as km 
          FROM (SELECT TripMember.member_id as member_no,
                       SUM(Trip.Meter) as distance
                FROM Trip
                JOIN TripMember ON Trip.id = TripMember.TripID
		WHERE DATE(Trip.OutTime) >= '" . $from_cut . "' AND DATE(Trip.OutTime) < '" . $to_cut . "'
                GROUP BY member_no)
          AS meh
          GROUP BY FLOOR(meh.distance/100000)";
    $r = $rodb->query($s);
    if ($r) {
       $res[$table][$y]['hundreds'] = [];
       $res[$table][$y]['intervals'] = [];
       while ($row = $r->fetch_assoc()) {
         $res[$table][$y]['hundreds'][$row['hundreds'] * 100] = [];
         $res[$table][$y]['hundreds'][$row['hundreds'] * 100]['count'] = $row['members'];
         $res[$table][$y]['hundreds'][$row['hundreds'] * 100]['percentage'] = round(100 * $row['members'] / $res[$table][$y]['total']['count'], 0);
	 $category = '';
	 if ($row['hundreds'] < 1) {
	    $category = '<100';
         } else if ($row['hundreds'] < 2) {
	    $category = '100-199';
         } else if ($row['hundreds'] < 3) {
	    $category = '200-299';
         } else if ($row['hundreds'] < 5) {
	    $category = '300-499';
         } else {
	    $category = '500+';
	 }

	 if (!isset($res[$table][$y]['intervals'][$category])) {
	   $res[$table][$y]['intervals'][$category] = [ 'count' => 0 , 'distance' => 0.0];
         }
         $res[$table][$y]['intervals'][$category]['count'] += $row['members'];
         $res[$table][$y]['intervals'][$category]['distance'] += $row['km'];
         $res[$table][$y]['intervals'][$category]['percentage'] =
           round(100 * $res[$table][$y]['intervals'][$category]['count'] / $res[$table][$y]['total']['count'], 0);
       }
    } else {
      make_error();
      goto end;
    }
}    



// Tabel 9 - Aktive instruktører
$table = 'instructors';
$res[$table] = [];
$to_cut = get_cut($to_year);
$from_cut = get_cut($to_year - 1);

$step = 'list';
$s = "SELECT Member.MemberID as member_no,
             CONCAT(Member.FirstName, ' ', Member.LastName) as name,
             COUNT(TripMember.member_id) as trips
      FROM Trip
      INNER JOIN TripMember ON (TripMember.TripID = Trip.id)
      INNER JOIN Boat ON (Boat.id = Trip.BoatID)
      LEFT OUTER JOIN Member ON (Member.id = TripMember.member_id)
      WHERE DATE(OutTime) >= '" . $from_cut . "' AND DATE(OutTime) < '" . $to_cut . "'
        AND TripMember.Seat=1
        AND Trip.TripTypeID IN (5)
        AND Boat.BoatType IN (1,2)
      GROUP BY TripMember.member_id, CONCAT(Member.FirstName, ' ', Member.LastName)
      ORDER BY trips DESC, name ASC";

$r = $rodb->query($s);
if ($r) {
  while ($row = $r->fetch_assoc()) {
    $res[$table][] = $row;
  }
} else {
  make_error();
  goto end;
}



// Tabel 11 (Kaniners aktivitet efter roret) udgår - er ikke anvendelig.
    // $s = "SELECT TripType.Name as triptype,
    //              COUNT(distinct TripMember.member_id) as members,
    //              IF((RemoveDate IS NOT NULL AND RemoveDate <= '" . $to_cut . "'), 1, 0) as removed_1,
    //              IF((RemoveDate IS NOT NULL AND RemoveDate <= '" . get_cut($y + 1) . "'), 1, 0) as removed_2
    //       FROM Trip 
    //       JOIN TripMember ON (TripMember.TripID = Trip.id)
    //       INNER JOIN Boat ON (Boat.id = Trip.BoatID)
    //       INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
    //       INNER JOIN TripType on (Trip.TripTypeID = TripType.id)
    //       INNER JOIN Member ON (Member.id = TripMember.member_id)
    //       WHERE DATE(Trip.OutTime) >= '" . $from_cut . "'
    //         AND DATE(Trip.OutTime) < '" . $to_cut . "'
    //         AND Trip.TripTypeID NOT IN (5)
    //         AND BoatType.Category=2
    //         AND DATE(Member.JoinDate) >= '" . $from_cut . "'
    //         AND DATE(Member.JoinDate) < '" . $to_cut . "'
    //       GROUP BY triptype, removed_1, removed_2
    //       ORDER BY triptype";



// Tabel 12 - Kaniners aktivitet efter roret – mere end 2 ture
$table = 'rabbit_activity';
$res[$table] = [];
for ($y = $from_year; $y <= $to_year; $y++) {
    $res[$table][$y] = [];
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);

    $step = 'trips';

    $s = "SELECT TripType.Name as triptype,
                 COUNT(distinct t.member_id) as members,
       		 SUM(t.trips)  as trips,
                 IF((RemoveDate IS NOT NULL AND RemoveDate <= '" . $to_cut . "'), 1, 0) as removed_1,
                 IF((RemoveDate IS NOT NULL AND RemoveDate <= '" . get_cut($y + 1) . "'), 1, 0) as removed_2
          FROM ( SELECT Trip.TripTypeID as TripTypeID,
                        TripMember.member_id as member_id,
                        COUNT(1) AS trips
                 FROM Trip
                 INNER JOIN TripMember ON (TripMember.TripID = Trip.id)
                 INNER JOIN Boat ON (Boat.id = Trip.BoatID)
                 INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
                 WHERE DATE(Trip.OutTime) >= '" . $from_cut . "'
                   AND DATE(Trip.OutTime) < '" . $to_cut . "'
                   AND Trip.TripTypeID NOT IN (5)
                   AND BoatType.Category=2
                 GROUP BY TripTypeID, member_id
                 HAVING COUNT(1) >= 2
                ) AS t
          INNER JOIN TripType on (t.TripTypeID = TripType.id)
          INNER JOIN Member ON (Member.id = t.member_id)
          WHERE DATE(Member.JoinDate) >= '" . $from_cut . "'
            AND DATE(Member.JoinDate) < '" . $to_cut . "'
          GROUP BY triptype, removed_1, removed_2
          ORDER BY triptype";

    $r = $rodb->query($s);
    if ($r) {
       while ($row = $r->fetch_assoc()) {
         if (! isset( $res[$table][$y][$row['triptype']] )) {
	   $res[$table][$y][$row['triptype']] = [ 'total' => 0,
                                                  'trips' => 0,
                                                  'after_1' => ['count' => 0],
                                                  'after_2' => ['count' => 0]
                                                ];
         }
	 $res[$table][$y][$row['triptype']]['total'] += $row['members'];
	 $res[$table][$y][$row['triptype']]['trips'] += $row['trips'];
	 $res[$table][$y][$row['triptype']]['after_1']['count'] += $row['removed_1'] * $row['members'];
	 $res[$table][$y][$row['triptype']]['after_2']['count'] += $row['removed_2'] ? $row['members'] : 0;
       }
       foreach ($res[$table][$y] as $tt => $row) {
          $res[$table][$y][$tt]['trips_per_person'] = round($res[$table][$y][$tt]['trips'] / $res[$table][$y][$tt]['total'], 1);
          $res[$table][$y][$tt]['after_1']['percentage'] = round(100 * $res[$table][$y][$tt]['after_1']['count'] / $res[$table][$y][$tt]['total'], 0);
          if ($y >= $now['year'] - 1) {
	    $res[$table][$y][$tt]['after_2'] = [ 'count' => null, 'percentage' => null ];
          } else {
	    $res[$table][$y][$tt]['after_2']['percentage'] = round(100 * $res[$table][$y][$tt]['after_2']['count'] / $res[$table][$y][$tt]['total'], 0);
          }
       }
    } else {
       make_error();
       goto end;
    }
}


// Tabel 13 - Frafald blandt medlemmer - efter aktivitet
$table = 'member_dropout';
$res[$table] = [];
for ($y = $from_year; $y <= $to_year; $y++) {
    $res[$table][$y] = [];
    $to_cut = get_cut($y);
    $from_cut = get_cut($y - 1);

    $step = 'trips';
 
    $s = "SELECT TripType.Name as triptype,
          COUNT(t.member_id) as members,
          SUM(t.trips)  as trips,
          IF((RemoveDate IS NOT NULL AND RemoveDate <= '" . $to_cut . "'), 1, 0) as removed_1,
          IF((RemoveDate IS NOT NULL AND RemoveDate <= '" . get_cut($y + 1) . "'), 1, 0) as removed_2,
          IF((RemoveDate IS NOT NULL AND RemoveDate <= '" . get_cut($y + 3) . "'), 1, 0) as removed_3
          FROM TripType
          INNER JOIN ( SELECT Trip.TripTypeID as TripTypeID,
                       TripMember.member_id as member_id,
		       COUNT(1) as trips
                       FROM Trip
                       INNER JOIN TripMember ON (TripMember.TripID = Trip.id)
                       INNER JOIN Boat ON (Boat.id = Trip.BoatID)
                       INNER JOIN BoatType ON (BoatType.id = Boat.BoatType)
                       WHERE DATE(Trip.OutTime) >= '" . $from_cut . "'
                         AND DATE(Trip.OutTime) < '" . $to_cut . "'
                         AND BoatType.Category=2
                       GROUP BY Trip.TripTypeID, TripMember.member_id
                       HAVING COUNT(1) >= 3
                     ) t ON (t.TripTypeID = TripType.id)
          INNER JOIN Member ON (Member.id = t.member_id)
	  GROUP BY triptype, removed_1, removed_2, removed_3
          ORDER BY triptype";

    $r = $rodb->query($s);
    if ($r) {
       while ($row = $r->fetch_assoc()) {
         if (! isset( $res[$table][$y][$row['triptype']] )) {
	   $res[$table][$y][$row['triptype']] = [ 'total' => 0,
                                                  'trips' => 0,
                                                  'after_1' => ['count' => 0],
                                                  'after_2' => ['count' => 0],
                                                  'after_3' => ['count' => 0]
                                                ];
         }
	 $res[$table][$y][$row['triptype']]['total'] += $row['members'];
	 $res[$table][$y][$row['triptype']]['trips'] += $row['trips'];
	 $res[$table][$y][$row['triptype']]['after_1']['count'] += $row['removed_1'] ? $row['members'] : 0;
	 $res[$table][$y][$row['triptype']]['after_2']['count'] += $row['removed_2'] ? $row['members'] : 0;
	 $res[$table][$y][$row['triptype']]['after_3']['count'] += $row['removed_3'] ? $row['members'] : 0;
       }
       foreach ($res[$table][$y] as $tt => $row) {
          $res[$table][$y][$tt]['trips_per_person'] = round($res[$table][$y][$tt]['trips'] / $res[$table][$y][$tt]['total'], 1);
          $res[$table][$y][$tt]['after_1']['percentage'] = round(100 * $res[$table][$y][$tt]['after_1']['count'] / $res[$table][$y][$tt]['total'], 0);
          if ($y >= $now['year'] - 1) {
	    $res[$table][$y][$tt]['after_2'] = [ 'count' => null, 'percentage' => null ];
          } else {
	    $res[$table][$y][$tt]['after_2']['percentage'] = round(100 * $res[$table][$y][$tt]['after_2']['count'] / $res[$table][$y][$tt]['total'], 0);
          }
          if ($y >= $now['year'] - 2) {
	    $res[$table][$y][$tt]['after_3'] = [ 'count' => null, 'percentage' => null ];
          } else {
	    $res[$table][$y][$tt]['after_3']['percentage'] = round(100 * $res[$table][$y][$tt]['after_3']['count'] / $res[$table][$y][$tt]['total'], 0);
          }
       }
    } else {
       make_error();
       goto end;
    }
}




// Tabel 14 - Både efter turtype og samlet kilometertal
// Tabel 15-16 bruger samme udtræk
$table = 'boats';
$to_cut = get_cut($to_year);
$from_cut = get_cut($to_year - 1);
$step = 'usage';
$res[$table] = [ 'boattypes' => [], 'triptypes' => [], 'boats' => []];

$s = "SELECT Boat.Name AS boat,
             BoatType.Name AS boattype,
             Sum(Meter)/1000 AS distance,
             COUNT(Trip.id) AS trips,
             TripType.Name AS triptype
      FROM BoatType
      INNER JOIN Boat ON (BoatType.id = Boat.BoatType)
      LEFT OUTER JOIN Trip ON Boat.id = Trip.BoatID
      LEFT OUTER JOIN TripType ON (TripType.id = Trip.TripTypeID)
      WHERE DATE(Trip.OutTime) >= '" . $from_cut . "'
        AND DATE(Trip.OutTime) < '"  . $to_cut . "'
      GROUP BY Boat.Name, Boat.id, BoatType.Name, triptype";

$messages[] = $s;
$r = $rodb->query($s);
if ($r) {
  $res[$table]['total'] = ['trips' => 0, 'distance' => 0];
  while ($row = $r->fetch_assoc()) {
    if (! isset($res[$table]['triptypes'][ $row['triptype']])) {
      $res[$table]['triptypes'][ $row['triptype']] = [ 'distance' => 0, 'trips'=> 0, 'boattypes' => [] ];
    } 
    if (! isset($res[$table]['boattypes'][ $row['boattype']])) {
      $res[$table]['boattypes'][ $row['boattype']] = [ 'distance' => 0, 'trips' => 0, 'boats' => [], 'triptypes' => [] ];
    } 
    if (! isset($res[$table]['boats'][ $row['boat']])) {
      $res[$table]['boats'][ $row['boat']] = [ 'distance' => 0, 'trips' => 0, 'triptypes' => [] ];
    } 

    if (! isset($res[$table]['triptypes'][ $row['triptype']]['boattypes'][$row['boattype']])) {
      $res[$table]['triptypes'][ $row['triptype']]['boattypes'][$row['boattype']] = ['distance' => 0, 'trips' => 0];
    }
    if (! isset($res[$table]['boattypes'][ $row['boattype']]['triptypes'][$row['triptype']])) {
      $res[$table]['boattypes'][ $row['boattype']]['triptypes'][$row['triptype']] = ['distance' => 0, 'trips' => 0];
    }


    $res[$table]['triptypes'][ $row['triptype']]['distance'] += $row['distance'];
    $res[$table]['triptypes'][ $row['triptype']]['trips'] += $row['trips'];
    $res[$table]['triptypes'][ $row['triptype']]['boattypes'][$row['boattype']]['distance'] += $row['distance'];
    $res[$table]['triptypes'][ $row['triptype']]['boattypes'][$row['boattype']]['trips'] += $row['trips'];


    $res[$table]['boattypes'][ $row['boattype']]['distance'] += $row['distance'];
    $res[$table]['boattypes'][ $row['boattype']]['trips'] += $row['trips'];
    $res[$table]['boattypes'][ $row['boattype']]['triptypes'][$row['triptype']]['distance'] += $row['distance'];
    $res[$table]['boattypes'][ $row['boattype']]['triptypes'][$row['triptype']]['trips'] += $row['trips'];


    if (! isset($res[$table]['boats'][ $row['boat']]['triptypes'][$row['triptype']])) {
      $res[$table]['boats'][ $row['boat']]['triptypes'][$row['triptype']] = ['distance' => 0, 'trips' => 0];
    }

    $res[$table]['boats'][ $row['boat']]['distance'] += $row['distance'];
    $res[$table]['boats'][ $row['boat']]['boattype'] = $row['boattype'];
    $res[$table]['boats'][ $row['boat']]['trips'] += $row['trips'];
    $res[$table]['boats'][ $row['boat']]['triptypes'][$row['triptype']]['distance'] += $row['distance'];
    $res[$table]['boats'][ $row['boat']]['triptypes'][$row['triptype']]['trips'] += $row['trips'];

    $res[$table]['total']['distance'] += $row['distance'];
    $res[$table]['total']['trips'] += $row['trips'];

    $res[$table]['boattypes'][ $row['boattype']]['boats'][ $row['boat']]  = 1;
  }
 
  foreach ($res[$table]['boattypes'] as $bt => $row) {
    $res[$table]['boattypes'][$bt]['boats'] = $res[$table]['boattypes'][$bt]['boats'] = array_keys( $res[$table]['boattypes'][$bt]['boats'] );
    sort($res[$table]['boattypes'][$bt]['boats']);
    $res[$table]['boattypes'][$bt]['boatcount'] = count($res[$table]['boattypes'][$bt]['boats']);
  }

} else {
  make_error();
  goto end;
}




// Tabel 17 - Ikke roede både
$table = 'boats';
$to_cut = get_cut($to_year);
$from_cut = get_cut($to_year - 1);
$step = 'unused';
$res[$table][$step] = [ 'types' => [], 'boats' => []];

$s = "SELECT b.Name AS boat,
             BoatType.Name AS boattype
       FROM BoatType
       INNER JOIN Boat b ON (BoatType.id = b.BoatType)
       WHERE b.Decommissioned IS NULL
         AND NOT EXISTS (
       	   SELECT 1
	   FROM Trip
	   WHERE b.id = BoatID
             AND DATE(Trip.OutTime) >= '" . $from_cut . "'
             AND DATE(Trip.OutTime) < '" . $to_cut . "'
       )
   ORDER BY BoatType.Name, b.Name";

$r = $rodb->query($s);
if ($r) {
  while ($row = $r->fetch_assoc()) {
    if (! isset($res[$table][$step]['types'][$row['boattype']]) ) {
      $res[$table][$step]['types'][$row['boattype']] = ['count' => 0, 'boats' => []];
    }
    $res[$table][$step]['boats'][] = $row;
    $res[$table][$step]['types'][$row['boattype']]['count']++;
    $res[$table][$step]['types'][$row['boattype']]['boats'][] = $row['boat'];
  }
} else {
  make_error();
  goto end;
}

end:

$rodb->close();


if ($error) {
   $res['error'] = $error;
   $res['status'] = 'error';
}
if (count($messages)) {
   $res['messages'] = $messages;
}

$res['trip_types'] = array_keys($tripTypes);
sort($res['trip_types']);

header('Content-type: application/json;charset=utf-8');
echo json_encode($res)."\n";

