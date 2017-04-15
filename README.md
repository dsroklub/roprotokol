# DSR Roprotokol

# Documentation

## [For DSR in Danish:](http://htmlpreview.github.io/?https://github.com/dsroklub/roprotokol/blob/master/documentation/DSR.html)

# Demo:

https://agol.dk/roprotokol/frontend/app/real.html


# Installation

    apt-get install npm python-mysqldb memcached php-memcached php-mysql libapache2-mod-php nodejs nodejs-legacy mysql-server
    # or mariadb
    #  php-mysqlnd/php-mysql is needed to make PHP know the difference between numbers and strings
    apt-get install  libaprutil1-dbd-mysql

    # Remember to restart your web server.

   a2enmod dbd  rewrite
   a2enmod authn_dbd
   a2enmod php7.0 

    sudo npm install -g bower karma

    # for debug scripts
    apt-get install php-cli

    cd rowingapp/frontend; npm install

Set <code>group_concat_max_len = 50000</code> e.g. in /etc/mysql/my.cnf

Create database:

    CREATE SCHEMA roprotokol DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol';
    GRANT ALL PRIVILEGES ON roprotokol.* TO 'roprotokol'@'localhost';
    FLUSH PRIVILEGES;

Import schema:


    mysql -u roprotokol -p'roprotokol' roprotokol < db_setup/mkdb.sql

Copy config.ini.template to config.ini and adjust


Test Webserver:

Run
<code>
   cd rowingapp/
   php5 -S localhost:8080
</code>


