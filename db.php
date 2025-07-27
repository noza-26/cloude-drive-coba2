<?php
$host = "localhost";
$user = "root";
$pass = ""; // Default XAMPP tanpa password
$db   = "cloud_storage";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
