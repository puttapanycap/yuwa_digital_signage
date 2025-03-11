<?php
// เชื่อมต่อฐานข้อมูล
$pdo = new PDO("mysql:host=localhost;dbname=signage_db;charset=utf8", "root", "");

// ตรวจสอบการอัปโหลดไฟล์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $duration = $_POST['duration'];
    $targetDir = "uploads/";
    !file_exists($targetDir) ? mkdir($targetDir,0777,true) : false;
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

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Digital Signage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>จัดการสื่อสำหรับ Digital Signage</h2>
    <form method="post" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label>เลือกไฟล์ (รูปภาพหรือวิดีโอ):</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>กำหนดระยะเวลาแสดงผล (วินาที):</label>
            <input type="number" name="duration" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">อัปโหลด</button>
    </form>
    
    <h3>รายการไฟล์ที่อัปโหลด</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ไฟล์</th>
                <th>ประเภท</th>
                <th>เวลาแสดงผล</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><?php echo basename($file['file_name']); ?></td>
                    <td><?php echo strtoupper($file['file_type']); ?></td>
                    <td><?php echo $file['duration']; ?> วินาที</td>
                    <td><a href="delete.php?id=<?php echo $file['id']; ?>" class="btn btn-danger btn-sm">ลบ</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
