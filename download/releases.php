<?php
// eu botei o robo maldito pra fazer isso
header('Content-Type: application/json; charset=utf-8');
 
$baseDir = __DIR__;
$repo = $_GET['repo'] ?? '';
 
if (!in_array($repo, ['game', 'launcher'], true)) {
	http_response_code(400);
	echo json_encode(['erm' => 'lol?']);
	exit;
}
 
$repoDir = $baseDir . '/' . $repo;
 
if (!is_dir($repoDir)) {
	http_response_code(404);
	echo json_encode(['erm' => 'lol??']);
	exit;
}
 
// every version folder, newest first
$versions = array_values(array_filter(scandir($repoDir), function ($item) use ($repoDir) {
	return $item !== '.' && $item !== '..' && $item !== 'redir' && is_dir($repoDir . '/' . $item);
}));
usort($versions, fn($a, $b) => version_compare($b, $a));

function buildRelease(string $repoDir, string $version): array
{
	$dir = $repoDir . '/' . $version;
 
	$changelogPath = $dir . '/description.txt';
	$body = is_file($changelogPath) ? file_get_contents($changelogPath) : '';
 
	$assets = [];
	foreach (scandir($dir) as $file) {
		if ($file === '.' || $file === '..' || $file === 'config.ini' || $file === 'description.txt' || is_dir($dir . '/' . $file)) continue;
		$assets[] = ['name' => $file];
	}

    // arquivos que só existem via redirect no config.ini (ex: exe compartilhado
    // entre versões) não aparecem no scandir acima, mas o cliente PRECISA
    // saber que eles existem pra baixar — senão o launcher nunca puxa o
    // jolas.exe e quebra o Process.Start depois
    $iniPath = $dir . '/config.ini';
    if (is_file($iniPath)) {
        $config = parse_ini_file($iniPath, true);
        if ($config !== false && isset($config['redirects']) && is_array($config['redirects'])) {
            foreach (array_keys($config['redirects']) as $redirectName) {
                $assets[$redirectName] = ['name' => $redirectName];
            }
        }
    }

    $assets = array_values($assets);
	
	return [
		'name'	 => $version,
		'tag_name' => $version,
		'body'	 => $body,
		'assets'   => $assets,
	];
}
 
$tag = $_GET['tag'] ?? null;
 
// --- list endpoint (equivalent to apiLinkVerless) ---
if ($tag === null) {
	echo json_encode(array_map(fn($v) => buildRelease($repoDir, $v), $versions));
	exit;
}
 
// --- single-release endpoint (equivalent to apiLink with "latest" or "tags/X.X.X") ---
if ($tag === 'latest') {
	if (empty($versions)) {
		echo json_encode(['erm' => 'lol??']);
		exit;
	}
	$version = $versions[0];
} elseif (str_starts_with($tag, 'tags/')) {
	$version = substr($tag, 5);
} else {
	$version = $tag;
}
 
$version = basename($version); // no path traversal shenanigans
 
if (!is_dir($repoDir . '/' . $version)) {
	echo json_encode(['erm' => 'lol???']);
	exit;
}
 
echo json_encode(buildRelease($repoDir, $version));
