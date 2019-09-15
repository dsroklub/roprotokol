<?php
include("../../rowing/backend/inc/common.php");
include("utils.php");
$sql="
SELECT requirement, end_time,description
FROM worker, Member
WHERE Member.id=member_id AND worker.assigner='vedligehold'";
