<?php
ini_set('session.use_strict_mode', 1);
session_start();
if (empty($_SESSION['logged'])) $_SESSION['logged'] = false;
if (empty($_SESSION['id'])) $_SESSION['id'] = session_id();
$id = $_SESSION['id'];
$uploadDir = __DIR__ . "/files/user_$id/";
if (empty($_SESSION['playlistUploaded'])){
    if(!file_exists($uploadDir)){
        $_SESSION['playlistUploaded'] = false;
    }
    else $_SESSION['playlistUploaded'] = true;
}
if (empty($_SESSION['l'])) $_SESSION['l'] = $_COOKIE['lang'] ?? "en";
if (empty($_SESSION['isgen'])) $_SESSION['isgen'] = false;

if (isset($_POST['gen'])) {
    if ($_SESSION['isgen'] === true){
    $_SESSION['isgen'] = false;  
    } else {
        $_SESSION['isgen'] = true;
    }
}
if (isset($_POST['lan'])) {
   $_SESSION['l'] = $_POST['lan'];
}

$lang = $_SESSION['l'];
$langFile = __DIR__ . "/lang/$lang.php";
$translations = include $langFile;
function t($key) {
    global $translations;
    return $translations[$key] ?? $key;
}
?>