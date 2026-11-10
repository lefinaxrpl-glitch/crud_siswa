
<?php
require_once "auth.php";
require_once "koneksi.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard_petugas.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT id, username, nama, role
     FROM users
     WHERE role = 'petugas'
     ORDER BY id DESC"
);
$stmt->execute();

$data_petugas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Petugas</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            max-width: 1000px;
            margin: auto;
        }

        h2 {
            color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #14532d;
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            background: #14532d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .kosong {
            text-align: center;
            color: #64748b;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>👥 Data Petugas</h2>

    <a href="dashboard_admin.php" class="btn">
        Kembali ke Dashboard
    </a>

    <table>
        <tr>
            <th>No</th>
            <th>Username</th>
            <th>Nama Petugas</th>
            <th>Role</th>
        </tr>

        <?php if (count($data_petugas) > 0): ?>

            <?php $no = 1; ?>

            <?php foreach ($data_petugas as $petugas): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($petugas['username']); ?></td>
                    <td><?= htmlspecialchars($petugas['nama']); ?></td>
                    <td><?= htmlspecialchars($petugas['role']); ?></td>
                </tr>
            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="4" class="kosong">
                    Belum ada data petugas.
                </td>
            </tr>

        <?php endif; ?>

    </table>

</div>

</body>
</html>
