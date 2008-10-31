<?php
  session_start();
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') === false) 
    header('Content-type: application/xhtml+xml; charset=utf-8');
  else 
    header('Content-type: text/html; charset=utf-8');

  require_once('functions.php');

  $now = time();
  $start = $now;
  $end = $now;

  $db = sqlite_open(DATABASE, 0666, $sqlerror);
  $query = sqlite_query($db, 'SELECT content_text.id, title, content FROM content_run, content, content_text WHERE content_run.start <= '.$start.' AND content_run.end >= '.$end.' AND content.id=content_run.contentid AND content.id=content_text.contentid AND (title <> \'\' AND content <> \'\');');
  $result = sqlite_fetch_all($query, SQLITE_ASSOC);
  sqlite_close($db);

?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
  <head>
    <title><?php echo OWNER; ?></title>
    <link rel="stylesheet" href="toevoegen.css" type="text/css" />
  </head>
  <body>
<?php
	while (list($ind, $var)=each($result)) {
	  echo '<b>'.$var['title'].'</b><br /><div>'.$var['content'].'</div><br />';
	}
?>
  </body>
</html>
