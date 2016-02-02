BASEDIR=$(dirname $0)
SCRIPT_PATH=$(readlink -f $BASEDIR)
DBCMD="mysql -u roprotokol -proprotokol roprotokol"

$SCRIPT_PATH/eksport.sh
$SCRIPT_PATH/eksport2.sh
$DBCMD < $SCRIPT_PATH/mkdb.sql
$SCRIPT_PATH/import.sh

