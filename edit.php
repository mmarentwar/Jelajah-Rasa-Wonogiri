<?php
session_start();
require_once "config.php";
require_once "includes/functions.php";
cek_login();

// --- Ambil ID dari URL (GET) dan pastikan angka ---
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    set_pesan('danger', 'Data tidak ditemukan.');
    header("Location: dashboard.php");
    exit;
}

// --- Ambil data lama dari database ---
$stmt = $koneksi->prepare("SELECT * FROM kuliner WHERE id_kuliner = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$lama) {
    set_pesan('danger', 'Data tidak ditemukan.');
    header("Location: dashboard.php");
    exit;
}

$kategori   = ambil_kategori($koneksi);
$errors     = [];
$foto_lama  = $lama['foto'];

// Isi awal form = data lama
$nilai = [
    'nama_makanan'   => $lama['nama_makanan'],
    'id_jenis'       => $lama['id_jenis'],
    'deskripsi'      => $lama['deskripsi'],
    'kisaran_harga'  => $lama['kisaran_harga'],
    'tanggal_dicoba' => $lama['tanggal_dicoba'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = [
        'nama_makanan'   => trim($_POST['nama_makanan'] ?? ''),
        'id_jenis'       => trim($_POST['id_jenis'] ?? ''),
        'deskripsi'      => trim($_POST['deskripsi'] ?? ''),
        'kisaran_harga'  => $_POST['kisaran_harga'] ?? '',
        'tanggal_dicoba' => $_POST['tanggal_dicoba'] ?? '',
    ];

    $errors = validasi_kuliner($nilai, $kategori);

    // Cek apakah ada foto baru
    $foto_baru = null;
    $ada_foto  = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;
    if (empty($errors) && $ada_foto) {
        $foto_baru = upload_foto($_FILES['foto'], $error_foto);
        if ($foto_baru === null) {
            $errors[] = $error_foto;
        }
    }

    if (empty($errors)) {
        // Tidak ada foto baru? Pakai foto lama
        $foto_simpan = ($foto_baru !== null) ? $foto_baru : $foto_lama;
        $id_jenis    = (int) $nilai['id_jenis'];

        $stmt = $koneksi->prepare(
            "UPDATE kuliner
             SET id_jenis = ?, nama_makanan = ?, deskripsi = ?, kisaran_harga = ?,
                 tanggal_dicoba = ?, foto = ?
             WHERE id_kuliner = ?"
        );
        $stmt->bind_param(
            "isssssi",
            $id_jenis, $nilai['nama_makanan'], $nilai['deskripsi'],
            $nilai['kisaran_harga'], $nilai['tanggal_dicoba'], $foto_simpan, $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            // Foto baru sudah aman tersimpan, baru foto lama dihapus
            if ($foto_baru !== null) {
                hapus_foto($foto_lama);
            }
            set_pesan('success', 'Data kuliner berhasil diperbarui.');
            header("Location: dashboard.php");
            exit;
        }

        // Gagal update: foto baru dibuang, foto lama tetap aman
        hapus_foto($foto_baru);
        $errors[] = "Data gagal diperbarui. Silakan coba lagi.";
        $stmt->close();
    }
}

$teks_tombol   = "Simpan Perubahan";
$judul_halaman = "Edit Kuliner";
require_once "includes/header.php";
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Edit Kuliner</h5>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $pesan): ?>
                                <li><?= e($pesan) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php require "includes/form_kuliner.php"; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>