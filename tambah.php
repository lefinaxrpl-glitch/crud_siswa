
<?php
require_once "auth.php";
require_once "koneksi.php";
require_once "log.php";

// Menentukan dashboard berdasarkan role
$link_dashboard = ($_SESSION['role'] === 'admin')
    ? 'dashboard_admin.php'
    : 'dashboard_petugas.php';

$pesan = "";
$nis = "";
$nama = "";
$kelas = "";
$jurusan = "";
$alamat = "";

if (isset($_POST['simpan'])) {
    $nis = trim($_POST['nis']);
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    $jurusan = trim($_POST['jurusan']);
    $alamat = trim($_POST['alamat']);

    if (!preg_match('/^[0-9]{10}$/', $nis)) {
        $pesan = "NIS wajib tepat 10 angka!";
    } elseif ($nama == "" || $kelas == "" || $jurusan == "") {
        $pesan = "Semua kolom wajib diisi kecuali alamat!";
    } else {
        $cek = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE nis = ?");
        $cek->execute([$nis]);

        if ($cek->fetchColumn() > 0) {
            $pesan = "NIS sudah terdaftar!";
        } else {
            $sql = "INSERT INTO siswa
                    (nis, nama, kelas, jurusan, alamat)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nis, $nama, $kelas, $jurusan, $alamat
            ]);

            catatAktivitas(
                $pdo,
                "Tambah Siswa",
                "Menambahkan siswa bernama " . $nama . " dengan NIS " . $nis
            );

            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Siswa</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .navigasi {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .navigasi a {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-dashboard {
            background: #172554;
        }

        .btn-data {
            background: #64748b;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="navigasi">
        <a href="<?= $link_dashboard; ?>" class="btn-dashboard">
            🏠 Dashboard
        </a>

        <a href="index.php" class="btn-data">
            👨‍🎓 Data Siswa
        </a>
    </div>

    <h2>Tambah Data Siswa</h2>
    <p>Silakan masukkan data siswa baru.</p>

    <?php if ($pesan != ""): ?>
        <p style="color:red;">
            <?= htmlspecialchars($pesan); ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>NIS</label>

        <div class="nis-wrapper">
            <input
                type="text"
                name="nis"
                id="nis"
                minlength="10"
                maxlength="10"
                pattern="[0-9]{10}"
                inputmode="numeric"
                placeholder="Masukkan 10 digit NIS"
                value="<?= htmlspecialchars($nis); ?>"
                required
            >

            <span id="nis-warning" class="nis-warning">!</span>
            <span id="nis-success" class="nis-success">✓</span>
        </div>

        <small id="nis-message">
            NIS harus tepat 10 angka.
        </small>

        <label>Nama</label>
        <input type="text" name="nama"
            value="<?= htmlspecialchars($nama); ?>" required>

        <label>Kelas</label>
        <input type="text" name="kelas"
            value="<?= htmlspecialchars($kelas); ?>" required>

        <label>Jurusan</label>
        <input type="text" name="jurusan"
            value="<?= htmlspecialchars($jurusan); ?>" required>

        <label>Alamat</label>
        <textarea name="alamat"><?= htmlspecialchars($alamat); ?></textarea>

        <button type="submit" name="simpan">
            Simpan Data
        </button>

    </form>
</div>

<script>
const nis = document.getElementById('nis');
const warning = document.getElementById('nis-warning');
const success = document.getElementById('nis-success');
const message = document.getElementById('nis-message');

function validasiNIS() {
    nis.value = nis.value.replace(/[^0-9]/g, '').slice(0, 10);

    if (nis.value.length !== 10) {
        warning.style.display = 'inline-flex';
        success.style.display = 'none';
        message.textContent = 'NIS harus tepat 10 angka!';
        message.style.color = 'red';
    } else {
        warning.style.display = 'none';
        success.style.display = 'inline-flex';
        message.textContent = 'NIS sudah benar.';
        message.style.color = 'green';
    }
}

nis.addEventListener('input', validasiNIS);
validasiNIS();
</script>

</body>
</html>
