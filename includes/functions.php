<?php
// =====================================================
// functions.php - Kumpulan function buatan sendiri
// =====================================================

// Aturan upload foto
const FOTO_MAKS     = 2 * 1024 * 1024;               // 2 MB
const FOTO_EKSTENSI = ['jpg', 'jpeg', 'png', 'webp'];
const FOTO_MIME     = ['image/jpeg', 'image/png', 'image/webp'];
const FOLDER_UPLOAD = __DIR__ . '/../uploads/';

// Mengamankan teks sebelum ditampilkan ke HTML (mencegah XSS)
function e($teks)
{
    return htmlspecialchars($teks ?? '', ENT_QUOTES, 'UTF-8');
}

// Halaman hanya bisa dibuka setelah login
function cek_login()
{
    if (!isset($_SESSION['id_user'])) {
        header("Location: login.php");
        exit;
    }
}

// Pesan sekali tampil (flash message), mis. "Data berhasil ditambahkan"
function set_pesan($tipe, $teks)
{
    $_SESSION['pesan'] = ['tipe' => $tipe, 'teks' => $teks];
}

function tampil_pesan()
{
    if (isset($_SESSION['pesan'])) {
        $p = $_SESSION['pesan'];
        echo '<div class="alert alert-' . e($p['tipe']) . ' alert-dismissible fade show" role="alert">'
           . e($p['teks'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['pesan']);
    }
}

// Mengubah 2026-10-05 menjadi "05 Oktober 2026"
function format_tanggal($tanggal)
{
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $waktu = strtotime($tanggal);
    if ($waktu === false) {
        return '-';
    }

    return date('d', $waktu) . ' ' . $bulan[(int) date('n', $waktu)] . ' ' . date('Y', $waktu);
}

// Mengubah nilai ENUM harga menjadi badge berwarna
function label_harga($harga)
{
    $daftar = [
        'murah'  => ['Murah',  'success'],
        'sedang' => ['Sedang', 'warning'],
        'mahal'  => ['Mahal',  'danger'],
    ];

    if (!isset($daftar[$harga])) {
        return '<span class="badge text-bg-secondary">-</span>';
    }

    [$teks, $warna] = $daftar[$harga];
    return '<span class="badge text-bg-' . $warna . '">' . $teks . '</span>';
}

// Mengambil semua kategori untuk pilihan <select>
function ambil_kategori($koneksi)
{
    $hasil = $koneksi->query("SELECT id_jenis, nama_jenis FROM jenis_makanan ORDER BY id_jenis");
    $daftar = [];
    while ($row = $hasil->fetch_assoc()) {
        $daftar[] = $row;
    }
    return $daftar;
}

// Validasi input form tambah/edit. Mengembalikan array pesan error (kosong = valid)
function validasi_kuliner($data, $kategori)
{
    $errors = [];

    // Nama makanan
    if ($data['nama_makanan'] === '') {
        $errors[] = "Nama makanan wajib diisi.";
    } elseif (mb_strlen($data['nama_makanan']) > 150) {
        $errors[] = "Nama makanan maksimal 150 karakter.";
    }

    // Kategori harus benar-benar ada di tabel jenis_makanan
    $id_valid = array_map('intval', array_column($kategori, 'id_jenis'));
    if ($data['id_jenis'] === '' || !in_array((int) $data['id_jenis'], $id_valid, true)) {
        $errors[] = "Kategori wajib dipilih.";
    }

    // Deskripsi
    if ($data['deskripsi'] === '') {
        $errors[] = "Deskripsi wajib diisi.";
    }

    // Kisaran harga harus salah satu nilai ENUM
    if (!in_array($data['kisaran_harga'], ['murah', 'sedang', 'mahal'], true)) {
        $errors[] = "Kisaran harga wajib dipilih.";
    }

    // Tanggal: format benar dan tidak boleh di masa depan
    $tgl = $data['tanggal_dicoba'];
    $d = DateTime::createFromFormat('Y-m-d', $tgl);
    if (!$d || $d->format('Y-m-d') !== $tgl) {
        $errors[] = "Tanggal dicoba tidak valid.";
    } elseif ($tgl > date('Y-m-d')) {
        $errors[] = "Tanggal dicoba tidak boleh lebih dari hari ini.";
    }

    return $errors;
}

// Validasi + simpan foto. Berhasil: return nama file baru. Gagal: return null, pesan di $error
function upload_foto($file, &$error)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            $error = "Ukuran foto terlalu besar. Maksimal 2 MB.";
        } else {
            $error = "Foto gagal diunggah. Silakan coba lagi.";
        }
        return null;
    }

    if ($file['size'] > FOTO_MAKS) {
        $error = "Ukuran foto terlalu besar. Maksimal 2 MB.";
        return null;
    }

    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, FOTO_EKSTENSI, true)) {
        $error = "Format foto harus JPG, JPEG, PNG, atau WEBP.";
        return null;
    }

    // Cek isi file yang sebenarnya, bukan cuma namanya
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (!in_array($finfo->file($file['tmp_name']), FOTO_MIME, true)) {
        $error = "File yang diunggah bukan gambar yang valid.";
        return null;
    }

    // Buat folder uploads otomatis kalau belum ada
    if (!is_dir(FOLDER_UPLOAD)) {
        mkdir(FOLDER_UPLOAD, 0755, true);
    }
    // Nama unik supaya tidak bentrok
    $nama_baru = 'kuliner_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ekstensi;

    if (!move_uploaded_file($file['tmp_name'], FOLDER_UPLOAD . $nama_baru)) {
        $error = "Foto gagal disimpan ke server.";
        return null;
    }

    return $nama_baru;
}

// Menghapus file foto dari folder uploads (kalau ada)
function hapus_foto($nama_file)
{
    if (empty($nama_file)) {
        return;
    }
    $path = FOLDER_UPLOAD . basename($nama_file);   // basename mencegah path traversal
    if (is_file($path)) {
        unlink($path);
    }
}