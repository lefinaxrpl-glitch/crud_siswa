
<?php
require_once "koneksi.php";

$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$nama = "Administrator";

$cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$cek->execute([$username]);

if ($cek->fetch()) {
    echo "Akun admin sudah tersedia.";
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, password, nama)
         VALUES (?, ?, ?)"
    );

    $stmt->execute([$username, $password, $nama]);

    echo "Akun admin berhasil dibuat.";
}
?>
