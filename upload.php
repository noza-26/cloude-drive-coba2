<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['file'])) {
    $fileName = $_FILES['file']['name'];
    $tmpName  = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $displayName = isset($_POST['display_name']) ? trim($_POST['display_name']) : '';

    // Sanitize input untuk mencegah injection
    $displayName = mysqli_real_escape_string($conn, htmlspecialchars($displayName));

    if (empty($displayName)) {
        die("❌ Nama file untuk ditampilkan harus diisi.");
    }

    $targetDir = 'uploads/';
    
    // Generate nama file unik untuk penyimpanan fisik
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $uniqueFileName = uniqid('file_', true) . '.' . strtolower($fileExt);
    $path = $targetDir . $uniqueFileName;

    // Validasi ekstensi dan MIME type
    $allowedExt = ['jpg','png','pdf','doc','docx','zip','pptx','mp4','avi','mkv','mov','wmv','flv','webm','m4v','3gp'];
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    
    // MIME type yang diperbolehkan
    $allowedMimes = [
        'image/jpeg', 'image/png', 'image/jpg',
        'application/pdf',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip', 'application/x-zip-compressed',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'video/mp4', 'video/avi', 'video/x-msvideo', 'video/quicktime', 'video/x-ms-wmv',
        'video/x-flv', 'video/webm', 'video/3gpp'
    ];
    
    $fileMime = mime_content_type($tmpName);

    
    if (!in_array(strtolower($fileExt), $allowedExt)) {
        die("❌ Ekstensi file tidak diperbolehkan.");
    }
    
    if (!in_array($fileMime, $allowedMimes)) {
        die("❌ Tipe file tidak diperbolehkan. MIME type: " . $fileMime);
    }

    if ($fileSize > 50 * 1024 * 1024) { // 50MB untuk video, 5MB untuk file lain
        // Cek apakah file adalah video
        $videoExts = ['mp4','avi','mkv','mov','wmv','flv','webm','m4v','3gp'];
        $isVideo = in_array(strtolower($fileExt), $videoExts);
        
        if (!$isVideo && $fileSize > 5 * 1024 * 1024) {
            die("❌ Ukuran file terlalu besar. Maksimal 5MB untuk dokumen/gambar.");
        } elseif ($isVideo && $fileSize > 50 * 1024 * 1024) {
            die("❌ Ukuran file video terlalu besar. Maksimal 50MB untuk video.");
        }
    }

    if (move_uploaded_file($tmpName, $path)) {
        // Simpan nama yang diberikan user ke database dengan user_id
        $stmt = $conn->prepare("INSERT INTO files (name, path, size, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $displayName, $path, $fileSize, $user_id);
        $stmt->execute();
        echo "✅ File berhasil diupload dengan nama: " . htmlspecialchars($displayName) . "<br>";
        echo "File asli: " . htmlspecialchars($fileName) . "<br>";
        echo "<a href='index.php'>Kembali</a>";
    } else {
        echo "❌ Gagal mengupload file.";
    }
}
?>
