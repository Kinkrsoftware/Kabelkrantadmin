<?php
  session_start();

  require_once('functions.php');

  post2sessionactive('name', 'newscreen');
  post2sessionactive('location', 'newscreen');
  post2sessionactive('ip', 'newscreen');

  $dbh = new PDO(DATABASE, DB_USER, DB_PASSWORD);

  if (!isset($_SESSION['newscreen']['activeid'])) {
    $_SESSION['newscreen']['activeid']=0;
  }
  
  if (isset($_POST['action'])) {

    if ($_POST['action']==ACT_NEW) {
      unset($_SESSION['newscreen'][0]);
      $_SESSION['newscreen']['activeid']=0; 
    }
    elseif ($_POST['action']==TPL_OPEN) {
        unset($_SESSION['newscreen']);
        $_SESSION['newscreen']['activeid']=0; 
        $stmt = $dbh->prepare('SELECT name, location, ip FROM screen WHERE id=:screenid');
        $stmt->bindParam(':screenid', $_POST['activeid'], PDO::PARAM_INT);
        $stmt->execute();
        $qresult = $stmt->fetchAll();
        if (isset($qresult[0])) {
            $_SESSION['newscreen'][$_POST['activeid']]['name'] = $qresult[0]['name'];
            $_SESSION['newscreen'][$_POST['activeid']]['location'] = $qresult[0]['location'];
            $_SESSION['newscreen'][$_POST['activeid']]['ip'] = $qresult[0]['ip'];
            $_SESSION['newscreen']['activeid']=$_POST['activeid'];
        } else {
            $_SESSION['newscreen']['activeid']=0;
        }
    }
    elseif ($_POST['action']==TPL_REMOVE) {
       $stmt = $dbh->prepare('delete from content_category_screen where screenid = :screenid;');
       $stmt->bindParam(':screenid', $_SESSION['newscreen']['activeid'], PDO::PARAM_INT);
       $stmt->execute();
       $stmt = $dbh->prepare('delete from screen where id = :screenid;');
       $stmt->bindParam(':screenid', $_SESSION['newscreen']['activeid'], PDO::PARAM_INT);
       $stmt->execute();
       unset($_SESSION['newscreen'][0]);
       $_SESSION['newscreen']['activeid']=0;
       $qresult = null; 
    }
  }

   if (isset($_POST['action'])) {
   if ($_POST['action']==ACT_SAVE && active('name', 'newscreen')!='' && active('location', 'newscreen')!='' && active('ip', 'newscreen')!='') {
    
    $dbh->beginTransaction();
    if ($_SESSION['newscreen']['activeid'] == 0) {
        $stmt = $dbh->prepare('INSERT INTO screen(name, location, ip) VALUES (:name, :location, :ip)');
    } else {
        $stmt = $dbh->prepare('UPDATE screen SET name = :name, location = :location, ip = :ip WHERE id = :id');
        $stmt->bindParam(':id', $_SESSION['newscreen']['activeid'], PDO::PARAM_INT);
    }
    $name = active('name', 'newscreen');
    $location = active('location', 'newscreen');
    $ip = active('ip', 'newscreen');

    $stmt->bindParam(':name', $name, PDO::PARAM_STR, 255);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR, 255);
    $stmt->bindParam(':ip', $ip, PDO::PARAM_STR, 16);
    $stmt->execute();
    $dbh->commit();
    #header('Location: toevoegen.php');
    #exit;
  }

  if ($_POST['action']==ACT_BACK) {
    $dbh = null;
    header('Location: toevoegen.php');
    exit;
  }

  if ($_POST['action']==ACT_SAVE_SCREEN) {
    $dbh->beginTransaction();
    $stmt = $dbh->query('DELETE FROM content_category_screen;');
    $stmt = $dbh->query('SELECT content_category.id as cid, screen.id as sid FROM screen, content_category');
    $cs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($cs as $entry) {
#    	$categoryid = $entry['cid'];
#    	$screenid = $entry['sid'];
    	$needle = 'cs_'.$entry['cid'].'_'.$entry['sid'];
    	$visible = (isset($_POST[$needle]) && $_POST[$needle] == 'on');
    	$stmt = $dbh->prepare('INSERT INTO content_category_screen (categoryid, screenid, visible) VALUES (:categoryid, :screenid, :visible);');
	$stmt->bindParam(':categoryid', $entry['cid'], PDO::PARAM_INT);
	$stmt->bindParam(':screenid', $entry['sid'], PDO::PARAM_INT);
	$stmt->bindParam(':visible', $visible, PDO::PARAM_BOOL);
    	$stmt->execute();
    }
    $dbh->commit();
  }
  }

  if (isset($_GET['debug'])) {
    $dbh = null;
    header('Content-Type: text/plain;');
    print_r($_SESSION);
    print_r($_POST);
    print_r($_GET);
    print_r($_FILES);
    exit;
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
      <legend><?php echo SCREEN_NEW; ?></legend>
      <fieldset>
        <legend><?php echo SCREENS; ?></legend>
        <?php
	        $stmt = $dbh->query('SELECT screen.id, screen.name, screen.location FROM screen ORDER BY screen.name, screen.location');
		$stmt->execute();
		$screens = $stmt->fetchAll(PDO::FETCH_ASSOC);

	        echo dbtoselect('activeid', $screens, active('screen', 'newscreen'), true);
	?>
      </fieldset>
      <fieldset class="buttons">
        <legend><?php echo ACTION; ?></legend>
	<input type="submit" name="action" value="<?php echo ACT_NEW; ?>" />
        <input type="submit" name="action" value="<?php echo ACT_SAVE; ?>" />
        <input type="submit" name="action" value="<?php echo ACT_BACK; ?>" />
        <input type="submit" name="action" value="<?php echo TPL_OPEN; ?>" />
        <input type="submit" name="action" style="float: right; background-color: #f00;" value="<?php echo TPL_REMOVE; ?>" />
      </fieldset>
      <fieldset>
        <legend><?php echo SCREEN_TITLE; ?></legend>
        <input type="text" name="name" value="<?php echo active('name', 'newscreen'); ?>" />
      </fieldset>
      <fieldset>
        <legend><?php echo SCREEN_LOCATION; ?></legend>
        <input type="text" name="location" value="<?php echo active('location', 'newscreen'); ?>" />
      </fieldset>
      <fieldset>
        <legend><?php echo SCREEN_IP; ?></legend>
        <input type="text" name="ip" value="<?php echo active('ip', 'newscreen'); ?>" />
      </fieldset>
      </fieldset>
      <fieldset>
        <legend><?php echo CATEGORY; ?> &amp; <?php echo TEMPLATE; ?></legend>
	<table class="screen">
	<?php
		$stmt = $dbh->query('SELECT content_category.id, content_category.title FROM content_category ORDER BY content_category.title');
		$stmt->execute();
		$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

	        $stmt = $dbh->query('SELECT categoryid, screenid, visible FROM content_category_screen');
                $stmt->execute();
	        $qresult = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$table = array();

	    	foreach ($qresult as $entry) {
			if (!isset($table[$entry['categoryid']])) {
				$table[$entry['categoryid']] = array();
			}

			$table[$entry['categoryid']][$entry['screenid']] = $entry['visible'];
		}
		echo '<tr><th></th>';
		foreach ($screens as $entry) {
			echo '<th>'.$entry['name'].'<br />'.$entry['location'].'</th>';
		}
		echo '</tr>'."\n";

		foreach ($categories as $cat) {
			echo '<tr><td>'.$cat['title'].'</td>';
			foreach ($screens as $entry) {
				echo '<td><input type="checkbox" name="cs_'.$cat['id'].'_'.$entry['id'].'"'.(isset($table[$cat['id']][$entry['id']]) && $table[$cat['id']][$entry['id']] === True ? ' checked="checked"' : '').' /></td>';
			}
			echo '</tr>'."\n";
		}

        ?>
	</table>
	<input type="submit" name="action" value="<?php echo ACT_SAVE_SCREEN; ?>" />
      </fieldset>
     </form>
  </body>
</html>
