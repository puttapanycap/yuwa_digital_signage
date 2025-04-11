<?php
date_default_timezone_set("Asia/Bangkok");
// เชื่อมต่อฐานข้อมูล
$pdo = new PDO("mysql:host=localhost;dbname=signage_db;charset=utf8", "root", "");
// ดึงข้อมูลไฟล์ทั้งหมด
$files = $pdo->query("SELECT * FROM media_files ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');

$allowedTypes = ['jpg', 'jpeg', 'png', 'mp4', 'avi'];
$acceptString = implode(', ', array_map(fn($ext) => '.' . $ext, $allowedTypes));


?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin - Digital Signage</title>
    <link rel="stylesheet" href="./assets/plugins/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h3>อัปโหลดไฟล์สื่อ</h3>
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="mediaFile" class="form-label">เลือกรูปภาพหรือวิดีโอ</label>
                <input type="file" class="form-control" id="mediaFile" name="mediaFile" accept="<?php echo $acceptString ?>" required>
                <div id="mediaFileHelp" class="form-text">ประเภทไฟล์ : <?php echo $acceptString; ?> / ขนาดไฟล์สูงสุด : <?php echo $upload_max; ?></div>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">ระยะเวลาแสดงผล (วินาที)</label>
                <input type="number" class="form-control" id="duration" name="duration" required>
            </div>
            <button type="submit" class="btn btn-primary">อัปโหลด</button>
        </form>

        <div class="progress mt-4" style="height: 25px; display: none;">
            <div class="progress-bar" role="progressbar" style="width: 0%;">0%</div>
        </div>

        <div id="result" class="mt-3"></div>

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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                $('.progress').show();
                $('.progress-bar').css('width', '0%').text('0%');
                $('#result').html('');

                $.ajax({
                    xhr: function() {
                        let xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                let percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                $('.progress-bar').css('width', percentComplete + '%').text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    url: 'upload.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('.progress-bar').css('width', '100%').text('100%');
                        $('#result').html('<div class="alert alert-success">อัปโหลดสำเร็จ!</div>');
                        $('#uploadForm')[0].reset();
                        setTimeout(function() {
                            location.reload();
                        }, 1000); // Reload the page after 2 seconds
                    },
                    error: function(xhr, status, error) {
                        $('#result').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการอัปโหลด: ' + xhr.responseText + '</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>