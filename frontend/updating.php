<?php
session_start();
$userId = $_SESSION['id'];
$json = __DIR__ . "/files/user_$userId/files.json";
$data = json_decode(file_get_contents($json), true);
echo json_encode([
  'updated' => filemtime($json),
  'tracks' => $data
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>