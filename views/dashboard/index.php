<?php
/**
 * View Dashboard Utama
 */
?>
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Ringkasan Inventaris</h2>
        <p class="form-hint" style="margin: 0;">Pantau ketersediaan barang, valuasi aset gudang, dan aktivitas mutasi stok secara langsung.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= baseUrl('index.php?page=transactions&action=in') ?>" class="btn btn-primary btn-sm">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
            <span>Catat Barang Masuk</span>
        </a>
        <a href="<?= baseUrl('index.php?page=transactions&action=out') ?>" class="btn btn-secondary btn-sm">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
            <span>Catat Barang Keluar</span>
        </a>
    </div>
</div>

<!-- 5 Widget Metrik Wajib PRD (Bagian 9.1) -->
<div class="stats-grid">
    <!-- Total Produk -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Total Item Produk</span>
            <div class="stat-icon primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
            </div>
        </div>
        <div class="stat-value"><?= number_format($summary['total_products']) ?></div>
        <div class="stat-desc"><?= number_format($summary['total_categories']) ?> kategori aktif</div>
    </div>

    <!-- Nilai Total Inventaris -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Nilai Total Inventaris</span>
            <div class="stat-icon success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
        </div>
        <div class="stat-value mono" style="font-size: 1.35rem;"><?= Sanitize::rupiah($summary['total_inventory_value']) ?></div>
        <div class="stat-desc">Kalkulasi agregat dari stok &times; harga jual</div>
    </div>

    <!-- Produk Stok Rendah -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Produk Stok Menipis</span>
            <div class="stat-icon warning">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
            </div>
        </div>
        <div class="stat-value text-warning"><?= number_format($summary['low_stock_count']) ?></div>
        <div class="stat-desc"><?= $summary['out_of_stock_count'] ?> item saat ini stok kosong (0)</div>
    </div>

    <!-- Total Barang Masuk -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Akumulasi Barang Masuk</span>
            <div class="stat-icon primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
            </div>
        </div>
        <div class="stat-value"><?= number_format($summary['total_stock_in']) ?> <span style="font-size: 0.875rem; font-weight: normal;">unit</span></div>
        <div class="stat-desc">Total penerimaan stok (IN)</div>
    </div>

    <!-- Total Barang Keluar -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">Akumulasi Barang Keluar</span>
            <div class="stat-icon danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
            </div>
        </div>
        <div class="stat-value"><?= number_format($summary['total_stock_out']) ?> <span style="font-size: 0.875rem; font-weight: normal;">unit</span></div>
        <div class="stat-desc">Total pengeluaran stok (OUT)</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 1.5rem;">
    <!-- Tabel Peringatan Stok Rendah -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title" style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" color="var(--warning)"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                <span>Peringatan Stok Perlu Restock</span>
            </h3>
            <a href="<?= baseUrl('index.php?page=products') ?>" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Sisa Stok</th>
                        <th>Min.</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lowStockProducts)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state" style="padding: 1.5rem;">
                                    <div class="empty-title">Semua Stok Produk Terjaga</div>
                                    <div class="empty-desc">Tidak ada produk yang berada pada atau di bawah batas minimum stok saat ini.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lowStockProducts as $p): 
                            $currStock = (int)$p['stock'];
                            $minStock = max(1, (int)$p['minimum_stock']);
                            $pct = min(100, round(($currStock / $minStock) * 100));
                            $fillClass = $currStock === 0 ? 'danger' : 'warning';
                        ?>
                            <tr>
                                <td class="mono"><strong data-tooltip="SKU Produk"><?= e($p['sku']) ?></strong></td>
                                <td>
                                    <a href="<?= baseUrl('index.php?page=products&action=detail&id=' . $p['id']) ?>">
                                        <?= e($p['name']) ?>
                                    </a>
                                </td>
                                <td><?= e($p['category_name']) ?></td>
                                <td>
                                    <div class="stock-progress-wrap">
                                        <?php if ($currStock === 0): ?>
                                            <span class="badge badge-danger">
                                                <span class="badge-dot"></span>
                                                <span>0 (Kosong)</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">
                                                <span class="badge-dot"></span>
                                                <span><?= $currStock ?> unit</span>
                                            </span>
                                        <?php endif; ?>
                                        <div class="stock-progress-bar" data-tooltip="<?= $pct ?>% dari batas minimum">
                                            <div class="stock-progress-fill <?= $fillClass ?>" style="width: <?= max(8, $pct) ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= (int)$p['minimum_stock'] ?></td>
                                <td>
                                    <a href="<?= baseUrl('index.php?page=transactions&action=in&product_id=' . $p['id']) ?>" class="btn btn-primary btn-sm" data-tooltip="Catat Restock Produk Ini">
                                        + Restock
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Transaksi Terbaru -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title" style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Mutasi Transaksi Terkini</span>
            </h3>
            <a href="<?= baseUrl('index.php?page=transactions&action=history') ?>" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Tipe</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentMovements)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state" style="padding: 1.5rem;">
                                    <div class="empty-title">Belum Ada Transaksi</div>
                                    <div class="empty-desc">Catatan mutasi barang masuk dan keluar akan tampil di sini.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentMovements as $m): ?>
                            <tr>
                                <td style="font-size: 0.8125rem; color: var(--text-muted);"><?= Sanitize::date($m['created_at']) ?></td>
                                <td>
                                    <?php if ($m['type'] === 'IN'): ?>
                                        <span class="badge badge-success">&darr; Masuk (IN)</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">&uarr; Keluar (OUT)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><strong><?= e($m['sku']) ?></strong></div>
                                    <div style="font-size: 0.8125rem; color: var(--text-muted);"><?= e($m['product_name']) ?></div>
                                </td>
                                <td><strong><?= (int)$m['quantity'] ?></strong> unit</td>
                                <td><?= e($m['user_full_name'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
