<?php
include 'db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Setup Database - Cloud Noza</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #00FA9A; text-align: center; }
        .success { color: #2e7d32; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #c62828; background: #ffebee; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .btn { background: #00FA9A; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #00e085; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h2>🔧 Setup Database Cloud Drive</h2>";
echo "<div class='success'>ℹ️ Setup ini akan:
      <br>• Membuat tabel users dengan password bcrypt hashing
      <br>• Mengupdate tabel files dengan kolom user_id
      <br>• Membuat user default untuk testing</div>";

try {
    // Buat tabel users
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql_users) === TRUE) {
        echo "<div class='success'>✅ Tabel users berhasil dibuat/sudah ada</div>";
    } else {
        throw new Exception("Error membuat tabel users: " . $conn->error);
    }

    // Cek apakah kolom user_id sudah ada di tabel files
    $check_column = "SHOW COLUMNS FROM files LIKE 'user_id'";
    $result = $conn->query($check_column);

    if ($result->num_rows == 0) {
        // Tambah kolom user_id ke tabel files
        $sql_alter = "ALTER TABLE files ADD COLUMN user_id INT";
        if ($conn->query($sql_alter) === TRUE) {
            echo "<div class='success'>✅ Kolom user_id berhasil ditambahkan ke tabel files</div>";
        } else {
            throw new Exception("Error menambah kolom user_id: " . $conn->error);
        }
        
        // Tambah foreign key constraint
        $sql_fk = "ALTER TABLE files ADD CONSTRAINT fk_files_user 
                   FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
        if ($conn->query($sql_fk) === TRUE) {
            echo "<div class='success'>✅ Foreign key constraint berhasil ditambahkan</div>";
        } else {
            // Jika foreign key gagal, tidak fatal
            echo "<div class='error'>⚠️ Warning: Gagal menambah foreign key constraint: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='success'>✅ Kolom user_id sudah ada di tabel files</div>";
    }

    // Buat user default untuk testing (jika belum ada)
    $check_admin = "SELECT id FROM users WHERE username = 'admin'";
    $admin_result = $conn->query($check_admin);
    
    if ($admin_result->num_rows == 0) {
        // Hash password menggunakan bcrypt
        $admin_username = "admin";
        $default_password = "admin123";
        $hashed_password = password_hash($default_password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $admin_username, $hashed_password);
        
        if ($stmt->execute()) {
            echo "<div class='success'>✅ User default berhasil dibuat:
                  <br><strong>Username:</strong> admin
                  <br><strong>Password:</strong> admin123
                  <br><em>Password telah di-hash dengan bcrypt untuk keamanan</em></div>";
        } else {
            echo "<div class='error'>⚠️ Warning: Gagal membuat user default: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='success'>✅ User admin sudah ada</div>";
    }

    echo "<div class='success'><strong>🎉 Setup berhasil!</strong></div>";
    echo "<div class='success'>🔐 <strong>Keamanan Password:</strong>
          <br>• Semua password di-hash menggunakan bcrypt (PASSWORD_BCRYPT)
          <br>• Cost factor default PHP untuk keamanan optimal
          <br>• Password tidak pernah disimpan dalam bentuk plain text</div>";
    echo "<div style='text-align: center;'>
            <a href='login.php' class='btn'>📝 Menuju Halaman Login</a>
            <a href='index.php' class='btn'>🏠 Menuju Dashboard</a>
            <a href='view_passwords.php' class='btn'>🔍 Lihat Password Hash</a>
          </div>";

} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<div style='text-align: center;'>
            <a href='login.php' class='btn'>🔙 Kembali ke Login</a>
          </div>";
}

echo "</div></body></html>";
?>
