<?php
  require_once('functions.php');

  $now = time();

  if (isset($_GET['now']) && is_numeric($_GET['now'])) {
    $now = $_GET['now'];
  }

  $db = sqlite_open(DATABASE, 0666, $sqlerror);
  if (isset($_GET['week'])) {
    $start = $now-(7*24*60*60);
  } else {
    $start = $now;
  }
  $end = $now;

  $vandaagresult = array();
  $contentresult = array();
  $colofonresult = array();
  $adsresult = array();
  $adsintro = array();
  $adsoutro = array();
	 
   $queryding = 'SELECT content_text.id, content_text.template, content_text.category, content_category.title, content_text.title, content_text.photo, content_text.content, content_text.duration  FROM content_run, content, content_text, content_category, content_category_image WHERE content_run.start <= '.$start.' AND content_run.end >= '.$end.' AND content.id = content_run.contentid AND content.id=content_text.contentid AND content_category.id=content_category_image.categoryid AND content_text.category=content_category_image.id AND content_category.title = \'Vandaag\' ORDER BY content_text.id, content.start, content.end ASC;';
   $query = sqlite_query($db, $queryding);
   $vandaagresult = sqlite_fetch_all($query, SQLITE_ASSOC);
   $queryding = 'SELECT content_text.id, content_text.template, content_text.category, content_category.title, content_text.title, content_text.photo, content_text.content, content_text.duration  FROM content_run, content, content_text, content_category, content_category_image WHERE content_run.start <= '.$start.' AND content_run.end >= '.$end.' AND content.id = content_run.contentid AND content.id=content_text.contentid AND (content_text.category=\'\' OR (content_category.id=content_category_image.categoryid AND content_text.category=content_category_image.id)) AND content_text.template <> \'ng-advertentie.xsl\' AND content_category.title <> \'Vandaag\' AND content_category.title <> \'Colofon\' GROUP BY content_text.id ORDER BY '.(THEMESEQ ? 'content_category_image.categoryid, ':'').'content_text.id, content.start, content.end ASC;';
   $query = sqlite_query($db, $queryding);
  
   $contentresult = sqlite_fetch_all($query, SQLITE_ASSOC);
   $queryding = 'SELECT content_text.id, content_text.template, content_text.category, content_category.title, content_text.title, content_text.photo, content_text.content, content_text.duration  FROM content_run, content, content_text, content_category, content_category_image WHERE content_run.start <= '.$start.' AND content_run.end >= '.$end.' AND content.id = content_run.contentid AND content.id=content_text.contentid AND content_category.id=content_category_image.categoryid AND content_text.category=content_category_image.id AND content_category.title = \'Colofon\' ORDER BY content_text.id, content.start, content.end ASC;';
  $query = sqlite_query($db, $queryding);
 
  $colofonresult = sqlite_fetch_all($query, SQLITE_ASSOC);
  if (!isset($_GET['no-ads'])) {
    $query = sqlite_query($db, 'SELECT content_text.id, content_text.template, content_text.category, content_text.title, content_text.photo, content_text.content, content_text.duration  FROM content_run, content, content_text WHERE content_run.start <= '.$start.' AND content_run.end >= '.$end.' AND content.id = content_run.contentid AND content.id=content_text.contentid AND content_text.template = \'ng-advertentie.xsl\' ORDER BY content_text.id, content.start, content.end ASC;');
    $adsresult = sqlite_fetch_all($query, SQLITE_ASSOC);
  
    if (count($adsresult) > 0) {
    	
  $adsintro = array(0 => array('content_text.id' => 22423, 'content_text.template' => 'ng-advertentie.xsl', 'content_text.category' => 216,
  				 'content_text.title' => '', 'content_text.photo' => 'adverteren 01.jpg', 'content_text.content' => '', 'content_text.duration' => 3));

  $adsoutro = array(0 => array('content_text.id' => 22423, 'content_text.template' => 'ng-advertentie.xsl', 'content_text.category' => 216,
  				 'content_text.title' => '', 'content_text.photo' => 'adverteren 02.jpg', 'content_text.content' => '', 'content_text.duration' => 3));
    }

  }
  sqlite_close($db);


  $result = array_merge($vandaagresult, $contentresult, $colofonresult, $adsintro, $adsresult, $adsoutro);

