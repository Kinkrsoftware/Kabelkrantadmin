<?php
  session_start();

  require_once('functions.php');

  $_SESSION['newtemplate']['activeid']=0; 

  post2sessionactive('x', 'newtemplate');
  post2sessionactive('y', 'newtemplate');
  post2sessionactive('h', 'newtemplate');
  post2sessionactive('w', 'newtemplate');
  post2sessionactive('photo', 'newtemplate');
  post2sessionactive('title', 'newtemplate');
  post2sessionactive('category', 'newtemplate');

  if ($_POST['action']==ACT_NEW) {
    unset($_SESSION['newtemplate'][0]);
  }


  if (!is_numeric(active('x', 'newtemplate'))) paste2sessionactive('x', 'newtemplate', 0);
  if (!is_numeric(active('y', 'newtemplate'))) paste2sessionactive('y', 'newtemplate', 0);
  if (!is_numeric(active('h', 'newtemplate'))) paste2sessionactive('h', 'newtemplate', 840);
  if (!is_numeric(active('w', 'newtemplate'))) paste2sessionactive('w', 'newtemplate', 1120);

  if (active('category', 'newtemplate') != '') {
    $db = sqlite_open(DATABASE, 0666, $sqlerror);
    $query = sqlite_query($db, 'SELECT title FROM content_category WHERE content_category.id='.active('category', 'newtemplate').';');
    $qresult = sqlite_fetch_all($query, SQLITE_ASSOC);
    sqlite_close($db);
  }

  $preview = checkandpreview($safebox=1, $width=269, $height=200, $format='png', 
  			       active('title', 'newtemplate'), 'Laten we het eens zonder tekst doen.', '',
			       'default.xsl', $dir='', $filename=md5('default.xsl'.$qresult[0]['title'].active('title', 'newtemplate').active('photo', 'newtemplate').active('w', 'newtemplate').active('h', 'newtemplate').active('x', 'newtemplate').active('y', 'newtemplate')),
			       $qresult[0]['title'],
			       active('photo', 'newtemplate'), active('w', 'newtemplate'),
			       active('h', 'newtemplate'), active('x', 'newtemplate'), active('y', 'newtemplate'));

   if ($_POST['action']==ACT_SAVE && active('title', 'newtemplate')!='' && active('photo', 'newtemplate')!='' && active('category', 'newtemplate')!='') {
    $db = sqlite_open(DATABASE, 0666, $sqlerror);
    sqlite_query('BEGIN;', $db);
    sqlite_query($db, 'INSERT INTO content_category_image(categoryid, title, photo, width, height, x, y) VALUES ('.active('category', 'newtemplate').', \''.sqlite_escape_string(active('title', 'newtemplate')).'\', \''.sqlite_escape_string(active('photo', 'newtemplate')).'\', '.active('w', 'newtemplate').', '.active('h', 'newtemplate').', '.active('x', 'newtemplate').', '.active('y', 'newtemplate').');');
    sqlite_query($db, 'COMMIT;');
    sqlite_close($db);
    header('Location: toevoegen.php');
    exit;
  }

  if ($_POST['action']==ACT_BACK) {
    header('Location: toevoegen.php');
    exit;
  }

  if (isset($_GET['debug'])) {
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
      <legend><?php echo NEWTEMP; ?></legend>
      <fieldset class="buttons">
        <legend><?php echo ACTION; ?></legend>
        <input type="submit" name="action" value="<?php echo ACT_UPD; ?>" />
	<input type="submit" name="action" value="<?php echo ACT_NEW; ?>" />
        <input type="submit" name="action" value="<?php echo ACT_SAVE; ?>" />
        <input type="submit" name="action" value="<?php echo ACT_BACK; ?>" />
      </fieldset>
      <fieldset>
        <legend><?php echo CATEGORY; ?></legend>
        <?php echo dbtoselect('category', 'SELECT content_category.id, content_category.title FROM content_category ORDER BY content_category.title', active('category', 'newtemplate'), true); ?>
      </fieldset>
      <fieldset>
        <legend><?php echo TITLE; ?></legend>
        <input type="text" name="title" value="<?php echo active('title', 'newtemplate'); ?>" />
      </fieldset>
      <fieldset>
        <legend><?php echo PHOTO; ?></legend>
        <?php echo dirtoselect('photo', 'fotos', active('photo', 'newtemplate'), true); ?>
        <a href="photoupload.php">Uploaden</a>
       </fieldset>
      <fieldset>
        <legend><?php echo PARAM; ?></legend>
	<label><?php echo LEFT; ?></label><input type="text" name="x" value="<?php echo active('x', 'newtemplate');?>" maxlength="4" />
	<label><?php echo TOP; ?></label><input type="text" name="y" value="<?php echo active('y', 'newtemplate'); ?>" maxlength="4" />
	<label><?php echo HEIGHT; ?> (840)</label><input type="text" name="h" value="<?php echo active('h', 'newtemplate'); ?>" maxlength="4" />
	<label><?php echo WIDTH; ?> (1120/630)</label><input type="text" name="w" value="<?php echo active('w', 'newtemplate'); ?>" maxlength="4" />
	<img alt="<?php echo EXAMPLE; ?>" src="preview/<?php echo $preview; ?>.png" style="float: left; border: solid 1px #000; margin-left: 2px; margin-top: 1px;"/>
      </fieldset>
     </fieldset>
     </form>
  </body>
</html>
