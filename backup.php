<?php
// เชื่อมต่อฐานข้อมูล
$pdo = new PDO("mysql:host=localhost;dbname=signage_db;charset=utf8", "root", "");

// ตรวจสอบการอัปโหลดไฟล์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $duration = $_POST['duration'];
    $targetDir = "uploads/";
    !file_exists($targetDir) ? mkdir($targetDir, 0777, true) : false;
    $filePath = $targetDir . basename($file["name"]);
    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

    // ตรวจสอบประเภทไฟล์
    $allowedTypes = ['jpg', 'jpeg', 'png', 'mp4', 'avi'];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($file["tmp_name"], $filePath)) {
            $stmt = $pdo->prepare("INSERT INTO media_files (file_name, file_type, duration) VALUES (?, ?, ?)");
            $stmt->execute([$filePath, $fileType, $duration]);
        }
    }
}

// ดึงข้อมูลไฟล์ทั้งหมด
$files = $pdo->query("SELECT * FROM media_files ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>