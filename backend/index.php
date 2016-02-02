<?php
echo "<h2>Index of backend:</h2>";
$dn= str_replace('/index.php','',$_SERVER['REQUEST_URI']);
$dn = rtrim($dn, '/');
error_log("dir ".$dn);
$g = glob("{*.php,*/*.php}",GLOB_BRACE);
echo implode("<br>",
array_map(function($a) {global $dn; return '<a href="'. $dn . '/'.$a.'">'.$a.'</a>';},$g)
	     );
