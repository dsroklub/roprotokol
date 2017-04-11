<?php
echo "<h2>INDEX of ".getcwd().":</h2>";
$g = glob("*.php");
$dn= str_replace('/index.php','',$_SERVER['REQUEST_URI']);
$dn = rtrim($dn, '/');
echo implode("<br>",
array_map(function($a) {global $dn; return '<a href="'.$dn.'/'.$a.'">'.$a.'</a>';},$g)
	     );
