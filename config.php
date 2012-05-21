<?php
  define ('VERSION', '1.1.0');
  define ('OWNER', 'DemoVM');
  define ('ABSOLUTEDIR', '/usr/src/bca/Kabelkrantadmin');
  define ('TEMPLATEDIR', '/home/tv/xsl');
  define ('PREVIEWDIR', '/home/tv/preview');
  define ('USER_IMAGEDIR', '/home/tv/fotos');
  define ('BROADCASTDIR', '/home/tv/broadcast');
  define ('BROADCASTCACHEDIR', '/home/tv/broadcast/cache');
  define ('CACHEDIR', '/home/tv/broadcast/cache');
  define ('DATABASE', 'pgsql:host=localhost;dbname=kka');
  define ('DB_USER', 'kka');
  define ('DB_PASSWORD', 'kka');
  define ('THEMESEQ', FALSE);
  define ('BUMPDURATION', 10);
  define ('DURATION', 27);
  define ('REMOTEHOST', '127.0.0.1');
  define ('REMOTEDIR', 'broadcast');


# SD 4:3
#  define ('RESOLUTIONW', '720');
#  define ('RESOLUTIONH', '576');


# HD Ready
  define ('RESOLUTIONW', '1280');
  define ('RESOLUTIONH', '720');

# HD
#  define ('RESOLUTIONW', '1920');
#  define ('RESOLUTIONH', '1080');


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
  
  date_default_timezone_set('Europe/Amsterdam');
  setlocale(LC_ALL,'nl_NL');
  setlocale(LC_NUMERIC, 'en_US'); /* Make sure this is EN, because of . , in XSLT */
?>
