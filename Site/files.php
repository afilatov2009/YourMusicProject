<?php
session_start();
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/playlists.php';
ini_set('display_errors', 1);
if (empty($_SESSION['plList'])) $_SESSION['plList'] = [];
$userId = $_SESSION['id'];
$currNum = $_SESSION['crplaylist'];
$uploadDir = __DIR__ . "/files/user_$userId/";
$allowedTypes = ['audio/mpeg','audio/flac','audio/wav','audio/ogg'];
if(!file_exists($uploadDir)){
    mkdir($uploadDir);
}
$maxTotalSize = 500 * 1024 * 1024;
$currPlNum = $_SESSION['crplaylist'];
$metaFile = $uploadDir . "$currPlNum.json";
if (empty($_SESSION['plList']) == false) {
    $uploadDir = $uploadDir . "$currNum/";
    $Meta = json_decode(file_get_contents($metaFile), true);
}

if(isset($_FILES['file'])){
$len = count($_SESSION['plList']);
$_SESSION['crplaylist'] = $len;
$json = __DIR__ . "/files/user_$userId/$len.json";
$dir = __DIR__ . "/files/user_$userId/$len/";
touch($json);
mkdir($dir);
$uploadDir = $dir;
file_put_contents($json, json_encode([],  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
if ($_SESSION['nextPlaylistName'] == ""){
    $forname = 1;
    while (in_array("Playlist-$forname",$_SESSION['plList'])){
        $forname += 1;
    }
    $plName = "Playlist-$forname";
    $plInfo = ["Playlist-$forname", false, uniqid('pl_', true)];
    array_push($_SESSION['plList'],$plInfo);
}
else{
    $plName = $_SESSION['nextPlaylistName'];
    $plInfo = [$_SESSION['nextPlaylistName'], false, uniqid('pl_', true)];
    array_push($_SESSION['plList'],$plInfo);
    $_SESSION['nextPlaylistName'] = "";
}
if ($_SESSION['logged'] == true) addPlaylist($pdo, $len, $plInfo[0], $plInfo[1]);
$_SESSION['playlistUploaded'] = true;
$uploadedFiles = [];

$totalSize = array_sum($_FILES['file']['size']);
if ($totalSize > $maxTotalSize) {
    die("Суммарный размер файлов превышает 500 МБ");
}

for ($i = 0; $i < count($_FILES['file']['name']); $i++) {

    if ($_FILES['file']['error'][$i] !== 0) continue;
    if (!in_array($_FILES['file']['type'][$i], $allowedTypes)) continue;

    $filename = basename($_FILES['file']['name'][$i]);
    $filePath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $filePath)) {
        $title = pathinfo($filename, PATHINFO_FILENAME);
        $uploadedFiles[] = [
            'filename' => $filename,
            'title' => $title,
            'size' => $_FILES['file']['size'][$i],
            'weight' => 5,
            'rating' => 0.0,
            'genre' => '',
            'instruments' => [],
            'tempo' => '',
            'mood' => '',
            'inPlay' => 0,
            'isAnalyzed' => false,
            'isSent' => false
        ];
    }
}

file_put_contents($json, json_encode($uploadedFiles,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
if ($_SESSION['isAnalyzingNow'] == ''){
    $crpl = $_SESSION['crplaylist'];
    session_write_close();
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/analyzing.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'analyze' => $crpl,
        'session_id_for_analyse' => $userId,
        'pl_id' => $_SESSION['plList'][$crpl][2]
    ]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_exec($ch);
    curl_close($ch);
    file_put_contents(
    __DIR__ . '/js.txt',
    "post sent" . PHP_EOL,
    FILE_APPEND
    );
}
header("Location: /change.php");
exit;
}

if(isset($_FILES['addfile'])){
$uploadedFiles = [];
$Size = 0;
for ($i = 0; $i < count($Meta); $i++){
    $Size += $Meta[$i]['size'];
}

$totalSize = array_sum($_FILES['addfile']['size']) + $Size;
if ($totalSize > $maxTotalSize) {
    die("Суммарный размер файлов превышает 100 МБ");
}

for ($i = 0; $i < count($_FILES['addfile']['name']); $i++) {

    if ($_FILES['addfile']['error'][$i] !== 0) continue;
    if (!in_array($_FILES['addfile']['type'][$i], $allowedTypes)) continue;

    $filename = basename($_FILES['addfile']['name'][$i]);

    $isInPlaylist = true;
    for ($j = 0; $j < count($Meta); $j++) {
        if ((string)($Meta[$j]['filename']) === $filename){
            $isInPlaylist = false;
        }
    }
    if ($isInPlaylist === false) continue;
    $filePath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['addfile']['tmp_name'][$i], $filePath)) {
        $title = pathinfo($filename, PATHINFO_FILENAME);
        $uploadedFiles[] = [
            'filename' => $filename,
            'title' => $title,
            'size' => $_FILES['addfile']['size'][$i],
            'weight' => 5,
            'genre' => '',
            'instruments' => [],
            'tempo' => '',
            'mood' => '',
            'inPlay' => 0,
            'isSent' => false,
            'isAnalyzed' => false
        ];
    }
    $crpl = $_SESSION['crplaylist'];
    $_SESSION['plList'][$crpl][1] = 1;
    session_write_close();
    if ($_SESSION['isAnalyzingNow'] == ''){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/analyzing.php';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'analyze' => $crpl,
            'session_id_for_analyse' => $userId,
            'pl_id' => $_SESSION['plList'][$crpl][2]
        ]));
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_exec($ch);
        curl_close($ch);
    }
}
$Meta = array_merge($Meta, $uploadedFiles);
file_put_contents($metaFile, json_encode($Meta,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
header("Location: /change.php");
exit;
}


if(isset($_POST['name'])){
    for ($i = 0; $i < count($Meta); $i++) {
        if ($Meta[$i]['title'] === $_POST['name']) {
            $Meta[$i]['weight'] = intval($_POST['weight']);
        }
    }
    file_put_contents($metaFile, json_encode($Meta,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

if(isset($_POST['playCount'])){
    for ($i = 0; $i < count($Meta); $i++) {
        if ($Meta[$i]['title'] === $_POST['playCount']) {
            $Meta[$i]['inPlay'] += 1;
        }
    }
    file_put_contents($metaFile, json_encode($Meta,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

if(isset($_POST['zero'])){
    for ($i = 0; $i < count($Meta); $i++) {
        $Meta[$i]['inPlay'] = 0;
    }
    file_put_contents($metaFile, json_encode($Meta,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function delPl($metaFile,$Meta,$uploadDir,$currNum,$userId){
    $deletedId = $_SESSION['plList'][$currNum][2];
    array_push($_SESSION['delList'],$deletedId);
    $_SESSION['crplaylist'] = 0;
    for ($i = 0; $i < count($Meta); $i++){
        $name = $Meta[$i]['filename'];
        unlink($uploadDir . "$name");
    }
    unlink($metaFile);
    rmdir($uploadDir);
    if (count($_SESSION['plList']) == 1){
        $_SESSION['playlistUploaded'] = false;
        $_SESSION['plList'] = [];
        echo 'No playlist';
        exit;
    }
    else{
        unset($_SESSION['plList'][$currNum]);
        $_SESSION['plList'] = array_values($_SESSION['plList']);
        for ($i = $currNum;$i < count($_SESSION['plList']); $i++){
            $pr = $i + 1;
            rename(__DIR__ . "/files/user_$userId/$pr",__DIR__ . "/files/user_$userId/$i");
            rename(__DIR__ . "/files/user_$userId/$pr.json",__DIR__ . "/files/user_$userId/$i.json");
        }
        echo 'Ok';
        exit;
    }
}

if(isset($_POST['del'])){
    for ($i = 0; $i < count($Meta); $i++) {
        if ($Meta[$i]['title'] === $_POST['del']) {
            $_SESSION['size'] -= $Meta[$i]['size'];
            $name = $Meta[$i]['filename'];
            unlink($uploadDir . "$name");
            unset($Meta[$i]);
        }
    }
    $Meta = array_values($Meta);
    file_put_contents($metaFile, json_encode($Meta,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    if (count($Meta) == 0){
        delPl($metaFile,$Meta,$uploadDir,$currNum,$userId);
    }
    echo 'Ok';
    exit;
}

if(isset($_POST['rateTrack'])){
    $delta = floatval($_POST['delta']);
    $delta = max(-1.0, min(1.0, $delta));
    for ($i = 0; $i < count($Meta); $i++) {
        if ($Meta[$i]['title'] === $_POST['rateTrack']) {
            $current = isset($Meta[$i]['rating']) ? floatval($Meta[$i]['rating']) : 0.0;
            $Meta[$i]['rating'] = max(-5.0, min(5.0, $current + $delta));
        }
    }
    file_put_contents($metaFile, json_encode($Meta,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo 'Ok';
    exit;
}

if (isset($_GET['getProfile'])) {
    $profileFile = __DIR__ . "/files/user_$userId/profile.json";
    echo file_exists($profileFile)
        ? file_get_contents($profileFile)
        : json_encode(['attrWeights' => [], 'synonymWeights' => [], 'instsList' => []]);
    exit;
}

if (isset($_POST['saveProfile'])) {
    $data = json_decode($_POST['saveProfile'], true);
    if ($data !== null) {
        $profileFile = __DIR__ . "/files/user_$userId/profile.json";
        file_put_contents($profileFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    file_put_contents(
        __DIR__ . '/js.txt',
        "saving profile" . PHP_EOL,
        FILE_APPEND
    );
    exit;
}

if(isset($_POST['delPlaylist'])){
    if ($_SESSION['logged'] == true) deletePlaylist($pdo, $_SESSION['crplaylist']);
    delPl($metaFile,$Meta,$uploadDir,$currNum,$userId);
}
