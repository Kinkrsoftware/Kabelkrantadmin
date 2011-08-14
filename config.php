<?php
  define ('OWNER', 'MidvlietTV');
  define ('ABSOLUTEDIR', '/var/www/kabelkrantadmin/htdocs');
  define ('TEMPLATEDIR', ABSOLUTEDIR.'/xsl');
  define ('PREVIEWDIR', ABSOLUTEDIR.'/preview');
  define ('USER_IMAGEDIR', ABSOLUTEDIR.'/fotos');
  define ('BROADCASTDIR', ABSOLUTEDIR.'/broadcast');
  define ('BROADCASTCACHEDIR', ABSOLUTEDIR.'/broadcast/cache');
  define ('CACHEDIR', ABSOLUTEDIR.'/broadcast/cache');
  define ('DATABASE', 'pgsql:host=localhost;dbname=kka');
  define ('DB_USER', 'kka');
  define ('DB_PASSWORD', '1234');
  define ('THEMESEQ', TRUE);
  define ('BUMPDURATION', 10);
  define ('DURATION', 23);
  define ('REMOTEHOST', '127.0.0.1');
  define ('REMOTEDIR', 'broadcast');
  define ('RESOLUTIONW', '1024');
  define ('RESOLUTIONH', '768');

/* 4:3  
  define ('PREVIEWRESOLUTIONW', '269');
  define ('PREVIEWRESOLUTIONH', '200'); 
  define ('WEBRESOLUTIONW', '480');
  define ('WEBRESOLUTIONH', '360');
*/

/* 16:9 */
  define ('PREVIEWRESOLUTIONW', '356');
  define ('PREVIEWRESOLUTIONH', '200');
  define ('WEBRESOLUTIONW', '640');
  define ('WEBRESOLUTIONH', '360');

  define ('EMERGENCY', false);
?>
