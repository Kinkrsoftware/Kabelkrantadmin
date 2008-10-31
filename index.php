<?php
  session_start();
  
  require_once('functions.php');

  $now = time();

  if (isset($_GET['now']) && is_numeric($_GET['now'])) {
     $now = $_GET['now'];
   }

   if (isset($_GET['category'])) {
   	if ($_GET['category'] == 'None') {
		unset($_SESSION['category']);
	} else {
	   	$_SESSION['category'] = sqlite_escape_string($_GET['category']);
	}
   } 
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
  <head>
    <title><?php echo OWNER; ?></title>
    <link rel="stylesheet" href="index.css" type="text/css" />
  </head>
  <body>
    <?php if (EMERGENCY) echo EMERG.'<br /><br />'; ?>
    <?php echo WELCOME; ?><br />

    <table>
      <tr>
        <?php
		$now = time();
		$start = $now;
		$end = $now;
			      
		$db = sqlite_open(DATABASE, 0666, $sqlerror);
		if (isset($_SESSION['category'])) {
			$select = 'SELECT sum(content_text.duration) FROM content_run, content, content_text, content_category, content_category_image WHERE content_category_image.categoryid = content_category.id AND content_category_image.id = content_text.category AND content_category.title = "'.$_SESSION['category'].'" AND content_run.start <= '.$start.' AND content_run.end >= '.$end.' AND content.id=content_run.contentid AND content.id=content_text.contentid;';
		} else {
			$select = 'SELECT sum(content_text.duration) FROM content_run, content, content_text WHERE content_run.start <= '.$start.' AND content_run.end >= '.$end.' AND content.id=content_run.contentid AND content.id=content_text.contentid;';
		}

		$query = sqlite_query($db, $select);
		$result = sqlite_fetch_all($query, SQLITE_ASSOC);
		sqlite_close($db);
	?>
        <th class="none"><?php echo OWNER; ?> <a href="toevoegen.php" style="text-align: right;"><?php echo ADD; ?></a><br />
	<?php echo LENGTH; ?>: <?php echo $result[0]['sum(content_text.duration)']; ?>s
	<?php echo '('.(($result[0]['sum(content_text.duration)']-($result[0]['sum(content_text.duration)']%60))/60).':'.(($result[0]['sum(content_text.duration)']%60)<10?'0':'').($result[0]['sum(content_text.duration)']%60).')'; ?> <a href="overzicht.html"><?php echo NEXTPER; ?></a></th>
	<th><?php echo START; ?></th>
	<th><?php echo UNTIL; ?></th>
	<th><?php echo LENGTH; ?></th>
	<th><?php echo LASTMOD; ?></th>
	<th><?php echo VIEW; ?></th>
      </tr>
<?php
// +7
// $maxdate = $now - 604800;
  $maxdate = $now - 172800;


  /* hackje even netjes maken en laten zien dat je datum en tijd beheerst*/
  $db = sqlite_open(DATABASE, 0666, $sqlerror);
  $search = 'content_run.end > '.$maxdate;
  
  if (isset($_POST['search'])) {
    $needle = sqlite_escape_string(strtolower($_POST['search']));
//    $search = '(lower(content_text.title) like \'%'.$needle.'%\' OR lower(content_text.content) like \'%'.$needle.'%\')';
      $search = '(lower(content_text.title) like \'%'.$needle.'%\')';
  }
  if (isset($_SESSION['category'])) {
	$select = 'SELECT content.id, content_run.start, content_run.end, sum(duration), content_text.title, content_text.photo FROM content_run, content, content_text, content_category, content_category_image WHERE '.$search.' AND content_category_image.categoryid = content_category.id AND content_category_image.id = content_text.category AND content_category.title = "'.$_SESSION['category'].'" AND content.id = content_run.contentid AND content.id = content_text.contentid GROUP BY content_run.id ORDER BY content_run.start DESC;';
  } else {
  	$select = 'SELECT content.id, content_run.start, content_run.end, sum(duration), content_text.title, content_text.photo FROM content_run, content, content_text WHERE '.$search.' AND content.id = content_run.contentid AND content.id = content_text.contentid GROUP BY content_run.id ORDER BY content_run.start DESC;';
  }
 
 if (($query = sqlite_query($db, $select)) !== false ) {
    $result = sqlite_fetch_all($query, SQLITE_ASSOC);

    if (is_array($result)) {
      foreach ($result as $entry) {
    	$didedit = '';
        //setlocale (LC_TIME, 'Dutch');
	if (($query1 = sqlite_query($db, 'SELECT givenname from content_editor, editors WHERE content_editor.contentid='.$entry['content.id'].' AND content_editor.editorid = editors.id;')) !== false ) {
	   $editors = sqlite_fetch_all($query1, SQLITE_ASSOC);
	   if (count($editors) < 4) {
	     foreach ($editors as $entry1) {
	       $didedit .= $entry1['givenname'].', ';
	     }
	     $didedit = substr($didedit, 0, -2);
	   } else {
	     $entry1 = reset($editors);
	     $didedit .= $entry1['givenname'].', ..., ';
	     $entry1 = end($editors);
	     $didedit .= $entry1['givenname'];
	   }
	}

	$seen = 0;
	
	if (($query1 = sqlite_query($db, 'SELECT count(contentid) FROM content_seens, editors WHERE contentid='.$entry['content.id'].' AND content_seens.editorid = editors.id AND login=\''.sqlite_escape_string($_SERVER['PHP_AUTH_USER']).'\'')) !== false ) {
	   $seen = sqlite_fetch_all($query1, SQLITE_ASSOC);
	   $seen = $seen[0]['count(contentid)'];
	}
        echo '        <tr><td class="title"><a href="toevoegen.php?databaseid='.$entry['content.id'].'">'.($entry['content_text.title']==''?($entry['content_text.photo']==''?UNDEFINED:$entry['content_text.photo']):$entry['content_text.title']).'</a></td><td class="startdate">'.date('H:i:s<\b\r />j F',$entry['content_run.start']).'</td><td class="enddate">'.date('H:i:s<\b\r />j F',$entry['content_run.end']).'</td><td class="duration">'.$entry['sum(duration)'].'</td><td>'.$didedit.'</td><td>'.($seen>0?YES:NO).'</td></tr>'.chr(13).chr(10);
      }
    } else echo NORESULT;
  } else echo NORESULT;
  sqlite_close($db);
?>
       <tr><td class="title"><form method="post"><input type="text" name="search" /></form></td></tr>
    </table>
  </body>
</html>
