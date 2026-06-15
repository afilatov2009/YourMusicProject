<?php
function addPlaylist($pdo, $number, $title, $isAnalyzed) {
    $sql = "INSERT INTO playlists (playlist_number, title, is_analyzed, user_id) 
            VALUES (:num, :title, :analyzed, :id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'num'      => $number,
        'title'    => $title,
        'analyzed' => (int)$isAnalyzed,
        'id'  => $_SESSION['id']
    ]);
}
function deletePlaylist($pdo, $number) {
    $pdo->beginTransaction();
    $sql = "DELETE FROM playlists WHERE playlist_number = :num AND user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['num' => $number, 'id' => $_SESSION['id']]);
    $updateSql = "UPDATE playlists 
                SET playlist_number = playlist_number - 1 
                WHERE user_id = :id AND playlist_number > :num";
    $pdo->prepare($updateSql)->execute([
        'id'     => $_SESSION['id'],
        'num' => $number
    ]);
    $pdo->commit();
}
function getPlaylists($pdo) {
    $sql = "SELECT title, is_analyzed FROM playlists WHERE user_id = :id ORDER BY playlist_number ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $_SESSION['id']]);
    $res = $stmt->fetchAll(PDO::FETCH_NUM);
    foreach ($res as $row) {
        $row[1] = (bool)$row[1];
    }
    return $res;
}
function putPlaylists($pdo, $List) {
    if (getPlaylists($pdo) == []){
        $pdo->beginTransaction();
        $sql = "INSERT INTO playlists (playlist_number, title, is_analyzed, user_id) 
                VALUES (:num, :title, :analyzed, :id)";
        $stmt = $pdo->prepare($sql);

        foreach ($List as $index => $item) {
            $stmt->execute([
                'num'      => $index,
                'title'    => $item[0],     
                'analyzed' => (int)$item[1],
                'id'  => $_SESSION['id']
            ]);
        }
        $pdo->commit();
    }
}
function changePlaylist($pdo, $number, $newTitle, $newAnalyzed) {
    $sql = "UPDATE playlists 
            SET title = :title, is_analyzed = :analyzed 
            WHERE playlist_number = :num AND user_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title'    => $newTitle,
        'analyzed' => (int)$newAnalyzed,
        'num'      => $number,
        'id'  => $_SESSION['id']
    ]);
}
?>