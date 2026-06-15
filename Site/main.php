<?php
ini_set('session.use_strict_mode', 1);
if (isset($_POST['getProgress'])) {
    session_start(['read_and_close' => true]);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['progress' => $_SESSION['progress'] ?? 0, 'anNow' => $_SESSION['isAnalyzingNow'] ?? '', 'canUseGen' => $_SESSION['canUseGen'] ?? false]);
    exit;
}
session_start();
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/playlists.php';
if (empty($_SESSION['logged'])) $_SESSION['logged'] = false;
if (empty($_SESSION['id'])) $_SESSION['id'] = session_id();
$id = $_SESSION['id'];
if (empty($_SESSION['crplaylist'])) $_SESSION['crplaylist'] = 0;
$plNum = $_SESSION['crplaylist'];
$uploadDir = __DIR__ . "/files/user_$id/$plNum/";
if (empty($_SESSION['l'])) $_SESSION['l'] = $_COOKIE['lang'] ?? "en";
if (empty($_SESSION['username'])) $_SESSION['username'] = "none";
if (empty($_SESSION['isgen'])) $_SESSION['isgen'] = false;
if (empty($_SESSION['canUseGen'])) $_SESSION['canUseGen'] = false;
if (empty($_SESSION['crplaylist'])) $_SESSION['crplaylist'] = 0;
if (empty($_SESSION['plList'])) $_SESSION['plList'] = []; 
if (empty($_SESSION['playlistUploaded'])){
    if(Count($_SESSION['plList']) > 0){
        $_SESSION['playlistUploaded'] = true;
    }
    else $_SESSION['playlistUploaded'] = false;
}
if (empty($_SESSION['canPlay'])) $_SESSION['canPlay'] = false;
if (empty($_SESSION['nextPlaylistName'])) $_SESSION['nextPlaylistName'] = "";
if (empty($_SESSION['delList'])) $_SESSION['delList'] = [];
if (empty($_SESSION['isAnalyzingNow'])) $_SESSION['isAnalyzingNow'] = '';
if (empty($_SESSION['progress'])) $_SESSION['progress'] = 0;
if (Count($_SESSION['plList']) > 0 && $_SESSION['plList'][$_SESSION['crplaylist']][1] != 0) $_SESSION['canPlay'] = true;

if (isset($_POST['gen'])) {
    if ($_SESSION['isgen'] === true){
    $_SESSION['isgen'] = false;  
    } else {
        $_SESSION['isgen'] = true;
    }
}
if (isset($_POST['newName'])) {
    file_put_contents(
  __DIR__ . '/js.txt',
  file_get_contents('php://input') . PHP_EOL,
  FILE_APPEND
);
    $num = $_SESSION['crplaylist'];
    $name = $_POST['newName'];
    foreach($_SESSION['plList'] as $pl){
        if ($pl[0] == $name) exit;
    }
    $_SESSION['plList'][$num][0] = $name;
    if ($_SESSION['logged'] == true) changePlaylist($pdo, $num, $name, $_SESSION['plList'][$num][1]);
}
if (isset($_POST['lan'])) {
   $_SESSION['l'] = $_POST['lan'];
}
if (isset($_POST['canUseGen'])) {
    if ($_POST['canUseGen'] == 1) $_SESSION['canUseGen'] = true;
    else $_SESSION['canUseGen'] = false;
}
if (isset($_POST['newCur'])) {
   $_SESSION['crplaylist'] = $_POST['newCur'];
   $_SESSION['isgen'] = false;
   $_SESSION['canUseGen'] = false;
}
if (isset($_POST['nextName'])) {
    $_SESSION['nextPlaylistName'] = $_POST['nextName'];
}
$lang = $_SESSION['l'];
$langFile = __DIR__ . "/lang/$lang.php";
$translations = include $langFile;
function t($key) {
    global $translations;
    return $translations[$key] ?? $key;
}
?>