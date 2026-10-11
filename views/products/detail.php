<?php
/**
 * View Detail Produk & Riwayat Mutasi Spesifik
 */
?>
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <a href="<?= baseUrl('index.php?page=products') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
            &larr; Kembali ke Katalog Produk
        </a>
        <h2 style="font-size: 1.5rem; font-weight: 700;"><?= e($product['name']) ?></h2>
        <p class="form-hint" style="margin: 0;">SKU: <strong class="mono"><?= e($product['sku']) ?></strong> &bull; Kategori: <strong><?= e($product['category_name']) ?></strong></p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= baseUrl('index.php?page=transactions&action=in&product_id=' . $product['id']) ?>" class="btn btn-primary btn-sm">
            + Catat Barang Masuk
        </a>
        <a href="<?= baseUrl('index.php?page=transactions&action=out&product_id=' . $product['id']) ?>" class="btn btn-secondary btn-sm">
            - Catat Barang Keluar
        </a>
        <?php if (Auth::isAdmin()): ?>
            <a href="<?= baseUrl('index.php?page=products&action=edit&id=' . $product['id']) ?>" class="btn btn-secondary btn-sm">
                Ubah Data
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Informasi Utama Produk -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-label">Sisa Stok Fisik</div>
        <div class="stat-value">
            <?= (int) $product['stock'] ?> <span style="font-size: 0.875rem; font-weight: normal;">unit</span>
        </div>
        <div class="stat-desc">
            <?php if ((int)$product['stock'] === 0): ?>
                <span class="badge badge-danger">Stok Habis (0)</span>
            <?php elseif ((int)$product['stock'] <= (int)$product['minimum_stock']): ?>
                <span class="badge badge-warning">Stok Menipis (&le; <?= (int)$product['minimum_stock'] ?>)</span>
            <?php else: ?>
                <span class="badge badge-success">Stok Aman</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Harga Jual Satuan</div>
        <div class="stat-value mono" style="font-size: 1.35rem;"><?= Sanitize::rupiah($product['price']) ?></div>
        <div class="stat-desc">Batas Min: <?= (int)$product['minimum_stock'] ?> unit</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Total Nilai Aset Stok</div>
        <div class="stat-value mono" style="font-size: 1.35rem;">
            <?= Sanitize::rupiah($product['stock'] * $product['price']) ?>
        </div>
        <div class="stat-desc">Valuasi stok saat ini</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Terdaftar Sejak</div>
        <div class="stat-value" style="font-size: 1.125rem;">
            <?= Sanitize::date($product['created_at'], false) ?>
        </div>
        <div class="stat-desc">Pembaruan: <?= Sanitize::date($product['updated_at']) ?></div>
    </div>
</div>

<!-- Daftar Supplier Terhubung (Many-to-Many) -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Pemasok / Supplier Terhubung</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Supplier</th>
                    <th>Kontak Person</th>
                    <th>Telepon / Email</th>
                    <th>Harga Pasok Supplier</th>
                    <th>Estimasi Margin Kotor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state" style="padding: 1.5rem;">
                                <div class="empty-title">Belum Ada Supplier Terhubung</div>
                                <div class="empty-desc">Produk ini belum dipetakan ke supplier mana pun. Ubah data produk untuk menambahkan relasi supplier.</div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($suppliers as $s): ?>
                        <?php 
                            $supplierPrice = (float) $s['supplier_price'];
                            $margin = (float) $product['price'] - $supplierPrice;
                        ?>
                        <tr>
                            <td><strong><?= e($s['supplier_name']) ?></strong></td>
                            <td><?= e($s['supplier_name']) ?></td>
                            <td><?= e($s['phone'] ?: '-') ?> &bull; <?= e($s['email'] ?: '-') ?></td>
                            <td class="mono"><?= Sanitize::rupiah($supplierPrice) ?></td>
                            <td>
                                <?php if ($margin >= 0): ?>
                                    <span class="badge badge-success">+ <?= Sanitize::rupiah($margin) ?></span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= Sanitize::rupiah($margin) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Riwayat Mutasi Khusus Produk Ini -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Histori Mutasi Stok Produk</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu Transaksi</th>
                    <th>Jenis Mutasi</th>
                    <th>Jumlah</th>
                    <th>Nomor Referensi</th>
                    <th>Petugas Pencatat</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movements)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state" style="padding: 1.5rem;">
                                <div class="empty-title">Belum Ada Mutasi</div>
                                <div class="empty-desc">Belum ada catatan barang masuk atau keluar untuk produk ini.</div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($movements as $m): ?>
                        <tr>
                            <td style="font-size: 0.8125rem; color: var(--text-muted);"><?= Sanitize::date($m['created_at']) ?></td>
                            <td>
                                <?php if ($m['type'] === 'IN'): ?>
                                    <span class="badge badge-success">&darr; Masuk (IN)</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">&uarr; Keluar (OUT)</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= (int) $m['quantity'] ?></strong> unit</td>
                            <td class="mono"><?= e($m['reference_number'] ?: '-') ?></td>
                            <td><?= e($m['user_full_name'] ?: $m['username']) ?></td>
                            <td><?= e($m['notes'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
