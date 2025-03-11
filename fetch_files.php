<?php
$pdo = new PDO("mysql:host=localhost;dbname=signage_db;charset=utf8", "root", "");
$new_id = $pdo->query("SELECT MAX(id) AS new_id FROM media_files ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC)[0]['new_id'];
$length = $pdo->query("SELECT * FROM media_files ORDER BY id ASC")->rowCount();

$return = [
    "new_id"=> $new_id,
    "length"=> $length
];
header("Content-Type: application/json; charset=utf-8");
echo json_encode($return);
?>
