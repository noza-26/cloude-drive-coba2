<?php
session_start();
include 'db.php';

// Cek apakah user sudah login (optional, bisa diakses tanpa login untuk debugging)
$is_logged_in = isset($_SESSION['user_id']);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Password Hash Viewer - Cloud Noza</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 40px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 30px; 
            border-radius: 15px; 
            max-width: 800px; 
            margin: 0 auto; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }
        h2 { 
            color: #764ba2; 
            text-align: center; 
            margin-bottom: 30px;
        }
        .user-card {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .username {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .hash-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #667eea;
        }
        .hash-text {
            font-family: 'Courier New', monospace;
            word-break: break-all;
            font-size: 12px;
            color: #555;
            background: white;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .hash-info {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }
        .created-date {
            color: #888;
            font-size: 14px;
            margin-top: 10px;
        }
        .btn { 
            background: linear-gradient(45deg, #667eea, #764ba2); 
            color: white; 
            padding: 12px 20px; 
            text-decoration: none; 
            border-radius: 8px; 
            display: inline-block; 
            margin: 10px 5px;
            transition: all 0.3s ease;
        }
        .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #ffeaa7;
        }
        .test-section {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #c8e6c9;
        }
        .test-result {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h2>🔐 Password Hash Viewer</h2>";

echo "<div class='warning'>
        ⚠️ <strong>Penting:</strong> 
        <br>• Password yang di-hash dengan bcrypt TIDAK BISA di-decrypt kembali
        <br>• Ini adalah fitur one-way hashing untuk keamanan
        <br>• Hash yang ditampilkan di sini hanya untuk debugging dan educational purposes
      </div>";

// Test password verification jika ada parameter
if (isset($_GET['test_user']) && isset($_GET['test_pass'])) {
    $test_user = $_GET['test_user'];
    $test_pass = $_GET['test_pass'];
    
    echo "<div class='test-section'>
            <h3>🧪 Test Password Verification</h3>";
    
    $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $test_user);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $is_valid = password_verify($test_pass, $user['password']);
        
        if ($is_valid) {
            echo "<div class='test-result success'>
                    ✅ Password BENAR untuk user: " . htmlspecialchars($test_user) . "
                  </div>";
        } else {
            echo "<div class='test-result error'>
                    ❌ Password SALAH untuk user: " . htmlspecialchars($test_user) . "
                  </div>";
        }
    } else {
        echo "<div class='test-result error'>
                ❌ User tidak ditemukan: " . htmlspecialchars($test_user) . "
              </div>";
    }
    echo "</div>";
}

try {
    // Ambil semua user dan password hash-nya
    $query = "SELECT id, username, password, created_at FROM users ORDER BY created_at DESC";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        echo "<h3>👥 Daftar User dan Password Hash:</h3>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<div class='user-card'>";
            echo "<div class='username'>👤 " . htmlspecialchars($row['username']) . " (ID: " . $row['id'] . ")</div>";
            
            echo "<div class='hash-container'>";
            echo "<strong>🔒 Password Hash (bcrypt):</strong>";
            echo "<div class='hash-text'>" . htmlspecialchars($row['password']) . "</div>";
            
            // Analisis hash
            $hash_info = password_get_info($row['password']);
            echo "<div class='hash-info'>";
            echo "• Algorithm: " . $hash_info['algoName'] . "<br>";
            echo "• Options: " . json_encode($hash_info['options']) . "<br>";
            echo "• Hash Length: " . strlen($row['password']) . " characters<br>";
            
            // Identifikasi komponen bcrypt
            if (substr($row['password'], 0, 4) === '$2y$') {
                $parts = explode('$', $row['password']);
                if (count($parts) >= 4) {
                    echo "• Cost Factor: " . $parts[2] . "<br>";
                    echo "• Salt: " . substr($parts[3], 0, 22) . "<br>";
                }
            }
            echo "</div>";
            echo "</div>";
            
            echo "<div class='created-date'>📅 Dibuat: " . $row['created_at'] . "</div>";
            
            // Test form untuk user ini
            echo "<div style='margin-top: 15px;'>
                    <strong>🧪 Test Password:</strong>
                    <form method='GET' style='display: inline-block; margin-left: 10px;'>
                        <input type='hidden' name='test_user' value='" . htmlspecialchars($row['username']) . "'>
                        <input type='password' name='test_pass' placeholder='Masukkan password' style='padding: 5px; margin-right: 5px;'>
                        <button type='submit' style='padding: 5px 10px; background: #667eea; color: white; border: none; border-radius: 3px; cursor: pointer;'>Test</button>
                    </form>
                  </div>";
            
            echo "</div>";
        }
    } else {
        echo "<div class='warning'>Tidak ada user yang ditemukan di database.</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "<div class='test-section'>
        <h3>💡 Informasi Bcrypt:</h3>
        <p><strong>Struktur Hash Bcrypt:</strong></p>
        <div class='hash-text'>\$2y\$[cost]\$[salt][hash]</div>
        <p><strong>Contoh:</strong></p>
        <ul>
            <li><strong>\$2y\$</strong> = Identifier untuk bcrypt variant</li>
            <li><strong>10</strong> = Cost factor (2^10 = 1024 iterations)</li>
            <li><strong>22 karakter pertama</strong> = Salt (random)</li>
            <li><strong>31 karakter terakhir</strong> = Hash dari password+salt</li>
        </ul>
      </div>";

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='login.php' class='btn'>📝 Menuju Login</a>
        <a href='index.php' class='btn'>🏠 Dashboard</a>
        <a href='setup.php' class='btn'>🔧 Setup Database</a>
      </div>";

echo "</div></body></html>";
?>
