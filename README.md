# DSR Roprotokol

# Documentation

## [For DSR in Danish:](http://htmlpreview.github.io/?https://github.com/dsroklub/roprotokol/blob/master/documentation/DSR.html)

# Demo: login 2001, pw demo

https://agol.dk/roprotokol/frontend/app/index.shtml


# Installation

    apt install composer npm memcached php-memcached php-mysql php-gd php-zip libapache2-mod-php nodejs  mariadb-server php-mail php-mailparse  composer php-cli ntp
    #  php-mysqlnd/php-mysql is needed to make PHP know the difference between numbers and strings
    apt install  libaprutil1-dbd-mysql pkg-php-tools php-fpdf mdbtools postfix postfix-sqlite automysqlbackup php-curl certbot // php-tcpdf
    # Remember to restart your web server.

   a2enmod ssl dbd  rewrite include authn_dbd
   a2enmod php8.X # where X is the php version
#   sudo npm install -g bower



  sudo mkdir /data
  chown roprotokol.roprotokol /date
  cd /data;git clone https://github.com/dsroklub/roprotokol.git

   cd /data/roprotokol/rowing/backend; composer update

   cd /data/roprotokol/rowingapp/frontend; npm install

   sudo chown www-data.www-data /data/roprotokol/externaladmin/uploads

# init git hooks
  cp /data/roprotokol/configuration/git/hooks/* /data/roprotokol/.git/hooks
  cd /data/roprotokol/; . .git/hooks/post-commit

Create database:

   sudo mysqladmin -p  --default-character-set=utf8mb4 create roprotokol
   echo "CREATE USER 'roprotokol'@'localhost' IDENTIFIED BY 'roprotokol'; CREATE USER 'apacheauth'@'localhost' IDENTIFIED BY 'XXXX';"|mysql -u root -p
   echo "GRANT ALL PRIVILEGES ON roprotokol.* TO 'roprotokol'@'localhost'; FLUSH PRIVILEGES;"|mysql -u root -p
   mysql -u roprotokol -p'roprotokol' roprotokol < /data/roprotokol/db_setup/mkdb.sql
   echo "GRANT ALL PRIVILEGES ON roprotokol.authentication TO 'apacheauth'@'localhost';GRANT SELECT ON roprotokol.Member TO 'apacheauth'@'localhost'; "|mysql -u root -p
   echo "GRANT SELECT ON roprotokol.MemberRights TO 'apacheauth'@'localhost';GRANT SELECT ON roprotokol.Member TO 'apacheauth'@'localhost'; "|mysql -u root -p


grant super on *.* to roprotokol@localhost ;

Import schema:



Copy config.ini.template to config.ini and adjust
