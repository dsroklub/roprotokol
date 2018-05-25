# DSR Roprotokol

# Documentation

## [For DSR in Danish:](http://htmlpreview.github.io/?https://github.com/dsroklub/roprotokol/blob/master/documentation/DSR.html)

# Demo:

https://agol.dk/roprotokol/frontend/app/real.html


# Installation

    apt-get install npm python-mysqldb memcached php-memcached php-mysql libapache2-mod-php nodejs nodejs-legacy mysql-server php-mail composer php-cli
    # or mariadb
    #  php-mysqlnd/php-mysql is needed to make PHP know the difference between numbers and strings
    apt-get install  libaprutil1-dbd-mysql pkg-php-tools
    # Remember to restart your web server.

   a2enmod dbd  rewrite include authn_dbd  php7.2
   sudo npm install -g bower karma

   # for debug scripts
   cd rowingapp/phplib; composer update
   cd rowingapp/frontend; npm install

Create database:

   sudo mysqladmin  --default-character-set=utf8 create roprotokol 
    CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol';
    CREATE USER 'apacheauth'@'localhost' IDENTIFIED BY 'XXXX';
    GRANT ALL PRIVILEGES ON roprotokol.* TO 'roprotokol'@'localhost';
    GRANT ALL PRIVILEGES ON roprotokol.authentification TO 'apacheauth'@'localhost';
    FLUSH PRIVILEGES;


Import schema:


    mysql -u roprotokol -p'roprotokol' roprotokol < db_setup/mkdb.sql

Copy config.ini.template to config.ini and adjust


Test Webserver:

Run
<code>
   cd rowingapp/
   php -S localhost:8080
</code>


