<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Upload Media</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Sarabun', sans-serif;
    }
  </style>
</head>
<body class="p-4">

  <div class="container">
    <h3>อัปโหลดไฟล์สื่อ</h3>
    <form id="uploadForm" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="mediaFile" class="form-label">เลือกรูปภาพหรือวิดีโอ</label>
        <input type="file" class="form-control" id="mediaFile" name="mediaFile" accept="image/*,video/*" required>
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
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#uploadForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        $('.progress').show();
        $('.progress-bar').css('width', '0%').text('0%');
        $('#result').html('');

        $.ajax({
          xhr: function () {
            let xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
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
          success: function (response) {
            $('.progress-bar').css('width', '100%').text('100%');
            $('#result').html('<div class="alert alert-success">อัปโหลดสำเร็จ!</div>');
            $('#uploadForm')[0].reset();
            setTimeout(function() {
              location.reload();
            }, 2000); // Reload the page after 2 seconds
          },
          error: function () {
            $('#result').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการอัปโหลด</div>');
          }
        });
      });
    });
  </script>

</body>
</html>
