<?php
require_once dirname(__DIR__) . '/global/include/flyer_facebook_publish.php';

$sites = array('elite', 'lidep', 'huskies', 'nuestrodeporte', 'vollidep', 'voleibalmetepec');
$repo = dirname(__DIR__);

foreach ($sites as $site) {
	$path = $repo . DIRECTORY_SEPARATOR . $site;
	$local = az_flyer_facebook_read_local_env($path);
	$cfg = az_flyer_facebook_read_config($path);
	echo $site . ': local=' . (is_array($local) ? 'yes' : 'no');
	echo ', configured=' . ($cfg !== null ? 'yes' : 'no') . PHP_EOL;
}
