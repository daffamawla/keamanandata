<?php
require 'lib.php';
$msg = $err = null;
$key = get_public_key();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if (!$key) {
        $err = 'Sistem belum siap. Administrator perlu menyelesaikan setup kunci terlebih dahulu.';
    } elseif ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $pesan === '') {
        $err = 'Lengkapi nama, email yang valid, dan pesan.';
    } else {
        $enc = hybrid_encrypt(json_encode([
            'nama' => $nama,
            'email' => $email,
            'pesan' => $pesan,
        ], JSON_UNESCAPED_UNICODE), $key['public_key']);
        db()->prepare('INSERT INTO submissions (ciphertext, iv, tag, enc_key) VALUES (?,?,?,?)')
            ->execute([$enc['ciphertext'], $enc['iv'], $enc['tag'], $enc['enc_key']]);
        $msg = 'Pesan berhasil dikirim dan disimpan dalam bentuk terenkripsi.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>SecureNote - Pesan Terenkripsi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <a class="brand" href="index.php" aria-label="Beranda SecureNote">
        <span class="brand-mark" aria-hidden="true">S</span>
        <span><strong>SecureNote</strong><small>Pesan pribadi terenkripsi</small></span>
    </a>
    <div class="nav-links">
        <a href="index.php" class="active">Kirim Pesan</a>
        <a href="report.php">Tentang Sistem</a>
        <a class="nav-button" href="login.php">Masuk Admin</a>
    </div>
</nav>

<main class="page-shell">
    <section class="hero">
        <div class="hero-copy">
            <span class="eyebrow">Layanan pesan aman</span>
            <h1>Kirim pesan rahasia dengan tenang.</h1>
            <p>Pesan Anda diproses menjadi kode rahasia sebelum disimpan. Hanya administrator yang memiliki kedua kunci dapat membacanya kembali.</p>
            <div class="security-pills">
                <span><b>A</b> AES-256-GCM</span>
                <span><b>R</b> RSA-2048</span>
                <span><b>DB</b> MySQL</span>
            </div>
        </div>
        <div class="hero-visual" aria-hidden="true">
            <div class="lock-ring"><div class="lock-icon">&#128274;</div></div>
            <div class="status-chip"><span></span> Enkripsi aktif</div>
        </div>
    </section>

    <section class="content-grid">
        <div class="form-card">
            <div class="section-heading">
                <div>
                    <span class="step-label">Form penerima aman</span>
                    <h2>Tulis pesan Anda</h2>
                </div>
                <span class="secure-badge">Terenkripsi</span>
            </div>

            <?php if ($msg): ?>
                <div class="alert success"><strong>Berhasil!</strong><br><?= e($msg) ?></div>
            <?php endif; ?>
            <?php if ($err): ?>
                <div class="alert error"><strong>Pesan belum dikirim.</strong><br><?= e($err) ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="on">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <div class="field">
                    <label for="nama">Nama lengkap</label>
                    <input id="nama" name="nama" placeholder="Contoh: Ahmad Pratama" required>
                </div>
                <div class="field">
                    <label for="email">Alamat email</label>
                    <input id="email" type="email" name="email" placeholder="nama@email.com" required>
                </div>
                <div class="field">
                    <label for="pesan">Pesan rahasia</label>
                    <textarea id="pesan" name="pesan" placeholder="Tuliskan pesan yang ingin disampaikan..." required></textarea>
                </div>
                <button class="primary-button" type="submit">Kirim pesan aman <span aria-hidden="true">&rarr;</span></button>
            </form>
        </div>

        <aside class="how-card">
            <span class="step-label">Bagaimana kerjanya?</span>
            <h2>Tiga langkah sederhana</h2>
            <ol class="steps">
                <li><span>1</span><div><strong>Anda mengirim</strong><p>Isi nama, email, dan pesan pada formulir.</p></div></li>
                <li><span>2</span><div><strong>Pesan dilindungi</strong><p>Pesan dienkripsi sebelum masuk ke database.</p></div></li>
                <li><span>3</span><div><strong>Admin membuka</strong><p>Pesan hanya dapat dibaca dengan dua jenis kunci.</p></div></li>
            </ol>
            <a class="text-link" href="report.php">Pelajari teknologi yang digunakan &rarr;</a>
        </aside>
    </section>
</main>

<footer>
    <div><strong>SecureNote</strong><span>Demonstrasi sistem informasi terenkripsi</span></div>
    <a href="report.php">Dokumentasi & laporan proyek</a>
</footer>
</body>
</html>
