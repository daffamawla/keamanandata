<?php
require 'lib.php';
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$priv = openssl_pkey_get_private($_SESSION['pem'], $_SESSION['pass']);
if (!$priv) {
    session_destroy();
    header('Location: login.php');
    exit;
}
$rows = db()->query('SELECT * FROM submissions ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Admin - SecureNote</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <a class="brand" href="index.php"><span class="brand-mark">S</span><span><strong>SecureNote</strong><small>Pesan pribadi terenkripsi</small></span></a>
    <div class="nav-links">
        <a href="report.php">Dokumentasi</a>
        <a class="nav-button" href="logout.php">Keluar</a>
    </div>
</nav>
<main class="page-shell dashboard">
    <div class="dashboard-heading">
        <div><span class="step-label">Pusat pesan aman</span><h1>Halaman Admin</h1><p>Semua pesan ditampilkan setelah berhasil didekripsi.</p></div>
        <div class="count-card"><span><?= count($rows) ?></span><small>Total pesan</small></div>
    </div>

    <section class="info-strip">
        <div><span class="mini-icon">&#128274;</span><p><strong>Database aman</strong><small>Pesan tersimpan dalam ciphertext</small></p></div>
        <div><span class="mini-icon">&#128273;</span><p><strong>Kunci terverifikasi</strong><small>Private key berhasil dibuka</small></p></div>
    </section>

    <section class="form-card data-card">
        <div class="section-heading">
            <div><span class="step-label">Daftar pesan</span><h2>Isi yang telah didekripsi</h2></div>
        </div>
        <?php if (!$rows): ?>
            <div class="empty-state"><div>&#9993;</div><h3>Belum ada pesan</h3><p>Pesan yang dikirim melalui beranda akan muncul di sini.</p></div>
        <?php else: ?>
            <div class="message-list">
            <?php foreach ($rows as $r):
                $plain = hybrid_decrypt($r, $priv);
                $d = $plain ? json_decode($plain, true) : null; ?>
                <article class="message-item">
                    <div class="message-meta">
                        <span class="avatar"><?= $d ? e(mb_strtoupper(mb_substr($d['nama'], 0, 1))) : '?' ?></span>
                        <div>
                            <strong><?= $d ? e($d['nama']) : 'Pesan tidak dapat dibaca' ?></strong>
                            <small>#<?= (int)$r['id'] ?> &middot; <?= e($r['created_at']) ?></small>
                        </div>
                        <span class="status-tag"><?= $d ? 'Didekripsi' : 'Gagal' ?></span>
                    </div>
                    <?php if ($d): ?>
                        <dl>
                            <div><dt>Email</dt><dd><?= e($d['email']) ?></dd></div>
                            <div><dt>Pesan</dt><dd><?= nl2br(e($d['pesan'])) ?></dd></div>
                        </dl>
                    <?php else: ?>
                        <p class="error-text">Gagal didekripsi. Data mungkin rusak atau dibuat dengan kunci yang berbeda.</p>
                    <?php endif; ?>
                    <details class="cipher-details">
                        <summary>Lihat bukti penyimpanan terenkripsi</summary>
                        <code><?= e(substr($r['ciphertext'], 0, 160)) ?>…</code>
                    </details>
                </article>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
