<?php
header("Access-Control-Allow-Origin: *");
// eu botei o robo maldito pra fazer isso tambem

$baseDir = __DIR__;

$version = basename($_GET['version'] ?? '');
$file    = basename($_GET['file'] ?? ''); // basename() also blocks path traversal

$allowed = ['game', 'launcher'];
$input = $_GET['where'] ?? 'game';
$where = '/' . (in_array($input, $allowed, true) ? $input : 'game') . '/';
 
// these two must never be downloadable as a "release artifact"
if ($file === '' || $file === 'config.ini' || $file === 'description.txt') {
    http_response_code(403);
    exit();
}

// essa parte no entanto eu que fiz
$inishit = $baseDir . $where . $version . '/config.ini';
$configFile = null;

if (is_file($inishit)) {
	$configFile = parse_ini_file($inishit, true);
}

if ($configFile !== null && isset($configFile['redirects'][$file])) {
	$trueFile = $configFile['redirects'][$file];
} else {
	$trueFile = $version . '/' . $file;
}
 
$path = $baseDir . $where . $trueFile;

if (!is_file($path)) {
    http_response_code(404);
    exit();
}
 
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
