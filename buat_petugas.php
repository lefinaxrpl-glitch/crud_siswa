
<?php
require_once "koneksi.php";

$username = "petugas";
$password = password_hash("password", PASSWORD_DEFAULT);
$nama = "Petugas Sekolah";

$cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$cek->execute([$username]);

if ($cek->fetch()) {
    $stmt = $pdo->prepare(
        "UPDATE users SET password = ?, nama = ?, role = 'petugas'
         WHERE username = ?"
    );
    $stmt->execute([$password, $nama, $username]);

    echo "Akun Petugas berhasil diperbarui!";
} else {
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, password, nama, role)
         VALUES (?, ?, ?, 'petugas')"
    );
    $stmt->execute([$username, $password, $nama]);

    echo "Akun Petugas berhasil dibuat!";
}
?>
