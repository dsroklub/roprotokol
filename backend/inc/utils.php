<?php
function multifield($fld) {
  $res=array();
  if (!is_null($fld)) {
    $rg=explode('££', $fld);
    foreach ($rg as $ri) {
      $ris=explode(":§§:",$ri);
      if (count($ris) > 1) {
        $res[$ris[0]]=$ris[1];
      } else {
        echo "UUUU";print_r($rg);echo "XXXX";
        error_log("Unparseable multifield: >>$fld<<");
      }
    }
  }
  return $res;
}
?>