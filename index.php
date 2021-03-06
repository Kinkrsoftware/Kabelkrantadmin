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
	   	$_SESSION['category'] = $_GET['category'];
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
    <?php echo WELCOME.' '.VERSION.' - <i>'.SIGNEDINUSER.'</i>: <b>'.$_SERVER['PHP_AUTH_USER'].'</b>'; ?><br />

    <table>
      <tr>
        <?php
		$now = time();
		$start = $now;
		$end = $now;

		$curday = date('N');
                
		$dbh = new PDO(DATABASE, DB_USER, DB_PASSWORD);

		if (isset($_SESSION['category'])) {
			$stmt = $dbh->prepare('select sum(duration) as "duration" from content_run, content_text where content_text.contentid = content_run.contentid AND content_run.enabled = 1 AND (content_run.day = 0 or content_run.day = :curday) AND content_run.start <= :start AND content_run.eind >= :end AND content_text.category IN (select content_category_image.id from content_category_image, content_category where content_category_image.categoryid = content_category.id AND content_category.title = :category)');

//:	                $stmt = $dbh->prepare('SELECT sum(content_text.duration) AS "duration" FROM content_run, content, content_text, content_category, content_category_image WHERE content_category_image.categoryid = content_category.id AND content_category_image.id = content_text.category AND content_category.title = :category AND content_run.start <= :start AND content_run.eind >= :end AND content.id=content_run.contentid AND content.id=content_text.contentid;');
	                $stmt->bindParam(':category', $_SESSION['category'], PDO::PARAM_STR, 15);
	                $stmt->bindParam(':start', $start, PDO::PARAM_INT);
	                $stmt->bindParam(':end', $end, PDO::PARAM_INT);
	                $stmt->bindParam(':curday', $curday, PDO::PARAM_INT);
	                $stmt->execute();
		} else {
	                $stmt = $dbh->prepare('SELECT sum(content_text.duration) AS "duration" FROM content_run, content_text WHERE content_run.enabled = 1 AND (content_run.day = 0 or content_run.day = :curday) AND content_run.start <= :start AND content_run.eind >= :end AND content_run.contentid = content_text.contentid;');
	                $stmt->bindParam(':start', $start, PDO::PARAM_INT);
	                $stmt->bindParam(':end', $end, PDO::PARAM_INT);
	                $stmt->bindParam(':curday', $curday, PDO::PARAM_INT);
	                $stmt->execute();
		}

		$result = $stmt->fetchAll();
	?>
        <th class="none"><?php echo OWNER; ?> <a href="toevoegen.php" style="text-align: right;"><?php echo ADD; ?></a> <a href="broadcast.php" style="text-align: right;"><?php echo GENERATEANDBROADCAST; ?></a><br />
	<?php echo LENGTH; ?>: <?php echo $result[0]['duration']; ?>s
	<?php echo '('.(($result[0]['duration']-($result[0]['duration']%60))/60).':'.(($result[0]['duration']%60)<10?'0':'').($result[0]['duration']%60).')'; ?> <a href="overzicht.html"><?php echo NEXTPER; ?></a></th>
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
  $search = 'content_run.eind > :maxdate AND content_run.enabled = 1';
  
  if (isset($_POST['search'])) {
    $needle = '%'.strtolower($_POST['search']).'%';
    $search = '(lower(content_text.title) like :needle OR lower(content_text.content) like :needle)';
  }
  if (isset($_SESSION['category'])) {
//	$select = 'SELECT content.id, content_run.start, content_run.eind, sum(duration) AS "duration", content_text.title, content_text.photo FROM content_run, content, content_text, content_category, content_category_image WHERE '.$search.' AND content_category_image.categoryid = content_category.id AND content_category_image.id = content_text.category AND content_category.title = :category AND content.id = content_run.contentid AND content.id = content_text.contentid GROUP BY content.id, content_text.id, content_run.id ORDER BY content_run.start DESC;';
	 $select = 'select content_text.contentid AS "contentid", start, eind, content_text.title AS "title", photo, duration.duration AS "duration" from content_text, content_run, (select contentid, sum(duration) as duration from content_text where content_text.category IN (select content_category_image.id from content_category_image, content_category where content_category_image.categoryid = content_category.id AND content_category.title = :category) group by contentid) as duration where '.$search.' AND content_text.contentid = content_run.contentid and content_text.id in (select min(content_text.id) from content_text group by contentid) and content_text.contentid = duration.contentid order by start DESC;';
  } else {
  	 // $select = 'SELECT content.id, content_run.start, content_run.eind, sum(duration) AS "duration", content_text.title, content_text.photo FROM content_run, content, content_text WHERE '.$search.' AND content.id = content_run.contentid AND content.id = content_text.contentid GROUP BY content_run.id, content.id, content_text.id ORDER BY content_run.start DESC;';
	 $select = 'select content_text.contentid AS "contentid", start, eind, title, photo, duration.duration AS "duration" from content_text, content_run, (select contentid, sum(duration) as duration from content_text group by contentid) as duration where '.$search.' AND content_text.contentid = content_run.contentid and content_text.id in (select min(id) from content_text group by contentid) and content_text.contentid = duration.contentid order by start DESC;';
  }

  $stmt = $dbh->prepare($select);
  if (isset($_POST['search'])) 
  	$stmt->bindParam(':needle', $needle, PDO::PARAM_STR);
  else
  	$stmt->bindParam(':maxdate', $maxdate, PDO::PARAM_INT);
  if (isset($_SESSION['category'])) $stmt->bindParam(':category', $_SESSION['category'], PDO::PARAM_STR, 15);

//  print_r($stmt);
  $stmt->execute();

  $result = $stmt->fetchAll();

    if (is_array($result)) {
      foreach ($result as $entry) {
        $stmt = $dbh->prepare('SELECT givenname from content_editor, editors WHERE content_editor.contentid=:contentid AND content_editor.editorid = editors.id;');
	$stmt->bindParam(':contentid', $entry['contentid'], PDO::PARAM_INT);
	$stmt->execute();
	$editors = $stmt->fetchAll();

    	$didedit = '';
	
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

	$seen = 0;

	$stmt = $dbh->prepare('SELECT count(contentid) AS aantal FROM content_seens, editors WHERE contentid=:contentid AND content_seens.editorid = editors.id AND login=:login');
	$stmt->bindParam(':contentid', $entry['contentid'], PDO::PARAM_INT);
	$stmt->bindParam(':login', $_SERVER['REMOTE_USER'], PDO::PARAM_STR, 15);
	$stmt->execute();
	$seen = $stmt->fetchAll();
	$seen = $seen[0]['aantal'];

        echo '        <tr><td class="title"><a href="toevoegen.php?databaseid='.$entry['contentid'].'">'.($entry['title']==''?($entry['photo']==''?UNDEFINED:$entry['photo']):$entry['title']).'</a></td><td class="startdate">'.date('H:i:s<\b\r />j F',$entry['start']).'</td><td class="enddate">'.date('H:i:s<\b\r />j F',$entry['eind']).'</td><td class="duration">'.$entry['duration'].'</td><td>'.$didedit.'</td><td>'.($seen>0?YES:NO).'</td></tr>'.chr(13).chr(10);
      }
    } else echo NORESULT;
   $dbh = null;
?>
       <tr><td class="title"><form method="post"><input type="text" name="search" /></form></td></tr>
    </table>
  </body>
</html>
