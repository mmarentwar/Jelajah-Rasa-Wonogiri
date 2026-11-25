<?php
// Variabel yang dibutuhkan dari halaman pemanggil:
// $kategori (array), $nilai (array isi form), $foto_lama (string/null), $teks_tombol (string)
$pilihan_harga = ['murah' => 'Murah', 'sedang' => 'Sedang', 'mahal' => 'Mahal'];
?>
<form method="POST" enctype="multipart/form-data" novalidate>

    <div class="mb-3">
        <label for="nama_makanan" class="form-label fw-semibold">Nama Makanan</label>
        <input type="text" class="form-control" id="nama_makanan" name="nama_makanan"
               maxlength="150" value="<?= e($nilai['nama_makanan']) ?>">
    </div>

    <div class="mb-3">
        <label for="id_jenis" class="form-label fw-semibold">Kategori</label>
        <select class="form-select" id="id_jenis" name="id_jenis">
            <option value="">-- Pilih kategori --</option>
            <?php foreach ($kategori as $k): ?>
                <option value="<?= (int) $k['id_jenis'] ?>"
                    <?= ((string) $nilai['id_jenis'] === (string) $k['id_jenis']) ? 'selected' : '' ?>>
                    <?= e($k['nama_jenis']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"><?= e($nilai['deskripsi']) ?></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold d-block">Kisaran Harga</label>
        <?php foreach ($pilihan_harga as $nilai_enum => $teks): ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="kisaran_harga"
                       id="harga_<?= $nilai_enum ?>" value="<?= $nilai_enum ?>"
                       <?= ($nilai['kisaran_harga'] === $nilai_enum) ? 'checked' : '' ?>>
                <label class="form-check-label" for="harga_<?= $nilai_enum ?>"><?= $teks ?></label>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mb-3">
        <label for="tanggal_dicoba" class="form-label fw-semibold">Tanggal Dicoba</label>
        <input type="date" class="form-control" id="tanggal_dicoba" name="tanggal_dicoba"
               max="<?= date('Y-m-d') ?>" value="<?= e($nilai['tanggal_dicoba']) ?>">
    </div>

    <div class="mb-4">
        <label for="foto" class="form-label fw-semibold">Foto</label>
        <?php if (!empty($foto_lama) && file_exists("uploads/" . $foto_lama)): ?>
            <div class="mb-2">
                <img src="uploads/<?= e($foto_lama) ?>" alt="Foto saat ini" class="rounded" style="max-height:140px;">
                <div class="form-text">Foto saat ini. Kosongkan input di bawah kalau tidak ingin mengganti.</div>
            </div>
        <?php endif; ?>
        <input type="file" class="form-control" id="foto" name="foto"
               accept=".jpg,.jpeg,.png,.webp">
        <div class="form-text">Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-wonogiri fw-semibold">
            <i class="bi bi-save"></i> <?= e($teks_tombol) ?>
        </button>
        <a href="dashboard.php" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>