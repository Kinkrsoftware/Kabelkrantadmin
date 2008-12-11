<?php
  require_once('functions.php');
  $now = time();

  if (isset($_GET['now']) && is_numeric($_GET['now'])) {
     $now = $_GET['now'];
  }

  if ($_POST['action']=='Maak Gebruiker') {
    $login = trim($_POST['newuser']);
    $surname = trim($_POST['surname']);
    $givenname = trim($_POST['givenname']);
    $addictions = trim($_POST['addictions']);
    $password = trim($_POST['password']);

    if ($login != '' && $givenname != '')  {
        $login = sqlite_escape_string($login);
	$givenname = sqlite_escape_string($givenname);
	$surname = sqlite_escape_string($surname);
	$addictions = sqlite_escape_string($addictions);
	$passphrase = md5($login.':Kabelkrantadmin:'.$password);
	
        $db = sqlite_open(DATABASE, 0666, $sqlerror);
        sqlite_query('BEGIN;', $db);

        sqlite_query($db, 'INSERT INTO editors (login, passphrase, surname, addictions, givenname) VALUES (\''.$login.'\', \''.$passphrase.'\', \''.$surname.'\', \''.$addictions.'\', \''.$givenname.'\');');
	sqlite_query('COMMIT;', $db);

	$fp = fopen('.htdigest', 'a');
	fwrite($fp, $login.':Kabelkrantadmin:'.$passphrase."\n");
	fflush($fp);
	fclose($fp);
//	header('Location: '.$_SERVER['PHP_SELF']);
    }
  }

  
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
  <head>
    <title><?php echo OWNER; ?></title>
    <link rel="stylesheet" href="toevoegen.css" type="text/css" />
  </head>
  <body>
    Welkom op de nieuwe beta-versie van kabelkrantadmin.<br />
    Momenteel worden wat toevoegingen gedaan aan de broncode, het kan zijn
    dat je daar iets van merkt.<br />
        <?php
		$now = time();
		$start = $now;
		$end = $now;
			      
		$db = sqlite_open(DATABASE, 0666, $sqlerror);
		$query = sqlite_query($db, 'SELECT sum(content_text.duration)  FROM content, content_text WHERE content.id=content_text.contentid AND content.start <= '.$start.' AND content.end >= '.$end.';');
		$result = sqlite_fetch_all($query, SQLITE_ASSOC);

		$lengte = $result[0]['sum(content_text.duration)'];
		
		sqlite_close($db);
	?>
     Lengte: <?php echo $lengte; ?>s
     <form method="post">
        <fieldset>
	   <legend>Gebruiker Toevoegen</legend>
	   <label for="newuser" style="display: block; width: 110px; float: left; clear: both;">Gebruikersnaam:</label> <input id="newuser" name="newuser" type="text" /> 
	   <label for="password" style="display: block; width: 110px; float: left; clear: both;">Wachtwoord:</label> <input id="password" name="password" type="password" />
	   <label for="givenname" style="display: block; width: 110px; float: left; clear: both;">Voornaam:</label> <input id="givenname" name="givenname" type="text" />
	   <label for="addictions" style="display: block; width: 110px; float: left; clear: both;">Tussenvoegsels:</label> <input id="addictions" name="addictions" type="text" />
	   <label for="surname" style="display: block; width: 110px; float: left; clear: both;">Achternaam:</label> <input id="surname" name="surname" type="text" />
	   <input name="action" type="submit" value="Maak Gebruiker" class="button" style="clear: both; width: auto;" />
        </fieldset>
     </form>
     <form method="post">
        <fieldset>
	   <legend>Wachtwoord wijzigen</legend>
	   <label for="curuser" style="display: block; width: 110px; float: left; clear: both;">Gebruikersnaam:</label> <input id="curuser" name="curuser" type="text" />
	   <label for="newpassword" style="display: block; width: 110px; float: left; clear: both;">Wachtwoord:</label> <input id="newpassword" name="newpassword" type="password" />
	   <input name="action" type="submit" value="Wijzig Wachtwoord" class="button" style="clear: both; width: auto;" />
        </fieldset>
     </form>
     <form method="post">
        <fieldset>
	   <legend>Rechten</legend>
	   <table>
	     <tr>
	       <th>Gebruiker</th>
	     </tr>

	     <?php 
		$db = sqlite_open(DATABASE, 0666, $sqlerror);
		$query = sqlite_query($db, 'SELECT login, givenname, addictions, surname FROM editors ORDER BY surname;');
                $result = sqlite_fetch_all($query, SQLITE_ASSOC);
                sqlite_close($db);
		if (is_array($result)) {
			foreach ($result as $entry) {
				echo '<tr><td>'.$entry['givenname'].($entry['addictions']!=''?' '.$entry['addictions']:'').
								($entry['surname']!=''?' '.$entry['surname']:'').
								' ('.$entry['login'].')</td></tr>';
			}
		}			 
	     ?>
	     
	   </table>
	</fieldset>
     </form>
     <form method="post">
        <fieldset>
	   <legend>Acties</legend>
	   <table>
	     <tr>
	       <th>Naam</th>
	       <th>Omschrijving</th>
	     </tr>
	   </table>
	</fieldset>
     </form>

     <i>Wanneer er serieuze problemen zijn, kan er altijd gebeld worden met +31 87 8700579. Kinkrsoftware/Stefan de Konink;<br />
	Jeroen heeft ook een noodnummer.</i>
</body>
</html>
