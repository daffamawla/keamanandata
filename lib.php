<?php
require_once __DIR__ . '/config.php';
session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
session_start();

function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Token CSRF tidak valid.');
    }
}
function get_public_key(): ?array {
    return db()->query('SELECT * FROM app_keys ORDER BY id DESC LIMIT 1')->fetch() ?: null;
}
function fingerprint(string $pem): string { return hash('sha256', trim($pem)); }

/** Enkripsi hibrida: AES-256-GCM untuk data, RSA-OAEP untuk kunci AES. */
function hybrid_encrypt(string $plain, string $publicPem): array {
    $key = random_bytes(32);
    $iv  = random_bytes(12);
    $tag = '';
    $ct  = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
    if ($ct === false) throw new RuntimeException('Enkripsi AES gagal');
    if (!openssl_public_encrypt($key, $encKey, $publicPem, OPENSSL_PKCS1_OAEP_PADDING))
        throw new RuntimeException('Enkripsi RSA gagal');
    return [
        'ciphertext' => base64_encode($ct),
        'iv'         => base64_encode($iv),
        'tag'        => base64_encode($tag),
        'enc_key'    => base64_encode($encKey),
    ];
}
function hybrid_decrypt(array $row, $privateKey): ?string {
    if (!openssl_private_decrypt(base64_decode($row['enc_key']), $key, $privateKey, OPENSSL_PKCS1_OAEP_PADDING))
        return null;
    $plain = openssl_decrypt(base64_decode($row['ciphertext']), 'aes-256-gcm', $key,
        OPENSSL_RAW_DATA, base64_decode($row['iv']), base64_decode($row['tag']));
    return $plain === false ? null : $plain;
}
