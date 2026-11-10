
<?php
require_once "auth.php";
require_once "koneksi.php";

// Hanya admin yang boleh masuk
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard_petugas.php");
    exit;
}

$pesan = "";

// Tambah petugas
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($nama === "" || $username === "" || $password === "") {
        $pesan = "Semua kolom wajib diisi!";
    } elseif (strlen($password) < 6) {
        $pesan = "Password minimal 6 karakter!";
    } else {
        $cek = $pdo->prepare(
            "SELECT id FROM users WHERE username = ?"
        );
        $cek->execute([$username]);

        if ($cek->fetch()) {
            $pesan = "Username sudah digunakan!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (nama, username, password, role)
                 VALUES (?, ?, ?, 'petugas')"
            );
            $stmt->execute([$nama, $username, $hash]);

            header("Location: kelola_petugas.php?status=berhasil");
            exit;
        }
    }
}

// Hapus petugas
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    $stmt = $pdo->prepare(
        "DELETE FROM users WHERE id = ? AND role = 'petugas'"
    );
    $stmt->execute([$id]);

    header("Location: kelola_petugas.php?status=hapus");
    exit;
}

// Ambil daftar petugas
$stmt = $pdo->query(
    "SELECT id, nama, username
     FROM users
     WHERE role = 'petugas'
     ORDER BY id DESC"
);
$petugas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'berhasil') {
        $pesan = "Petugas berhasil ditambahkan!";
    } elseif ($_GET['status'] === 'hapus') {
        $pesan = "Petugas berhasil dihapus!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Petugas</title>

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

        .form-box, .table-box {
            background: white;
            padding: 22px;
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: 0 3px 12px #00000010;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 11px 18px;
            border-radius: 6px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #eff6ff;
        }

        .hapus {
            color: #dc2626;
            text-decoration: none;
        }

        .pesan {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .logout {
            background: #dc2626;
        }

        @media(max-width: 650px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main { margin-left: 0; }

            .table-box { overflow-x: auto; }
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
    <a href="logout.php" class="logout"
       onclick="return confirm('Yakin ingin logout?')">
        🚪 Logout
    </a>
</div>

<div class="main">

    <h1>Kelola Petugas</h1>
    <p>Tambah dan kelola akun petugas sekolah.</p>

    <?php if ($pesan !== ""): ?>
        <div class="pesan">
            <?= htmlspecialchars($pesan); ?>
        </div>
    <?php endif; ?>

    <div class="form-box">
        <h3>Tambah Petugas</h3>

        <form method="POST">
            <label>Nama Petugas</label>
            <input type="text" name="nama"
                   placeholder="Masukkan nama petugas" required>

            <label>Username</label>
            <input type="text" name="username"
                   placeholder="Masukkan username" required>

            <label>Password</label>
            <input type="password" name="password"
                   placeholder="Minimal 6 karakter"
                   minlength="6" required>

            <button type="submit" name="tambah">
                + Tambah Petugas
            </button>
        </form>
    </div>

    <div class="table-box">
        <h3>Daftar Petugas</h3>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($petugas) > 0): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($petugas as $p): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($p['nama']); ?></td>
                            <td><?= htmlspecialchars($p['username']); ?></td>
                            <td>
                                <a class="hapus"
                                   href="kelola_petugas.php?hapus=<?= $p['id']; ?>"
                                   onclick="return confirm('Yakin ingin menghapus petugas ini?')">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Belum ada petugas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
