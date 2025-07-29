<?php 
session_start();
include 'db.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cloud Noza</title>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info h1 {
            color: #764ba2;
            font-size: 28px;
            font-weight: bold;
        }
        
        .user-welcome {
            color: #555;
            font-size: 16px;
            background: #f8f9fa;
            padding: 8px 15px;
            border-radius: 25px;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .change-password-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            font-size: 14px;
        }
        
        .change-password-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .logout-btn {
            background: linear-gradient(45deg, #ff6b6b, #ee5a6f);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(238, 90, 111, 0.4);
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 90, 111, 0.6);
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .upload-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            height: fit-content;
        }
        
        .upload-section h2 {
            color: #764ba2;
            margin-bottom: 25px;
            font-size: 24px;
            text-align: center;
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
        
        .form-group input[type="file"], 
        .form-group input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .file-info {
            margin-top: 8px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .file-info small {
            color: #666;
            line-height: 1.4;
        }
        
        .upload-btn {
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
        }
        
        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        
        .files-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }
        
        .files-section h2 {
            color: #764ba2;
            margin-bottom: 25px;
            font-size: 24px;
            text-align: center;
        }
        
        .files-grid {
            display: grid;
            gap: 15px;
        }
        
        .file-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e1e8ed;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .file-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #667eea;
        }
        
        .file-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .file-name {
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }
        
        .file-size {
            color: #888;
            font-size: 12px;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 15px;
        }
        
        .file-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .file-date {
            color: #666;
            font-size: 12px;
        }
        
        .file-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-download {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            color: white;
        }
        
        .btn-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(58, 123, 213, 0.4);
        }
        
        .btn-delete {
            background: linear-gradient(45deg, #ff6b6b, #ee5a6f);
            color: white;
        }
        
        .btn-delete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(238, 90, 111, 0.4);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .header-actions {
                flex-direction: column;
                gap: 10px;
                width: 100%;
            }
            
            .change-password-btn,
            .logout-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-info">
                <h1>🌤️ Drive Noza</h1>
                <div class="user-welcome">
                    👋 Selamat datang, <strong><?= htmlspecialchars($username) ?></strong>
                </div>
            </div>
            <div class="header-actions">
                <a href="change_password.php" class="change-password-btn">🔒 Ubah Password</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <!-- Upload Section -->
            <div class="upload-section">
                <h2>📤 Upload File</h2>
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">📁 Pilih File:</label>
                        <input type="file" name="file" id="file" required />
                        <div class="file-info">
                            <small>📋 Format yang didukung: JPG, PNG, PDF, DOC, DOCX, ZIP, PPTX, MP4, AVI, MKV, MOV, WMV, FLV, WEBM, M4V, 3GP</small><br>
                            <small>📏 Maksimal: 5MB (dokumen/gambar), 50MB (video)</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="display_name">✏️ Nama File untuk Ditampilkan:</label>
                        <input type="text" name="display_name" id="display_name" 
                               placeholder="Masukkan nama file yang ingin ditampilkan" required />
                    </div>
                    <button type="submit" class="upload-btn">Upload File</button>
                </form>
            </div>
            
            <!-- Files Section -->
            <div class="files-section">
                <h2>📂 File Saya</h2>
                <div class="files-grid">
                    <?php
                    // Query files berdasarkan user yang login
                    $stmt = $conn->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY uploaded_at DESC");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                    <div class="file-card">
                        <div class="file-info">
                            <div class="file-name">
                                <?php
                                // Tentukan icon berdasarkan ekstensi file
                                $fileExt = strtolower(pathinfo($row['name'], PATHINFO_EXTENSION));
                                $videoExts = ['mp4','avi','mkv','mov','wmv','flv','webm','m4v','3gp'];
                                $imageExts = ['jpg','jpeg','png','gif','bmp'];
                                $docExts = ['pdf','doc','docx','ppt','pptx'];
                                
                                if (in_array($fileExt, $videoExts)) {
                                    echo "🎬 ";
                                } elseif (in_array($fileExt, $imageExts)) {
                                    echo "🖼️ ";
                                } elseif (in_array($fileExt, $docExts)) {
                                    echo "📄 ";
                                } elseif ($fileExt === 'zip') {
                                    echo "📦 ";
                                } else {
                                    echo "📄 ";
                                }
                                ?>
                                <?= htmlspecialchars($row['name']) ?>
                            </div>
                            <div class="file-size">
                                <?= round($row['size']/1024, 2) ?> KB
                            </div>
                        </div>
                        <div class="file-meta">
                            <div class="file-date">
                                🕒 <?= date('d M Y, H:i', strtotime($row['uploaded_at'])) ?>
                            </div>
                        </div>
                        <div class="file-actions">
                            <a href="download.php?id=<?= $row['id'] ?>" class="btn btn-download">
                                ⬇️ Download
                            </a>
                            <a href="delete.php?id=<?= $row['id'] ?>" 
                               onclick="return confirm('Yakin ingin menghapus file ini?')" 
                               class="btn btn-delete">
                                🗑️ Hapus
                            </a>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <div class="empty-state">
                        <div style="font-size: 48px; margin-bottom: 15px;">📁</div>
                        <h3>Belum ada file</h3>
                        <p>Upload file pertama Anda untuk memulai!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
