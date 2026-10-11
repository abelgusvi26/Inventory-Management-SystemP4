<?php
/**
 * View Detail Supplier & Produk yang Disuplai
 */
?>
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <a href="<?= baseUrl('index.php?page=suppliers') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
            &larr; Kembali ke Daftar Supplier
        </a>
        <h2 style="font-size: 1.5rem; font-weight: 700;"><?= e($supplier['name']) ?></h2>
        <p class="form-hint" style="margin: 0;">Kontak PIC: <strong><?= e($supplier['contact_person'] ?: '-') ?></strong> &bull; Terdaftar: <?= Sanitize::date($supplier['created_at'], false) ?></p>
    </div>
    <?php if (Auth::isAdmin()): ?>
    <div>
        <a href="<?= baseUrl('index.php?page=suppliers&action=edit&id=' . $supplier['id']) ?>" class="btn btn-secondary btn-sm">
            Ubah Data
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Informasi Narahubung & Alamat</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
            <div>
                <span class="stat-label">Nomor Telepon:</span>
                <p style="font-size: 1rem; font-weight: 600; margin-top: 4px;"><?= e($supplier['phone'] ?: '-') ?></p>
            </div>
            <div>
                <span class="stat-label">Alamat Email:</span>
                <p style="font-size: 1rem; font-weight: 600; margin-top: 4px;"><?= e($supplier['email'] ?: '-') ?></p>
            </div>
            <div>
                <span class="stat-label">Alamat Kantor / Pergudangan:</span>
                <p style="font-size: 0.9375rem; margin-top: 4px;"><?= e($supplier['address'] ?: '-') ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Produk yang Disuplai oleh <?= e($supplier['name']) ?></h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Harga Pasok Supplier</th>
                    <th>Harga Jual Normal</th>
                    <th>Stok Fisik Saat Ini</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state" style="padding: 1.5rem;">
                                <div class="empty-title">Belum Ada Produk Terhubung</div>
                                <div class="empty-desc">Supplier ini belum didaftarkan sebagai pemasok untuk item produk mana pun.</div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="mono"><strong><?= e($p['sku']) ?></strong></td>
                            <td>
                                <a href="<?= baseUrl('index.php?page=products&action=detail&id=' . $p['id']) ?>">
                                    <?= e($p['name']) ?>
                                </a>
                            </td>
                            <td class="mono"><?= Sanitize::rupiah($p['supplier_price']) ?></td>
                            <td class="mono"><?= Sanitize::rupiah($p['price']) ?></td>
                            <td><?= (int) $p['stock'] ?> unit</td>
                            <td style="text-align: right;">
                                <a href="<?= baseUrl('index.php?page=transactions&action=in&product_id=' . $p['id']) ?>" class="btn btn-primary btn-sm">
                                    Order / Restock
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
