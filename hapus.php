
<?php
require_once "auth.php";
require_once "koneksi.php";
require_once "log.php";

$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    die("ID siswa tidak valid.");
}

// Ambil data siswa sebelum dihapus
$stmt = $pdo->prepare("SELECT * FROM siswa WHERE id = ?");
$stmt->execute([$id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

// Hapus data siswa
$stmt = $pdo->prepare("DELETE FROM siswa WHERE id = ?");
$stmt->execute([$id]);

// Catat aktivitas penghapusan
catatAktivitas(
    $pdo,
    "Hapus Siswa",
    "Menghapus siswa bernama " . $siswa['nama'] . " dengan NIS " . $siswa['nis']
);

header("Location: index.php");
exit;
?>
