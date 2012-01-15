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

  if (isset($_POST['action']) && $_POST['action']==ACT_NEW) {
    unset($_SESSION['newtemplate'][0]);
  }


  if (!is_numeric(active('x', 'newtemplate'))) paste2sessionactive('x', 'newtemplate', 0);
  if (!is_numeric(active('y', 'newtemplate'))) paste2sessionactive('y', 'newtemplate', 0);
  if (!is_numeric(active('h', 'newtemplate'))) paste2sessionactive('h', 'newtemplate', 840);
  if (!is_numeric(active('w', 'newtemplate'))) paste2sessionactive('w', 'newtemplate', 1120);

  $dbh = new PDO(DATABASE, DB_USER, DB_PASSWORD);

  if (active('category', 'newtemplate') != '') {
    $param_newtemplate = active('category', 'newtemplate');
    $stmt = $dbh->prepare('SELECT title FROM content_category WHERE content_category.id=:contentcategoryid');
    $stmt->bindParam(':contentcategoryid', $param_newtemplate, PDO::PARAM_INT);
    $stmt->execute();
    $qresult = $stmt->fetchAll();
  }

  $preview = '404';

  if (isset($qresult[0])) {

  $preview = checkandpreview($safebox=1, $width=PREVIEWRESOLUTIONW, $height=PREVIEWRESOLUTIONH, $format='png', 
  			       active('title', 'newtemplate'), 'Laten we het eens zonder tekst doen.', '',
			       'default.xsl', $dir='', $filename=md5('default.xsl'.$qresult[0]['title'].active('title', 'newtemplate').active('photo', 'newtemplate').active('w', 'newtemplate').active('h', 'newtemplate').active('x', 'newtemplate').active('y', 'newtemplate')),
			       $qresult[0]['title'],
			       active('photo', 'newtemplate'), active('w', 'newtemplate'),
			       active('h', 'newtemplate'), active('x', 'newtemplate'), active('y', 'newtemplate'));
}


   if (isset($_POST['action'])) {
   if ($_POST['action']==ACT_SAVE && active('title', 'newtemplate')!='' && active('photo', 'newtemplate')!='' && active('category', 'newtemplate')!='') {
    $dbh->beginTransaction();
    $stmt = $dbh->prepare('INSERT INTO content_category_image(categoryid, title, photo, width, height, x, y) VALUES (:categoryid, :title, :photo, :width, :height, :x, :y)');
    $stmt->bindParam(':categoryid', active('category', 'newtemplate'), PDO::PARAM_INT);
    $stmt->bindParam(':title', active('title', 'newtemplate'), PDO::PARAM_STR, 20);
    $stmt->bindParam(':photo', active('photo', 'newtemplate'), PDO::PARAM_STR, 100);
    $stmt->bindParam(':width', active('w', 'newtemplate'), PDO::PARAM_INT);
    $stmt->bindParam(':height', active('h', 'newtemplate'), PDO::PARAM_INT);
    $stmt->bindParam(':x', active('x', 'newtemplate'), PDO::PARAM_INT);
    $stmt->bindParam(':y', active('y', 'newtemplate'), PDO::PARAM_INT);
    $stmt->execute();
    $dbh->commit();
    $dbh = null;
    header('Location: toevoegen.php');
    exit;
  }

  if ($_POST['action']==ACT_BACK) {
    $dbh = null;
    header('Location: toevoegen.php');
    exit;
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
        <?php 
	  $stmt = $dbh->query('SELECT content_category.id, content_category.title FROM content_category ORDER BY content_category.title');
	  $stmt->execute();
	  $qresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
	  echo dbtoselect('category', $qresult, active('category', 'newtemplate'), true);
        ?>
        <a href="categorie-toevoegen.php"><?php echo NEWCATEGORY; ?></a>
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
