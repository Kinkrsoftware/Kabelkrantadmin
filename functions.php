<?php
  require_once('message.php');
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') === false)
    header('Content-type: application/xhtml+xml; charset=utf-8');
  else 
    header('Content-type: text/html; charset=utf-8');
	      
  require_once('config.php');

  function postruntimes2session($part = 'document') {
    foreach ($_POST as $post => $value) {
      if (substr($post, 0, 12) == 'content_run_') {
        preg_match('/^content_run_([S,E,X,D])(\d+)_.+$/', $post, $match); // THIS WAS NOT INTENTIONAL
        $_SESSION[$part]['run'][$match[2]]['enabled'] = false;
        if ($match[1]=='S') {
          $_SESSION[$part]['run'][$match[2]]['content_start'] = postdate2return('content_run_'.$match[1].$match[2], 'document');
        } else if ($match[1]=='E') {
          $_SESSION[$part]['run'][$match[2]]['content_end'] = postdate2return('content_run_'.$match[1].$match[2], 'document');
        } else if ($match[1]=='X') {
          $_SESSION[$part]['run'][$match[2]]['enabled'] = true;
        } else if ($match[1]=='D') { 
          $_SESSION[$part]['run'][$match[2]]['day'] = (is_numeric($_POST['content_run_'.$match[1].$match[2].'_day'])?$_POST['content_run_'.$match[1].$match[2].'_day']:0);
        }
      }
    }
  }

  function post2sessionactive($var, $part = 'document') {
    if (isset($_POST[$var])) $_SESSION[$part][$_SESSION[$part]['activeid']][$var]=htmlspecialchars($_POST[$var], ENT_QUOTES, 'UTF-8');
  }

  function postdate2return($var) {
     if (isset($_POST[$var.'_hh']) || isset($_POST[$var.'_dd'])) {
      $hour = (is_numeric($_POST[$var.'_hh'])?$_POST[$var.'_hh']:0);
      $minute = (is_numeric($_POST[$var.'_mm'])?$_POST[$var.'_mm']:0);
      $second = (is_numeric($_POST[$var.'_ss'])?$_POST[$var.'_ss']:0);
      $month = (is_numeric($_POST[$var.'_mo'])?$_POST[$var.'_mo']:0);
      $day = (is_numeric($_POST[$var.'_dd'])?$_POST[$var.'_dd']:0);
      $year = (is_numeric($_POST[$var.'_yyyy'])?$_POST[$var.'_yyyy']:0);
      return mktime($hour, $minute, $second, $month, $day, $year);
     } else {
      return null;
     }
  }

  function postdate2session($var, $part = 'document') {
    if (($return = postdate2return($var)) != null)
      $_SESSION[$part][$var]=postdate2return($var);
  }

  function posttime2sessionactive($var, $part = 'document') {
    if (isset($_POST[$var.'_hh']) || isset($_POST[$var.'_mm']) || isset($_POST[$var.'_ss'])) {
      $hour = (is_numeric($_POST[$var.'_hh'])?$_POST[$var.'_hh']:0);
      $minute = (is_numeric($_POST[$var.'_mm'])?$_POST[$var.'_mm']:0);
      $second = (is_numeric($_POST[$var.'_ss'])?$_POST[$var.'_ss']:0);

      $_SESSION[$part][$_SESSION[$part]['activeid']][$var]=$hour*60*60+$minute*60+$second;
    }
  }

  function passive($var, $id, $part = 'document') {
    if (isset($_SESSION[$part][$id][$var]))
	    return stripslashes($_SESSION[$part][$id][$var]);
  }

  function active($var, $part = 'document') {
    $in = passive($var, $_SESSION[$part]['activeid'], $part); 
    return $in;
  }

  function paste2sessionpassive($var, $id, $part = 'document', $value) {
    $_SESSION[$part][$id][$var] = $value;
  }

  function paste2sessionactive($var, $part = 'document', $value) {
    paste2sessionpassive($var, $_SESSION[$part]['activeid'], $part, $value); 
  }
 
  function dirtoselect($name, $dir, $active = '', $empty = false, $maxdate = 0, $extension = '') {
    $templates = array();
    if ($empty===true) $templates[] = '';
    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
          if ($file[0] != '.' && !is_dir($dir.'/'.$file)) {
	    if ($maxdate == 0 || (filectime($dir.'/'.$file) > $maxdate)) {
              if ($extension == '' || (substr( $file, strlen( $file ) - strlen( $extension ) ) === $extension)) {
                $templates[]=$file;
              }
	    }
          }
        }
      }
    }
    sort($templates);
    $result = '';
    if ($active != '' && !in_array($active, $templates)) {
      $result = '<input type="text" name="'.$name.'" value="'.$active.'" />';
    } else {
      $result = '<select name="'.$name.'">';
      foreach ($templates as $key => $value)
        $result.='<option value="'.$value.'"'.($value==$active?' selected="1"':'').'>'.$value.'</option>';
      $result .= '</select>';
    }
    return $result;
  }

  function dbtoselect($name, $qresult, $active = '', $empty = false) { 
    $result = '<select name="'.$name.'">';

    if ($empty===true) $result.='<option value=""></option>';

    foreach ($qresult as $entry) {
      $id = array_shift($entry);
      $result.='<option value="'.$id.'"'.($id==$active?' selected="1"':'').'>';
      foreach ($entry as $value) {
        $result.=$value.' ';
      }
      $result.='</option>';
    }

    $result .= '</select>';
    return $result;
  }

  function datetimetoinput($var, $active) {
    $time = localtime($active);
  
    return '<input type="text" name="'.$var.'_hh" value="'.$time[2].'" size="2" maxlength="2" />:'.
           '<input type="text" name="'.$var.'_mm" value="'.(strlen($time[1])<2?'0':(strlen($time[1])<1?'00':'')).$time[1].'" size="2" maxlength="2" />.'.
	   '<input type="text" name="'.$var.'_ss" value="'.(strlen($time[0])<2?'0':(strlen($time[0])<1?'00':'')).$time[0].'" size="2" maxlength="2" /> '.
	   '<input type="text" name="'.$var.'_dd" value="'.$time[3].'" size="2" maxlength="2" />-'.
	   '<input type="text" name="'.$var.'_mo" value="'.($time[4]+1).'" size="2" maxlength="2" />-'.
	   '<input type="text" name="'.$var.'_yyyy" value="'.($time[5]+1900).'" size="4" maxlength="4" />';
  }

  function runtimes($active) {
    $out = '<table><tr><th>Van:</th><th>Tot:</th><th>Actief</th></tr>';
    for ($i = 0; $i < count($active['run']); $i++) {
      $out .= '<tr><td class="startdate">'.datetimetoinput('content_run_S'.$i, $active['run'][$i]['content_start']).'</td>'.
              '<td class="enddate">'.datetimetoinput('content_run_E'.$i, $active['run'][$i]['content_end']).'</td>'.
              '<td><select name="content_run_D'.$i.'_day"><option value="0"'.($active['run'][$i]['day']==0?' selected="selected"':'').'>Iedere dag</option><option value="1"'.($active['run'][$i]['day']==1?' selected="selected"':'').'>Maandag</option><option value="2"'.($active['run'][$i]['day']==2?' selected="selected"':'').'>Dinsdag</option><option value="3"'.($active['run'][$i]['day']==3?' selected="selected"':'').'>Woensdag</option><option value="4"'.($active['run'][$i]['day']==4?' selected="selected"':'').'>Donderdag</option><option value="5"'.($active['run'][$i]['day']==5?' selected="selected"':'').'>Vrijdag</option><option value="6"'.($active['run'][$i]['day']==6?' selected="selected"':'').'>Zaterdag</option><option value="7"'.($active['run'][$i]['day']==7?' selected="selected"':'').'>Zondag</option></select></td>'.
             '<td><input type="checkbox" name="content_run_X'.$i.'_enabled"'.($active['run'][$i]['enabled']==1?' checked="checked"':'').'/></td></tr>';
    }
    $out .= '</table>';
    return $out;
  }
			     

  function timetoinput($var, $active) {
    $hour = ($active - ($active % 3600)) / 3600;
    $min =  ($active % 3600);
    $min = ($min - ($min % 60)) / 60;
    $sec = ($active % 3600 % 60);
    
    return '<input type="text" name="'.$var.'_hh" value="'.(strlen($hour)<2?'0':(strlen($hour)<1?'00':'')).$hour.'" size="2" maxlength="2" />:'.
           '<input type="text" name="'.$var.'_mm" value="'.(strlen($min)<2?'0':(strlen($min)<1?'00':'')).$min.'" size="2" maxlength="2" />.'.
	   '<input type="text" name="'.$var.'_ss" value="'.(strlen($sec)<2?'0':(strlen($sec)<1?'00':'')).$sec.'" size="2" maxlength="2" /> ';
  } 

  function newdocument() {
    unset($_SESSION['document']);
    $tmp_time = time();
    $_SESSION['document']=array('text_category'=>0, 'activeid'=>0, 'content_start'=>$tmp_time, 'content_end'=>$tmp_time+172800, 'run'=>array(0=>array('content_start'=>$tmp_time, 'content_end'=>$tmp_time+172800, 'enabled'=>true, 'day'=>0)));
    newpreview();
  }

  function newrun() {
    $tmp_time = time();
    $_SESSION['document']['run'][]=array('content_start'=>$tmp_time, 'content_end'=>$tmp_time+172800, 'enabled'=>true);
  }
	      
  function newpreview() {
    unset($_SESSION['document'][0]);
    $_SESSION['document'][0]['text_duration']=DURATION;
    $_SESSION['document'][0]['text_title']='';
    $_SESSION['document'][0]['text_content']='';
//    $_SESSION['document'][0]['text_template']=0;
    $_SESSION['document'][0]['text_category']=0;
    $_SESSION['document']['activeid']=0;
  }

  function checkandgenerate($dbh, $id=0, $safebox=0, $width=PREVIEWRESOLUTIONW, $height=PREVIEWRESOLUTIONH, $format='png') {
    $title = passive('text_title', $id);
    $para = passive('text_content', $id);
    $photo = passive('text_photo', $id);
    $template = passive('text_template', $id);
    $category = passive('text_category', $id);
    return checkandbroadcast($dbh, $safebox, $width, $height, $format='png', $title, $para, $photo, $template, $category, $dir=PREVIEWDIR, $filename=md5($title.$para.$photo.$template.$category));
  }


  function checkandbroadcast($dbh, $safebox=0, $width=RESOLUTIONW, $height=RESOLUTIONH, $format='png', $title, $para, $photo, $template, $category, $dir='', $filename='', $batch=false) {
    $filename = ($filename!=''?$filename:md5($title.$para.$photo.$template.$category));
    $dir = ($dir!=''?$dir:PREVIEWDIR);
    $pngfile = $dir.'/'.$filename.'.png';
    
    if (!file_exists($pngfile)) {
      $category = ($category!=''?$category:'0');
    
      $stmt = $dbh->prepare('SELECT content_category.title AS "categorytitle", content_category_image.title AS "imagetitle", content_category_image.photo, content_category_image.width, content_category_image.height, content_category_image.x, content_category_image.y FROM content_category, content_category_image WHERE content_category.id=content_category_image.categoryid AND content_category_image.id=:content_category_image_id');
      $stmt->bindParam(':content_category_image_id', $category, PDO::PARAM_INT);
      $stmt->execute();
      $qresult = $stmt->fetchAll();

      if (count($qresult)>=1) {
        if ($qresult[0]['imagetitle']=='') {
	  $newfilename = checkandpreview($safebox, $width, $height, $format, $title, $para, $photo, TEMPLATEDIR.'/'.$template, $dir, $filename,
                                         $qresult[0]['categorytitle'],
                                         '', '',
                                         0, 0, 0, 0, $batch);
        } else {
	  $newfilename = checkandpreview($safebox, $width, $height, $format, $title, $para, $photo, TEMPLATEDIR.'/'.$template, $dir, $filename,
	                                 $qresult[0]['categorytitle'],
			                 $qresult[0]['photo'],
    			                 $qresult[0]['width'],
			                 $qresult[0]['height'],
			                 $qresult[0]['x'],
			                 $qresult[0]['y'],
                                         $batch);
        }
      }
    }
    
    return $filename;
  }

  function checkandpreview($safebox=0, $width=RESOLUTIONW, $height=RESOLUTIONH, $format='png', $title, $para, $photo, $template, $dir='', $filename='', $cat_title='', $cat_photo='', $cat_width=0, $cat_height=0, $cat_x=0, $cat_y=0, $batch=false) {
    $filename = ($filename!=''?$filename:md5($title.$para.$photo.$template.$category));
    $dir = ($dir!=''?$dir:PREVIEWDIR);
    $file = $dir.'/'.$filename.'.png';

    if (!file_exists($file)) {
          $category_xml = '<category><title>'.$cat_title.'</title><img><src>'.($cat_photo!=''?USER_IMAGEDIR.'/'.$cat_photo:'').'</src>'.
                          '<width>'.$cat_width.'</width>'.
                          '<height>'.$cat_height.'</height>'.
                          '<x>'.$cat_x.'</x>'.
                          '<y>'.$cat_y.'</y></img></category>';
       
   
      $photo_xml = '<photo>'.($photo!=''?USER_IMAGEDIR.'/'.$photo:'').'</photo>';

      $template = ($template!=''&&file_exists($template)?$template:TEMPLATEDIR.'/default.xsl');

      $xml = new domDocument();
      $xml->loadXML('<article>'.
                    $category_xml.
                    '<safebox>'.$safebox.'</safebox>'.
                    '<title>'.$title.'</title>'.
                    '<para>'.$para.'</para>'.
                    $photo_xml.
                    '</article>');

      $xsl = new DomDocument;
      $xsl->load($template);

      $proc = new xsltprocessor();
      $proc->importStyleSheet($xsl);

      $svgfile = $dir.'/'.$filename.'.svg';
      $fp = fopen($svgfile, 'w');
      fputs($fp, $proc->transformToXML($xml));
      fflush($fp);
      fclose($fp);

      $xml = null;

      if ($batch === true) return $filename;

      $debug = shell_exec('/usr/bin/inkscape -z --file='.$svgfile.' --export-width='.$width.' --export-height='.$height.' --export-png='.$file.' 2>&1 1>/dev/null');

	//echo $debug;
	//exit;

      if ($format != 'png') {
	      shell_exec('/usr/bin/convert -format '.$format.' '.$file);
      }


      unlink($svgfile);
     }
     return $filename;
  }

  function batchrender($dir, $width, $height, $playlist) {
    $cmd = '';
    foreach ($playlist as $filename) {
      $svgfile = $dir.'/'.$filename.'.svg';
      $file = $dir.'/'.$filename.'.png';

      $cmd .= $svgfile.' --export-width='.$width.' --export-height='.$height.' --export-png='.$file."\n";
    }
    $cmd .= 'quit';
    shell_exec('echo "'.$cmd.'" | /usr/bin/inkscape --shell 2>&1 1>/dev/null');

    foreach ($playlist as $filename) {
      $svgfile = $dir.'/'.$filename.'.svg';
      unlink($svgfile);
    }
  }
?>
