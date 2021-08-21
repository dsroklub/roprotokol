<?php
if (isset($_GET["returnlink"])){
    if ($_GET["returnlink"]=="history") {
        print '<FORM><INPUT Type="button" VALUE="Tilbage til DSR aftaler" onClick="history.go(-1);return true;"></FORM>';
    } else {
        print '<h2><a href="/frontend/event/index.shtml">Tilbage til DSR aftaler</a></h2>';
    }
}
