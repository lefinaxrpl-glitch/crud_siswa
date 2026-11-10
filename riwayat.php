
<?php
require_once "auth.php";
require_once "koneksi.php";

// Khusus admin
if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Halaman ini khusus admin.");
}

// Ambil riwayat aktivitas
$stmt = $pdo->query("
    SELECT * FROM log_aktivitas
    ORDER BY waktu DESC
");

$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aktivitas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Riwayat Aktivitas</h2>
    <p>Daftar aktivitas pengguna dalam sistem.</p>

    <a href="dashboard_admin.php">Kembali ke Dashboard</a>

    <br><br>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Aktivitas</th>
                <th>Detail</th>
                <th>Waktu</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($riwayat) > 0): ?>
                <?php $no = 1; ?>
                <?php foreach ($riwayat as $data): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($data['username']); ?></td>
                        <td><?= htmlspecialchars($data['aktivitas']); ?></td>
                        <td><?= htmlspecialchars($data['detail'] ?? ''); ?></td>
                        <td><?= htmlspecialchars($data['waktu']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Belum ada riwayat aktivitas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
