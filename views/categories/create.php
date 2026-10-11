<?php
/**
 * View Tambah Kategori
 */
?>
<div style="margin-bottom: 1.5rem;">
    <a href="<?= baseUrl('index.php?page=categories') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
        &larr; Kembali ke Daftar Kategori
    </a>
    <h2 style="font-size: 1.5rem; font-weight: 700;">Tambah Kategori Baru</h2>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <div>
            <strong>Terdapat kesalahan input:</strong>
            <ul style="margin: 0.5rem 0 0 1.25rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="<?= baseUrl('index.php?page=categories&action=create') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">

            <div class="form-group">
                <label for="name" class="form-label">Nama Kategori <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-input" placeholder="Contoh: Elektronik & Display" value="<?= e($data['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" class="form-textarea" rows="3" placeholder="Keterangan singkat mengenai kategori"><?= e($data['description']) ?></textarea>
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">
                    <span>Simpan Kategori</span>
                </button>
                <a href="<?= baseUrl('index.php?page=categories') ?>" class="btn btn-secondary">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>
