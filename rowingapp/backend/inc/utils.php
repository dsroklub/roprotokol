<?php
function multifield($fld) {
  $res=array();
#  print_r($fld."\n\n");
  if (!is_null($fld)) {
    $rg=explode('££', $fld);
    foreach ($rg as $ri) {
      $ris=explode(":§§:",$ri);
      if (count($ris) > 1) {
        $res[$ris[0]]=$ris[1];
      } else {
        echo "UUUU";print_r($rg);echo "XXXX\n\n";
        error_log("Unparseable multifield: >>$fld<<");
      }
    }
  }
  return $res;
}

function multifield_array($fld,$keyname,$valname) {
  $res=array();
  if (!is_null($fld)) {
    $rg=explode('££', $fld);
    foreach ($rg as $ri) {
      $ris=explode(":§§:",$ri);
      $rx = new StdClass();
      $rx->$keyname=$ris[0];
      $rx->$valname=$ris[1];
//      echo "\nrix=";
//            print_r($ri);
//      echo "\nris=";
//            print_r($ris);

      if (count($ris) > 1) {
          array_push($res,$rx);
//        $res[$ris[0]]=$ris[1];
      } else {
        echo "UUUU";print_r($rg);echo "XXXX\n\n";
        error_log("Unparseable multifield: >>$fld<<");
      }
    }
  }
  return $res;
}
?>