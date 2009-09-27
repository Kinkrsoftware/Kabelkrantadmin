<?php
  
  session_start();

  require_once('functions.php');

  $dbh = new PDO(DATABASE, DB_USER, DB_PASSWORD);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if (isset($_GET['databaseid']) && is_numeric($_GET['databaseid']) && $_GET['databaseid'] > 0) {
    $stmt = $dbh->prepare('SELECT template, category, duration, photo, title, content FROM content, content_text WHERE content.id=:databaseid AND content.id = content_text.contentid;');
    $stmt->bindParam(':databaseid', $_GET['databaseid'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $stmt = $dbh->prepare('SELECT start, eind, enabled FROM content_run WHERE contentid = :databaseid');
    $stmt->bindParam(':databaseid', $_GET['databaseid'], PDO::PARAM_INT);
    $stmt->execute();
    $result1 = $stmt->fetchAll();

    $stmt = $dbh->prepare('INSERT INTO content_seens (contentid, editorid) VALUES (:databaseid, (SELECT id FROM editors WHERE login = :remoteuser))');
    $stmt->bindParam(':databaseid', $_GET['databaseid'], PDO::PARAM_INT);
    $stmt->bindParam(':remoteuser', $_SERVER['REMOTE_USER'], PDO::PARAM_STR);
    try {
	    $stmt->execute();
    }
    catch (PDOException $e) {} 

    
    if (count($result) > 0) {
      newdocument();
      $_SESSION['document']['databaseid']=$_GET['databaseid'];
      $_SESSION['document']['content_start']=$result[0]['start'];
      $_SESSION['document']['content_end']=$result[0]['eind'];
      foreach ($result as $entry)
        $_SESSION['document'][]=array('text_template'=>$entry['template'],
				      'text_category'=>$entry['category'],
	                              'text_duration'=>$entry['duration'],
				      'text_photo'=>$entry['photo'],
				      'text_title'=>$entry['title'],
				      'text_content'=>$entry['content']);
      
      
      $_SESSION['document']['run']=array();
      foreach ($result1 as $entry) {
        $_SESSION['document']['run'][]=array('content_start'=>$entry['start'],
					     'content_end'=>$entry['eind'],
					     'content_enabled'=>$entry['enabled']);
      }
    }
    
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
  }

  if (!isset($_SESSION['document']) || isset($_GET['nieuw']) || (isset($_POST['action']) && $_POST['action']==ACT_NEWI)) {
    newdocument();
    header('Location: toevoegen.php');
    exit;
  }
 
  postruntimes2session();
//  postdate2session('content_start');
//  postdate2session('content_end');
  post2sessionactive('text_title');
  post2sessionactive('text_content');
  posttime2sessionactive('text_duration');
  post2sessionactive('text_template');
  post2sessionactive('text_category');
  post2sessionactive('text_photo');

  if ($_POST['action']==ACT_SAVE) {
    $dbh->beginTransaction();

    $result = array();

    if (isset($_SESSION['document']['databaseid'])) {
	$stmt = $dbh->prepare('DELETE FROM content_text WHERE contentid = :databaseid');
	$stmt->bindParam(':databaseid', $_SESSION['document']['databaseid'], PDO::PARAM_INT);
	$stmt->execute();
    
//      sqlite_query($db, 'DELETE FROM content WHERE id='.$_SESSION['document']['databaseid'].';'); /* even checken waarom dit nodig was */

	$stmt = $dbh->prepare('UPDATE content SET state=1 WHERE id = :databaseid');
	$stmt->bindParam(':databaseid', $_SESSION['document']['databaseid'], PDO::PARAM_INT);
	$stmt->execute();
    } else {
        $dbh->query('INSERT INTO content (state) VALUES (0);'); /* Ooit van Auto-Increment gehoord? */
	$stmt = $dbh->query('SELECT max(id) AS "max" FROM content WHERE state=0;');
	$result = $stmt->fetchAll();
        $_SESSION['document']['databaseid']=$result[0]['max'];
    }

    if (!isset($_SESSION['document']['databaseid'])) {
      $dbh->rollBack();
    } else {
      try {
      foreach ($_SESSION['document'] as $key => $value) {
        if (is_numeric($key) && $key > 0) {
	  $stmt = $dbh->prepare('INSERT INTO content_text (contentid, template, category, duration, photo, title, content) VALUES (:databaseid, :text_template, :text_category, :text_duration, :text_photo, :text_title, :text_content)');

//	  .'\', \''.str_replace('\'', '\\\'', passive('text_content', $key)).'\')');

	  $stmt->bindParam(':databaseid', $_SESSION['document']['databaseid'], PDO::PARAM_INT);
	  $stmt->bindParam(':text_template', passive('text_template', $key), PDO::PARAM_STR, 50);
	  $stmt->bindParam(':text_category', passive('text_category', $key), PDO::PARAM_INT);
	  $stmt->bindParam(':text_duration', passive('text_duration', $key), PDO::PARAM_INT);
	  $stmt->bindParam(':text_photo', passive('text_photo', $key), PDO::PARAM_STR, 100);
	  $stmt->bindParam(':text_title', passive('text_title', $key), PDO::PARAM_STR, 128);
	  $stmt->bindParam(':text_content', passive('text_content', $key), PDO::PARAM_STR);
	  $stmt->execute();
	}
      }
      $stmt = $dbh->prepare('DELETE FROM content_run WHERE contentid = :databaseid');
      $stmt->bindParam(':databaseid', $_SESSION['document']['databaseid'], PDO::PARAM_INT);
      $stmt->execute();
      
      foreach ($_SESSION['document']['run'] as $entry) {
        $stmt = $dbh->prepare('INSERT INTO content_run (contentid, start, eind, enabled) VALUES (:databaseid, :start, :end, :enabled)');
	$stmt->bindParam(':databaseid', $_SESSION['document']['databaseid'], PDO::PARAM_INT);
	$stmt->bindParam(':start', $entry['content_start'], PDO::PARAM_INT);
	$stmt->bindParam(':end', $entry['content_end'], PDO::PARAM_INT);
	$enabled = ($entry['enabled']?1:0);
	$stmt->bindParam(':enabled', $enabled, PDO::PARAM_INT);
	$stmt->execute();
      }
      
      $stmt = $dbh->prepare('DELETE FROM content_seens WHERE contentid = :databaseid');
      $stmt->bindParam(':databaseid', $_SESSION['document']['databaseid'], PDO::PARAM_INT);
      $stmt->execute();
     
      $stmt = $dbh->prepare('SELECT editors.login FROM content_editor, editors WHERE content_editor.editorid=editors.id AND content_editor.contentid = :databaseid ORDER BY content_editor.id DESC LIMIT 1');
      $stmt->bindParam(':databaseid', $_SESSION['document']['databaseid'], PDO::PARAM_INT);
      $stmt->execute();

      if (($result = $stmt->fetchAll()) === false || ($result !== false && $result[0]['editors.login'] != $_SERVER['REMOTE_USER'])) {
         $stmt = $dbh->prepare('INSERT INTO content_editor (contentid, editorid) VALUES (:databaseid, (SELECT id FROM editors WHERE login = :remoteuser))');
	 $stmt->bindParam(':databaseid', $_SESSION['document']['databaseid'], PDO::PARAM_INT);
	 $stmt->bindParam(':remoteuser', $_SERVER['REMOTE_USER'], PDO::PARAM_STR);
	 $stmt->execute();
      }

      $dbh->commit();
      } 
      catch(PDOException $e)
          {
	      echo $e->getMessage();
	          }
    }
   
    $dbh = null;
    header('Location: '.$_SERVER['PHP_SELF'].'?databaseid='.$_SESSION['document']['databaseid']);
    exit;
  } 

  if (isset($_POST['action'])) {

  if ($_POST['action']==ACT_ADD) {
    $_SESSION['document'][]=$_SESSION['document'][$_SESSION['document']['activeid']];
    newpreview();
  }

  if ($_POST['action']==ACT_DEL || isset($_GET['verwijder'])) {
    unset($_SESSION['document'][$_SESSION['document']['activeid']]);
    $_SESSION['document']['activeid']=0;
  }

  if ($_POST['action']==ACT_NEW) {
    newpreview();
  }

  if($_POST['action']==ACT_NEWP) {
    newrun();
  }

  if (is_numeric($_POST['action']) && isset($_SESSION['document'][$_POST['action']])) {
    $_SESSION['document']['activeid']=$_POST['action'];
  }

  if ($_POST['action']==ACT_BACK) {
    header('Location: index.php');
  }
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
        <legend><?php echo EXAMPLE; ?></legend>
<?php
//preview
foreach ($_SESSION['document'] as $key => $value) {
    if (is_numeric($key) && $key > 0) {
      echo '      <input type="submit" class="image" name="action" value="'.$key.'" style="background-image: url(\'preview/'.@checkandgenerate($dbh, $key, 1).'.png\'); width: 269px; height: 200px; font-size: 0;" />';
    }
  }

?>
      </fieldset>
      <fieldset class="datetime">
         <legend><?php echo PLAYEDAT; ?></legend>
	 <?php echo runtimes($_SESSION['document']); ?>
      </fieldset>
      <fieldset class="buttons">
        <legend><?php echo ACTION; ?></legend>
	<input type="submit" name="action" value="<?php echo ACT_UPD; ?>" />
	<input type="submit" name="action" value="<?php echo ACT_ADD; ?>" />
	<input type="submit" name="action" value="<?php echo ACT_DEL; ?>" />
	<input type="submit" name="action" value="<?php echo ACT_NEW; ?>"  />
	<input type="submit" name="action" value="<?php echo ACT_SAVE; ?>" />
	<input type="submit" name="action" value="<?php echo ACT_NEWI; ?>" />
	<input type="submit" name="action" value="<?php echo ACT_NEWP; ?>" />
	<input type="submit" name="action" value="<?php echo ACT_BACK; ?>" />
      </fieldset>
      <fieldset>
        <legend><?php echo TEXTPAGE; ?></legend>
	<fieldset>
	  <legend><?php echo TEMPLATE; ?></legend>
	  <?php echo dirtoselect('text_template', TEMPLATEDIR, active('text_template')); ?>
	  <?php 
		$stmt = $dbh->prepare('SELECT content_category_image.id, content_category.title AS "category_title", content_category_image.title AS "title" FROM content_category, content_category_image WHERE content_category.id=content_category_image.categoryid'.(isset($_SESSION['category']) ? ' AND content_category.title = :category' : '').' ORDER BY content_category.title, content_category_image.title;');
		if (isset($_SESSION['category']))  $stmt->bindParam(':category', $_SESSION['category'], PDO::PARAM_STR, 20);
		$stmt->execute();
		$qresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
	  	echo dbtoselect('text_category', $qresult, active('text_category')); ?> <a href="template-toevoegen.php"><?php echo NEWIMAGE; ?></a>
	</fieldset>
	<fieldset class="datetime"><legend><?php echo LENGTH; ?></legend><?php echo timetoinput('text_duration', active('text_duration')); ?></fieldset>
	<fieldset>
	  <legend><?php echo PHOTO; ?></legend>
	  <?php echo @dirtoselect('text_photo', USER_IMAGEDIR, active('text_photo'), true, (time() - 604800)); ?>
	  <a href="photoupload.php"><?php echo UPLOAD; ?></a>
	</fieldset>
        <label><?php echo TITLE; ?></label><input type="text" name="text_title" value="<?php echo active('text_title'); ?>" /><br />
        <label><?php echo TEXT; ?></label><textarea name="text_content" rows="16" cols="70"><?php echo active('text_content'); ?></textarea>
	<img src="preview/<?php echo checkandgenerate($dbh, $_SESSION['document']['activeid'], 1); ?>.png" alt="Preview" style="float: left; border: solid 1px #000; margin-left: 2px; margin-top: 1px;" onclick="if (this.width == '500') { this.width=269; this.height='200'; } else { this.width='500'; this.height='375'; }"/>
      </fieldset>
    </form>
  </body>
</html>
