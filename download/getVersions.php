<?php
header("Access-Control-Allow-Origin: *");

$baseDir = __DIR__ . "/game";

// every version folder, newest first
$versions = array_values(array_filter(scandir($baseDir), function ($item) use ($baseDir) {
	return $item !== '.' && $item !== '..' && $item !== 'redir' && is_dir($baseDir . '/' . $item);
}));

usort($versions, fn($a, $b) => version_compare($b, $a));

foreach ($versions as $version) {
	echo "<option value=\"$version\">$version</option>";
}