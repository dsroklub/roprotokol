# DSR Roprotokol

# Installation

apt-get install python-mysqldb
apt-get install php5-mysqlnd
  # nødvendigt for at få PHP til at kende forskel på strenge og tal
  # hust at genstarte webserveren

Opret database til roprotokollen:

    CREATE SCHEMA roprotokol DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol';
    GRANT ALL PRIVILEGES ON roprotokol.* TO 'roprotokol'@'localhost';
    FLUSH PRIVILEGES;

Importer skema og views:

    mysql -u roprotokol -p'roprotokol' roprotokol < backend/convert/mkdb.sql

  copier config.ini.template til config.ini og tilret
Skriv adgangsvejen til databasen til filen backend/convert/secret.sh og filen backend/tests/secret.db
Fx:
  echo 'DBCMD="mysql -u roprotokol --password=roprotokol roprotokol"' >  backend/convert/secret.sh
  echo "roprotokol" > backend/tests/secret.db


Herefter kan man bruge enten rigtig data fra DSR, hvis man har adgang til de gamle databasefiler. Eller man kan bruge testdata uden personhenførbart data. Det består i store træk af DSR båddata og tilfældigt genereret brugerdata for roere..


BRUG TESTDATA:

./backend/convert/import.sh fake


BRUG DSR DATA:
Eksporter gammel data til sql filer ved at kopiere gammel data Roprotokol_sommer.mdb og Members.mdb.til konvert mappen og køre følgende funktioner:

   ./backend/convert/eksport.sh

Importer gammel data:

   ./backend/convert/import.sh real

Test Webserver:

Kør
   php5 -S localhost:8080
i roden


# Noter om ASP til PHP konvertering

Migrering til MySQL and PHP består af følgende opgaver.

* Lave et MySQL skema
* Flytte data fra MS Access til Mysql Databasen

Begge dele er overstået.
Der er genereret PHP ud fra ASP-koden, men det benyttes ikke og er kun vedlagt som reference. I stedet er der skrevet et nyt system i Angular med en simpel backend i PHP. 

##SQL skema

Nyt skema er i backend/convert/mkdb.sql
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

Access/ASP gør brug af en hel del gemte queries. De er konverteret til SQL views i filen queries.sql
Det forventes, at disse views ikke skal anvendes i det nye system.

##Konvertering fra ASP til PHP

Er foretaget med asp2php og derefter i et vist omfang tilrettet.
Til at tilgå databasen bruges mysqli.

##Hvad virker?

Af php-koden er det kun enkelte funktioner, der virker, fx rostatistikken, valg af bådtype.
SQL-koden virker.

##TODO

* Unikke nøgler. Der er nøgler som burde være unikke, men ikke er erklæret som sådan fordi der er dubletter i datasættet. Det skal rettes i data først.
* Administration af både
** Opret båd
** Ret båd
** Pensioner båd
** Opret/ret/slet lokation
* Reservation af både
* Indberetning af fejl på ture
* Behandling af fejl på ture
* Opret/ret/slet bådtype
* Indberetning af fejl i rettigheder -> mail til instruktionschefen
* Kovertering af kanin/midlertidig roer
* Administration af røde svensknøgler
* Skift rosæson
* Udskriv båd
* Indskriv båd
** Hvis turen er kortere end x minutter, så foreslå at slette turen i stedet
* Meld skade
* Kommenter skade
* Klarmeld skade
* Skadesliste for alle både
* Vis roer
** Rettigheder
** Statistik fordelt på turtyper
** Turoversigt
* Dagens ture
** Både på vandet
* Statistik for både
* Statistik over roere
* Årsstatistik


# Terminaler

Slå autofill fra i Chromium
