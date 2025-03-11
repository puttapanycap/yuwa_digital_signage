<?php
$pdo = new PDO("mysql:host=localhost;dbname=signage_db;charset=utf8", "root", "");
$files = $pdo->query("SELECT * FROM media_files ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($files);
?>
