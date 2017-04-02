<?php
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
<title>DSR Styrmandsinstruktion</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<link rel="stylesheet" type="text/css" href="styrmand_style.css" />


<?php
//define('DEBUG',TRUE);
define('DEBUG',FALSE);

include_once("db.php");

$authenticated = 0;
$id="";
$kode="";
$login_error = "";
$form_fields = "";

if (isset($_POST["medlemsnummer"]) && isset($_POST["password"])){
  $medlemsnummer=trim($_POST["medlemsnummer"]);  
  $kode=trim($_POST["password"]);

  $res = $link->query("SELECT p.*, k.navn as kategori_navn, k.timer as kategori_timer
                       FROM person p
                       LEFT JOIN roer_kategori k ON (p.kategori = k.ID)
                       WHERE p.ID = " . (int) $medlemsnummer . " AND p.kode = '" . $link->escape_string($kode) . "'");
  if ($res) {
    if ($res->num_rows == 1) {
      $user = $res->fetch_assoc();
      $authenticated = 1;
      $form_fields = "<input type=\"hidden\" name=\"medlemsnummer\" value=\"" 
                      . $user['ID'] . "\" /><input type=\"hidden\" name=\"password\" value=\""
                      . $user['kode'] . "\" />";
    } else {
 	$login_error = "Vi kunne ikke finde en bruger med det medlemsnummer og det password";
    }
    $res->close();
  }
} else {
  $login_error = "Du skal være logget ind for at se denne side.";
}

if (!$authenticated && ! (isset($public_page) && $public_page) ) {
    echo "</head>\n";
    echo "<body>\n";
    if ($login_error) {
	echo "<p class=\"error\">$login_error</p>";
    }
    ?>
	<form action="tilmeld2.php" method="POST">
  	   <table border="0" class="login-boks">
     	    <tr>
	      <th colspan="2">Login</th>
            </tr>
            <tr>
	      <td>Medlemsnummer:</td>
	      <td><input type='text' name='medlemsnummer' size='4'></td>
            </tr>
            <tr>
	      <td>Styrmandsinstruktion:</td>
	      <td><input type="password" name="password" size="20"></td>
            </tr>
            <tr>
	      <td colspan="2"><input type='submit' value='Login...'></td>
            </tr>
          </table>
        </form>
        <a href="glemt.php">Glemt password?</a>
   <?php
} 

?>
