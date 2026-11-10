
<?php
require_once "auth.php";
require_once "koneksi.php";

$link_dashboard = ($_SESSION['role'] === 'admin')
    ? 'dashboard_admin.php'
    : 'dashboard_petugas.php';

// Mengambil semua siswa
$stmt = $pdo->query("SELECT * FROM siswa ORDER BY kelas ASC, nama ASC");
$daftar_siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mode = $_POST['mode'] ?? '';
$data_cetak = [];
$pesan = "";

if (isset($_POST['tampilkan'])) {

    if ($mode === 'semua') {
        $data_cetak = $daftar_siswa;

    } elseif ($mode === 'satu') {
        $id = $_POST['id_siswa'] ?? '';

        if ($id !== '' && ctype_digit((string)$id)) {
            $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id = ?");
            $stmt->execute([$id]);
            $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($siswa) {
                $data_cetak = [$siswa];
            }
        }

    } elseif ($mode === 'beberapa') {
        $ids = $_POST['id_siswa'] ?? [];

        if (is_array($ids) && count($ids) > 0) {
            $ids = array_unique(array_filter($ids, function ($id) {
                return ctype_digit((string)$id);
            }));

            if (count($ids) > 0) {
                $placeholder = implode(',', array_fill(0, count($ids), '?'));

                $stmt = $pdo->prepare(
                    "SELECT * FROM siswa WHERE id IN ($placeholder)
                     ORDER BY kelas ASC, nama ASC"
                );

                $stmt->execute(array_values($ids));
                $data_cetak = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }

    if (empty($data_cetak)) {
        $pesan = "Silakan pilih siswa atau pastikan data siswa tersedia.";
    }
}

$bulan = [
    1 => "Januari", "Februari", "Maret", "April",
    "Mei", "Juni", "Juli", "Agustus",
    "September", "Oktober", "November", "Desember"
];

$tanggal = date('d') . " " .
           $bulan[(int)date('m')] . " " .
           date('Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Data Siswa</title>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 30px;
        color: #222;
    }

    .navigasi, .tombol {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    button, .navigasi a {
        padding: 10px 16px;
        border: none;
        border-radius: 5px;
        color: white;
        text-decoration: none;
        cursor: pointer;
        background: #2563eb;
    }

    .dashboard { background: #172554 !important; }
    .kembali { background: #64748b !important; }
    .cetak { background: #16a34a; }

    .form-laporan {
        padding: 20px;
        background: #f1f5f9;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .pilihan {
        margin: 15px 0;
    }

    .daftar {
        display: none;
        margin: 15px 0;
        padding: 15px;
        background: white;
        border-radius: 5px;
        max-height: 250px;
        overflow-y: auto;
    }

    .item-siswa {
        display: block;
        margin: 10px 0;
    }

    select {
        padding: 9px;
        width: 100%;
        max-width: 400px;
    }

    .pesan {
        color: red;
        margin: 15px 0;
    }

    .header {
        text-align: center;
        border-bottom: 3px solid black;
        padding-bottom: 15px;
    }

    .judul {
        text-align: center;
        margin: 25px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid black;
        padding: 9px;
        font-size: 12px;
        text-align: left;
    }

    th {
        background: #e5e7eb;
        text-align: center;
    }

    .total {
        margin-top: 15px;
        font-weight: bold;
    }

    .tanggal {
        margin-top: 40px;
        text-align: right;
    }

    @media print {
        body { margin: 15px; }

        .navigasi, .form-laporan, .tombol, .pesan {
            display: none !important;
        }

        th {
            background: #e5e7eb !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
</head>

<body>

<div class="navigasi">
    <a href="<?= $link_dashboard; ?>" class="dashboard">🏠 Dashboard</a>
    <a href="index.php" class="kembali">👨‍🎓 Data Siswa</a>
</div>

<h2>Laporan Data Siswa</h2>
<p>Pilih jenis laporan yang ingin dicetak.</p>

<form method="POST" class="form-laporan">

    <strong>Jenis Laporan:</strong>

    <div class="pilihan">
        <label>
            <input type="radio" name="mode" value="semua"
                <?= $mode === 'semua' ? 'checked' : ''; ?>
                required>
            Semua Siswa
        </label>
        <br><br>

        <label>
            <input type="radio" name="mode" value="satu"
                <?= $mode === 'satu' ? 'checked' : ''; ?>>
            Satu Siswa
        </label>
        <br><br>

        <label>
            <input type="radio" name="mode" value="beberapa"
                <?= $mode === 'beberapa' ? 'checked' : ''; ?>>
            Beberapa Siswa
        </label>
    </div>

    <div id="pilih-satu" class="daftar">
        <strong>Pilih Satu Siswa:</strong>
        <br><br>

        <select name="id_siswa" id="select-satu">
            <option value="">-- Pilih Siswa --</option>

            <?php foreach ($daftar_siswa as $s): ?>
                <option value="<?= $s['id']; ?>">
                    <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="pilih-beberapa" class="daftar">
        <strong>Pilih Beberapa Siswa:</strong>
        <br><br>

        <button type="button" onclick="pilihSemua(true)">
            Pilih Semua
        </button>

        <button type="button" onclick="pilihSemua(false)">
            Hapus Pilihan
        </button>

        <?php foreach ($daftar_siswa as $s): ?>
            <label class="item-siswa">
                <input
                    type="checkbox"
                    name="id_siswa[]"
                    value="<?= $s['id']; ?>"
                    class="checkbox-siswa"
                >
                <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
            </label>
        <?php endforeach; ?>
    </div>

    <br>
    <button type="submit" name="tampilkan">
        Tampilkan Laporan
    </button>

</form>

<?php if ($pesan !== ''): ?>
    <p class="pesan"><?= htmlspecialchars($pesan); ?></p>
<?php endif; ?>

<?php if (!empty($data_cetak)): ?>

    <div class="tombol">
        <button class="cetak" onclick="window.print()">
            🖨️ Cetak Laporan
        </button>
    </div>

    <div class="header">
        <h2>LAPORAN DATA SISWA</h2>
        <p>SISTEM INFORMASI DATA SISWA</p>
        <p>SMK PGRI 2 PONOROGO</p>
    </div>

    <div class="judul">
        <h3>DAFTAR DATA SISWA</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Alamat</th>
            </tr>
        </thead>

        <tbody>
            <?php $no = 1; ?>

            <?php foreach ($data_cetak as $s): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($s['nis']); ?></td>
                    <td><?= htmlspecialchars($s['nama']); ?></td>
                    <td><?= htmlspecialchars($s['kelas']); ?></td>
                    <td><?= htmlspecialchars($s['jurusan']); ?></td>
                    <td><?= htmlspecialchars($s['alamat']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="total">
        Total Siswa: <?= count($data_cetak); ?> orang
    </p>

    <div class="tanggal">
        <p>Ponorogo, <?= $tanggal; ?></p>
        <br><br><br>
        <p><b>Admin / Petugas</b></p>
    </div>

<?php endif; ?>

<script>
const radioMode = document.querySelectorAll('input[name="mode"]');
const pilihSatu = document.getElementById('pilih-satu');
const pilihBeberapa = document.getElementById('pilih-beberapa');
const selectSatu = document.getElementById('select-satu');

function aturPilihan() {
    const mode = document.querySelector('input[name="mode"]:checked')?.value;

    pilihSatu.style.display = mode === 'satu' ? 'block' : 'none';
    pilihBeberapa.style.display = mode === 'beberapa' ? 'block' : 'none';

    selectSatu.required = mode === 'satu';
}

radioMode.forEach(radio => {
    radio.addEventListener('change', aturPilihan);
});

function pilihSemua(status) {
    document.querySelectorAll('.checkbox-siswa').forEach(checkbox => {
        checkbox.checked = status;
    });
}

aturPilihan();
</script>

</body>
</html>