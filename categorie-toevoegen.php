<?php
  require_once('functions.php');

  $error = '';
  if (isset($_POST['action'])) {
  if ($_POST['action']==ACT_ADD) {
    $title = trim($_POST['newtitle']);
    
    if ($title != '')  {
    	$dbh = new PDO(DATABASE, DB_USER, DB_PASSWORD);
	$dbh->beginTransaction();

	$stmt = $dbh->prepare('INSERT INTO content_category (title) VALUES (:title)');
	$stmt->bindParam(':title', $title, PDO::PARAM_STR, 15);
	$stmt->execute();

	$dbh->commit();
	$dbh = null;

//	header('Location: '.$_SERVER['PHP_SELF']);
    }
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
     <form method="post">
        <fieldset>
	   <legend>Categorie Toevoegen</legend>
	   <label for="newtitle" style="display: block; width: 110px; float: left; clear: both;">Titel:</label> <input id="newtitle" name="newtitle" type="text" /> 
	   <input name="action" type="submit" value="<?php echo ACT_ADD;?>" class="button" style="clear: both; width: auto;" />
        </fieldset>
     </form>
     <form method="post">
        <fieldset>
	   <legend>Bestaande Categorien</legend>
	   <table>
	     <tr>
	       <th>Categorie</th>
	     </tr>

	     <?php 
	    	$dbh = new PDO(DATABASE, DB_USER, DB_PASSWORD);
		$stmt = $dbh->query('SELECT title FROM content_category ORDER BY idx;');
		$result = $stmt->fetchAll();
		$dbh = null;

		if (is_array($result)) {
			foreach ($result as $entry) {
				echo '<tr><td>'.$entry['title'].'</td></tr>';
			}
		}
	     ?>
	   </table>
	</fieldset>
     </form>
</body>
</html>
