<?php
session_start();
require_once "config.php";
require_once "includes/functions.php";
cek_login();

$kategori = ambil_kategori($koneksi);
$errors   = [];
$nilai    = [
    'nama_makanan'   => '',
    'id_jenis'       => '',
    'deskripsi'      => '',
    'kisaran_harga'  => '',
    'tanggal_dicoba' => date('Y-m-d'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form (POST)
    $nilai = [
        'nama_makanan'   => trim($_POST['nama_makanan'] ?? ''),
        'id_jenis'       => trim($_POST['id_jenis'] ?? ''),
        'deskripsi'      => trim($_POST['deskripsi'] ?? ''),
        'kisaran_harga'  => $_POST['kisaran_harga'] ?? '',
        'tanggal_dicoba' => $_POST['tanggal_dicoba'] ?? '',
    ];

    $errors = validasi_kuliner($nilai, $kategori);

    // Foto bersifat opsional. Diproses hanya kalau data lain sudah valid
    $foto = null;
    $ada_foto = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;
    if (empty($errors) && $ada_foto) {
        $foto = upload_foto($_FILES['foto'], $error_foto);
        if ($foto === null) {
            $errors[] = $error_foto;
        }
    }

    if (empty($errors)) {
        $id_jenis = (int) $nilai['id_jenis'];
        $stmt = $koneksi->prepare(
            "INSERT INTO kuliner (id_jenis, nama_makanan, deskripsi, kisaran_harga, tanggal_dicoba, foto)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "isssss",
            $id_jenis, $nilai['nama_makanan'], $nilai['deskripsi'],
            $nilai['kisaran_harga'], $nilai['tanggal_dicoba'], $foto
        );

        if ($stmt->execute()) {
            $stmt->close();
            set_pesan('success', 'Data kuliner berhasil ditambahkan.');
            header("Location: dashboard.php");
            exit;
        }

        // Gagal simpan: foto yang sudah terupload dibersihkan lagi
        hapus_foto($foto);
        $errors[] = "Data gagal disimpan. Silakan coba lagi.";
        $stmt->close();
    }
}

$foto_lama     = null;
$teks_tombol   = "Simpan";
$judul_halaman = "Tambah Kuliner";
require_once "includes/header.php";
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Tambah Kuliner Baru</h5>

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