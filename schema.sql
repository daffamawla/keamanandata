CREATE DATABASE IF NOT EXISTS sisfo_kripto CHARACTER SET utf8mb4;
USE sisfo_kripto;

-- Menyimpan PUBLIC KEY RSA saja (private key tidak pernah disimpan di server)
CREATE TABLE IF NOT EXISTS app_keys (
  id INT AUTO_INCREMENT PRIMARY KEY,
  public_key TEXT NOT NULL,
  fingerprint CHAR(64) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Data hasil enkripsi
CREATE TABLE IF NOT EXISTS submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ciphertext MEDIUMTEXT NOT NULL,   -- AES-256-GCM (base64)
  iv VARCHAR(32) NOT NULL,          -- base64
  tag VARCHAR(32) NOT NULL,         -- base64
  enc_key TEXT NOT NULL,            -- kunci AES acak, dienkripsi RSA-OAEP (base64)
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
