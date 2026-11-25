<?php
// Variabel $judul_halaman diisi dari halaman yang memanggil
$judul_halaman = $judul_halaman ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul_halaman) ?> - Jelajah Rasa Wonogiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark navbar-wonogiri">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-egg-fried"></i> Jelajah Rasa Wonogiri
        </a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3 d-none d-sm-inline">
                <i class="bi bi-person-circle"></i> <?= e($_SESSION['username']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">
<?php tampil_pesan(); ?>