//  exec('/usr/bin/sudo -u broadcast sshfs -o nonempty -o allow_other tv@'.REMOTEHOST.':'.REMOTEDIR.' '.BROADCASTDIR);
  

  $tmpdirectory = CACHEDIR.'/'.date('Y-m-d',$now);
//  $tmpdirectory = CACHEDIR;
  if (!file_exists($tmpdirectory)) {
    if (!file_exists(CACHEDIR)) mkdir(CACHEDIR);
    mkdir($tmpdirectory);
  }

  $out = array();
  foreach ($result as $entry) {
    $template = stripslashes($entry['content_text.template']);
    $category = stripslashes($entry['content_text.category']);
    $category_title = stripslashes($entry['content_category.title']);
    $title = stripslashes($entry['content_text.title']);
    $content = stripslashes($entry['content_text.content']);
    $photo = stripslashes($entry['content_text.photo']);
    $dur = $entry['content_text.duration'];
    $id = $entry['content_text.id'];

    $file = md5($title.$para.$photo.$template.$category).'.png';
    $location = BROADCASTCACHEDIR.'/'.$hash;

    if (!file_exists($location)) {
	    $file = checkandbroadcast($safebox=0, $width=RESOLUTIONW, $height=RESOLUTIONH, $format='png', $title, $content, $photo, $template, $category, $dir=$tmpdirectory);
    }

    $out[] = array('title'=>($title==''?($photo==''?'Naamloos':$photo):$title), 'src'=>REMOTEDIR.'/cache/'.$file.'.png', 'dur'=>$dur, 'template'=>$template, 'category'=>$category_title);
  }
  reset($out);

  $current_cat = '';

  $fp = fopen($tmpdirectory.'/'.date('Y-m-d',$now).'.smil', 'w');
  $rn = chr(13).chr(10);

  fputs($fp, '<?xml version="1.0"?>'.$rn.
             '<!DOCTYPE smil PUBLIC "-//W3C//DTD SMIL 2.0//EN" "http://www.w3.org/2001/SMIL20/SMIL20.dtd">'.$rn.
	     '<smil xmlns="http://www.w3.org/2001/SMIL20/Language" xmlns:rn="http://features.real.com/2001/SMIL20/Extensions">'.$rn.
	     '  <head>'.$rn.
	     '    <layout>'.$rn.
	     '      <root-layout width="'.RESOLUTIONW.'" height="'.RESOLUTIONH.'" />'.$rn.
	     '      <region id="content" top="0" left="0" width="'.RESOLUTIONW.'" height="'.RESOLUTIONH.'" />'.$rn.
	     '    </layout>'.$rn.
	     '  </head>'.$rn.
	     '  <body>'.$rn.
	     '    <seq repeat="indefinite" fillDefault="remove">'.$rn);

  foreach ($out as $image) {
    if (EMERGENCY) {
      if ($image['template'] == 'NOOD.xsl') {
        fputs($fp, '      <img src="'.$image['src'].'" alt="'.htmlspecialchars($image['title'], ENT_QUOTES, 'UTF-8').'" dur="'.$image['dur'].'s" region="content" fill="remove" erase="whenDone" />'.chr(13).chr(10));
      }

    } else {
      if (defined('BUMPDURATION') && $current_cat != $image['category']) {
	$current_cat = $image['category'];
	$bump = $image['category'].'.png';
	exec('cp "'.USER_IMAGEDIR.'/'.$bump.'" "'.$tmpdirectory.'/'.$bump.'"');
	if (file_exists(USER_IMAGEDIR.'/'.$bump)) {
	  fputs($fp, '      <img src="'.REMOTEDIR.'/cache/'.$bump.'" alt="'.htmlspecialchars('Bump - '.$image['category'], ENT_QUOTES, 'UTF-8').'" dur="'.BUMPDURATION.'s" region="content" fill="remove" erase="whenDone" />'.chr(13).chr(10));
	}

      }

      if ($image['template'] != 'video.xsl') {
        fputs($fp, '      <img src="'.$image['src'].'" alt="'.htmlspecialchars($image['title'], ENT_QUOTES, 'UTF-8').'" dur="'.$image['dur'].'s" region="content" fill="remove" erase="whenDone" />'.chr(13).chr(10));
      } else {
        fputs($fp, '      <video src="'.htmlspecialchars($image['title'], ENT_QUOTES, 'UTF-8').'" alt="Intermezzo" dur="'.$image['dur'].'s" region="content" fill="remove" erase="whenDone" />'.chr(13).chr(10));
      }
    }
  }
  fputs($fp, '    </seq>'.$rn.
             '  </body>'.$rn.
	     '</smil>'.$rn);
  fflush($fp);
  fclose($fp);

  $fp = fopen($tmpdirectory.'/index.html', 'w');
  fputs($fp, '<?xml version="1.0" encoding="UTF-8"?>'.$rn.
             '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'.$rn.
	     '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >'.$rn.
             '  <head>'.$rn.
	     '    <title>PlusRTV</title>'.$rn.
	     '    <script type="text/javascript" src="smil.js"></script>'.$rn.
             '    <link rel="stylesheet" href="index.css" type="text/css" />'.$rn.
             '  </head>'.$rn.
             '  <body onLoad="start();">'.$rn);
  foreach ($out as $image) {
    if (EMERGENCY) {
      if ($image['template'] == 'NOOD.xsl') {
        fputs($fp, '      <img src="'.$image['src'].'" alt="'.htmlspecialchars($image['title'], ENT_QUOTES, 'UTF-8').'" dur="'.$image['dur'].'s" />'.chr(13).chr(10));
      }
    } else {
      if (defined('BUMPDURATION') && $current_cat != $image['category']) {
	$current_cat = $image['category'];
	$bump = $image['category'].'.png';
	if (file_exists(USER_IMAGEDIR.'/'.$bump)) {
	  fputs($fp, '      <img src="'.REMOTEDIR.'/cache/'.substr($bump, 0, -3).'jpg" alt="'.htmlspecialchars('Bump - '.$image['category'], ENT_QUOTES, 'UTF-8').'" dur="'.BUMPDURATION.'s" />'.chr(13).chr(10));
	}
      }
      if ($image['template'] != 'video.xsl') {
        fputs($fp, '      <img src="'.substr($image['src'], 0, -3).'jpg" alt="'.htmlspecialchars($image['title'], ENT_QUOTES, 'UTF-8').'" dur="'.$image['dur'].'s" region="content" fill="remove" erase="whenDone" />'.chr(13).chr(10));
      } else {
//        fputs($fp, '      <video src="'.htmlspecialchars($image['title'], ENT_QUOTES, 'UTF-8').'" alt="Intermezzo" dur="'.$image['dur'].'s" region="content" fill="remove" erase="whenDone" />'.chr(13).chr(10));
      }
    }
  }

  fputs($fp, '  </body>'.$rn.
             '</html>'.$rn);
  fflush($fp);
  fclose($fp);

  if (!file_exists($tmpdirectory)) {
    exec('mkdir '.$tmpdirectory);
  }

  exec('/usr/bin/mogrify -resize '.WEBRESOLUTIONW.'x'.WEBRESOLUTIONH.' -format jpg '.$tmpdirectory.'/*.png');
  exec('mv -u '.$tmpdirectory.'/*.smil '.BROADCASTDIR.'/.');
  exec('mv -u '.$tmpdirectory.'/*.html '.BROADCASTDIR.'/.');
  exec('mv -u '.$tmpdirectory.'/*.jpg '.BROADCASTCACHEDIR.'/.');
  exec('mv -u '.$tmpdirectory.'/*.png '.BROADCASTCACHEDIR.'/.');
  exec('ln -sf /home/tv/broadcast/'.date('Y-m-d', $now).'.smil /home/tv/broadcast.smil');

//  exec('/usr/bin/sudo -u broadcast ssh tv@'.REMOTEHOST.' ln -sf '.REMOTEDIR.'/'.date('Y-m-d', $now).'.smil broadcast.smil');
//  exec('/usr/bin/sudo -u broadcast fusermount -u '.BROADCASTDIR);
//  exec('/usr/bin/sudo -u broadcast ssh tv@'.REMOTEHOST.' ln -sf '.REMOTEDIR.'/'.date('Y-m-d', $now).'.smil broadcast.smil');
  
  exec('rm -rf '.$tmpdirectory);

  header('Location: index.php');
?>
