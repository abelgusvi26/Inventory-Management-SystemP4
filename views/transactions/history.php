<?php
/**
 * View Riwayat Seluruh Mutasi Stok
 */
$data = $pagination['data'];
$page = $pagination['page'];
$totalPages = $pagination['total_pages'];
$totalRecords = $pagination['total'];

function makeHistoryPageUrl($p, $productId, $type, $startDate, $endDate) {
    $params = [
        'page'       => 'transactions',
        'action'     => 'history',
        'p'          => $p,
        'product_id' => $productId,
        'type'       => $type,
        'start_date' => $startDate,
        'end_date'   => $endDate,
    ];
    return baseUrl('index.php?' . http_build_query(array_filter($params)));
}
?>
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Riwayat Mutasi Stok Barang</h2>
        <p class="form-hint" style="margin: 0;">Log kronologis pergerakan barang masuk dan keluar dengan pencatat transaksi.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= baseUrl('index.php?page=transactions&action=in') ?>" class="btn btn-primary btn-sm">
            + Catat Barang Masuk
        </a>
        <a href="<?= baseUrl('index.php?page=transactions&action=out') ?>" class="btn btn-danger btn-sm">
            - Catat Barang Keluar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filter Bar Mutasi -->
        <form method="GET" action="<?= baseUrl('index.php') ?>" class="filter-bar">
            <input type="hidden" name="page" value="transactions">
            <input type="hidden" name="action" value="history">

            <div style="min-width: 200px;">
                <select name="product_id" class="form-select" aria-label="Filter Produk" onchange="this.form.submit()">
                    <option value="">Semua Produk</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($productId == $p['id']) ? 'selected' : '' ?>>
                            [<?= e($p['sku']) ?>] <?= e($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="min-width: 140px;">
                <select name="type" class="form-select" aria-label="Filter Jenis" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="IN" <?= ($type === 'IN') ? 'selected' : '' ?>>Barang Masuk (IN)</option>
                    <option value="OUT" <?= ($type === 'OUT') ? 'selected' : '' ?>>Barang Keluar (OUT)</option>
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
                <label style="font-size: 0.8125rem; color: var(--text-subtle);">Dari:</label>
                <input type="date" name="start_date" class="form-input" style="min-width: 130px;" value="<?= e($startDate) ?>" onchange="this.form.submit()">
            </div>

            <div style="display: flex; align-items: center; gap: 6px;">
                <label style="font-size: 0.8125rem; color: var(--text-subtle);">Sampai:</label>
                <input type="date" name="end_date" class="form-input" style="min-width: 130px;" value="<?= e($endDate) ?>" onchange="this.form.submit()">
            </div>

            <?php if (!empty($productId) || !empty($type) || !empty($startDate) || !empty($endDate)): ?>
                <a href="<?= baseUrl('index.php?page=transactions&action=history') ?>" class="btn btn-secondary btn-sm" style="color: var(--danger);">Reset Filter</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu Transaksi</th>
                        <th>Jenis Mutasi</th>
                        <th>Kode SKU</th>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>No. Referensi</th>
                        <th>Petugas</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-title">Tidak Ada Catatan Mutasi</div>
                                    <div class="empty-desc">Tidak ditemukan histori pergerakan barang yang sesuai dengan filter yang dipilih.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $m): ?>
                            <tr>
                                <td style="font-size: 0.8125rem; color: var(--text-muted); white-space: nowrap;">
                                    <?= Sanitize::date($m['created_at']) ?>
                                </td>
                                <td>
                                    <?php if ($m['type'] === 'IN'): ?>
                                        <span class="badge badge-success">&darr; Masuk (IN)</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">&uarr; Keluar (OUT)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="mono"><strong><?= e($m['sku']) ?></strong></td>
                                <td>
                                    <a href="<?= baseUrl('index.php?page=products&action=detail&id=' . $m['product_id']) ?>">
                                        <?= e($m['product_name']) ?>
                                    </a>
                                </td>
                                <td><strong><?= (int) $m['quantity'] ?></strong> unit</td>
                                <td class="mono"><?= e($m['reference_number'] ?: '-') ?></td>
                                <td><?= e($m['user_full_name'] ?: $m['username']) ?></td>
                                <td style="max-width: 250px;"><?= e($m['notes'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <div>
                    Menampilkan halaman <strong><?= $page ?></strong> dari <strong><?= $totalPages ?></strong> (Total <strong><?= $totalRecords ?></strong> mutasi)
                </div>
                <div class="pagination-pages">
                    <?php if ($page > 1): ?>
                        <a href="<?= makeHistoryPageUrl($page - 1, $productId, $type, $startDate, $endDate) ?>" class="page-btn">&laquo; Sebelumnya</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?= makeHistoryPageUrl($i, $productId, $type, $startDate, $endDate) ?>" class="page-btn <?= ($i === $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= makeHistoryPageUrl($page + 1, $productId, $type, $startDate, $endDate) ?>" class="page-btn">Berikutnya &raquo;</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
