# DSR Roprotokol

# Documentation

## [For DSR in Danish:](http://htmlpreview.github.io/?https://github.com/elgaard/DSR-roprotokol/blob/master/documentation/DSR.html)

# Demo:

https://agol.dk/roprotokol/frontend/app/real.html


# Installation

apt-get mdbtools
apt-get install npm python-mysqldb memcached php5-memcached mysql-server
apt-get install php5-mysqlnd nodejs nodejs-legacy
  # php5-mysqlnd nødvendig for at få PHP til at kende forskel på strenge og tal
  # husk at genstarte webserveren

sudo npm install -g bower


mkdir frontend/app/bower_components

# for debug scripts
apt-get install php5-cli

Opret database til roprotokollen:

    CREATE SCHEMA roprotokol DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol';
    GRANT ALL PRIVILEGES ON roprotokol.* TO 'roprotokol'@'localhost';
    FLUSH PRIVILEGES;

Importer skema og views:

Sæt <code>group_concat_max_len = 50000</code> fx i /etc/mysql/my.cnf

    mysql -u roprotokol -p'roprotokol' roprotokol < convert/mkdb.sql

  copier config.ini.template til config.ini og tilret
Skriv adgangsvejen til databasen til filen secret.sh
Fx:
  echo 'DBCMD="mysql -u roprotokol --password=roprotokol roprotokol"' >  secret.sh



Test Webserver:

Kør
<code>
   cd rowingapp/
   php5 -S localhost:8080
</code>


##SQL skema

Nyt skema er i convert/mkdb.sql
Det er lavet med mdb-schema, baseret på Roprotokol.mdb og Members.mdb. Derefter tilrettet i hånden. Tilretningere består af:

* Primærnøgler 
* Indexes. 
* Konsistent brug af store og små bogstaver
* Tilladte tegn i kolonnenavne. "Motion+" er fx blevet til "MotionPlus"	     	  
* Tids og datoformater
* Typer for tal og tekster


##DATA, indhold i databasen

Det er lavet med konvert/eksport.sh og konvert/import.sh som er baseret på mdb-export og mdb-import med lidt perl-kode, der sørger for at det passer med skemaet.


