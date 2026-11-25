<?php
session_start();
require_once "config.php";
require_once "includes/functions.php";
cek_login();

// --- Statistik ---
$total_kuliner  = $koneksi->query("SELECT COUNT(*) AS total FROM kuliner")->fetch_assoc()['total'];
$total_kategori = $koneksi->query("SELECT COUNT(*) AS total FROM jenis_makanan")->fetch_assoc()['total'];

// --- Data kuliner + nama kategori (JOIN) ---
$sql = "SELECT k.id_kuliner, k.nama_makanan, k.kisaran_harga,
               k.tanggal_dicoba, k.foto, j.nama_jenis
        FROM kuliner k
        JOIN jenis_makanan j ON k.id_jenis = j.id_jenis
        ORDER BY k.id_kuliner DESC";
$data = $koneksi->query($sql);

$judul_halaman = "Dashboard";
require_once "includes/header.php";
?>

<!-- Kartu statistik -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-basket3 fs-1 stat-icon me-3"></i>
                <div>
                    <div class="text-muted">Jumlah Kuliner</div>
                    <div class="fs-3 fw-bold"><?= (int) $total_kuliner ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-tags fs-1 stat-icon me-3"></i>
                <div>
                    <div class="text-muted">Jumlah Kategori</div>
                    <div class="fs-3 fw-bold"><?= (int) $total_kategori ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel data -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">Daftar Kuliner Khas Wonogiri</h5>
            <a href="tambah.php" class="btn btn-wonogiri fw-semibold">
                <i class="bi bi-plus-lg"></i> Tambah Kuliner
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Makanan</th>
                        <th>Kategori</th>
                        <th>Kisaran Harga</th>
                        <th>Tanggal Dicoba</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($data->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $data->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <?php if (!empty($row['foto']) && file_exists("uploads/" . $row['foto'])): ?>
                                    <img src="uploads/<?= e($row['foto']) ?>" alt="<?= e($row['nama_makanan']) ?>"
                                         class="foto-thumb">
                                <?php else: ?>
                                    <div class="foto-kosong"><i class="bi bi-image fs-4"></i></div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?= e($row['nama_makanan']) ?></td>
                            <td><?= e($row['nama_jenis']) ?></td>
                            <td><?= label_harga($row['kisaran_harga']) ?></td>
                            <td><?= e(format_tanggal($row['tanggal_dicoba'])) ?></td>
                            <td class="text-center text-nowrap">
                                <a href="edit.php?id=<?= (int) $row['id_kuliner'] ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="hapus.php?id=<?= (int) $row['id_kuliner'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Yakin ingin menghapus data ini?');">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada data kuliner. Klik "Tambah Kuliner" untuk mulai.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>