<?php
/**
 * View Daftar Supplier
 */
?>
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Daftar Pemasok / Supplier</h2>
        <p class="form-hint" style="margin: 0;">Kelola data vendor rekanan pemasok barang dan katalog item yang mereka suplai.</p>
    </div>
    <?php if (Auth::isAdmin()): ?>
    <div>
        <a href="<?= baseUrl('index.php?page=suppliers&action=create') ?>" class="btn btn-primary">
            + Tambah Supplier
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Perusahaan</th>
                    <th>Kontak Person</th>
                    <th>Nomor Telepon</th>
                    <th>Email</th>
                    <th>Item Disuplai</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-title">Belum Ada Supplier Terdaftar</div>
                                <div class="empty-desc">Daftarkan supplier pertama untuk menghubungkannya dengan produk inventaris.</div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($suppliers as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= baseUrl('index.php?page=suppliers&action=detail&id=' . $s['id']) ?>" style="font-weight: 600;">
                                    <?= e($s['name']) ?>
                                </a>
                            </td>
                            <td><?= e($s['contact_person'] ?: '-') ?></td>
                            <td class="mono"><?= e($s['phone'] ?: '-') ?></td>
                            <td><?= e($s['email'] ?: '-') ?></td>
                            <td>
                                <a href="<?= baseUrl('index.php?page=suppliers&action=detail&id=' . $s['id']) ?>">
                                    <?= (int) $s['total_supplied_products'] ?> item produk
                                </a>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= baseUrl('index.php?page=suppliers&action=detail&id=' . $s['id']) ?>" class="btn btn-secondary btn-sm">Detail</a>
                                <?php if (Auth::isAdmin()): ?>
                                    <a href="<?= baseUrl('index.php?page=suppliers&action=edit&id=' . $s['id']) ?>" class="btn btn-secondary btn-sm">Ubah</a>
                                    <form method="POST" action="<?= baseUrl('index.php?page=suppliers&action=delete') ?>" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                        <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
