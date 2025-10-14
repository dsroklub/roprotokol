<?php

set_include_path(get_include_path().':..');
include("inc/common.php");

$url = "https://danskestudentersroklub-rest.membercare.dk/api/v1/persons?page=1&pageSize=5000&includeEmployments=false&includeMemberships=true&includeBoardMemberships=false&includeUnionRepresentatives=false&includeUnionGroups=false&onlyValid=true&includeCustomFields=false&includeInterests=false&includeProfilePictureIdentification=false";
$ch = curl_init();

$impUrl="https://danskestudentersroklub-rest.membercare.dk/api/v1/token?clientApiKey=${wstoken}&personToImpersonate=11328";
// echo "impUrl= $impUrl\n";
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
curl_setopt($ch, CURLOPT_URL, $impUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json']);
$tokenS = curl_exec($ch);
$tokenA=json_decode($tokenS,true);
#print_r($tokenA);
$token=json_decode($tokenS,true)["value"];
curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json',"token: ${token}"]);

$membersS = curl_exec($ch);
#echo $membersS;
if (!curl_errno($ch)) {
  switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
    case 200:  # OK
      break;
    default:
      echo 'Unexpected HTTP code: ', $http_code, "\n";
  }
}

//print_r(curl_getinfo($ch));

if (!$membersS) {
    echo "\nmember get failed\n";
}

$members=json_decode($membersS,true);

$rodb->begin_transaction();
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
        //echo $member["name"]." exists\n";
    } else {

        if (! in_array($member["name"],["Konvertering Membercare","Økonomisystem Integration","Membersite Connector","Roprotokol API","Niels Bak2","Christina Brandstrup2","Alex Henry2"]) ) {
            echo $member["name"]." ".$memberID." mangler i roprotokollen\n";
            // print_r($member);
            if (empty($member["memberships"])) {
                echo $member["name"]." ".$memberID." has no memberships, skipping\n";
                continue;
            }
            $joinDate=$member["memberships"][0]["applicationDate"];
            $removeDate=$member["memberships"][0]["disaffiliateDate"];

            $memberType=mb_strtolower($member["memberships"][0]["membershipCategory"]["membershipCategoryGroup"]["description"]);
            if ($memberType=="accocieret") {
                $memberType=="associeret";
            }
            if (empty($member["addresses"])) {
                echo $member["name"]." ".$memberID." har ingen adresse, så ingen kommunekode\n";
            } else if (empty($member["addresses"][0]["municipality"])) {
                echo $member["name"]." ".$memberID." har adresse men ingen kommune, så ingen kommunekode\n";
            } else {
                $kommunekode=$member["addresses"][0]["municipality"]["officialCode"];
            }
            if ($member["gender"]==2) {
                $gender=1;
            } else if ($member["gender"]==1) {
                $gender=0;
            } else if ($member["gender"]==0) {
                $gender=0;
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
            $stmt = $rodb->prepare(
                "INSERT INTO Member(MemberID,FirstName,LastName,phone1,Email,Gender,KommuneKode,CprNo,membertype,JoinDate,RemoveDate,Birthday)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            echo "id=$memberID,fm=$firstName,ln=$lastName,ph=$phone,email=$email,g=$gender,kk=$kommunekode,cpr=$CprNo,mtype=$memberType,jd=$joinDate,rd=$removeDate,bd=$birthDay";
            $stmt->bind_param('sssssiiissss',
                              $memberID,$firstName,$lastName,$phone,$email,$gender,$kommunekode,$CprNo,$memberType,$joinDate,$removeDate,$birthDay)|| dbErr($rodb,$res,"member insert");
            $stmt->execute() || dbErr($rodb,$res,"membersite member error");
        }
    }
}
$rodb->commit();
curl_close($ch);
