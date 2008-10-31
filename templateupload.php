<?php
  require_once('config.php'); 
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') === false)
    header('Content-type: application/xhtml+xml; charset=utf-8');
  else
    header('Content-type: text/html; charset=utf-8');
  
  $error = '';
  if (isset($_FILES['userfile']['tmp_name'])) {
  	if (substr($_FILES['userfile']['name'], -4) == '.svg') {
		$svg = file_get_contents($_FILES['userfile']['tmp_name']);
		$svg = str_replace(array('TITLE',
		       	                 'CATEGORY_TITLE',
		  	                 'CATEGORY_IMAGE',
		  			 'PHOTO',
					 'PARA'),
				   array('<xsl:value-of select="/article/title" />',
				   	 '{<xsl:value-of select="/article/category/img/title" />',
				         '{article/category/img/src}',
					 '{/article/photo}',
					 '<xsl:apply-templates select="/article/para" />'),
				   $svg);

		$tmp = explode("\n", $svg);
		$tmp[0] = 
'<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:exsl="http://exslt.org/common" extension-element-prefixes="exsl">
<xsl:output omit-xml-declaration="yes" media-type="image/svg+xml" doctype-public="-//W3C//DTD SVG 20010904//EN" doctype-system="http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd" standalone="no" indent="no" method="xml"/>

<xsl:template name="break" match="/article/para">
 <xsl:param name="text" select="."/>
 <xsl:choose>
   <xsl:when test="contains($text, \'&#xA;\')">
     <flowPara xmlns="http://www.w3.org/2000/svg"><xsl:value-of select="substring-before($text, \'&#xA;\')"/></flowPara>
     <xsl:call-template name="break">
       <xsl:with-param name="text" select="substring-after($text,\'&#xA;\')"/>
     </xsl:call-template>
   </xsl:when>
   <xsl:otherwise>
     <flowPara xmlns="http://www.w3.org/2000/svg"><xsl:value-of select="$text"/></flowPara>
   </xsl:otherwise>
 </xsl:choose>
</xsl:template>

<xsl:template match="/">';
		$tmp[] =
'</xsl:template>
</xsl:stylesheet>';

		$svg = implode("\n", $tmp);

  	        $xsl = new DomDocument;
		if ($xsl->loadXML($svg)) {
			$xsl->save(TEMPLATEDIR.'/'.str_replace(array('&', '.svg'), array('en', '.xsl'), $_FILES['userfile']['name']));
		} else {
			$error = ERROR_IN_TEMPLATE;
		}
	 } else if (substr($_FILES['userfile']['name'], -4) == '.xsl') {
	 	$xsl = new DomDocument;
		if ($xsl->load($_FILES['userfile']['tmp_name'])) {
			$xsl->save(TEMPLATEDIR.'/'.str_replace('&', '.xsl', $_FILES['userfile']['name']));
		} else {
			$error = ERROR_IN_TEMPLATE;
		}
	 }
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
    <?php echo $error; ?>
    <form method="post" enctype="multipart/form-data">
      <fieldset>
        <legend>Template uploaden</legend>
	<input name="userfile" type="file" style="float: left;" /> <input type="submit" value="Stuur!" class="button" />
      </fieldset>
    </form>
    <a href="toevoegen.php">Toevoegen</a>
  </body>
</html>
