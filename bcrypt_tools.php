<?php
echo "<!DOCTYPE html>
<html>
<head>
    <title>Bcrypt Hash Generator - Cloud Noza</title>
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
            max-width: 600px; 
            margin: 0 auto; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }
        h2 { 
            color: #764ba2; 
            text-align: center; 
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type='text'], input[type='password'] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
            margin: 10px 0;
        }
        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .result {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        .hash-output {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            word-break: break-all;
            font-size: 12px;
            margin: 10px 0;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        .btn-link {
            background: #6c757d;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 5px;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class='container'>";

echo "<h2>🔐 Bcrypt Hash Generator & Tester</h2>";

// Generate Hash
if (isset($_POST['generate'])) {
    $password = $_POST['password'];
    $cost = (int)$_POST['cost'];
    
    if (!empty($password)) {
        $options = ['cost' => $cost];
        $hash = password_hash($password, PASSWORD_BCRYPT, $options);
        
        echo "<div class='result success'>
                <h3>✅ Hash berhasil di-generate!</h3>
                <strong>Password:</strong> " . htmlspecialchars($password) . "<br>
                <strong>Cost:</strong> $cost<br>
                <strong>Hash:</strong>
                <div class='hash-output'>$hash</div>
                
                <strong>Informasi Hash:</strong><br>";
        
        $info = password_get_info($hash);
        echo "• Algorithm: " . $info['algoName'] . "<br>";
        echo "• Hash Length: " . strlen($hash) . " characters<br>";
        
        if (substr($hash, 0, 4) === '$2y$') {
            $parts = explode('$', $hash);
            if (count($parts) >= 4) {
                echo "• Cost Factor: " . $parts[2] . "<br>";
                echo "• Salt: " . substr($parts[3], 0, 22) . "<br>";
            }
        }
        
        echo "</div>";
    }
}

// Verify Hash
if (isset($_POST['verify'])) {
    $test_password = $_POST['test_password'];
    $test_hash = $_POST['test_hash'];
    
    if (!empty($test_password) && !empty($test_hash)) {
        $is_valid = password_verify($test_password, $test_hash);
        
        if ($is_valid) {
            echo "<div class='result success'>
                    ✅ <strong>Password COCOK!</strong><br>
                    Password yang dimasukkan sesuai dengan hash.
                  </div>";
        } else {
            echo "<div class='result error'>
                    ❌ <strong>Password TIDAK COCOK!</strong><br>
                    Password yang dimasukkan tidak sesuai dengan hash.
                  </div>";
        }
    }
}

echo "<div class='info'>
        💡 <strong>Bcrypt Cost Factor:</strong><br>
        • Cost 10 = ~0.1 detik (default, recommended)<br>
        • Cost 12 = ~0.4 detik (lebih aman)<br>
        • Cost 15 = ~3 detik (sangat aman, tapi lambat)<br>
      </div>";

echo "<h3>🔨 Generate Hash Baru</h3>
      <form method='POST'>
        <div class='form-group'>
            <label for='password'>Password yang akan di-hash:</label>
            <input type='text' name='password' id='password' placeholder='Masukkan password' required>
        </div>
        <div class='form-group'>
            <label for='cost'>Cost Factor (4-15):</label>
            <input type='number' name='cost' id='cost' value='10' min='4' max='15' required>
        </div>
        <button type='submit' name='generate'>Generate Hash</button>
      </form>";

echo "<h3>🔍 Test Hash yang Ada</h3>
      <form method='POST'>
        <div class='form-group'>
            <label for='test_password'>Password untuk ditest:</label>
            <input type='text' name='test_password' id='test_password' placeholder='Masukkan password'>
        </div>
        <div class='form-group'>
            <label for='test_hash'>Hash bcrypt:</label>
            <input type='text' name='test_hash' id='test_hash' placeholder='Paste hash bcrypt di sini'>
        </div>
        <button type='submit' name='verify'>Verify Password</button>
      </form>";

echo "<div style='text-align: center; margin-top: 30px;'>
        <a href='view_passwords.php' class='btn-link'>🔍 Lihat Password Database</a>
        <a href='login.php' class='btn-link'>📝 Login</a>
        <a href='setup.php' class='btn-link'>🔧 Setup</a>
      </div>";

echo "</div></body></html>";
?>
