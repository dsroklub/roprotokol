<?php
if (isset($_GET["returnlink"])){
    if ($_GET["returnlink"]=="history") {
        print '<FORM><INPUT Type="button" VALUE="Tilbage til roprotkollen" onClick="history.go(-1);return true;"></FORM>';
    } else {    
        print '<h2><a href="/frontend/app/real.html">Tilbage til roprotokollen</a></h2>';
    }
}
?>
