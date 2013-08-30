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
		<h2>Rovagtens oversigt over tilgængelige både</h2>

    </td>
    <td width="42%" style="border-style: none; border-width: medium"><form>
      <input type=button value="Udskriv tilgængelige både" onClick="window.open('Rovagt_printer.php', 'Printervenlig', 'toolbar=no, directories=no, location=no, status=no, menubar=no, resizable=no, scrollbars=yes, width=600, height=500, top=0, left=0');" style="float: right"></form>
</td>
  </tr>
</table>
<p>
		<br>
		<font size="2">Oversigten viser de både, der er tilgængelige og utilgængelige fordelt på hhv. 2 og 4-åres inriggere samt andre bådtyper.
        </font>
</p>

<p>
		<font size="2">En båd er utilgængelig, når den er på vandet eller er  svært skadet. En båd er tilgængelig, når den ikke er på vandet, og ikke er svært skadet.
<br>
<br>
</p>
</td></tr></table>

<table align="center"><tr><td>

<h3>Tilgængelige 4-åres inriggere</h3>
<? 
$db=OpenDatabase();
$s="select * from QRYRovagtInr4 WHERE tilgængelig='Ja'";
$rs=$db->query($s);
if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 
?>

		<h3>Tilgængelige 2-åres inriggere</h3>
		<? 
$s="select * from QRYRovagtInr2 where tilgængelig='ja'";
$rs=$db->query($s);

if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

?>
		
		<h3>Tilgængelige både - andre bådtyper</h3>
		<? 
$s="select * from QRYRovagtAndet  where tilgængelig='ja'";
$rs=$db->query($s);
if ($rs && $rs->num_rows > 0) {
  Rovagt($rs);
} else {

?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

?>
				
		<h3>Ikke-tilgængelige 4-åres inriggere</h3>
		<? 
$db=OpenDatabase();
$s="select * from QRYRovagtInr4 where tilgængelig='nej'";
$rs=$db->query($s);
if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

?>	
		<h3>Ikke-tilgængelige 2-åres inriggere</h3>
		<? 
$s="select * from QRYRovagtInr2 where tilgængelig='nej'";
$rs=$db->query($s);
if ($rs && $rs->num_rows>0) {
  Rovagt($rs);
} else {
?>
		<STRONG>Ingen data  at vise</STRONG>
		<? 
} 

?>

		<h3>Ikke-tilgængelige både - andre bådtyper</h3>
		<? 
$s="select * from QRYRovagtAndet  where tilgængelig='nej'";
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
