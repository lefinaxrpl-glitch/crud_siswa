
<?php
require_once "auth.php";
require_once "koneksi.php";
require_once "log.php";

$pesan = "";
$jenis_pesan = "";

if (isset($_POST['simpan'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi'];

    // Mengambil password pengguna saat ini
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password_lama, $user['password'])) {
        $pesan = "Password lama tidak sesuai!";
        $jenis_pesan = "error";
    } elseif (strlen($password_baru) < 6) {
        $pesan = "Password baru minimal 6 karakter!";
        $jenis_pesan = "error";
    } elseif ($password_baru !== $konfirmasi) {
        $pesan = "Konfirmasi password tidak cocok!";
        $jenis_pesan = "error";
    } else {
        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

        $update = $pdo->prepare(
            "UPDATE users SET password = ? WHERE id = ?"
        );

        $update->execute([
            $password_hash,
            $_SESSION['user_id']
        ]);

        catatAktivitas(
            $pdo,
            "Ganti Password",
            "Mengubah password akun sendiri"
        );

        $pesan = "Password berhasil diganti!";
        $jenis_pesan = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .navigasi {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .navigasi a {
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .dashboard {
            background: #172554;
        }

        .kembali {
            background: #64748b;
        }

        .password-wrapper {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .password-wrapper input {
            flex: 1;
            min-width: 0;
        }

        .lihat {
            padding: 9px 12px;
            cursor: pointer;
        }

        .pesan {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .error {
            color: #b91c1c;
            background: #fee2e2;
        }

        .success {
            color: #166534;
            background: #dcfce7;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="navigasi">
        <a href="<?= $_SESSION['role'] === 'admin'
            ? 'dashboard_admin.php'
            : 'dashboard_petugas.php'; ?>" class="dashboard">
            🏠 Dashboard
        </a>

        <a href="index.php" class="kembali">
            👨‍🎓 Data Siswa
        </a>
    </div>

    <h2>Ganti Password</h2>
    <p>Silakan masukkan password lama dan password baru.</p>

    <?php if ($pesan !== ""): ?>
        <div class="pesan <?= $jenis_pesan; ?>">
            <?= htmlspecialchars($pesan); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Password Lama</label>
        <div class="password-wrapper">
            <input type="password" name="password_lama"
                id="password_lama" required>
            <button type="button" class="lihat"
                onclick="togglePassword('password_lama', this)">
                Lihat
            </button>
        </div>

        <label>Password Baru</label>
        <div class="password-wrapper">
            <input type="password" name="password_baru"
                id="password_baru" minlength="6" required>
            <button type="button" class="lihat"
                onclick="togglePassword('password_baru', this)">
                Lihat
            </button>
        </div>

        <label>Konfirmasi Password Baru</label>
        <div class="password-wrapper">
            <input type="password" name="konfirmasi"
                id="konfirmasi" minlength="6" required>
            <button type="button" class="lihat"
                onclick="togglePassword('konfirmasi', this)">
                Lihat
            </button>
        </div>

        <button type="submit" name="simpan">
            Simpan Password
        </button>

    </form>
</div>

<script>
function togglePassword(id, tombol) {
    const input = document.getElementById(id);

    if (input.type === "password") {
        input.type = "text";
        tombol.textContent = "Sembunyikan";
    } else {
        input.type = "password";
        tombol.textContent = "Lihat";
    }
}
</script>

</body>
</html>
