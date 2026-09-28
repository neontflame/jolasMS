<?php
header("Access-Control-Allow-Origin: *");

$baseDir = __DIR__ . "/game/" . $_GET['v'] . "/description.txt";

echo is_file($baseDir) ? file_get_contents($baseDir) : ""
?>