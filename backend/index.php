<?php
echo "<h2>Index of backend:</h2>";
$g = glob("*.php");
echo implode("<br>",
	     array_map(function($a) {return '<a href="/DSR-roprotokol/backend/'.$a.'">'.$a.'</a>';},$g)
	     );
