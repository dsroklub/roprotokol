<?php
   $public_page = true;
   include("inc/header.php");

   echo "</head>\n<body>\n";

   $year = get_setting('year');
   $text = nl2br(get_setting('welcome_page'));

?>

<h2>Tilmelding til styrmandsinstruktion</h2>
<p><?= $text ?>
<p>Hvis du ikke har modtaget dit password (eller ikke kan huske det) så kan du <a href="glemt.php">klikke her for at få det tilsendt</a>.</p>

<br />
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
	<td>styrmandsaspirant-password:</td>
	<td><input type="password" name="password" size="20"></td>
    </tr>
    <tr>
	<td colspan="2"><input type='submit' value='Login...'></td>
    </tr>
  </table>
</form>
<a href="glemt.php">Glemt password?</a>

<?php
  include("inc/footer.php");
?>

