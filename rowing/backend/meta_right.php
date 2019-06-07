<?php
include("inc/common.php");

$s="SELECT member_right,arg,description,showname,predicate From  MemberRightType ORDER by description,arg";
$metas = $rodb->query("SELECT JSON_OBJECT('member_right',member_right,'meta',meta) as json FROM meta_right") or dbErr($rodb,$res,"meta right");
$first=1;
echo '[';
while ($meta = $metas->fetch_assoc()) {
    if ($first) $first=0; else echo ',';
    echo $meta["json"];
}
     echo ']';
$rodb->close();
