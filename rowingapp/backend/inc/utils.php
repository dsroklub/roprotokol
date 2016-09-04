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

// Please call using
//   multifield_array($input, ['field1', 'field2'...]);
//
// Deprecated uses:
//    multifield_array($input, 'field1', 'field2')
function multifield_array($fld,$keys,   $deprecated_valname) {
  // Handle deprecated call arguments
  if ($deprecated_valname) {
    if (! is_array($keys)) {
       $keys = [$keys, $deprecated_valname];
    } else {
       die("Unsuitable arguments for multifield_array");
    }
  }
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