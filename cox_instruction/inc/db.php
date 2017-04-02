<?php
$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../config.ini');

// include af denne fil medfører automatisk connection. Brug $link

function print_array(&$a) {
  echo "<hr><pre>";
  print_r($a);
  echo "</pre><hr>\n";
}

function sql_connect() {
  global $config;
  $link = new mysqli("localhost", $config["dbuser"], $config["dbpassword"], $config["database"]);

  if ($link->connect_errno) {
    printf("Could not connect to database: %s\n", $mysqli->connect_error);
    exit();
  }
  $link->set_charset('utf8');
  return $link;
}

function generate_password($len = 10) {
   $vokaler = array('a', 'e', 'i', 'o', 'u', 'y');
   $konsonanter = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'z');
   $res = '';
   for ($i = 0; $i < $len; $i++) {
	if ($i % 2 == 0) {
	   $res .= $konsonanter[ mt_rand(0, 18) ];
        } else {
           $res .= $vokaler[ mt_rand(0, 5) ];
	}
   }

   return $res;
}

function get_setting($name, $link = false) {
  if (!$link) {
    $link = $GLOBALS['link'];
  }
  $res = $link->query("SELECT * FROM settings WHERE name = '" . $link->escape_string($name) . "'");
  if ($res && $row = $res->fetch_assoc() ) {
    return $row['content'];
  }
  return "";
}

$link=sql_connect();
?>
