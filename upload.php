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

    // Validasi sederhana
    $allowedExt = ['jpg','png','pdf','doc','docx','zip','pptx'];
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    
    if (!in_array(strtolower($fileExt), $allowedExt)) {
        die("Ekstensi tidak diperbolehkan.");
    }

    if ($fileSize > 5 * 1024 * 1024) { // 5MB
        die("Ukuran file terlalu besar.");
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
