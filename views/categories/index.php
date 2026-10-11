<?php
/**
 * View Daftar Kategori Produk
 */
?>
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Manajemen Kategori Produk</h2>
        <p class="form-hint" style="margin: 0;">Pengelompokan barang untuk memudahkan filter, klasifikasi, dan pelaporan inventaris.</p>
    </div>
    <?php if (Auth::isAdmin()): ?>
    <div>
        <a href="<?= baseUrl('index.php?page=categories&action=create') ?>" class="btn btn-primary">
            + Tambah Kategori
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
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Produk</th>
                    <th>Total Stok Fisik</th>
                    <?php if (Auth::isAdmin()): ?>
                        <th style="text-align: right;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-title">Belum Ada Kategori</div>
                                <div class="empty-desc">Buat kategori pertama untuk mulai mengelompokkan katalog produk.</div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= e($cat['name']) ?></strong>
                            </td>
                            <td><?= e($cat['description'] ?: '-') ?></td>
                            <td>
                                <a href="<?= baseUrl('index.php?page=products&category_id=' . $cat['id']) ?>">
                                    <?= (int) $cat['total_products'] ?> produk
                                </a>
                            </td>
                            <td><strong><?= (int) $cat['total_stock'] ?></strong> unit</td>
                            <?php if (Auth::isAdmin()): ?>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="<?= baseUrl('index.php?page=categories&action=edit&id=' . $cat['id']) ?>" class="btn btn-secondary btn-sm">Ubah</a>

                                    <?php if ((int)$cat['total_products'] === 0): ?>
                                        <form method="POST" action="<?= baseUrl('index.php?page=categories&action=delete') ?>" style="display: inline-block;" onsubmit="return confirm('Hapus kategori ini?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">
                                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary btn-sm" style="opacity: 0.5; cursor: not-allowed;" title="Kategori tidak dapat dihapus karena masih menampung produk aktif (Protected)">Terkunci</button>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
