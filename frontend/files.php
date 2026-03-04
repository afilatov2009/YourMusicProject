<?php
session_start();
$_SESSION['size'] = 0;
ini_set('display_errors', 1);
$userId = $_SESSION['id'];
$uploadDir = __DIR__ . "/files/user_$userId/";
$allowedTypes = ['audio/mpeg','audio/flac','audio/wav','audio/ogg'];
$maxTotalSize = 100 * 1024 * 1024;
if(!file_exists($uploadDir)){
    mkdir($uploadDir);
    touch($uploadDir . 'files.json');
    file_put_contents($uploadDir . 'files.json', json_encode([],  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
$metaFile = $uploadDir . 'files.json';
$Meta = json_decode(file_get_contents($metaFile), true);

if(isset($_FILES['file'])){
$_SESSION['playlistUploaded'] = true;
$uploadedFiles = [];

if($_SESSION['size'] === 0){
    $totalSize = array_sum($_FILES['file']['size']);
}
else {
    $totalSize = array_sum($_FILES['file']['size']) + $_SESSION["size"];
}

if ($totalSize > $maxTotalSize) {
    die("Суммарный размер файлов превышает 100 МБ");
}
$_SESSION['size'] = $totalSize;

for ($i = 0; $i < count($_FILES['file']['name']); $i++) {

    if ($_FILES['file']['error'][$i] !== 0) continue;
    if (!in_array($_FILES['file']['type'][$i], $allowedTypes)) continue;

    $filename = basename($_FILES['file']['name'][$i]);

    $isInPlaylist = true;
    for ($j = 0; $j < count($Meta); $j++) {
        if ((string)($Meta[$j]['filename']) === $filename){
            $isInPlaylist = false;        
        }
    }
    if ($isInPlaylist === false) continue;
    $filePath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $filePath)) {
        $title = pathinfo($filename, PATHINFO_FILENAME);
        $uploadedFiles[] = [
            'filename' => $filename,
            'title' => $title,
            'size' => $_FILES['file']['size'][$i],
            'weight' => 5,
            'genre' => '',
            'instruments' => [],
            'tempo' => '',
            'mood' => '',
            'beat' => '',
            'inPlay' => 0
        ];
    }
}

$Meta = array_merge($Meta, $uploadedFiles);
file_put_contents($metaFile, json_encode($Meta,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
$_SESSION['playlistUploaded'] = true;
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

if(isset($_POST['del'])){
    for ($i = 0; $i < count($Meta); $i++) {
        if ($Meta[$i]['title'] === $_POST['del']) {
            $_SESSION['size'] -= $Meta[$i]['size'];
            $name = $Meta[$i]['filename'];
            unlink(__DIR__ . "/files/user_$userId/$name");
            unset($Meta[$i]);
        }
    }   
    $Meta = array_values($Meta);
    file_put_contents($metaFile, json_encode($Meta,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
