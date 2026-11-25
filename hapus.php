<?php
session_start();
require_once "config.php";
require_once "includes/functions.php";
cek_login();

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    set_pesan('danger', 'Data tidak ditemukan.');
    header("Location: dashboard.php");
    exit;
}

// 1. Ambil nama foto dulu (sebelum barisnya hilang)
$stmt = $koneksi->prepare("SELECT foto FROM kuliner WHERE id_kuliner = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    set_pesan('danger', 'Data tidak ditemukan.');
    header("Location: dashboard.php");
    exit;
}

// 2. Hapus baris dari database
$stmt = $koneksi->prepare("DELETE FROM kuliner WHERE id_kuliner = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // 3. Setelah data terhapus, hapus file fotonya
    hapus_foto($data['foto']);
    set_pesan('success', 'Data kuliner berhasil dihapus.');
} else {
    set_pesan('danger', 'Data gagal dihapus.');
}
$stmt->close();

header("Location: dashboard.php");
exit;