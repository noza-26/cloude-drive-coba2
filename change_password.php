<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$error = '';
$success = '';

// Proses change password
if (isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif (strlen($new_password) < 6) {
        $error = "Password baru minimal 6 karakter!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Konfirmasi password baru tidak cocok!";
    } elseif ($current_password === $new_password) {
        $error = "Password baru harus berbeda dari password lama!";
    } else {
        try {
            // Ambil password hash saat ini dari database
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verify password lama
                if (password_verify($current_password, $user['password'])) {
                    // Hash password baru
                    $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
                    
                    // Update password di database
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $new_password_hash, $user_id);
                    
                    if ($stmt->execute()) {
                        $success = "Password berhasil diubah!";
                        
                        // Log activity
                        $log_message = date('Y-m-d H:i:s') . " - User ID: $user_id ($username) changed password\n";
                        error_log($log_message, 3, "password_changes.log");
                    } else {
                        throw new Exception("Gagal mengupdate password: " . $stmt->error);
                    }
                } else {
                    $error = "Password lama tidak benar!";
                }
            } else {
                $error = "User tidak ditemukan!";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password - Cloud Noza</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 500px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #764ba2;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .user-info {
            color: #666;
            font-size: 16px;
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 22px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #555;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .password-strength {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }
        
        .btn {
            width: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            margin-bottom: 15px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        
        .btn-secondary {
            background: linear-gradient(45deg, #6c757d, #5a6268);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }
        
        .btn-secondary:hover {
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.6);
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffcdd2;
        }
        
        .success {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c8e6c9;
        }
        
        .security-tips {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 1px solid #bbdefb;
        }
        
        .security-tips h3 {
            color: #1976d2;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .security-tips ul {
            color: #424242;
            padding-left: 20px;
        }
        
        .security-tips li {
            margin-bottom: 8px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Change Password</h1>
            <div class="user-info">
                👤 Logged in as: <strong><?= htmlspecialchars($username) ?></strong>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                ✅ <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <div class="form-section">
            <h2>Ubah Password</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">🔑 Password Lama:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">🆕 Password Baru:</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                    <div class="password-strength">Minimal 6 karakter</div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">✅ Konfirmasi Password Baru:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                
                <button type="submit" name="change_password" class="btn">
                    Ubah Password
                </button>
                
                <a href="index.php" class="btn btn-secondary" style="text-decoration: none; display: block; text-align: center;">
                    Kembali ke Dashboard
                </a>
            </form>
        </div>
        
        <div class="security-tips">
            <h3>🛡️ Tips Keamanan Password:</h3>
            <ul>
                <li>Gunakan kombinasi huruf besar, kecil, angka, dan simbol</li>
                <li>Minimal 8-12 karakter untuk keamanan optimal</li>
                <li>Jangan gunakan informasi pribadi (nama, tanggal lahir)</li>
                <li>Jangan gunakan password yang sama di berbagai akun</li>
                <li>Ubah password secara berkala</li>
            </ul>
        </div>
        
        <div class="back-link">
            <a href="index.php">← Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>
