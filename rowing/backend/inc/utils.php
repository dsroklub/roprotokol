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

function multifield_array($fld,$keys) {
  // Handle deprecated call arguments
  $args = count($keys);

  $res=array();
  if (!is_null($fld)) {
    $rg=explode('££', $fld);
    foreach ($rg as $ri) {
      $ris=explode(":§§:",$ri);

      if (count($ris) != $args) {
        echo "UUUU";print_r($rg);echo "XXXX\n\n";
        error_log("Unparseable multifield for $args columns: >>$fld<<");
      } else {
        $rx = new StdClass();
        for ($i = 0; $i < $args; $i++){
          $keyname = $keys[$i];

          $rx->$keyname=$ris[$i];
        }
        array_push($res,$rx);
      }
    }
  }
  return $res;
}


?>