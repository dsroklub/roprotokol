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


Herefter kan man bruge enten rigtig data fra DSR, hvis man har adgang til de gamle databasefiler. Eller man kan bruge testdata uden personhenførbart data. Det består i store træk af DSR båddata og tilfældigt genereret brugerdata for roere.


BRUG TESTDATA:

./convert/import.sh roprotokol fake database password
(man kan angive en anden database end roprotokol)


BRUG DSR DATA:
Eksporter gammel data til sql filer ved at kopiere gammel data Roprotokol.mdb og Members.mdb.til konvert mappen og køre følgende funktioner:

   ./convert/eksport.sh

Importer gammel data:

   ./convert/import.sh real

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


##TODO

* Afkobl distance fra json datalisten.

* Fix background, white again

*Ikon til ture

### BUGS

# Database issues.

can be done manually when we move to the new system.

* Unikke nøgler. Der er nøgler som burde være unikke, men ikke er erklæret som sådan fordi der er dubletter i datasættet. Det skal rettes i data først.
Senere kan vi lave en alter statement i databasen, så det ikke sker igen.

* Der er et problem med den timepicker vi bruger. Man kan slette minutter og senere få en exception

### Missing features

* Administration af både
  ** slet bådtype


### Feature requests

* Reservation af både
* Indberetning af fejl i rettigheder -> mail til instruktionschefen

* Kommenter skade


* Årsstatistik
* Konfiguration af klienter. Noget local storage. Klienter i bådhallen skal vide, at de er i bådhallen og hvilken klient de er (fx et hostnavn)
* opdatere browsere i baadhallen. Chrome og Firefox virker i nyeste udgaver.
* Do form validation: http://stackoverflow.com/questions/27224661/angularjs-validation-for-ui-select-multiple

### Terminaler

##TODO

# Feature ønsker

## Roere.

* Vi kunne checke at den samme roer ikke kan være udskrevet flere gange.
* Typeahead for roere, der udskrives kunne forfines, så den kun vis roere, som ikke er på vandet og som passer med turtypen. Fx kun kaprorer for INKA ture.
