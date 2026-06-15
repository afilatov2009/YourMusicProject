<?php
require_once 'base.php';

if(isset($_POST['username'])){
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
if (strlen($password) < 6) {
  file_put_contents(
  __DIR__ . '/js.txt',
      "Short" . PHP_EOL,
  FILE_APPEND);
  exit(json_encode(["status" => "shortPass"]));
}
if(!preg_match("/^[A-Za-z0-9+#!?&*.]{6,20}$/",$password)){
  file_put_contents(
  __DIR__ . '/js.txt',
      "Bad" . PHP_EOL,
  FILE_APPEND);
  exit(json_encode(["status" => "badPass"]));
}
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
  file_put_contents(
  __DIR__ . '/js.txt',
      "Alr" . PHP_EOL,
  FILE_APPEND);
  exit(json_encode(["status" => "alrCreated"]));
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("
  INSERT INTO users (username, password)
  VALUES (?, ?)
");
$stmt->execute([$username, $hash]);

$_SESSION['id'] = $pdo->lastInsertId();
echo json_encode(["status" => "Yes"]);
}
?>