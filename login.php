<?php
session_start();
require_once "config.php";
require_once "includes/functions.php";

// Kalau sudah login, tidak perlu lihat form lagi
if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit;
}

$errors   = [];   // array penampung pesan kesalahan
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- Validasi input ---
    if ($username === '') {
        $errors[] = "Username wajib diisi.";
    } elseif (strlen($username) > 50) {
        $errors[] = "Username maksimal 50 karakter.";
    }

    if ($password === '') {
        $errors[] = "Password wajib diisi.";
    }

    // --- Cek ke database kalau input sudah valid ---
    if (empty($errors)) {
        $stmt = $koneksi->prepare(
            "SELECT id_user, username, password FROM users WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['id_user']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Jelajah Rasa Wonogiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="text-center fw-bold mb-1">Jelajah Rasa Wonogiri</h4>
                    <p class="text-center text-muted mb-4">Masuk untuk mengelola katalog</p>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger py-2">
                            <?php foreach ($errors as $pesan): ?>
                                <div><?= e($pesan) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <a href="index.php" class="d-block text-center mt-3 small">&larr; Kembali ke beranda</a>
                    <form method="POST" action="login.php" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username"
                                   name="username" value="<?= e($username) ?>" autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password"
                                   name="password">
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-semibold">Masuk</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>