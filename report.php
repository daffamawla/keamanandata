<?php
require 'lib.php';
$counts = [
    'keys' => (int) db()->query('SELECT COUNT(*) FROM app_keys')->fetchColumn(),
    'messages' => (int) db()->query('SELECT COUNT(*) FROM submissions')->fetchColumn(),
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tentang Sistem - SecureNote</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <a class="brand" href="index.php"><span class="brand-mark">S</span><span><strong>SecureNote</strong><small>Pesan pribadi terenkripsi</small></span></a>
    <div class="nav-links">
        <a href="index.php">Kirim Pesan</a>
        <a href="report.php" class="active">Tentang Sistem</a>
        <a class="nav-button" href="login.php">Masuk Admin</a>
    </div>
</nav>
<main class="page-shell">
    <section class="hero">
        <div class="hero-copy">
            <span class="eyebrow">Dokumentasi proyek</span>
            <h1>Apa fungsi aplikasi ini?</h1>
            <p>SecureNote adalah sistem pengiriman pesan rahasia berbasis PHP. Data tidak disimpan sebagai teks biasa, melainkan diubah menjadi ciphertext yang hanya dapat dibuka oleh administrator.</p>
        </div>
        <div class="hero-visual" aria-hidden="true"><div class="lock-ring"><div class="lock-icon">&#128274;</div></div></div>
    </section>

    <div class="report-grid" style="margin-top:24px">
        <article class="report-card">
            <span class="step-label">Alur 1</span>
            <h2>Pengunjung mengirim data</h2>
            <p>Pengunjung mengisi nama, email, dan pesan pada beranda. Sistem memeriksa kelengkapan input dan format email sebelum memproses data.</p>
        </article>
        <article class="report-card">
            <span class="step-label">Alur 2</span>
            <h2>Data dienkripsi</h2>
            <p>Isi pesan dienkripsi dengan AES-256-GCM. Kunci AES kemudian dikunci menggunakan public key RSA-2048 sebelum disimpan ke MySQL.</p>
        </article>
        <article class="report-card">
            <span class="step-label">Alur 3</span>
            <h2>Admin membacanya</h2>
            <p>Administrator harus memasukkan passphrase dan private key yang cocok. Sistem membuka private key, lalu mendekripsi setiap pesan.</p>
        </article>

        <article class="report-card">
            <span class="step-label">Status sekarang</span>
            <h2>Kesiapan sistem</h2>
            <ul>
                <li><?= $counts['keys'] > 0 ? 'Pasangan kunci RSA sudah dibuat.' : 'Pasangan kunci RSA belum dibuat.' ?></li>
                <li><?= $counts['messages'] ?> pesan tersimpan di database.</li>
                <li>Halaman admin menampilkan seluruh pesan terenkripsi dan hasil dekripsi.</li>
            </ul>
        </article>
        <article class="report-card">
            <span class="step-label">Teknologi</span>
            <h2>Komponen utama</h2>
            <ul>
                <li>PHP 8.4</li>
                <li>MySQL/MariaDB</li>
                <li>OpenSSL AES-256-GCM</li>
                <li>RSA-2048 OAEP</li>
            </ul>
        </article>
        <article class="report-card">
            <span class="step-label">Ketentuan tugas</span>
            <h2>Pemenuhan proyek</h2>
            <ul>
                <li>Beranda berbeda dari halaman admin.</li>
                <li>Login admin menggunakan dua kunci.</li>
                <li>Admin menampilkan seluruh data dan hasil dekripsi.</li>
            </ul>
        </article>

        <article class="report-card">
            <span class="step-label">Batas pengumpulan</span>
            <h2>19 November 2026</h2>
            <p>Pukul 00.00 WIB sesuai ketentuan pada foto tugas.</p>
        </article>

        <article class="report-card report-wide">
            <span class="step-label">Lampiran</span>
            <h2>Berkas proyek dan database</h2>
            <p>Gunakan berkas berikut sebagai lampiran laporan. Unggah folder proyek dan skema database ke Drive atau GitHub, lalu tambahkan tautan publik pada laporan.</p>
            <div class="report-actions">
                <a href="README.md" target="_blank">Baca README</a>
                <a href="schema.sql" target="_blank">Lihat skema database</a>
                <a href="index.php">Buka beranda</a>
                <a href="login.php">Buka halaman admin</a>
            </div>
        </article>
    </div>
</main>
</body>
</html>
