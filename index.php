<?php
// เชื่อมต่อฐานข้อมูล
$pdo = new PDO("mysql:host=localhost;dbname=signage_db;charset=utf8", "root", "");

// ดึงข้อมูลไฟล์ทั้งหมดจากฐานข้อมูล
$files = $pdo->query("SELECT * FROM media_files ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Digital Signage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { margin: 0; overflow: hidden; background: black; }
        #signage-container { width: 100vw; height: 100vh; display: flex; align-items: center; justify-content: center; }
        img, video { max-width: 100%; max-height: 100%; }
    </style>
</head>
<body>
    <div id="signage-container">
        <img id="image-display" style="display:none;">
        <video id="video-display" style="display:none;" loop muted></video>
    </div>

    <script>
        let mediaList = <?php echo json_encode($files); ?>;
        let currentIndex = 0;

        function showMedia() {
            if (mediaList.length === 0) return;
            
            let media = mediaList[currentIndex];
            let duration = parseInt(media.duration) * 1000;
            let imageElement = $('#image-display');
            let videoElement = $('#video-display');
            
            if (media.file_type === 'jpg' || media.file_type === 'jpeg' || media.file_type === 'png') {
                videoElement.hide();
                imageElement.attr('src', media.file_name).show();
                setTimeout(nextMedia, duration);
            } else {
                imageElement.hide();
                videoElement.attr('src', media.file_name).show()[0].play();
                
                let videoDuration = videoElement[0].duration * 1000 || duration;
                let playTime = Math.max(videoDuration, duration);
                setTimeout(nextMedia, playTime);
            }
        }

        function nextMedia() {
            currentIndex = (currentIndex + 1) % mediaList.length;
            showMedia();
        }

        $(document).ready(function() {
            showMedia();
        });
    </script>
</body>
</html>
