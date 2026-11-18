# SecureNote - Sistem Informasi Terenkripsi

SecureNote adalah aplikasi PHP untuk mengirim pesan yang disimpan dalam format terenkripsi. Beranda ditujukan kepada pengunjung, sedangkan halaman admin hanya dapat dibuka melalui dua lapis kunci.

## Ringkasan Fungsi
1. Pengunjung mengisi nama, email, dan pesan pada beranda.
2. Isi pesan dienkripsi dengan AES-256-GCM.
3. Kunci AES dienkripsi menggunakan RSA-2048 OAEP.
4. Administrator login menggunakan passphrase dan private key RSA.
5. Halaman admin mendekripsi serta menampilkan seluruh pesan.

## Instalasi
1. Salin folder proyek ke `htdocs/sisfo-kripto`.
2. Import `schema.sql` lewat phpMyAdmin atau MySQL.
3. Sesuaikan koneksi database pada `config.php`.
4. Aktifkan ekstensi `pdo_mysql` dan `openssl`.
5. Buka `setup.php`, buat pasangan kunci, dan unduh `private_key.pem`.
6. Simpan private key secara aman di luar folder web.
7. Buka `index.php`, kirim contoh data, lalu login pada halaman admin.

## Struktur Utama
- `index.php`: halaman pengiriman pesan.
- `login.php`: login dua kunci administrator.
- `admin.php`: menampilkan semua data hasil dekripsi.
- `report.php`: penjelasan aplikasi untuk pengguna dan laporan.
- `lib.php`: fungsi database dan proses kriptografi.
- `schema.sql`: struktur database `app_keys` dan `submissions`.

## Alur Kriptografi
- Penyimpanan: JSON -> AES-256-GCM -> kunci AES dienkripsi RSA-2048 OAEP.
- Login: passphrase AES membuka private key RSA.
- Verifikasi: fingerprint public key dicocokkan dengan data pada database.
- Dekripsi: private key membuka kunci AES, lalu AES-GCM membuka isi pesan.
