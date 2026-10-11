<?php
/**
 * View Laporan Inventaris Agregat & Mutasi Stok (PRD Bagian 9.2)
 */

$totalValuation = 0;
$totalInAll = 0;
$totalOutAll = 0;
$totalStockAll = 0;

foreach ($reportData as $r) {
    $totalValuation += (float) $r['total_asset_value'];
    $totalInAll += (int) $r['total_in'];
    $totalOutAll += (int) $r['total_out'];
    $totalStockAll += (int) $r['current_stock'];
}

$queryString = http_build_query(array_filter([
    'category_id' => $categoryId,
    'start_date'  => $startDate,
    'end_date'    => $endDate,
]));
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Laporan Mutasi Inventaris Teragregasi</h2>
        <p class="form-hint" style="margin: 0;">Laporan kalkulasi agregat mutasi barang masuk, barang keluar, dan valuasi aset stok gudang.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= baseUrl('index.php?page=reports&action=print' . ($queryString ? '&' . $queryString : '')) ?>" target="_blank" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            <span>Cetak Laporan</span>
        </a>
        <a href="<?= baseUrl('index.php?page=reports&action=export' . ($queryString ? '&' . $queryString : '')) ?>" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Ekspor CSV / Excel</span>
        </a>
    </div>
</div>

<!-- Filter Rentang Tanggal dan Kategori -->
<div class="card">
    <div class="card-body">
        <form method="GET" action="<?= baseUrl('index.php') ?>" class="filter-bar" style="margin-bottom: 0;">
            <input type="hidden" name="page" value="reports">

            <div style="min-width: 220px;">
                <select name="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId == $cat['id']) ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
                <label style="font-size: 0.8125rem; color: var(--text-subtle);">Dari Tanggal:</label>
                <input type="date" name="start_date" class="form-input" value="<?= e($startDate) ?>" onchange="this.form.submit()">
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
                <label style="font-size: 0.8125rem; color: var(--text-subtle);">Sampai:</label>
                <input type="date" name="end_date" class="form-input" value="<?= e($endDate) ?>" onchange="this.form.submit()">
            </div>

            <button type="submit" class="btn btn-secondary">Terapkan Filter</button>

            <?php if (!empty($categoryId) || !empty($startDate) || !empty($endDate)): ?>
                <a href="<?= baseUrl('index.php?page=reports') ?>" class="btn btn-secondary btn-sm" style="color: var(--danger);">Reset Filter</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Tabel Laporan Agregat Inventaris -->
<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Jual Satuan</th>
                    <th style="color: var(--success-text);">Total Masuk (IN)</th>
                    <th style="color: var(--danger-text);">Total Keluar (OUT)</th>
                    <th>Stok Akhir</th>
                    <th>Batas Min.</th>
                    <th>Total Nilai Aset</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reportData)): ?>
                    <tr>
                        <td colspan="11">
                            <div class="empty-state">
                                <div class="empty-title">Tidak Ada Data Laporan</div>
                                <div class="empty-desc">Tidak ada data produk yang terdaftar untuk kriteria filter ini.</div>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($reportData as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="mono"><strong><?= e($row['sku']) ?></strong></td>
                            <td>
                                <a href="<?= baseUrl('index.php?page=products&action=detail&id=' . $row['id']) ?>">
                                    <?= e($row['product_name']) ?>
                                </a>
                            </td>
                            <td><?= e($row['category_name']) ?></td>
                            <td class="mono"><?= Sanitize::rupiah($row['price']) ?></td>
                            <td>
                                <strong style="color: var(--success);">&darr; <?= (int) $row['total_in'] ?></strong> unit
                            </td>
                            <td>
                                <strong style="color: var(--danger);">&uarr; <?= (int) $row['total_out'] ?></strong> unit
                            </td>
                            <td>
                                <strong><?= (int) $row['current_stock'] ?></strong> unit
                            </td>
                            <td><?= (int) $row['minimum_stock'] ?></td>
                            <td class="mono"><strong><?= Sanitize::rupiah($row['total_asset_value']) ?></strong></td>
                            <td>
                                <?php if ((int)$row['current_stock'] === 0): ?>
                                    <span class="badge badge-danger">Habis</span>
                                <?php elseif ((int)$row['current_stock'] <= (int)$row['minimum_stock']): ?>
                                    <span class="badge badge-warning">Menipis</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Aman</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($reportData)): ?>
                <tfoot style="background-color: var(--bg-subtle); font-weight: 700; border-top: 2px solid var(--border-color);">
                    <tr>
                        <td colspan="5" style="text-align: right; padding: 1rem;">TOTAL AGREGAT KESELURUHAN:</td>
                        <td style="color: var(--success); padding: 1rem;">&darr; <?= number_format($totalInAll) ?> unit</td>
                        <td style="color: var(--danger); padding: 1rem;">&uarr; <?= number_format($totalOutAll) ?> unit</td>
                        <td style="padding: 1rem;"><?= number_format($totalStockAll) ?> unit</td>
                        <td>-</td>
                        <td class="mono" style="font-size: 1rem; color: var(--primary); padding: 1rem;"><?= Sanitize::rupiah($totalValuation) ?></td>
                        <td>-</td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
