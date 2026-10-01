<?php
header("Access-Control-Allow-Origin: *");

$baseDir = __DIR__ . "/launcher/" . $_GET['v'];

$blacklist = [
'.',
'..',
'config.ini',
'description.txt'
];

if (isset($_GET['plat'])) {
	if (strtolower($_GET['plat']) === 'linux') {
		array_push($blacklist, 'jolasLauncher.exe');
	} else {
		array_push($blacklist, 'jolasLauncher');
		array_push($blacklist, 'libsciter.so');
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
			if (in_array($redirectName, $blacklist)) continue;
			$assets[$redirectName] = ['name' => $redirectName];
		}
	}
}

$assets = array_values($assets);

foreach ($assets as $asset) {
	echo $asset['name'];
	echo "<br>";
}