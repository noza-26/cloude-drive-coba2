<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$file_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($file_id <= 0) {
    die("❌ ID file tidak valid.");
}

// Ambil informasi file dari database
$stmt = $conn->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $file_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ File tidak ditemukan atau Anda tidak memiliki akses.");
}

$file = $result->fetch_assoc();
$file_path = $file['path'];
$display_name = $file['name'];
$file_size = $file['size'];

// Debug - hapus setelah testing
// echo "Display name: " . $display_name . "<br>";
// echo "File path: " . $file_path . "<br>";
// exit();

// Cek apakah file fisik masih ada
if (!file_exists($file_path)) {
    die("❌ File fisik tidak ditemukan di server.");
}

// Dapatkan ekstensi file asli dari path fisik
$physical_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

// Cek apakah display name sudah memiliki ekstensi
$display_extension = strtolower(pathinfo($display_name, PATHINFO_EXTENSION));

// Buat nama file untuk download
if (empty($display_extension) || $display_extension !== $physical_extension) {
    // Jika tidak ada ekstensi atau ekstensi berbeda, gunakan ekstensi fisik
    $filename_without_ext = pathinfo($display_name, PATHINFO_FILENAME);
    $download_filename = $filename_without_ext . '.' . $physical_extension;
} else {
    // Jika ekstensi sudah benar, gunakan display name
    $download_filename = $display_name;
}

// Clean filename - hanya karakter aman
$download_filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $download_filename);

// Tentukan MIME type berdasarkan ekstensi untuk browser
$mime_types = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'txt' => 'text/plain',
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed'
];

$content_type = isset($mime_types[$physical_extension]) ? $mime_types[$physical_extension] : 'application/octet-stream';

// Clear any previous output
if (ob_get_level()) {
    ob_end_clean();
}

// Set headers untuk download dengan format yang sederhana dan pasti bekerja
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $download_filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Log download activity (opsional)
$log_message = date('Y-m-d H:i:s') . " - User ID: $user_id downloaded file: $download_filename (ID: $file_id)\n";
error_log($log_message, 3, "downloads.log");

// Output file content
readfile($file_path);
exit();
?>
