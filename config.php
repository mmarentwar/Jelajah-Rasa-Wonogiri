<?php
// =====================================================
// config.php - Koneksi database (MySQLi)
// =====================================================
date_default_timezone_set('Asia/Jakarta');
// 1. Data koneksi (default XAMPP)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kuliner_wonogiri_3548";

// 2. Membuat koneksi
$koneksi = new mysqli($host, $user, $pass, $db);

// 3. Cek apakah koneksi berhasil
if ($koneksi->connect_error) {
    die("Koneksi database gagal. Periksa pengaturan di config.php.");
}

// 4. Atur charset supaya karakter Indonesia tampil benar
$koneksi->set_charset("utf8mb4");