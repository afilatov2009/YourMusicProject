<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/AnalyingErrors.log');
ignore_user_abort(true);
set_time_limit(0); 
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/playlists.php';
function analyzingPlaylist($uploadDir,$pdo,$plId){
    file_put_contents(
  __DIR__ . '/js.txt',
  "analyzing" . PHP_EOL,
  FILE_APPEND
   );
    if (session_status() === PHP_SESSION_NONE) session_start();
    $allAnalyzed = false;
    while ($allAnalyzed == false){
        $canSend = false;
        while ($canSend == false) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $plNum = false;
            foreach($_SESSION['plList'] as $index => $pl){
                if ($pl[2] == $plId) {
                    $plNum = $index;
                    break;
                }
            }
            if ($plNum === false || in_array($plId, $_SESSION['delList'])) {
                break 2;
            }
            $json = json_decode(file_get_contents($uploadDir . "$plNum.json"), true);
            $notAll = false;
            $count = 0;
            $an = -1;
            for ($i = 0;$i < count($json);$i++){
                if ($json[$i]['isAnalyzed'] == false) {
                 $notAll = true;
                    if($json[$i]['isSent'] == false) $an = $i;
                }
                else{
                    $count += 1;
                }
            }
            $_SESSION['progress'] = round($count/count($json),2) * 100;
            if ($notAll == false){
                $allAnalyzed = true;
                break;
            }

            if ($an === -1) {
                session_write_close();
                sleep(2); 
                session_start();
                continue;
            }

            session_write_close();
            $curl = curl_init();
            curl_setopt_array($curl, [
                //CURLOPT_URL => "https://3sfz6lx6-novyy-0558.dslab.tech/proxy/6767",
                CURLOPT_URL => "http://localhost:6767/health",
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_TIMEOUT => 5,
            ]);
            $response = json_decode(curl_exec($curl),true);
            if ($response && isset($response['load'])) {
                $free = explode('/',$response['load']);
                file_put_contents(
                __DIR__ . '/js.txt',
                $free[0] . PHP_EOL,
                FILE_APPEND
                );
                if (count($free) == 2 && (int)$free[0] < (int)$free[1]) $canSend = true;
                else sleep(3); 
            }
            else{
                sleep(5);
            }
        }
        session_start();

        if ($an !== -1 && !in_array($plId, $_SESSION['delList'])){
            file_put_contents(
                __DIR__ . '/js.txt',
                "sent" . PHP_EOL,
                FILE_APPEND
                );
            $curl = curl_init();
            $name = $json[$an]['filename'];
            $file = curl_file_create($uploadDir . "$plNum/$name", 'audio/mpeg',"$name");
            $data = ['file' => $file, 'sessionId' => session_id(),'id' => $_SESSION['id'],'track' => $name, 'playlist' => $plId, 'url' => 'http://localhost:8080/analyzer2.php'];
            curl_setopt_array($curl, [
                //CURLOPT_URL => "https://3sfz6lx6-novyy-0558.dslab.tech/proxy/6767",
                CURLOPT_URL => "http://localhost:6767",
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_TIMEOUT => 100,
            ]);
            $json = json_decode(file_get_contents($uploadDir . "$plNum.json"), true);
            $json[$an]['isSent'] = true;
            file_put_contents($uploadDir . "$plNum.json", json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            session_write_close();
            curl_exec($curl);
            curl_close($curl);
            session_start();
            file_put_contents(
                __DIR__ . '/js.txt',
                "got response" . PHP_EOL,
                FILE_APPEND
            );
        }
        else break;
    }
    if (session_status() === PHP_SESSION_NONE) session_start();
    $plNum = false;
    foreach($_SESSION['plList'] as $index => $pl){
        if ($pl[2] == $plId) { $plNum = $index; break; }
    }
    if ($plNum !== false && !in_array($plId, $_SESSION['delList'])) {
        $_SESSION['plList'][$plNum][1] = 2;
        if ($_SESSION['logged'] == true) changePlaylist($pdo, $plNum, $_SESSION['plList'][$plNum][0], true);
    }
}
if (isset($_POST['analyze'])){
    file_put_contents(
        __DIR__ . '/js.txt',
        "post" . PHP_EOL,
        FILE_APPEND
    );
    session_id($_POST['session_id_for_analyse']);
    session_start();
    $userId = $_SESSION['id'];
    $plId = $_POST['pl_id'];
    $uploadDir = __DIR__ . "/files/user_$userId/";
    $num = $_POST['analyze'];
    $_SESSION['isAnalyzingNow'] = $_SESSION['plList'][$num][0];
    analyzingPlaylist($uploadDir,$pdo,$plId);
    $_SESSION['isAnalyzingNow'] = '';
    for ($i = 0; $i < count($_SESSION['plList']); $i++){
        if ($_SESSION['plList'][$i][1] != 2 && $_SESSION['crplaylist'] == $i){
            $_SESSION['isAnalyzingNow'] = $_SESSION['plList'][$i][0];
            analyzingPlaylist($uploadDir,$pdo,$_SESSION['plList'][$i][2]);
            $_SESSION['isAnalyzingNow'] = '';
        }
    }
    for ($i = 0; $i < count($_SESSION['plList']); $i++){
        if ($_SESSION['plList'][$i][1] != 2){
            $_SESSION['isAnalyzingNow'] = $_SESSION['plList'][$i][0];
            analyzingPlaylist($uploadDir,$pdo,$_SESSION['plList'][$i][2]);
            $_SESSION['isAnalyzingNow'] = '';
        }
    }
}
?>