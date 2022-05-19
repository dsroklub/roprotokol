# DSR Roprotokol

# Documentation

## [For DSR in Danish:](http://htmlpreview.github.io/?https://github.com/dsroklub/roprotokol/blob/master/documentation/DSR.html)

# Demo: login 2001, pw demo

https://agol.dk/roprotokol/frontend/app/index.shtml


# Installation

    apt install composer npm memcached php-memcached php-mysql php-gd php-zip libapache2-mod-php nodejs  mariadb-server php-mail php-mailparse  composer php-cli
    #  php-mysqlnd/php-mysql is needed to make PHP know the difference between numbers and strings
    apt install  libaprutil1-dbd-mysql pkg-php-tools postfix postfix-sqlite automysqlbackup php-curl certbot mdbtools
    # Remember to restart your web server.

   a2enmod ssl dbd  rewrite include authn_dbd
   a2enmod php7.X # where X is the php version
#   sudo npm install -g bower



  sudo mkdir /data
  chown roprotokol.roprotokol /date
  cd /data;git clone https://github.com/dsroklub/roprotokol.git

   cd /data/roprotokol/rowing/backend; composer update

   cd /data/roprotokol/rowingapp/frontend; npm install


# init git hooks
  cp /data/roprotokol/configuration/git/hooks/* /data/roprotokol/.git/hooks
  cd /data/roprotokol/; . .git/hooks/post-commit

Create database:

   sudo mysqladmin  --default-character-set=utf8mb4 create roprotokol
   sudo "echo "CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol'; CREATE USER 'apacheauth'@'localhost' IDENTIFIED BY 'XXXX';"|mysql
   sudo echo "GRANT ALL PRIVILEGES ON roprotokol.* TO 'roprotokol'@'localhost'; FLUSH PRIVILEGES;"|mysql
   sudo echo "GRANT ALL PRIVILEGES ON roprotokol.authentication TO 'apacheauth'@'localhost';GRANT SELECT ON roprotokol.Member TO 'apacheauth'@'localhost'; "|mysql
    sudo echo "GRANT SELECT ON roprotokol.MemberRights TO 'apacheauth'@'localhost';GRANT SELECT ON roprotokol.Member TO 'apacheauth'@'localhost'; "|mysql


grant super on *.* to roprotokol@localhost ;

Import schema:


    mysql -u roprotokol -p'roprotokol' roprotokol < /data/roprotokol/db_setup/mkdb.sql

Copy config.ini.template to config.ini and adjust
