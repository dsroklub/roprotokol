<?php
$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);
function process ($result,$output="json",$name="cvsfile",$captions=null) {
    if ($output=="json") {
        header('Content-type: application/json;charset=utf-8');
        echo '[';
        $rn=1;
        while ($row = $result->fetch_assoc()) {
            if ($rn>1) echo ',';
            echo json_encode($row,JSON_PRETTY_PRINT);
            $rn=$rn+1;
        }
        echo ']';
    } else if ($output=="csv") {
        header('Content-type: text/csv');
        header('Content-Disposition: filename="'.$name.'.csv"');
        if ($captions) {
            echo implode(",",$captions)."\n";
        }
        while ($row = $result->fetch_assoc()) {
            echo implode(",",$row)."\n";
        }
    }  else if ($output=="text" || $output=="html") {
        header('Content-type: text/html');
        echo " <link rel=\"stylesheet\" href=\"/public/basic.css\">\n<table>\n";
        if ($captions) {
            echo "<tr>\n<th>";
            echo implode("</th><th>",$captions)."\n";
            echo "</th></tr>\n";
        }
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>";
            echo implode("</td><td>",$row)."\n";
            echo "</td></tr>\n";
        }
        echo "</table>\n";
    }
}
