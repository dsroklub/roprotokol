<?php
function multifield($fld) {
  $res=array();
  $rg=explode(',', $fld);
  foreach ($rg as $ri) {
    $ris=explode(":",$ri);
    if (count($ris) > 1) {
      $res[$ris[0]]=$ris[1];
    } else {
      error_log("Unparseable multifield: $fld");
    }
  }
  return $res;
}
?>