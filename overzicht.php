<?php
	require_once('functions.php');

	$now = time() - (7 * 24 * 60 * 60);
	$totable = array();

	$db = sqlite_open(DATABASE, 0666, $sqlerror);
	for ($i=0; $i<=60; $i++) {
		$start = $now;
		$end = $now;

		$query = sqlite_query($db, 'SELECT sum(content_text.duration) FROM content_run, content, content_text WHERE content_run.start <= '.$start.' AND content_run.end >= '.$end.' AND content.id=content_run.contentid AND content.id=content_text.contentid;');
		$result = sqlite_fetch_all($query, SQLITE_ASSOC);
		$totable[$i] = $result[0]['sum(content_text.duration)'];
		$now += 43200;
	}
	
	sqlite_close($db);

	$im = imagecreate(600,200);
	$background_color = imagecolorallocate($im, 255, 255, 255);
	$line_color = imagecolorallocate($im, 0, 0, 0);
	$red = imagecolorallocate($im, 255, 0, 0);
	$green = imagecolorallocate($im, 0, 255, 0);
	$blue = imagecolorallocate($im, 0, 0, 255);
	imagesetthickness($im, 1);

	imageline($im, 0, (200-120), 600, (200-120), $green);
	imagestring($im, 4, 550, (200-120), '20 min', $green);
	imageline($im, 0, (200-60), 600, (200-60), $red);
	imagestring($im, 4, 550, (200-60), '10 min', $red);
	imageline($im, (2*70), 0, (2*70), 200, $blue);
	imagestring($im, 4, (2*70)+4, 183, TODAY, $blue);

	$now = time();

	for ($i=2; $i<4; $i++) {
		imageline($im, (70*2*$i), 0, (70*2*$i), 200, $blue);
		imagestring($im, 4, (70*2*$i)+4, 183, date("n-j", $now+(604800 *($i-1))), $blue);
	}

	for($i=0; $i<60; $i++) {
		$top1 = 200 - (int)($totable[$i]/10);
		$top2 = 200 - (int)($totable[$i+1]/10);
		if ($top1 < 0) $top1 = 0;
		if ($top2 < 0) $top2 = 0; 
		imageline($im, ($i*10), $top1, (($i+1)*10), $top2, $line_color);
	}

	header('Content-type: image/png');
	imagepng($im);
	imagedestroy($im);

?>
