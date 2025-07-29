<?php
// Template konfigurasi database untuk Drive Noza
// Copy file ini ke db.php dan sesuaikan dengan setting database Anda

// Database Configuration
$servername = "localhost";      // Host database
$username = "your_username";    // Username database
$password = "your_password";    // Password database  
$dbname = "cloud_drive";        // Nama database

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset untuk mencegah masalah encoding
$conn->set_charset("utf8");

// Optional: Timezone setting
date_default_timezone_set('Asia/Jakarta');

// Optional: Error reporting (untuk development)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
?>
