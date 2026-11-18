<?php
// Jalankan SEKALI untuk membuat pasangan kunci. Hapus/rename file ini setelah selesai.
require 'lib.php';
$msg = $err = $privOut = null;
$existing = get_public_key();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$existing) {
    csrf_check();
    $pass = $_POST['passphrase'] ?? '';
    if (strlen($pass) < 12) {
        $err = 'Kunci simetris (passphrase) minimal 12 karakter.';
    } else {
        $res = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        if (!$res) {
            $err = 'Gagal membuat kunci RSA. Periksa ekstensi openssl / openssl.cnf Anda.';
        } else {
            // Private key diproteksi AES-256-CBC dengan passphrase (kunci simetris)
            openssl_pkey_export($res, $privOut, $pass, ['encrypt_key' => true, 'encrypt_key_cipher' => OPENSSL_CIPHER_AES_256_CBC]);
            $pub = openssl_pkey_get_details($res)['key'];
            db()->prepare('INSERT INTO app_keys (public_key, fingerprint) VALUES (?,?)')->execute([$pub, fingerprint($pub)]);
            $msg = 'Kunci berhasil dibuat. SIMPAN private key di bawah ini; server tidak menyimpannya.';
            $existing = true;
        }
    }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><title>Setup Kunci</title>
<link rel="stylesheet" href="style.css"></head><body>
<header><strong>Setup Kunci Kriptografi</strong><a href="index.php">Beranda</a></header><main>
<?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<?php if ($privOut): ?>
  <div class="card"><div class="ok"><?= e($msg) ?></div>
  <p>Kunci Asimetris (Private Key RSA terenkripsi):</p>
  <textarea class="mono" id="pk" readonly style="min-height:260px"><?= e($privOut) ?></textarea>
  <button onclick="dl()">Unduh private_key.pem</button>
  <p><small>Kunci simetris = passphrase yang barusan Anda isi. Keduanya dibutuhkan untuk login admin.</small></p></div>
  <script>function dl(){const b=new Blob([document.getElementById('pk').value],{type:'text/plain'});
  const a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='private_key.pem';a.click();}</script>
<?php elseif ($existing): ?>
  <div class="card"><div class="err">Kunci sudah pernah dibuat. Setup dikunci agar data lama tidak menjadi tidak terbaca.</div></div>
<?php else: ?>
  <div class="card"><form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Kunci Simetris (passphrase, min. 12 karakter)</label>
    <input type="password" name="passphrase" required minlength="12">
    <small>Digunakan (AES-256-CBC) untuk mengunci private key RSA.</small><br>
    <button>Generate Pasangan Kunci</button></form></div>
<?php endif; ?></main></body></html>
