<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/AnalyingErrors.log');
if (true) {
    $data = json_decode(file_get_contents('php://input'),true);
    if ($data === null || !($data['sessionId']) || !($data['id']) || !($data['genre']) || !($data['track']) || !($data['playlist'])|| !($data['mood']) || !($data['bpm'])|| !($data['instruments'])) {
        http_response_code(400);
        exit('Некорректный JSON');
    }
    file_put_contents(
            __DIR__ . '/js.txt',
            "got data" . PHP_EOL,
            FILE_APPEND
            );
    $id = $data['id'];
    $playlist = $data['playlist'];
    $track = $data['track'];
    $uploadDir = __DIR__ . "/files/user_$id/";
    $plNum = -1;
    session_id($data['sessionId']);
    session_start();
    for ($i = 0;isset($_SESSION['plList']) && $i < count($_SESSION['plList']);$i++){
        if ($playlist == $_SESSION['plList'][$i][2]){
            $plNum = $i;
            break;
        }
    }
    if ($plNum != -1){
        $json = json_decode(file_get_contents($uploadDir . "$plNum.json"), true);
        for ($j = 0;$j < count($json);$j++){
            if ($json[$j]['filename'] == $track){
                $json[$j]["genre"] = $data["genre"];
                $json[$j]["mood"] = $data["mood"];
                $json[$j]["instruments"] = $data["instruments"];
                $json[$j]["tempo"] = $data["bpm"];
                $json[$j]['isAnalyzed'] = true;
                $name = $json[$j]['filename'];
                file_put_contents($uploadDir . "$plNum.json", json_encode($json,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                unlink($uploadDir . "$plNum/$track");    
            } 
        }
        file_put_contents(
            __DIR__ . '/js.txt',
            "all right" . PHP_EOL,
            FILE_APPEND
            );
    }
    session_write_close();
}
?>