<?php
  
  session_start();

  require_once('functions.php');


  if (isset($_GET['databaseid']) && is_numeric($_GET['databaseid']) && $_GET['databaseid'] > 0) {
    $db = sqlite_open(DATABASE, 0666, $sqlerror);
    $query = sqlite_query($db, 'SELECT template, category, duration, photo, title, content FROM content, content_text WHERE content.id='.$_GET['databaseid'].' AND content.id = content_text.contentid;');
//  $query = sqlite_query($db, 'SELECT start, end, template, category, duration, photo, title, content FROM content, content_text WHERE content.id='.$_GET['databaseid'].' AND content.id = content_text.contentid;');
    $result = sqlite_fetch_all($query, SQLITE_ASSOC);
    $query = sqlite_query($db, 'SELECT start, end, enabled FROM content_run WHERE contentid = '.$_GET['databaseid'].';');
    $result1 = sqlite_fetch_all($query, SQLITE_ASSOC);

    $query = sqlite_query($db, 'INSERT INTO content_seens (contentid, editorid) VALUES ('.$_GET['databaseid'].', (SELECT id FROM editors WHERE login=\''.sqlite_escape_string($_SERVER['PHP_AUTH_USER']).'\'));');
    sqlite_close($db);
    if (count($result) > 0) {
      newdocument();
      $_SESSION['document']['databaseid']=$_GET['databaseid'];
      $_SESSION['document']['content_start']=$result[0]['start'];
      $_SESSION['document']['content_end']=$result[0]['end'];
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
					     'content_end'=>$entry['end'],
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
    $db = sqlite_open(DATABASE, 0666, $sqlerror);
    
    sqlite_query('BEGIN;', $db);

    $result = array();

    if (isset($_SESSION['document']['databaseid'])) {
      sqlite_query($db, 'DELETE FROM content_text WHERE contentid='.$_SESSION['document']['databaseid'].';');
//      sqlite_query($db, 'DELETE FROM content WHERE id='.$_SESSION['document']['databaseid'].';');
      sqlite_query($db, 'UPDATE content SET state=1 WHERE id='.$_SESSION['document']['databaseid'].';');
    } else {
        sqlite_query($db, 'INSERT INTO content (state) VALUES (0);');
        $query = sqlite_query($db, 'SELECT max(id) FROM content WHERE state=0 ORDER BY id DESC;');
        $result = sqlite_fetch_all($query, SQLITE_ASSOC);
        $_SESSION['document']['databaseid']=$result[0]['max(id)'];
    }

    if (!isset($_SESSION['document']['databaseid'])) {
      sqlite_query($db, 'ROLLBACK;');
    } else {
      foreach ($_SESSION['document'] as $key => $value) {
        if (is_numeric($key) && $key > 0) {
	  sqlite_query($db, 'INSERT INTO content_text (contentid, template, category, duration, photo, title, content) VALUES ('.$_SESSION['document']['databaseid'].', \''.sqlite_escape_string(passive('text_template', $key)).'\', \''.sqlite_escape_string(passive('text_category', $key)).'\', \''.sqlite_escape_string(passive('text_duration', $key)).'\', \''.sqlite_escape_string(passive('text_photo', $key)).'\', \''.sqlite_escape_string(passive('text_title', $key)).'\', \''.str_replace('\'', '\\\'', passive('text_content', $key)).'\');');
	  
	}
      }

      sqlite_query($db, 'DELETE FROM content_run WHERE contentid='.$_SESSION['document']['databaseid'].';');
      
      foreach ($_SESSION['document']['run'] as $entry) {
        sqlite_query($db, 'INSERT INTO content_run (contentid, start, end, enabled) VALUES ('.$_SESSION['document']['databaseid'].', '.$entry['content_start'].', '.$entry['content_end'].', '.($entry['enabled']?1:0).');');
      }

      $query = sqlite_query($db, 'DELETE FROM content_seens WHERE contentid = '.$_SESSION['document']['databaseid'].';');
      
      $query = sqlite_query($db, 'SELECT editors.login FROM content_editor, editors WHERE content_editor.editorid=editors.id AND content_editor.contentid='.$_SESSION['document']['databaseid'].' ORDER BY content_editor.id DESC LIMIT 1;');

      if (($result = sqlite_fetch_all($query, SQLITE_ASSOC)) === false || ($result !== false && $result[0]['editors.login'] != $_SERVER['PHP_AUTH_USER'])) {
         sqlite_query($db, 'INSERT INTO content_editor (contentid, editorid) VALUES ('.$_SESSION['document']['databaseid'].', (SELECT id FROM editors WHERE login=\''.sqlite_escape_string($_SERVER['PHP_AUTH_USER']).'\'));');
      }
    }
    
    sqlite_query($db, 'COMMIT;');
    sqlite_close($db);
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
      echo '      <input type="submit" class="image" name="action" value="'.$key.'" style="background-image: url(\'preview/'.@checkandgenerate($key, 1).'.png\'); width: 269px; height: 200px; font-size: 0;" />';
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
	  <?php echo dbtoselect('text_category', 'SELECT content_category_image.id, content_category.title, content_category_image.title FROM content_category, content_category_image WHERE content_category.id=content_category_image.categoryid'.(isset($_SESSION['category']) ? ' AND content_category.title = "'.$_SESSION['category'].'"' : '').' ORDER BY content_category.title, content_category_image.title;', active('text_category')); ?> <a href="template-toevoegen.php"><?php echo NEWIMAGE; ?></a>
	</fieldset>
	<fieldset class="datetime"><legend><?php echo LENGTH; ?></legend><?php echo timetoinput('text_duration', active('text_duration')); ?></fieldset>
	<fieldset>
	  <legend><?php echo PHOTO; ?></legend>
	  <?php echo @dirtoselect('text_photo', USER_IMAGEDIR, active('text_photo'), true, (time() - 604800)); ?>
	  <a href="photoupload.php"><?php echo UPLOAD; ?></a>
	</fieldset>
        <label><?php echo TITLE; ?></label><input type="text" name="text_title" value="<?php echo active('text_title'); ?>" /><br />
        <label><?php echo TEXT; ?></label><textarea name="text_content" rows="16" cols="70"><?php echo active('text_content'); ?></textarea>
	<img src="preview/<?php echo @checkandgenerate($_SESSION['document']['activeid'], 1); ?>.png" alt="Preview" style="float: left; border: solid 1px #000; margin-left: 2px; margin-top: 1px;" onclick="if (this.width == '500') { this.width=269; this.height='200'; } else { this.width='500'; this.height='375'; }"/>
      </fieldset>
    </form>
  </body>
</html>
