<?php
/**
 * View Tambah Pengguna Baru (Admin Only)
 */
?>
<div style="margin-bottom: 1.5rem;">
    <a href="<?= baseUrl('index.php?page=users') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
        &larr; Kembali ke Manajemen User
    </a>
    <h2 style="font-size: 1.5rem; font-weight: 700;">Tambah Pengguna Baru</h2>
    <p class="form-hint" style="margin: 0;">Buat akun login sistem beserta data profil pengguna dan peran otorisasi.</p>
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
        <form method="POST" action="<?= baseUrl('index.php?page=users&action=create') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="username" class="form-label">Username Login <span class="required">*</span></label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="Contoh: staf_gudang" value="<?= e($data['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi <span class="required">*</span></label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 6 karakter" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="role" class="form-label">Peran / Otorisasi <span class="required">*</span></label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="staff" <?= ($data['role'] === 'staff') ? 'selected' : '' ?>>Staf Petugas (Operasional Stok)</option>
                        <option value="admin" <?= ($data['role'] === 'admin') ? 'selected' : '' ?>>Administrator (Akses Penuh)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone" class="form-input" placeholder="08xxxxxxxx" value="<?= e($data['phone']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="full_name" class="form-label">Nama Lengkap (User Profile) <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" class="form-input" placeholder="Nama lengkap petugas" value="<?= e($data['full_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="bio" class="form-label">Keterangan / Posisi Divisi</label>
                <textarea id="bio" name="bio" class="form-textarea" rows="3" placeholder="Informasi penugasan atau departemen..."><?= e($data['bio']) ?></textarea>
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">
                    <span>Simpan Pengguna</span>
                </button>
                <a href="<?= baseUrl('index.php?page=users') ?>" class="btn btn-secondary">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>
