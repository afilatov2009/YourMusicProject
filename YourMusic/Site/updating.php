<?php
session_start();
$userId = $_SESSION['id'];
$num = $_SESSION['crplaylist'];
$json = __DIR__ . "/files/user_$userId/$num.json";
clearstatcache(true, $json);
header('Content-Type: application/json; charset=utf-8');
$data = array_values(json_decode(file_get_contents($json), true));
echo json_encode([
  'updated' => filemtime($json),
  'tracks' => $data
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>