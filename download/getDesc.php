<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: text/html; charset=utf-8');

$baseDir = __DIR__ . "/game/" . $_GET['v'] . "/description.txt";

echo is_file($baseDir) ? file_get_contents($baseDir) : ""
?>