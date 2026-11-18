<?php
require 'lib.php';
$err = null;
if (!empty($_SESSION['admin'])) { header('Location: admin.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    usleep(500000); // memperlambat brute force
    $pem  = trim($_POST['private_key'] ?? '');
    if (!empty($_FILES['key_file']['tmp_name']) && is_uploaded_file($_FILES['key_file']['tmp_name']))
        $pem = trim(file_get_contents($_FILES['key_file']['tmp_name']));
    $pass = $_POST['passphrase'] ?? '';
    $stored = get_public_key();

    $priv = $pem !== '' ? @openssl_pkey_get_private($pem, $pass) : false;
    if (!$stored) $err = 'Sistem belum disetup (jalankan setup.php).';
    elseif (!$priv) $err = 'Kunci simetris (passphrase) salah atau kunci asimetris tidak valid.';
    else {
        $pub = openssl_pkey_get_details($priv)['key'];
        if (!hash_equals($stored['fingerprint'], fingerprint($pub))) {
            $err = 'Kunci asimetris tidak cocok dengan kunci yang terdaftar.';
        } else {
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            $_SESSION['pem'] = $pem;
            $_SESSION['pass'] = $pass;
            header('Location: admin.php'); exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login Admin - SecureNote</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<nav class="navbar">
    <a class="brand" href="index.php"><span class="brand-mark">S</span><span><strong>SecureNote</strong><small>Pesan pribadi terenkripsi</small></span></a>
    <div class="nav-links"><a href="index.php">Kembali ke Beranda</a></div>
</nav>
<main class="auth-shell">
    <section class="auth-aside">
        <span class="eyebrow">Area administrator</span>
        <h1>Verifikasi dua lapis kunci.</h1>
        <p>Login ini membutuhkan passphrase dan private key RSA milik administrator. Tanpa keduanya, isi pesan tidak dapat dibuka.</p>
        <div class="key-steps">
            <div><span>1</span><p><strong>Kunci simetris</strong><br>Passphrase untuk membuka private key.</p></div>
            <div><span>2</span><p><strong>Kunci asimetris</strong><br>File atau isi private key RSA.</p></div>
        </div>
    </section>
    <section class="auth-card">
        <span class="step-label">Autentikasi aman</span>
        <h2>Masuk Halaman Admin</h2>
        <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <div class="field">
                <label for="passphrase">Kunci simetris / passphrase</label>
                <input id="passphrase" type="password" name="passphrase" placeholder="Masukkan passphrase" required>
            </div>
            <div class="field">
                <label for="private_key">Kunci asimetris RSA</label>
                <textarea id="private_key" name="private_key" class="mono" placeholder="Tempel isi private_key.pem, atau gunakan unggahan di bawah"></textarea>
            </div>
            <div class="field">
                <label for="key_file">Unggah file private_key.pem</label>
                <input id="key_file" type="file" name="key_file" accept=".pem,.key,.txt">
            </div>
            <button class="primary-button" type="submit">Verifikasi &amp; Masuk <span>&rarr;</span></button>
        </form>
    </section>
</main>
</body>
</html>
