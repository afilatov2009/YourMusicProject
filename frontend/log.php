<?php
session_start();
file_put_contents(
  __DIR__ . '/js.txt',
  file_get_contents('php://input') . PHP_EOL,
  FILE_APPEND
);
?>