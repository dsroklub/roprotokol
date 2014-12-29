<?php
function multifield($fld) {
    $res=array();
    $rg=explode(',', $fld);
    foreach ($rg as $ri) {
        $ris=explode(":",$ri);
        $res[$ris[0]]=$ris[1];
    }
    return $res;
}
?>