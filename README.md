# DSR Roprotokol

# Documentation

## [For DSR in Danish:](http://htmlpreview.github.io/?https://github.com/dsroklub/roprotokol/blob/master/documentation/DSR.html)

# Demo: login 2001, pw demo

https://agol.dk/roprotokol/frontend/app/index.shtml


# Installation

    apt-get install composer npm python-mysqldb memcached php-memcached php-mysql php-gd php-zip libapache2-mod-php nodejs  mysql-server php-mail php-mailparse  composer php-cli
    # or mariadb
    #  php-mysqlnd/php-mysql is needed to make PHP know the difference between numbers and strings
    apt-get install  libaprutil1-dbd-mysql pkg-php-tools
    # Remember to restart your web server.

   a2enmod dbd  rewrite include authn_dbd  php7.X # where X is the php version
   sudo npm install -g bower

   cd rowingapp/backend; composer update

   cd rowingapp/frontend; npm install

Create database:

   sudo mysqladmin  --default-character-set=utf8mb4 create roprotokol
    sudo "echo "CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol'; CREATE USER 'apacheauth'@'localhost' IDENTIFIED BY 'XXXX';"|mysql
    sudo echo "GRANT ALL PRIVILEGES ON roprotokol.authentication TO 'apacheauth'@'localhost';GRANT SELECT ON roprotokol.Member TO 'apacheauth'@'localhost'; "|mysql
    sudo echo "GRANT SELECT ON roprotokol.MemberRights TO 'apacheauth'@'localhost';GRANT SELECT ON roprotokol.Member TO 'apacheauth'@'localhost'; "|mysql
    sudo echo "GRANT ALL PRIVILEGES ON roprotokol.* TO 'roprotokol'@'localhost'; FLUSH PRIVILEGES;"|mysql


grant super on *.* to roprotokol@localhost ;

Import schema:


    mysql -u roprotokol -p'roprotokol' roprotokol < db_setup/mkdb.sql

Copy config.ini.template to config.ini and adjust

