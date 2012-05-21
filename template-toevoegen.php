<?php
  session_start();

  require_once('functions.php');

  post2sessionactive('x', 'newtemplate');
  post2sessionactive('y', 'newtemplate');
  post2sessionactive('h', 'newtemplate');
  post2sessionactive('w', 'newtemplate');
  post2sessionactive('photo', 'newtemplate');
  post2sessionactive('title', 'newtemplate');
  post2sessionactive('category', 'newtemplate');

  $dbh = new PDO(DATABASE, DB_USER, DB_PASSWORD);

  if (!isset($_SESSION['newtemplate']['activeid'])) {
    $_SESSION['newtemplate']['activeid']=0;
  }
  
  if (isset($_POST['action'])) {

    if ($_POST['action']==ACT_NEW) {
      unset($_SESSION['newtemplate'][0]);
      $_SESSION['newtemplate']['activeid']=0; 
    }
    elseif ($_POST['action']==TPL_OPEN) {
        unset($_SESSION['newtemplate']);
        $_SESSION['newtemplate']['activeid']=0; 
        $stmt = $dbh->prepare('SELECT categoryid, title, photo, width, height, x, y FROM content_category_image AS cci WHERE cci.id=:categoryimageid');
        $stmt->bindParam(':categoryimageid', $_POST['activeid'], PDO::PARAM_INT);
        $stmt->execute();
        $qresult = $stmt->fetchAll();
        if (isset($qresult[0])) {
            $_SESSION['newtemplate'][$_POST['activeid']]['x'] = $qresult[0]['x'];
            $_SESSION['newtemplate'][$_POST['activeid']]['y'] = $qresult[0]['y'];
            $_SESSION['newtemplate'][$_POST['activeid']]['h'] = $qresult[0]['height'];
            $_SESSION['newtemplate'][$_POST['activeid']]['w'] = $qresult[0]['width'];
            $_SESSION['newtemplate'][$_POST['activeid']]['photo'] = $qresult[0]['photo'];
            $_SESSION['newtemplate'][$_POST['activeid']]['title'] = $qresult[0]['title'];
            $_SESSION['newtemplate'][$_POST['activeid']]['category'] = $qresult[0]['categoryid'];
            $_SESSION['newtemplate']['activeid']=$_POST['activeid'];
        } else {
            $_SESSION['newtemplate']['activeid']=0;
        }
    }
    elseif ($_POST['action']==TPL_REMOVE) {
       $dbh->beginTransaction();
       $stmt = $dbh->prepare('select min(id) AS min, max(id) AS max from content_category_image where categoryid = :categoryid group by categoryid;');
       $stmt->bindParam(':categoryid', active('category', 'newtemplate'), PDO::PARAM_INT);
       $stmt->execute();
       $qresult = $stmt->fetchAll(); 
       if (isset($qresult[0])) {
          $current = active('category', 'newtemplate');
          $suggestion = $qresult[0]['min'];
          if ($suggestion == $current) {
               $suggestion = $qresult[0]['min'];
               if ($suggestion == $current) {
                   $suggestion = 1;
               }
          }
          
          $stmt = $dbh->prepare('update content_text set category = :suggestion where category = :categoryid;');
          $stmt->bindParam(':categoryid', $_SESSION['newtemplate']['activeid'], PDO::PARAM_INT);
          $stmt->bindParam(':suggestion', $suggestion, PDO::PARAM_INT);
          $stmt->execute();
          $stmt = $dbh->prepare('delete from content_category_image where id = :categoryid;');
          $stmt->bindParam(':categoryid', $_SESSION['newtemplate']['activeid'], PDO::PARAM_INT);
          $stmt->execute();
          $dbh->commit();
          unset($_SESSION['newtemplate'][0]);
          $_SESSION['newtemplate']['activeid']=0;
       } else {
          $dbh->rollback();
       }
       $qresult = null; 
    }
  }
  if (!is_numeric(active('x', 'newtemplate'))) paste2sessionactive('x', 'newtemplate', 0);
  if (!is_numeric(active('y', 'newtemplate'))) paste2sessionactive('y', 'newtemplate', 0);
  if (!is_numeric(active('h', 'newtemplate'))) paste2sessionactive('h', 'newtemplate', 850);
  if (!is_numeric(active('w', 'newtemplate'))) paste2sessionactive('w', 'newtemplate', 1120);

   
  if (active('category', 'newtemplate') != '') {
    $stmt = $dbh->prepare('SELECT title FROM content_category WHERE content_category.id=:contentcategoryid');
    $stmt->bindParam(':contentcategoryid', active('category', 'newtemplate'), PDO::PARAM_INT);
    $stmt->execute();
    $qresult = $stmt->fetchAll();
  }

  $preview = '404';

  if (isset($qresult[0])) {

  $preview = checkandpreview($safebox=1, $width=269, $height=200, $format='png', 
  			       active('title', 'newtemplate'), 'Laten we het eens zonder tekst doen.', '',
			       'default.xsl', $dir='', $filename=md5('default.xsl'.$qresult[0]['title'].active('title', 'newtemplate').active('photo', 'newtemplate').active('w', 'newtemplate').active('h', 'newtemplate').active('x', 'newtemplate').active('y', 'newtemplate')),
			       $qresult[0]['title'],
			       active('photo', 'newtemplate'), active('w', 'newtemplate'),
			       active('h', 'newtemplate'), active('x', 'newtemplate'), active('y', 'newtemplate'));
}


   if (isset($_POST['action'])) {
   if ($_POST['action']==ACT_SAVE && active('title', 'newtemplate')!='' && active('photo', 'newtemplate')!='' && active('category', 'newtemplate')!='') {
    
    $dbh->beginTransaction();
    if ($_SESSION['newtemplate']['activeid'] == 0) {
        $stmt = $dbh->prepare('INSERT INTO content_category_image(categoryid, title, photo, width, height, x, y) VALUES (:categoryid, :title, :photo, :width, :height, :x, :y)');
    } else {
        $stmt = $dbh->prepare('UPDATE content_category_image SET categoryid = :categoryid, title = :title, photo = :photo, width = :width, height = :height, x = :x, y = :y WHERE id = :id');
        $stmt->bindParam(':id', $_SESSION['newtemplate']['activeid'], PDO::PARAM_INT);
    }
    $stmt->bindParam(':categoryid', active('category', 'newtemplate'), PDO::PARAM_INT);
    $stmt->bindParam(':title', active('title', 'newtemplate'), PDO::PARAM_STR, 20);
    $stmt->bindParam(':photo', active('photo', 'newtemplate'), PDO::PARAM_STR, 100);
    $stmt->bindParam(':width', active('w', 'newtemplate'), PDO::PARAM_INT);
    $stmt->bindParam(':height', active('h', 'newtemplate'), PDO::PARAM_INT);
    $stmt->bindParam(':x', active('x', 'newtemplate'), PDO::PARAM_INT);
    $stmt->bindParam(':y', active('y', 'newtemplate'), PDO::PARAM_INT);
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
      <fieldset>
        <legend><?php echo CATEGORY; ?> &amp; <?php echo TEMPLATE; ?></legend>
        <?php 
	        $stmt = $dbh->query('SELECT content_category.id, content_category.title FROM content_category ORDER BY content_category.title');
            $stmt->execute();
	        $qresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
	        echo dbtoselect('category', $qresult, active('category', 'newtemplate'), true);
            
            if (active('category', 'newtemplate') > 0) {
                $stmt = $dbh->prepare('SELECT cci.id, cci.title FROM content_category_image AS cci WHERE cci.categoryid = :categoryid ORDER BY cci.title');
                $stmt->bindParam(':categoryid', active('category', 'newtemplate'), PDO::PARAM_INT);
                $stmt->execute();
                $qresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo dbtoselect('activeid', $qresult, active('activeid', 'newtemplate'), true);
            }

        ?>
        <a href="categorie-toevoegen.php"><?php echo NEWCATEGORY; ?></a>
      </fieldset>
      <fieldset class="buttons">
        <legend><?php echo ACTION; ?></legend>
        <input type="submit" name="action" value="<?php echo ACT_UPD; ?>" />
	    <input type="submit" name="action" value="<?php echo ACT_NEW; ?>" />
        <input type="submit" name="action" value="<?php echo ACT_SAVE; ?>" />
        <input type="submit" name="action" value="<?php echo ACT_BACK; ?>" />
        <input type="submit" name="action" value="<?php echo TPL_OPEN; ?>" />
        <input type="submit" name="action" style="float: right; background-color: #f00;" value="<?php echo TPL_REMOVE; ?>" />
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
    <div style="float: left;">
	<label><?php echo LEFT; ?></label><input type="text" name="x" value="<?php echo active('x', 'newtemplate');?>" maxlength="4" style="width: 7em;" />
	<label><?php echo TOP; ?></label><input type="text" name="y" value="<?php echo active('y', 'newtemplate'); ?>" maxlength="4" style="width: 7em;" />
	<label><?php echo HEIGHT; ?> (850)</label><input type="text" name="h" value="<?php echo active('h', 'newtemplate'); ?>" maxlength="4" style="width: 7em;" />
	<label><?php echo WIDTH; ?> (1120/630)</label><input type="text" name="w" value="<?php echo active('w', 'newtemplate'); ?>" maxlength="4" style="width: 7em;" />
    </div>
	<img alt="<?php echo EXAMPLE; ?>" src="preview/<?php echo $preview; ?>.png" style="float: left; border: solid 1px #000; margin-left: 2px; margin-top: 1px;"/>
      </fieldset>
     </fieldset>
     </form>
  </body>
</html>
