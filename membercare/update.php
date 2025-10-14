<?php
set_include_path(get_include_path().':..');
include("inc/common.php");

$url = "https://danskestudentersroklub-rest.membercare.dk/api/v1/persons?page=1&pageSize=5000&includeEmployments=false&includeMemberships=false&includeBoardMemberships=false&includeUnionRepresentatives=false&includeUnionGroups=false&onlyValid=true&includeCustomFields=false&includeInterests=false&includeProfilePictureIdentification=false";
$ch = curl_init();

$impUrl="https://danskestudentersroklub-rest.membercare.dk/api/v1/token?clientApiKey=${wstoken}&personToImpersonate=11328";
echo "impUrl= $impUrl\n";
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
curl_setopt($ch, CURLOPT_URL, $impUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json']);
$tokenS = curl_exec($ch);
#echo "tokenS=$tokenS\n";
$tokenA=json_decode($tokenS,true);
#print_r($tokenA);
$token=json_decode($tokenS,true)["value"];

curl_setopt($ch, CURLOPT_URL, $url);

#echo "token: ${token}\n";

curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json',"token: ${token}"]);

$membersS = curl_exec($ch);


if (!curl_errno($ch)) {
  switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
    case 200:  # OK
      break;
    default:
      echo 'Unexpected HTTP code: ', $http_code, "\n";
  }
}


#print_r(curl_getinfo($ch));

if (!$membersS) {
    echo "\nmember get failed\n";
}

$members=json_decode($membersS,true);

#print_r($members);


$s="
SELECT
  Member.MemberID,
  CprNo,
  phone1,
  Gender,
  Email,
  KommuneKode,
  JoinDate,
  RemoveDate,
  member_type,
  Member.FirstName,
  Member.LastName
FROM
      Member
WHERE
     Member.MemberID!='0'
";

if ($sqldebug) {
    echo $s."\n<br>\n";
}
$dbrowersR=$rodb->query($s) or dbErr($rodb,$res,"get rowers");
while ($rower = $dbrowersR->fetch_assoc()) {
    $dbrowers[$rower["MemberID"]]=$rower;
}


foreach ($members["result"] as $member) {
    $memberID=$member["debtorAccountNumber"];
    $email=null;
    $CprNo=null;
    $gender=null;
    $kommunekode=null;
    $memberType=null;
    $phone=null;
    $joinDate=null;
    $removeDate=null;
    $birthDay=null;
    if (isset($dbrowers[$memberID])) {
        #echo $member["name"]." exists\n";
    } else {
        $murl = "https://danskestudentersroklub-rest.membercare.dk/api/v1/persons/$memberID/memberships";
        curl_setopt($ch, CURLOPT_URL, $murl);
        $membershipsS = curl_exec($ch);
        $memberships=json_decode($membershipsS,true)["results"];

        foreach ($memberships as $membership) {
            $joinDate=$membership["applicationDate"];
            $removeDate=$membership["disaffiliateDate"];
            $memberType=$membership["membershipCategory"]["description"];
        }
        if (! in_array($member["name"],["Konvertering Membercare","Økonomisystem Integration","Membersite Connector","Roprotokol API","Niels Bak2","Christina Brandstrup2","Alex Henry2"]) ) {
            echo $member["name"]." ".$memberID." does not exists\n";
        }
        $gender=$member["gender"]-1;
        if ($gender<0) {
            $gender=null;
        }
        $firstName=$member["firstname"];
        $lastName=$member["lastname"];
        $CprNo=!empty($member["socialSecurityNumber"]["number"]);
        $birthDay=$member["birthDate"];
        foreach ($member["contacts"] as $contact) {
            if ($contact["type"]==1) {
                $email=$contact["value"];
            } else if ($contact["type"]==5) {
                $phone=$contact["value"];
            }
        }
        $memberType=$member["memberType"];

        $stmt = $rodb->prepare(
            "INSERT INTO Member(MemberID,FirstName,LastName,phone1,Email,Gender,KommuneKode,CprNo,membertype)
            VALUES(?,?,?,?,?,?,?,?,?)"
        );
        $stmt->bind_param('sssssiiis',
                          $memberID,$firstName,$lastName,$phone,$email,$gender,$kommunekode,$CprNo,$memberType)|| dbErr($rodb,$res,"member insert");
        $stmt->execute() || dbErr($rodb,$res,"membersite member error");

    }
}
curl_close($ch);
