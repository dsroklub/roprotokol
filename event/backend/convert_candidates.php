<?php
include("inc/common.php");
include("inc/utils.php");
verify_right(["admin"=>["roprotokol"]]);
$res=array ("status" => "ok");
$data = file_get_contents("php://input");
$fromto=json_decode($data);
$error=null;
error_log('convertrower '.json_encode($fromto));

$s="SELECT CONCAT(m.FirstName, ' ', IFNULL(m.LastName,'')) as member_name,
           CONCAT(rabbit.FirstName, ' ', IFNULL(rabbit.LastName,'')) as rabbit_name,
           m.id as member_id,
           rabbit.id as rabbit_id,
           rabbit.MemberID as rabbit_number,
           m.MemberID as member_number,
           DATE(MIN(tk.OutTime)) as rabbit_first_trip,
           DATE(MAX(tk.OutTime)) as rabbit_last_trip,
           DATE(MIN(tm.OutTime)) as member_first_trip,
           DATE(m.JoinDate) as member_join_date
    FROM  Member rabbit
          INNER JOIN TripMember tmk ON tmk.member_id=rabbit.id
          INNER JOIN Trip tk ON tmk.TripID=tk.id,
          Member m
          INNER JOIN TripMember tmm ON tmm.member_id=m.id
          INNER JOIN Trip tm ON tmm.TripID=tm.id
    WHERE rabbit.MemberID LIKE 'k%'
      AND rabbit.FirstName NOT LIKE 'udløbet%'
      AND m.id!=rabbit.id
      AND m.MemberID NOT LIKE 'k%'
      AND m.MemberID NOT LIKE 'g%'
      AND m.FirstName = TRIM(TRIM(CHAR(9) FROM rabbit.FirstName))
      AND m.LastName = TRIM(TRIM(CHAR(9) FROM rabbit.LastName))
    GROUP BY rabbit.id,m.id
    HAVING DATEDIFF(rabbit_first_trip, member_join_date) BETWEEN -150 AND 150
        OR DATEDIFF(rabbit_last_trip, member_first_trip) BETWEEN -90 AND 90
   ";

$r = $rodb->query($s);
if ($r) {
  $candidates = [];
  $res['candidates'] = [];
  while ($row = $r->fetch_assoc()) {
    $res['candidates'][] = $row;
    $candidates[] = $row['rabbit_id'];
  }

  $s="SELECT CONCAT(m.FirstName, ' ', IFNULL(m.LastName,'')) as name,
             m.MemberID as rabbit_number,
             m.id as id,
             DATE(MIN(t.OutTime)) as first_trip,
             DATE(MAX(t.OutTime)) as last_trip
      FROM   Member m
             INNER JOIN TripMember tm ON tm.member_id=m.id
             INNER JOIN Trip t ON tm.TripID=t.id
      WHERE m.MemberID LIKE 'k%'
      AND m.FirstName NOT LIKE 'udløbet%'
        AND m.id NOT IN (" . (count($candidates)?join(', ', $candidates):'0') . ")
      GROUP BY m.id
      ORDER BY m.MemberID
     ";

  $r = $rodb->query($s);
  if ($r) {
    $res['rabbits'] = [];
    while ($row = $r->fetch_assoc()) {
      $res['rabbits'][] = $row;
    }
  } else {
    $error = 'Could not find all rabits: ' . $rodb->error;
  }
} else {
  $error = 'Could not find candidates: ' . $rodb->error;
}
if ($error) {
  $res['error'] = $error;
  $res['status'] = 'error';
}
header('Content-type: application/json;charset=utf-8');
echo json_encode($res);
