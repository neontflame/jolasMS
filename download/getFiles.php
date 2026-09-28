<?php
header("Access-Control-Allow-Origin: *");

$baseDir = __DIR__ . "/game/" . $_GET['v'];

$blacklist = [
'.',
'..',
'config.ini',
'description.txt'
];

if (isset($_GET['plat'])) {
	if ($_GET['plat'] == 'Linux') {
		array_push($blacklist, 'jolas.exe');
	} else {
		array_push($blacklist, 'jolas.x86_32');
	}
}

$assets = [];
foreach (scandir($baseDir) as $file) {
	if (in_array($file, $blacklist) || is_dir($baseDir . '/' . $file)) continue;
	$assets[] = ['name' => $file];
}

$iniPath = $baseDir . '/config.ini';
if (is_file($iniPath)) {
	$config = parse_ini_file($iniPath, true);
	if ($config !== false && isset($config['redirects']) && is_array($config['redirects'])) {
		foreach (array_keys($config['redirects']) as $redirectName) {
			$assets[$redirectName] = ['name' => $redirectName];
		}
	}
}

$assets = array_values($assets);

foreach ($assets as $asset) {
	echo $asset['name'];
	echo "<br>";
}