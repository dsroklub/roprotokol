<?php
ini_set('default_charset', 'utf-8');
ini_set('display_errors', 'On');
error_reporting(E_ALL);

if(!isset($_SESSION)){
  session_start();
}

require_once("db.php");
?>