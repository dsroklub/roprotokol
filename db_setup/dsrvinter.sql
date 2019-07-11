
CREATE DATABASE IF NOT EXISTS dsrvinter;


CREATE TABLE IF NOT EXISTS dsrvinter.baad (
  ID int(10) unsigned NOT NULL AUTO_INCREMENT,
  navn varchar(135) NOT NULL,
  type int(10) unsigned NOT NULL,
  beskrivelse text  NOT NULL,
  periode varchar(255)  NOT NULL,
  max_timer int(10)  NOT NULL,
  hidden tinyint(3)  NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `navn` (`navn`),
  KEY `type` (`type`),
);

CREATE TABLE IF NOT EXITS baadformand(
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  baad int(10) unsigned NOT NULL,
  formand int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY baad (baad),
  KEY formand (formand),
)


GRANT SELECT ON dsrvinter.person TO 'roprotokol'@'localhost';
GRANT SELECT ON dsrvinter.baad TO 'roprotokol'@'localhost';
GRANT SELECT ON dsrvinter.baadformand TO 'roprotokol'@'localhost';
