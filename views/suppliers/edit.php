<?php
/**
 * View Ubah Data Supplier
 */
?>
<div style="margin-bottom: 1.5rem;">
    <a href="<?= baseUrl('index.php?page=suppliers') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
        &larr; Kembali ke Daftar Supplier
    </a>
    <h2 style="font-size: 1.5rem; font-weight: 700;">Ubah Data Supplier: <?= e($supplier['name']) ?></h2>
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

<div class="card" style="max-width: 650px;">
    <div class="card-body">
        <form method="POST" action="<?= baseUrl('index.php?page=suppliers&action=edit&id=' . $supplier['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">

            <div class="form-group">
                <label for="name" class="form-label">Nama Perusahaan / Supplier <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-input" value="<?= e($data['name']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_person" class="form-label">Nama Kontak Person (PIC)</label>
                    <input type="text" id="contact_person" name="contact_person" class="form-input" value="<?= e($data['contact_person']) ?>">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Nomor Telepon / WhatsApp</label>
                    <input type="text" id="phone" name="phone" class="form-input" value="<?= e($data['phone']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-input" value="<?= e($data['email']) ?>">
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Alamat Kantor / Gudang</label>
                <textarea id="address" name="address" class="form-textarea" rows="3"><?= e($data['address']) ?></textarea>
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">
                    <span>Simpan Perubahan</span>
                </button>
                <a href="<?= baseUrl('index.php?page=suppliers') ?>" class="btn btn-secondary">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>
