-- Database untuk Drive Noza Cloud Storage
-- Version: 1.0
-- Author: Drive Noza Team

-- Buat database
CREATE DATABASE IF NOT EXISTS cloud_drive;
USE cloud_drive;

-- Tabel users untuk autentikasi
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
);

-- Tabel files untuk manajemen file
CREATE TABLE IF NOT EXISTS files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    size INT NOT NULL,
    user_id INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_uploaded_at (uploaded_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert user default untuk testing (opsional)
-- Password: admin123 (sudah di-hash dengan bcrypt)
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username = username;

-- Tampilkan informasi setup
SELECT 'Database cloud_drive berhasil dibuat!' as status;
SELECT 'Tabel users dan files berhasil dibuat!' as status;
SELECT 'User default: admin / admin123' as info;
