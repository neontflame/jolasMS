<?php
header('Content-Type: application/json; charset=utf-8');

include 'autoload.php';

limpar_servers();

$servs = server_listator(isset($_GET["mods"]) && $_GET["mods"] == "true");
$serversQueForam = 1;

echo '{"servers": [';
foreach ($servs as $servinho) {
	echo '{';
	echo '"ip": "' . $servinho->ip . '",';
	echo '"port": "' . $servinho->port . '",';
	echo '"nome": "' . $servinho->nome . '",';
	echo '"mods": [' . arrayizar_mods($servinho->mods) . ']';
	if (count($servs) > $serversQueForam) {
		echo '},';
	} else {
		echo '}';
	}
		
	$serversQueForam += 1;
}
echo ']}';

function arrayizar_mods($mods) {
	$treco = explode("\n", $mods);
	$stringicio = '';
	
	$quantosForam = 1;
	foreach($treco as $trequinho) {
		$stringicio .= '"' . $trequinho . '"';
		if (count($treco) > $quantosForam) {
			$stringicio .= ', ';
		}
		
		$quantosForam += 1;
	}
	
	return $stringicio;
}