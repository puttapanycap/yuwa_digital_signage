<?php
// เชื่อมต่อฐานข้อมูล
$pdo = new PDO("mysql:host=localhost;dbname=signage_db;charset=utf8", "root", "");

// ตรวจสอบว่ามีพารามิเตอร์ id ถูกส่งมาหรือไม่
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ดึงข้อมูลไฟล์จากฐานข้อมูล
    $stmt = $pdo->prepare("SELECT file_name FROM media_files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        // ลบไฟล์จากเซิร์ฟเวอร์
        if (file_exists($file['file_name'])) {
            unlink($file['file_name']);
        }

        // ลบข้อมูลจากฐานข้อมูล
        $stmt = $pdo->prepare("DELETE FROM media_files WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// กลับไปที่หน้า admin.php
header("Location: admin.php");
exit;
