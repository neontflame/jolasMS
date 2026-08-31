<?php
$db = new PDO("mysql:host=" . $config['DB_HOST'] . ";dbname=" . $config['DB_NAME'], $config['DB_USER'], $config['DB_PASS']);

function adicionar_server($port, $nome, $mods)
{
	global $db;
	
	$ip = $_SERVER["REMOTE_ADDR"];

	if (server_requestIPator($ip, $port) != null) {
		$rows = $db->prepare("UPDATE j_servers SET nome = ?, mods = ?, dataBump = NOW() WHERE ip = ? AND port = ?");
		$rows->bindParam(1, $nome);
		$rows->bindParam(2, $mods);
		$rows->bindParam(3, $ip);
		$rows->bindParam(4, $port);
		$rows->execute();
		return;
	}
	
	$rows = $db->prepare("INSERT INTO j_servers (ip, port, nome, mods) VALUES (?, ?, ?, ?)");
	$rows->bindParam(1, $ip);
	$rows->bindParam(2, $port);
	$rows->bindParam(3, $nome);
	$rows->bindParam(4, $mods);
	$rows->execute();
}

function deletar_server($port)
{
	global $db;
	$ip = $_SERVER["REMOTE_ADDR"];
	
	$rows = $db->prepare("DELETE FROM j_servers WHERE ip = ? AND port = ?");
	$rows->bindParam(1, $ip);
	$rows->bindParam(2, $port);
	$rows->execute();
}

function limpar_servers()
{
	global $db;
	
	$rows = $db->prepare("DELETE FROM j_servers WHERE dataBump < NOW() - INTERVAL 5 MINUTE");
	$rows->execute();
}

function server_requestIPator($ip, $port)
{
	global $db;

	$rows = $db->prepare("SELECT * FROM j_servers WHERE ip = ? AND port = ?");
	$rows->bindParam(1, $ip);
	$rows->bindParam(2, $port);
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

	$rows = $db->prepare("SELECT * FROM j_servers");
	$rows->execute();

	$servers = [];

	while ($row = $rows->fetch(PDO::FETCH_OBJ)) {
		if ($row->mods == "" || $temMods) {
			array_push($servers, $row);
		}
	}

	return $servers;
}