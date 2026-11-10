
<?php
require_once "auth.php";
require_once "koneksi.php";

if ($_SESSION['role'] !== 'petugas') {
    header("Location: dashboard_admin.php");
    exit;
}

$total_siswa = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();

$total_kelas = $pdo->query(
    "SELECT COUNT(DISTINCT kelas) FROM siswa"
)->fetchColumn();

$total_jurusan = $pdo->query(
    "SELECT COUNT(DISTINCT jurusan) FROM siswa"
)->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #14532d;
            color: white;
            position: fixed;
            padding: 25px 15px;
        }

        .sidebar h2 {
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 13px;
            margin: 7px 0;
            border-radius: 7px;
        }

        .sidebar a:hover {
            background: #16a34a;
        }

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

        .card h3 {
            color: #64748b;
        }

        .card p {
            font-size: 30px;
            font-weight: bold;
            color: #16a34a;
        }

        .logout {
            background: #dc2626;
        }

        .welcome {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
        }

        @media(max-width: 650px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h2>PETUGAS PANEL</h2>

    <p>
        Halo, <?= htmlspecialchars($_SESSION['nama']); ?>
    </p>

    <a href="dashboard_petugas.php">🏠 Dashboard</a>
    <a href="index.php">👨‍🎓 Data Siswa</a>
    <a href="tambah.php">➕ Tambah Siswa</a>
    <a href="laporan.php">📊 Laporan Siswa</a>
    <a href="ganti_password.php">🔑 Ganti Password</a>
    <a href="logout.php" class="logout"
       onclick="return confirm('Yakin ingin logout?')">
        🚪 Logout
    </a>
</div>

<div class="main">

    <h1>Dashboard Petugas</h1>

    <p>Selamat datang di halaman petugas sekolah.</p>

    <div class="welcome">
        <h3>Selamat Bekerja!</h3>
        <p>
            Silakan gunakan menu di sebelah kiri
            untuk mengelola data siswa.
        </p>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Total Siswa</h3>
            <p><?= $total_siswa; ?></p>
        </div>

        <div class="card">
            <h3>Jumlah Kelas</h3>
            <p><?= $total_kelas; ?></p>
        </div>

        <div class="card">
            <h3>Jumlah Jurusan</h3>
            <p><?= $total_jurusan; ?></p>
        </div>

    </div>

</div>

</body>
</html>
