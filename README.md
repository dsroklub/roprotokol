# DSR Roprotokol

# Installation

apt-get install python-mysqldb
 
Opret database til roprotokollen:

    CREATE SCHEMA `roprotokol` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol';
    GRANT ALL PRIVILEGES ON `roprotokol`.* TO 'roprotokol'@'localhost';
    FLUSH PRIVILEGES;

Importer skema og views:

    mysql -u roprotokol -p'roprotokol' roprotokol < konvert/mkdb.sql
    mysql -u roprotokol -p'roprotokol' roprotokol < konvert/queries.sql

Skriv adgangsvejen til databasen til filen backend/convert/secret.sh og filen backend/tests/secret.db
Fx:
  echo 'DBCMD="mysql -u roprotokol --password=roprotokol roprotokol"' >  backend/convert/secret.sh
  echo "roprotokol" > backend/tests/secret.db


Sæt adgangkoden i filen backend/inc/db.php. Det er det 3. argument til mysqli

Herefter kan man bruge enten rigtig data fra DSR, hvis man har adgang til de gamle databasefiler. Eller man kan bruge testdata uden personhenførbart data. Det består i store træk af DSR båddata og tilfældigt genereret brugerdata for roere..


BRUG TESTDATA:

./import.sh fake


BRUG DSR DATA:
Eksporter gammel data til sql filer ved at kopiere gammel data Roprotokol_sommer.mdb og Members.mdb.til konvert mappen og køre følgende funktioner:

   ./convert/eksport.sh

Importer gammel data:

   ./import.sh

# Noter om ASP til PHP konvertering

Migrering til MySQL and PHP
består af følgende opgaver.

* Lave et MySQL skema
* Flytte data fra MS Access til Mysql Databasen
* Konvertere ASP til PHP

##SQL skema

Nyt skema er i konvert/mkdb.sql
Det er lavet med mdb-schema, baseret på Roprotokol_sommer.mdb og Members.mdb. Derefter tilrettet i hånden. Tilretningere består af:

* Primærnøgler 
* Indexes. 
* Konsistent brug af store og små bogstaver
* Tilladte tegn i kolonnenavne. "Motion+" er fx blevet til "MotionPlus"	     	  
* Tids og datoformater
* Typer for tal og tekster


##DATA, indhold i databasen

Det er lavet med konvert/eksport.sh og konvert/import.sh som er baseret på mdb-export og mdb-import med lidt perl-kode, der sørger for at det passer med skemaet.

##Procedurer Views

Access/ASP gør brug af en hel del gemte queries. De er konterveret til SQL views i filen queries.sql

##Konvertering fra ASP til PHP

Er foretaget med asp2php og derefter i et vist omfang tilrettet.
Til at tilgå databasen bruges mysqli.

##Hvad virker?

Af php-koden er det kun enkelte funktioner, der virker, fx rostatistikken, valg af bådtype.
SQL-koden virker.

##TODO

Skemast skal opdateres. Fx. dur det ikke at vi har tabeller for Tur, Tur_backup2012,Tur_backup2011 osv.
* I første omgang skal vi nok sørge for at ikke at ændre skemaet mere end vi kan importere indhold fra MDB.
* Mange-til-mange forhold mellem ture og turdeltagere, istedet for 9 faste felter.
* Unikke nøgler. Der er nøgler som burde være unikke, men ikke er erklæret som sådan fordi der er dubletter i datasættet. Det skal rettes i data først.
* tegnsæt
* Ajax-kode, til autocompletion af Navne
* filen membersdata.txt i config.php. herfra hentes Navne og medlemsnumre.
  Vi skal have en bedre måde at overføre det på.
