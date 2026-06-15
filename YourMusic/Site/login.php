<?php
session_start();
require_once 'base.php';
require_once 'playlists.php';
function recursiveDelete($str){
  if(is_file($str)){
    return @unlink($str);
  }
  elseif(is_dir($str)){
    $scan = glob(rtrim($str,'/').'/*');
    foreach($scan as $index=>$path){
      recursiveDelete($path);
    }
    return @rmdir($str);
  }
}

if(isset($_POST['username'])){
$id = $_SESSION["id"];
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
  http_response_code(401);
  exit(json_encode(["status" => "Wrong"]));
}

$_SESSION['id'] = $user['id'];
$newId = $_SESSION["id"];
$_SESSION['logged'] = true;
$_SESSION['username'] = $username;
if (empty($_SESSION['plList']) || $_SESSION['plList'] == []){
  $_SESSION['plList'] = getPlaylists($pdo);
}
else{
  putPlaylists($pdo, $_SESSION['plList']);
  if (file_exists(__DIR__ . "/files/user_$newId")){
    recursiveDelete(__DIR__ . "/files/user_$id");
  }
  else{
    rename(__DIR__ . "/files/user_$id", __DIR__ . "/files/user_$newId");
  }
  $_SESSION['plList'] = getPlaylists($pdo);
}
file_put_contents(
  __DIR__ . '/js.txt',
  "Logged" . PHP_EOL,
  FILE_APPEND
);
session_regenerate_id(true);
echo json_encode(["status" => "Yes"]);
}
?>