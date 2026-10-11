<?php
/**
 * View Manajemen Pengguna & Hak Akses (Admin Only)
 */
?>
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Manajemen Pengguna & Otorisasi</h2>
        <p class="form-hint" style="margin: 0;">Kelola akun login dan hak akses operasional (Administrator vs Staf Petugas).</p>
    </div>
    <div>
        <a href="<?= baseUrl('index.php?page=users&action=create') ?>" class="btn btn-primary">
            + Tambah Pengguna Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nama Lengkap Profil</th>
                    <th>Peran / Role</th>
                    <th>No. Telepon</th>
                    <th>Aktivitas Transaksi</th>
                    <th>Terdaftar Sejak</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td>
                            <strong><?= e($u['username']) ?></strong>
                            <?php if ($u['id'] === Auth::id()): ?>
                                <span class="badge badge-primary" style="font-size: 0.7rem; margin-left: 4px;">Akun Anda</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($u['full_name'] ?: '-') ?></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="badge badge-primary">Administrator</span>
                            <?php else: ?>
                                <span class="badge badge-neutral">Staf Petugas</span>
                            <?php endif; ?>
                        </td>
                        <td class="mono"><?= e($u['phone'] ?: '-') ?></td>
                        <td>
                            <span class="badge badge-neutral"><?= (int)$u['transaction_count'] ?> transaksi</span>
                        </td>
                        <td style="font-size: 0.8125rem; color: var(--text-muted);"><?= Sanitize::date($u['created_at'], false) ?></td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="<?= baseUrl('index.php?page=users&action=edit&id=' . $u['id']) ?>" class="btn btn-secondary btn-sm">Ubah</a>

                            <?php if ($u['id'] !== Auth::id()): ?>
                                <form method="POST" action="<?= baseUrl('index.php?page=users&action=delete') ?>" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary btn-sm" style="opacity: 0.5; cursor: not-allowed;" title="Tidak dapat menghapus akun Anda sendiri yang sedang aktif">Diri Sendiri</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
