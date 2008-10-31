<?php
  require_once('config.php'); 
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') === false)
    header('Content-type: application/xhtml+xml; charset=utf-8');
  else
    header('Content-type: text/html; charset=utf-8');
  
  if (isset($_FILES['userfile']['tmp_name'])) {
    move_uploaded_file($_FILES['userfile']['tmp_name'], USER_IMAGEDIR.'/'.str_replace('&', 'en', $_FILES['userfile']['name']));
  }
  clearstatcache();
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
  <head>
    <title><?php echo OWNER; ?></title>
    <style type="text/css">
      <![CDATA[
      html {
        background-color: #ffff99;
      }
      
      body {
        font-family: verdana, sans-serif;
	font-size: small;
      }

      input,textarea {
        border: 1px solid #000;
	width: auto;
	float: left;
      }

      fieldset,select {
        border: 1px solid #000;
      }

      ]]>
    </style>
  </head>
  <body>
    <form method="post" enctype="multipart/form-data">
      <fieldset>
        <legend>Fotos uploaden</legend>
	<input name="userfile" type="file" style="float: left;" /> <input type="submit" value="Stuur!" class="button" />
      </fieldset>
    </form>
    <a href="toevoegen.php">Toevoegen</a>
  </body>
</html>
