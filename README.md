# DSR Roprotokol

# Documentation

## [For DSR in Danish:](http://htmlpreview.github.io/?https://github.com/dsroklub/roprotokol/blob/master/documentation/DSR.html)

# Demo:

https://agol.dk/roprotokol/frontend/app/real.html


# Installation

    apt-get install mdbtools
    apt-get install npm python-mysqldb memcached php-memcached mysql-server
    apt-get install php5-mysqlnd nodejs nodejs-legacy
    # php5-mysqlnd is needed to make PHP know the difference between numbers and strings
    # Remember to restart your web server.

    sudo npm install -g bower karma

    # for debug scripts
    apt-get install php5-cli

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


