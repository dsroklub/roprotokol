mysql -u roprotokol --password=roprotokol roprotokol<<EOSQL
  DROP  DATABASE IF EXISTS roprotokol;
  CREATE SCHEMA roprotokol DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
  CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol';
  GRANT ALL PRIVILEGES ON 'roprotokol'.* TO 'roprotokol'@'localhost';
  FLUSH PRIVILEGES;
EOSQL
