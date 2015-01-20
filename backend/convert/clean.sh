. $SCRIPT_PATH/secret.sh

$DBCMD -e "DELETE FROM Fejl_tblMembersSportData;"
$DBCMD -e "DROP TABLE Motionstatus;"
$DBCMD -e "DROP TABLE Fejl_system";
$DBCMD -e "DROP  TABLE Vintervedligehold;"
$DBCMD -e "DROP TABLE Tur;"
$DBCMD -e "DROP TABLE TurDeltager;"
$DBCMD -e "DROP TABLE tblMembersSportData;"
$DBCMD -e "DROP TABLE tblMembers;"
$DBCMD -e "DELETE FROM Error_Trip;"
