<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");


$s="SELECT is_public,forum_subscription.role,JSON_MERGE(
   JSON_OBJECT(
    'forum', forum.name,
     'boat',forum.boat,
     'forumtype',forum.forumtype,
     'description',forum.description,
     'owner',owner.MemberID,
     'is_open',is_open,
     'is_public',is_public,
     'role',forum_subscription.role
     ),
     CONCAT(
     '{', JSON_QUOTE('folders'),': [',
        IFNULL(GROUP_CONCAT(DISTINCT CONCAT(JSON_QUOTE(forum_file.folder)) SEPARATOR ','),''),
   ']}')
   ) AS json
    FROM Member owner,
        (forum JOIN Member m LEFT JOIN forum_subscription ON (forum.name=forum_subscription.forum AND forum_subscription.member=m.id)
      LEFT JOIN forum_file on forum_file.forum=forum.name)
    WHERE owner.id=forum.owner and m.MemberID=?
    GROUP BY forum_file.forum,forum.name,roprotokol.forum_subscription.role
HAVING is_public OR role IS NOT NULL;
" ;


if ($sqldebug) {
    echo "$s<br>\n";
}

$stmt = $rodb->prepare($s) or dbErr($rodb,$res,"fora Q $s");
$stmt->bind_param("s", $cuser)  or dbErr($rodb,$res,"fora B");
$stmt->execute() or dbErr($rodb,$res,"fora Exe");
$result= $stmt->get_result() or dbErr($rodb,$res,"Error in fora query: " );

output_json($result);
$rodb->close();
