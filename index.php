
<?php
require_once "auth.php";
require_once "koneksi.php";

// Menentukan dashboard berdasarkan role
$link_dashboard = ($_SESSION['role'] === 'admin')
    ? 'dashboard_admin.php'
    : 'dashboard_petugas.php';

// Mengambil input pencarian dan filter
$cari = trim($_GET['cari'] ?? '');
$kelas = trim($_GET['kelas'] ?? '');
$jurusan = trim($_GET['jurusan'] ?? '');

$where = [];
$params = [];

if ($cari !== '') {
    $where[] = "(nis LIKE ? OR nama LIKE ?)";
    $params[] = "%$cari%";
    $params[] = "%$cari%";
}

if ($kelas !== '') {
    $where[] = "kelas = ?";
    $params[] = $kelas;
}

if ($jurusan !== '') {
    $where[] = "jurusan = ?";
    $params[] = $jurusan;
}

$sqlWhere = count($where) > 0
    ? " WHERE " . implode(" AND ", $where)
    : "";

// Pilihan kelas dan jurusan
$daftar_kelas = $pdo->query(
    "SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC"
)->fetchAll(PDO::FETCH_COLUMN);

$daftar_jurusan = $pdo->query(
    "SELECT DISTINCT jurusan FROM siswa ORDER BY jurusan ASC"
)->fetchAll(PDO::FETCH_COLUMN);

// Pagination
$batas = 10;
$halaman = max(1, (int)($_GET['halaman'] ?? 1));

$stmtTotal = $pdo->prepare(
    "SELECT COUNT(*) FROM siswa" . $sqlWhere
);
$stmtTotal->execute($params);

$total_data = (int)$stmtTotal->fetchColumn();
$total_halaman = max(1, ceil($total_data / $batas));
$halaman = min($halaman, $total_halaman);
$posisi = ($halaman - 1) * $batas;

// Mengambil data siswa
$sql = "SELECT * FROM siswa" . $sqlWhere .
       " ORDER BY id DESC LIMIT :batas OFFSET :posisi";

$stmt = $pdo->prepare($sql);

foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param);
}

$stmt->bindValue(':batas', $batas, PDO::PARAM_INT);
$stmt->bindValue(':posisi', $posisi, PDO::PARAM_INT);
$stmt->execute();

$data_siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Link pagination
$queryLink = [
    'cari' => $cari,
    'kelas' => $kelas,
    'jurusan' => $jurusan
];

function linkHalaman($nomor, $queryLink) {
    $queryLink['halaman'] = $nomor;
    return '?' . http_build_query($queryLink);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .filter-box {
            background: white;
            padding: 18px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 2px 8px #00000010;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-form input,
        .filter-form select {
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            min-width: 150px;
        }

        .btn-cari {
            padding: 11px 18px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-reset {
            padding: 11px 18px;
            background: #64748b;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-dashboard {
            display: inline-block;
            padding: 10px 16px;
            background: #172554;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 8px;
        }

        .btn-dashboard:hover {
            background: #2563eb;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 25px 0;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: 9px 14px;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid #2563eb;
            color: #2563eb;
        }

        .pagination a:hover,
        .pagination .aktif {
            background: #2563eb;
            color: white;
        }

        .pagination .disabled {
            color: #999;
            border-color: #ddd;
        }

        .info-pagination {
            text-align: center;
            margin-top: 15px;
            color: #666;
        }

        .hasil-kosong {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Data Siswa</h2>

    <p>
        Selamat datang,
        <b><?= htmlspecialchars($_SESSION['nama']); ?></b>
    </p>

    <a href="<?= $link_dashboard; ?>" class="btn-dashboard">
        🏠 Dashboard
    </a>

    <a href="logout.php" class="btn-logout"
       onclick="return confirm('Yakin ingin logout?')">
        Logout
    </a>

    <br><br>

    <a href="tambah.php" class="btn-tambah">+ Tambah Siswa</a>

    <div class="filter-box">
        <h3>Pencarian dan Filter Data</h3>

        <form method="GET" class="filter-form">

            <input
                type="text"
                name="cari"
                placeholder="Cari NIS atau Nama..."
                value="<?= htmlspecialchars($cari); ?>"
            >

            <select name="kelas">
                <option value="">Semua Kelas</option>

                <?php foreach ($daftar_kelas as $k): ?>
                    <option value="<?= htmlspecialchars($k); ?>"
                        <?= $kelas === $k ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($k); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="jurusan">
                <option value="">Semua Jurusan</option>

                <?php foreach ($daftar_jurusan as $j): ?>
                    <option value="<?= htmlspecialchars($j); ?>"
                        <?= $jurusan === $j ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($j); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-cari">
                Cari / Filter
            </button>

            <a href="index.php" class="btn-reset">
                Reset
            </a>

        </form>
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
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php if (count($data_siswa) > 0): ?>

                <?php $no = $posisi + 1; ?>

                <?php foreach ($data_siswa as $siswa): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($siswa['nis']); ?></td>
                        <td><?= htmlspecialchars($siswa['nama']); ?></td>
                        <td><?= htmlspecialchars($siswa['kelas']); ?></td>
                        <td><?= htmlspecialchars($siswa['jurusan']); ?></td>
                        <td><?= htmlspecialchars($siswa['alamat']); ?></td>
                        <td>
                            <a href="edit.php?id=<?= $siswa['id']; ?>">
                                Edit
                            </a>
                            |
                            <a href="hapus.php?id=<?= $siswa['id']; ?>"
                               onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="7" class="hasil-kosong">
                        Data siswa tidak ditemukan.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p class="info-pagination">
        Menampilkan <?= count($data_siswa); ?>
        dari <?= $total_data; ?> siswa
    </p>

    <?php if ($total_halaman > 1): ?>

        <div class="pagination">

            <?php if ($halaman > 1): ?>
                <a href="<?= linkHalaman($halaman - 1, $queryLink); ?>">
                    « Sebelumnya
                </a>
            <?php else: ?>
                <span class="disabled">« Sebelumnya</span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>

                <?php if ($i == $halaman): ?>
                    <span class="aktif"><?= $i; ?></span>
                <?php else: ?>
                    <a href="<?= linkHalaman($i, $queryLink); ?>">
                        <?= $i; ?>
                    </a>
                <?php endif; ?>

            <?php endfor; ?>

            <?php if ($halaman < $total_halaman): ?>
                <a href="<?= linkHalaman($halaman + 1, $queryLink); ?>">
                    Selanjutnya »
                </a>
            <?php else: ?>
                <span class="disabled">Selanjutnya »</span>
            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
