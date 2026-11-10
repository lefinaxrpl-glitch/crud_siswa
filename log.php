
<?php
function catatAktivitas($pdo, $aktivitas, $detail = "")
{
    if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
        return;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO log_aktivitas
        (user_id, username, aktivitas, detail)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->execute([
        $_SESSION['user_id'],
        $_SESSION['username'],
        $aktivitas,
        $detail
    ]);
}
?>
