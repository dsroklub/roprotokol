<? // asp2php (vbscript) converted on Sun Aug 11 21:16:37 2013
include "DatabaseINC.php";
 ?>

<HTML>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="roprotokol.css">
</head>

<BODY bgproperties="fixed" background="images/baggrund.jpg">
<table align="center"><tr><td>
<P>
<? 
$s="";

$db=OpenDatabase();
//$WriteHit"Rovagt"
//NEL CloseDatabase();

?>
<table border="1" cellpadding="0" cellspacing="0" style="border-width:0; border-collapse: collapse" bordercolor="#111111" width="100%" id="AutoNumber1">
  <tr>
    <td width="58%" style="border-style: none; border-width: medium">
		<h2>Rovagtens oversigt over tilg�ngelige b�de</h2>

    </td>
    <td width="42%" style="border-style: none; border-width: medium"><form>
      <input type=button value="Udskriv tilg�ngelige b�de" onClick="window.open('Rovagt_printer.php', 'Printervenlig', 'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=no, scrollbars=yes, width=600, height=500, top=0, left=0');" style="float: right"></form>
</td>
  </tr>
</table>
<p>
		<br>
		<font size="2">Oversigten viser de b�de, der er tilg�ngelige og utilg�ngelige fordelt p� hhv. 2 og 4-�res inriggere samt andre b�dtyper.
        </font>
</p>

<p>
		<font size="2">En b�d er utilg�ngelig, n�r den er p� vandet eller er  sv�rt skadet. En b�d er tilg�ngelig, n�r den ikke er p� vandet, og ikke er sv�rt skadet.
<br>
<br>
</p>
</td></tr></table>

<table align="center"><tr><td>

<h3>Tilg�ngelige 4-�res inriggere</h3>
<? 
$db=OpenDatabase();
$s="select * from QRYRovagtInr4 WHERE tilg�ngelig='Ja'";
$rs=$db->query($s);
if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 
?>

		<h3>Tilg�ngelige 2-�res inriggere</h3>
		<? 
$s="select * from QRYRovagtInr2 where tilg�ngelig='ja'";
$rs=$db->query($s);

if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

?>
		
		<h3>Tilg�ngelige b�de - andre b�dtyper</h3>
		<? 
$s="select * from QRYRovagtAndet  where tilg�ngelig='ja'";
$rs=$db->query($s);
if ($rs && $rs->num_rows > 0) {
  Rovagt($rs);
} else {

?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

?>
				
		<h3>Ikke-tilg�ngelige 4-�res inriggere</h3>
		<? 
$db=OpenDatabase();
$s="select * from QRYRovagtInr4 where tilg�ngelig='nej'";
$rs=$db->query($s);
if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

?>	
		<h3>Ikke-tilg�ngelige 2-�res inriggere</h3>
		<? 
$s="select * from QRYRovagtInr2 where tilg�ngelig='nej'";
$rs=$db->query($s);
if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

?>

		<h3>Ikke-tilg�ngelige b�de - andre b�dtyper</h3>
		<? 
$s="select * from QRYRovagtAndet  where tilg�ngelig='nej'";
$rs=$db->query($s);
if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

//$rs->close;
//CloseDatabase();
?>
</P>
</td></tr></table>
</BODY>
</HTML>
