
<?php
session_start();
require_once "koneksi.php";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$pesan = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE username = ?"
    );
    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard_petugas.php");
    }

    exit;
}
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CRUD Data Siswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">

        <h2>LOGIN</h2>
        <p>Aplikasi Data Siswa</p>

        <?php if ($pesan != ""): ?>
            <div class="login-error">
                <?= htmlspecialchars($pesan); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Username</label>
            <input
                type="text"
                name="username"
                placeholder="Masukkan username"
                required
            >

           
<label>Password</label>

<div class="password-wrapper">
    <input
        type="password"
        name="password"
        id="password"
        placeholder="Masukkan password"
        required
    >

    <button
        type="button"
        class="toggle-password"
        onclick="togglePassword()"
        id="toggleBtn"
        aria-label="Tampilkan password"
    >
        
    </button>
</div>


            <button type="submit" name="login">
                Login
            </button>

        </form>

        <p class="login-footer">
            Sistem Informasi Data Siswa
        </p>

    </div>
</div>
</body>
</html>
