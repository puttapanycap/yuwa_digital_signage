<?php
// เชื่อมต่อฐานข้อมูล
$pdo = new PDO("mysql:host=localhost;dbname=signage_db;charset=utf8", "root", "");

// ดึงข้อมูลไฟล์ทั้งหมดจากฐานข้อมูล
$files = $pdo->query("SELECT * FROM media_files ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
$last_id = $pdo->query("SELECT MAX(id) AS max_id FROM media_files")->fetchAll(PDO::FETCH_ASSOC)[0]['max_id'];
$fileLength = $pdo->query("SELECT * FROM media_files ORDER BY id ASC")->rowCount();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Digital Signage</title>
    <link rel="stylesheet" href="./assets/plugins/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="./assets/plugins/jquery-3.7.1/jquery-3.7.1.min.js"></script>
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
        let lastId = "<?php echo $last_id; ?>";
        let itemsLength = "<?php echo $fileLength; ?>";
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

        function updateMediaList() {
            $.ajax({
                url: "fetch_files.php",
                method: "POST",
                data: { lastId: lastId },
                dataType: "JSON",
                success: function(data) {
                    if (lastId != data.new_id || itemsLength != data.length) {
                        location.reload();
                    }
                }
            });
        }

        $(document).ready(function() {
            showMedia();
            setInterval(updateMediaList, 5000); // เช็คอัปเดตทุก 10 วินาที
        });
    </script>
</body>
</html>
