<?php
require("inc/utils.php");
include("../../rowing/backend/inc/common.php");
verify_real_user();
$data = file_get_contents("php://input");
error_log("create $data");
$newforum=json_decode($data);
$message='';
error_log("NEW FORUM: ".print_r($newforum,true));
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $cuser=$_SERVER['PHP_AUTH_USER'];
}

/*,MemberRights AND */
/*    MemberRights.member_id=Member.id AND */
/*    MemberRights.MemberRight='event' AND */
/*    MemberRights.argument='fora' LIMIT 1 */


$forumEmail=saneEmail($newforum->forum);
$forumName=sanestring($newforum->forum);

$stmt = $rodb->prepare(
    "INSERT INTO forum (name,email_local,description,is_open,is_public,owner,created_by)
   SELECT ?,?,?,?,?,Member.id,Member.id FROM Member WHERE
   Member.MemberId=?"
) or dbErr($rodb,$res,"Forum create b $forumEmail");

$triptype="NULL";
$isopen=empty($newforum->is_open)?0:1;
$ispublic=empty($newforum->is_public)?0:1;
$stmt->bind_param(
    'sssiis',
    $newforum->forum,
    $forumEmail,
    $newforum->description,
    $isopen,
    $ispublic,
    $cuser
) or  dbErr($rodb,$res,"create forum BIND errro ");

$stmt->execute() || dbErr($rodb,$res," forum exe error newforum=".print_r($newforum,true));

if ($newforum->owner_subscribe) {
    $stmt = $rodb->prepare(
        "INSERT INTO forum_subscription(member,forum,role)
         SELECT Member.id ,forum.name, 'owner'
         FROM Member, forum
         WHERE
           forum.name=? AND
           MemberId=?") or dbErr($rodb,$res,"forum sub prep");
    $stmt->bind_param(
            'ss',
            $forumName,
            $cuser) or dbErr($rodb,$res,"create forum owner subscribe BIND ");
    $stmt->execute() or dbErr($rodb,$res, "forum create owner subscription ");
}
invalidate("event");
invalidate("fora");
echo json_encode($res);
