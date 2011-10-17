<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
  <head>
    <title><?php echo OWNER; ?></title>
    <link rel="stylesheet" href="toevoegen.css" type="text/css" />
  </head>
  <body>
    <h1>Ontbrekende foto's (Linker kant)</h1>
<?php

  require_once('functions.php');
  $dbh = new PDO(DATABASE, DB_USER, DB_PASSWORD);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare('select distinct photo from content_category_image;');
    $stmt->execute();
    $result = $stmt->fetchAll();

    foreach ($result as $entry) {
	if (!file_exists(USER_IMAGEDIR.'/'.$entry['photo'])) {
		echo $entry['photo'].'<br />';
	}
    }

?>
    <h1>Ontbrekende foto's bij artikelen</h1>
<?php
    $stmt = $dbh->prepare('select distinct photo from content_text where photo <> \'\';');
    $stmt->execute();
    $result = $stmt->fetchAll();

    foreach ($result as $entry) {
	if (!file_exists(USER_IMAGEDIR.'/'.$entry['photo'])) {
		echo $entry['photo'].'<br />';
	}
    }

?>

  </body>
</html>
