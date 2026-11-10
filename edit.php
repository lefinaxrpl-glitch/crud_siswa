
<?php
require_once "auth.php";
require_once "koneksi.php";
require_once "log.php";

$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    die("ID siswa tidak valid.");
}

$stmt = $pdo->prepare("SELECT * FROM siswa WHERE id = ?");
$stmt->execute([$id]);

$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

$pesan = "";

if (isset($_POST['update'])) {
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
        $cek = $pdo->prepare(
            "SELECT COUNT(*) FROM siswa WHERE nis = ? AND id != ?"
        );
        $cek->execute([$nis, $id]);

        if ($cek->fetchColumn() > 0) {
            $pesan = "NIS sudah digunakan siswa lain!";
        } else {
            $sql = "UPDATE siswa SET
                    nis = ?,
                    nama = ?,
                    kelas = ?,
                    jurusan = ?,
                    alamat = ?
                    WHERE id = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nis, $nama, $kelas, $jurusan, $alamat, $id
            ]);

            // Catat aktivitas edit siswa
            catatAktivitas(
                $pdo,
                "Edit Siswa",
                "Mengubah data siswa bernama " . $nama . " dengan NIS " . $nis
            );

            header("Location: index.php");
            exit;
        }
    }

    $siswa['nis'] = $nis;
    $siswa['nama'] = $nama;
    $siswa['kelas'] = $kelas;
    $siswa['jurusan'] = $jurusan;
    $siswa['alamat'] = $alamat;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Edit Data Siswa</h2>
    <p>Silakan ubah informasi siswa.</p>

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
                value="<?= htmlspecialchars($siswa['nis']); ?>"
                required
            >

            <span id="nis-warning" class="nis-warning">!</span>
            <span id="nis-success" class="nis-success">✓</span>
        </div>

        <small id="nis-message">
            NIS harus tepat 10 angka.
        </small>

        <label>Nama</label>
        <input
            type="text"
            name="nama"
            value="<?= htmlspecialchars($siswa['nama']); ?>"
            required
        >

        <label>Kelas</label>
        <input
            type="text"
            name="kelas"
            value="<?= htmlspecialchars($siswa['kelas']); ?>"
            required
        >

        <label>Jurusan</label>
        <input
            type="text"
            name="jurusan"
            value="<?= htmlspecialchars($siswa['jurusan']); ?>"
            required
        >

        <label>Alamat</label>
        <textarea name="alamat"><?= htmlspecialchars($siswa['alamat'] ?? ''); ?></textarea>

        <button type="submit" name="update">
            Update Data
        </button>

        <a href="index.php">Kembali</a>

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
