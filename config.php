<?php
  define ('OWNER', 'PlusRTV');
  define ('ABSOLUTEDIR', '/opt/cherokee/var/www');
  define ('TEMPLATEDIR', ABSOLUTEDIR.'/xsl');
  define ('PREVIEWDIR', ABSOLUTEDIR.'/preview');
  define ('USER_IMAGEDIR', ABSOLUTEDIR.'/fotos');
  define ('BROADCASTDIR', ABSOLUTEDIR.'/broadcast');
  define ('BROADCASTCACHEDIR', ABSOLUTEDIR.'/broadcast/cache');
  define ('CACHEDIR', ABSOLUTEDIR.'/broadcast/cache');
  define ('DATABASE', ABSOLUTEDIR.'/database/'.strtolower(OWNER).'-olifant.db');
  define ('THEMESEQ', TRUE);
  define ('BUMPDURATION', 10);
  define ('DURATION', 23);
  define ('REMOTEHOST', '127.0.0.1');
  define ('REMOTEDIR', 'broadcast');
  define ('RESOLUTIONW', '1024');
  define ('RESOLUTIONH', '768');
  define ('EMERGENCY', false);
?>
