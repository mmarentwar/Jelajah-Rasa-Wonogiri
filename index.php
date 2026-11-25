<?php
session_start();
require_once "config.php";
require_once "includes/functions.php";

$kategori = ambil_kategori($koneksi);

// --- Ambil filter dari URL (GET) ---
$q        = trim($_GET['q'] ?? '');
$id_jenis = filter_var($_GET['kategori'] ?? null, FILTER_VALIDATE_INT);

// --- Susun query sesuai filter yang dipakai ---
$sql = "SELECT k.nama_makanan, k.deskripsi, k.kisaran_harga,
               k.tanggal_dicoba, k.foto, j.nama_jenis
        FROM kuliner k
        JOIN jenis_makanan j ON k.id_jenis = j.id_jenis";

$syarat = [];
$tipe   = "";
$param  = [];

if ($q !== '') {
    $syarat[] = "k.nama_makanan LIKE ?";
    $tipe    .= "s";
    $param[]  = "%" . $q . "%";
}
if ($id_jenis) {
    $syarat[] = "k.id_jenis = ?";
    $tipe    .= "i";
    $param[]  = $id_jenis;
}
if (!empty($syarat)) {
    $sql .= " WHERE " . implode(" AND ", $syarat);
}
$sql .= " ORDER BY k.nama_makanan ASC";

$stmt = $koneksi->prepare($sql);
if (!empty($param)) {
    $stmt->bind_param($tipe, ...$param);
}
$stmt->execute();
$hasil = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jelajah Rasa Wonogiri - Eksplorasi Kuliner Khas Kabupaten Wonogiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark navbar-wonogiri">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-egg-fried"></i> Jelajah Rasa Wonogiri
        </a>
        <?php if (isset($_SESSION['id_user'])): ?>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-in-right"></i> Login Admin
            </a>
        <?php endif; ?>
    </div>
</nav>

<!-- Hero -->
<header class="hero-wonogiri text-center">
    <div class="container py-5">
        <h1 class="fw-bold">Jelajah Rasa Wonogiri</h1>
        <p class="lead mb-0">Eksplorasi kuliner khas Kabupaten Wonogiri: makanan berat, camilan, minuman, hingga oleh-oleh.</p>
    </div>
</header>

<div class="container py-4">

    <!-- Pencarian dan filter -->
    <form method="GET" action="index.php" class="row g-2 mb-4">
        <div class="col-12 col-md-6">
            <input type="text" name="q" class="form-control" placeholder="Cari nama makanan..."
                   value="<?= e($q) ?>">
        </div>
        <div class="col-8 col-md-4">
            <select name="kategori" class="form-select">
                <option value="">Semua kategori</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= (int) $k['id_jenis'] ?>"
                        <?= ((int) $id_jenis === (int) $k['id_jenis']) ? 'selected' : '' ?>>
                        <?= e($k['nama_jenis']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-4 col-md-2 d-grid">
            <button type="submit" class="btn btn-wonogiri"><i class="bi bi-search"></i> Cari</button>
        </div>
    </form>

    <!-- Daftar kuliner -->
    <?php if ($hasil->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($row = $hasil->fetch_assoc()): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm kartu-kuliner">
                        <?php if (!empty($row['foto']) && file_exists("uploads/" . $row['foto'])): ?>
                            <img src="uploads/<?= e($row['foto']) ?>" class="card-img-top kartu-foto"
                                 alt="<?= e($row['nama_makanan']) ?>">
                        <?php else: ?>
                            <div class="kartu-foto kartu-foto-kosong">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <span class="badge bg-light text-dark border mb-2"><?= e($row['nama_jenis']) ?></span>
                            <h5 class="card-title fw-bold"><?= e($row['nama_makanan']) ?></h5>
                            <p class="card-text text-muted">
                                <?= e(mb_strimwidth($row['deskripsi'], 0, 110, '...')) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pb-3">
                            <?= label_harga($row['kisaran_harga']) ?>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event"></i>
                                Dicoba <?= e(format_tanggal($row['tanggal_dicoba'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-emoji-frown fs-1"></i>
            <p class="mt-2 mb-0">Belum ada kuliner yang cocok dengan pencarianmu.</p>
        </div>
    <?php endif; ?>

</div>

<footer class="text-center text-muted py-4 small">
    &copy; <?= date('Y') ?> Jelajah Rasa Wonogiri
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>