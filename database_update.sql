-- SQL untuk membuat tabel users dan mengupdate tabel files

-- Buat tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cek apakah kolom user_id sudah ada sebelum menambahkan
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'files' 
                   AND COLUMN_NAME = 'user_id');

-- Tambah kolom user_id ke tabel files (jika belum ada)
SET @sql = IF(@col_exists = 0, 'ALTER TABLE files ADD COLUMN user_id INT', 'SELECT "Column user_id already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah foreign key constraint (jika belum ada)
-- Hapus constraint lama jika ada
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
                  WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'files' 
                  AND CONSTRAINT_NAME = 'fk_files_user');

SET @sql = IF(@fk_exists = 0, 
              'ALTER TABLE files ADD CONSTRAINT fk_files_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE', 
              'SELECT "Foreign key already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
