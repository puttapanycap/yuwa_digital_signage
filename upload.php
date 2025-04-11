<?php
date_default_timezone_set("Asia/Bangkok");

$host = "localhost";
$dbname = "signage_db";
$username = "root";
$password = "";
$charset = "utf8";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $username, $password);
} catch (PDOException $e) {
    http_response_code(500);
    echo "เชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['mediaFile']) && isset($_POST['duration'])) {
    $file = $_FILES['mediaFile'];
    $duration = intval($_POST['duration']);

    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $fileName = basename($file['name']);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;
    // $fileType = mime_content_type($file['tmp_name']);

    $filePath = $targetDir . basename($file["name"]);
    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
    // ตรวจสอบประเภทไฟล์
    $allowedTypes = ['jpg', 'jpeg', 'png', 'mp4', 'avi'];
    if (!in_array($fileType, $allowedTypes)) {
        http_response_code(400);
        echo "ประเภทไฟล์ไม่ถูกต้อง";
        exit();
    }

    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        $stmt = $pdo->prepare("INSERT INTO media_files (file_name, file_type, duration) VALUES (?, ?, ?)");
        $stmt->execute([$targetFilePath, $fileType, $duration]);
        http_response_code(200);
        echo "เสร็จสิ้น";
    } else {
        http_response_code(500);
        $error = error_get_last();
        echo "อัปโหลดไฟล์ล้มเหลว: " . ($error ? $error['message'] : 'Unknown error');
    }
} else {
    http_response_code(400);
    echo "ข้อมูลไม่ครบถ้วน";
}
