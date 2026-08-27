<?php
$db = new PDO("mysql:host=" . $config['DB_HOST'] . ";dbname=" . $config['DB_NAME'], $config['DB_USER'], $config['DB_PASS']);

function adicionar_server($port, $nome, $mods)
{
	global $db;
	
	$ip = $_SERVER["REMOTE_ADDR"];
	$fullIp = $ip . ":" . $port;

	if (server_requestIPator($ip, $port) != null) {
		$rows = $db->prepare("UPDATE servers SET nome = ?, mods = ?, dataBump = NOW() WHERE ip = ?");
		$rows->bindParam(1, $nome);
		$rows->bindParam(2, $mods);
		$rows->bindValue(3, $fullIp);
		$rows->execute();
		return;
	}
	
	$rows = $db->prepare("INSERT INTO servers (ip, nome, mods) VALUES (?, ?, ?)");
	$rows->bindValue(1, $fullIp);
	$rows->bindParam(2, $nome);
	$rows->bindParam(3, $mods);
	$rows->execute();
}

function deletar_server($port)
{
	global $db;
	$ip = $_SERVER["REMOTE_ADDR"];
	
	$rows = $db->prepare("DELETE FROM servers WHERE ip = ?");
	$rows->bindValue(1, $ip . ":" . $port);
	$rows->execute();
}

function limpar_servers()
{
	global $db;
	
	$rows = $db->prepare("DELETE FROM servers WHERE dataBump < NOW() - INTERVAL 5 MINUTE");
	$rows->execute();
}

function server_requestIPator($ip, $port)
{
	global $db;

	$rows = $db->prepare("SELECT * FROM servers WHERE ip = ?");
	$rows->bindValue(1, $ip . ":" . $port);
	$rows->execute();
	$server = $rows->fetch(PDO::FETCH_OBJ);

	if ($server == false) {
		return null;
	}

	return $server;
}
function server_listator($temMods = true)
{
	global $db;

	$rows = $db->prepare("SELECT * FROM servers");
	$rows->execute();

	$servers = [];

	while ($row = $rows->fetch(PDO::FETCH_OBJ)) {
		if ($row->mods == "" || $temMods) {
			array_push($servers, $row);
		}
	}

	return $servers;
}