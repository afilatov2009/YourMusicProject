<?php
session_start();
if(isset($_POST["logout"])){
setcookie('lang', $_SESSION['l'], time() + (86400 * 30), "/");
file_put_contents(
  __DIR__ . '/js.txt',
  "Logged out" . PHP_EOL,
  FILE_APPEND
);
session_destroy();
exit;
}
?>