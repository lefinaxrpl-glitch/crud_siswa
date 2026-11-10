
<?php
require_once "auth.php";
require_once "koneksi.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard_petugas.php");
    exit;
}

$total_siswa = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();

$total_petugas = $pdo->query(
    "SELECT COUNT(*) FROM users WHERE role = 'petugas'"
)->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #172554;
            color: white;
            position: fixed;
            padding: 25px 15px;
        }

        .sidebar h2 { text-align: center; }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px;
            margin: 7px 0;
            border-radius: 7px;
        }

        .sidebar a:hover { background: #2563eb; }

        .main {
            margin-left: 240px;
            padding: 30px;
        }

        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 3px 12px #00000010;
        }

        .card h3 { color: #64748b; }

        .card p {
            font-size: 30px;
            font-weight: bold;
            color: #2563eb;
        }

        .logout { background: #dc2626; }

        @media(max-width: 650px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>ADMIN PANEL</h2>
    <p>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></p>

    <a href="dashboard_admin.php">🏠 Dashboard</a>
    <a href="index.php">👨‍🎓 Data Siswa</a>
    <a href="tambah.php">➕ Tambah Siswa</a>
    <a href="kelola_petugas.php">👥 Data Petugas</a>
    <a href="laporan.php">📊 Laporan</a>
    <a href="riwayat.php">📋 Riwayat Aktivitas</a>
    <a href="ganti_password.php">🔑 Ganti Password</a>
    <a href="logout.php" class="logout"
       onclick="return confirm('Yakin ingin logout?')">🚪 Logout</a>
</div>

<div class="main">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang di Sistem Informasi Data Siswa.</p>

    <div class="cards">
        <div class="card">
            <h3>Total Siswa</h3>
            <p><?= $total_siswa; ?></p>
        </div>

        <div class="card">
            <h3>Total Petugas</h3>
            <p><?= $total_petugas; ?></p>
        </div>
    </div>
</div>

</body>
</html>
