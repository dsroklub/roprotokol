<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
$s="
    SELECT CONCAT('{', JSON_QUOTE(baad.navn),': [',
       GROUP_CONCAT(JSON_OBJECT(
   'member_id',Member.MemberId, 
   'name',CONCAT(Member.FirstName,' ',Member.LastName),
    'phone',person.tlf,
    'hours',person.hours
      )),
   ']}') as json
 FROM Member,dsrvinter.baad,dsrvinter.person WHERE person.baad=baad.id AND Member.MemberID=person.ID
GROUP BY person.baad
";
$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"vinter prep");
$stmt->execute() or dbErr($rodb,$res,"vinter exe");
$result= $stmt->get_result() or dbErr($rodb,$res,"vinter res");

echo '[';
$first=1;
while ($row = $result->fetch_assoc()) {
    if ($first) $first=0; else echo ",\n";
    echo $row["json"];
}
echo ']';
invalidate("work");
invalidate("fora");
