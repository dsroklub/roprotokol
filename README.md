# DSR Roprotokol

# Documentation

## [For DSR in Danish:](http://htmlpreview.github.io/?https://github.com/elgaard/DSR-roprotokol/blob/master/documentation/DSR.html)

# Installation

apt-get install npm python-mysqldb memcached php5-memcached
apt-get install php5-mysqlnd nodejs nodejs-legacy
  # nødvendigt for at få PHP til at kende forskel på strenge og tal
  # husk at genstarte webserveren

sudo npm install -g bower


mkdir frontend/app/bower_components
cd frontent
bower install ng-table
npm install

# for debug scripts
apt-get install php5-cli

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
Eksporter gammel data til sql filer ved at kopiere gammel data Roprotokol.mdb og Members.mdb.til konvert mappen og køre følgende funktioner:

   ./backend/convert/eksport.sh

Importer gammel data:

   ./backend/convert/import.sh real

Test Webserver:

Kør
<code>
   php5 -S localhost:8080
</code>
i roden


# Noter om ASP til PHP konvertering

Migrering til MySQL and PHP består af følgende opgaver.

* Lave et MySQL skema
* Flytte data fra MS Access til Mysql Databasen

Begge dele er overstået.
Der er genereret PHP ud fra ASP-koden, men det benyttes ikke og er kun vedlagt som reference. I stedet er der skrevet et nyt system i Angular med en simpel backend i PHP. 

##SQL skema

Nyt skema er i backend/convert/mkdb.sql
Det er lavet med mdb-schema, baseret på Roprotokol.mdb og Members.mdb. Derefter tilrettet i hånden. Tilretningere består af:

* Primærnøgler 
* Indexes. 
* Konsistent brug af store og små bogstaver
* Tilladte tegn i kolonnenavne. "Motion+" er fx blevet til "MotionPlus"	     	  
* Tids og datoformater
* Typer for tal og tekster


##DATA, indhold i databasen

Det er lavet med konvert/eksport.sh og konvert/import.sh som er baseret på mdb-export og mdb-import med lidt perl-kode, der sørger for at det passer med skemaet.


##TODO

### BUGS

* Time zone error in checkout times

# Database ission.

can be done manually when we move to the new system.

* Trim destination names in DB for trailing whitespace
* Unikke nøgler. Der er nøgler som burde være unikke, men ikke er erklæret som sådan fordi der er dubletter i datasættet. Det skal rettes i data først.

### Missing features

* Indberetning af fejl på ture
* Behandling af fejl på ture
* Kovertering af kanin/midlertidig roer
* Administration af røde svensknøgler

* Administration af rettigheder for roere
* Administration af rettigheder for turtyper

* Administration af både
  ** Opret/ret/slet bådtype
  ** Opret båd
  ** Ret båd
  ** Pensioner båd
  ** Opret/ret/slet lokation

* Reservation af både
* Indberetning af fejl i rettigheder -> mail til instruktionschefen

** Hvis turen er kortere end x minutter, så foreslå at slette turen i stedet
* Kommenter skade

** Roller og Rettigheder. Fx at man ikke skal logge ind på terminalerne i bådhallen.

** Statistik fordelt på turtyper.
   SQL forespørgslerne er lavet. Der skal laves et webinterface til dem.
** Turoversigt: handler om at de steder, vi viser ture, skal man kunne
  klikke på en tur og se turens data og roerne på turen, og de steder vi
  viser roere, skal man kunne klikke på en roer og se roeren ture.
  Vi bliver nok nødt til at reorganisere lidt. Måske lave et nested scope.

* Statistik
	** både
* Årsstatistik
* Konfiguration af klienter. Noget local storage. Klienter i bådhallen skal vide, at de er i bådhallen og hvilken klient de er (fx et hostnavn)
* Updatere browsere i baadhallen. Chrome og Firefox virker i nyeste udgaver.
* Do form validation: http://stackoverflow.com/questions/27224661/angularjs-validation-for-ui-select-multiple

* Show wrench next to name in checkout view


### Terminaler

Slå autofill fra i Chromium

# Feature ønsker

## Roere.

Vi kunne checke at den samme roer ikke kan være udskrevet flere gange.

Typeahead for roere, der udskrives kunne forfines, så den kun vis roere, som ikke er på vandet og som passer med turtypen. Fx kun kaprorer for INKA ture.
