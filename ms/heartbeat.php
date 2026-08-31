<?php
include 'autoload.php';

$mods = "";
if (isset($_GET["mods"])) {
	$mods = $_GET["mods"];
}

adicionar_server($_GET["port"], $_GET["nome"], $mods);