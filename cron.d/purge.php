#!/usr/bin/php
<?php
define( 'ROOT_DIR', dirname(__FILE__) );
$config = parse_ini_file(ROOT_DIR . '/../config.ini');
$rodb=new mysqli("localhost",$config["dbuser"],$config["dbpassword"],$config["database"]);

$stmt = $rodb->prepare('
UPDATE Member
SET Password=NULL,Address=NULL,FK_Postnr=NULL,phone1=NULL,phone2=NULL,Birthday=NULL,
  Email=NULL,Gender=NULL,KommuneKode=NULL,CprNo=NULL,notificationEmail=NULL,
  FirstName="Ikke medlem",LastName=MemberID, member_type=-1
WHERE DATE_ADD(RemoveDate,INTERVAL 3 YEAR) < CURDATE()
') or exit("purge sql error ". $rodb->error);

$stmt->execute();
echo "DONE\n";
