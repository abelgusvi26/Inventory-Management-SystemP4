<?php
/**
 * View Listing Produk (Search, Filter, Sorting, Paginasi)
 */
$data = $pagination['data'];
$page = $pagination['page'];
$totalPages = $pagination['total_pages'];
$totalRecords = $pagination['total'];

// Fungsi helper untuk menyusun tautan sorting dengan mempertahankan parameter lain
function makeSortUrl($col, $currentSort, $currentDir, $search, $categoryId) {
    $newDir = ($currentSort === $col && $currentDir === 'ASC') ? 'DESC' : 'ASC';
    $params = [
        'page'        => 'products',
        'sort'        => $col,
        'direction'   => $newDir,
        'q'           => $search,
        'category_id' => $categoryId,
    ];
    return baseUrl('index.php?' . http_build_query(array_filter($params)));
}

// Fungsi helper untuk menyusun tautan paginasi
function makePageUrl($p, $sortBy, $sortDir, $search, $categoryId) {
    $params = [
        'page'        => 'products',
        'p'           => $p,
        'sort'        => $sortBy,
        'direction'   => $sortDir,
        'q'           => $search,
        'category_id' => $categoryId,
    ];
    return baseUrl('index.php?' . http_build_query(array_filter($params)));
}
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Katalog Produk Inventaris</h2>
        <p class="form-hint" style="margin: 0;">Kelola master data produk, pantau ketersediaan stok, dan periksa harga pasok supplier.</p>
    </div>
    <?php if (Auth::isAdmin()): ?>
    <div>
        <a href="<?= baseUrl('index.php?page=products&action=create') ?>" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Tambah Produk Baru</span>
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body">
        <!-- Form Pencarian dan Filter Kategori (PRD 7.1) -->
        <form method="GET" action="<?= baseUrl('index.php') ?>" class="filter-bar">
            <input type="hidden" name="page" value="products">
            <input type="hidden" name="sort" value="<?= e($sortBy) ?>">
            <input type="hidden" name="direction" value="<?= e($sortDir) ?>">

            <div class="search-input-wrapper">
                <input type="text" name="q" class="form-input" placeholder="Cari nama produk atau kode SKU..." value="<?= e($search) ?>" aria-label="Cari produk">
            </div>

            <div style="min-width: 200px;">
                <select name="category_id" class="form-select" aria-label="Filter berdasarkan kategori" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryId == $cat['id']) ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <span>Cari</span>
            </button>

            <?php if (!empty($search) || !empty($categoryId)): ?>
                <a href="<?= baseUrl('index.php?page=products') ?>" class="btn btn-secondary btn-sm" style="color: var(--danger);">Reset Filter</a>
            <?php endif; ?>
        </form>

        <!-- Tabel Listing Produk dengan Sorting Kolom Aman -->
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <a href="<?= makeSortUrl('sku', $sortBy, $sortDir, $search, $categoryId) ?>" style="color: inherit;">
                                SKU <?= ($sortBy === 'sku') ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= makeSortUrl('name', $sortBy, $sortDir, $search, $categoryId) ?>" style="color: inherit;">
                                Nama Produk <?= ($sortBy === 'name') ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= makeSortUrl('category_name', $sortBy, $sortDir, $search, $categoryId) ?>" style="color: inherit;">
                                Kategori <?= ($sortBy === 'category_name') ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= makeSortUrl('price', $sortBy, $sortDir, $search, $categoryId) ?>" style="color: inherit;">
                                Harga Jual <?= ($sortBy === 'price') ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?= makeSortUrl('stock', $sortBy, $sortDir, $search, $categoryId) ?>" style="color: inherit;">
                                Stok Fisik <?= ($sortBy === 'stock') ? ($sortDir === 'ASC' ? '▲' : '▼') : '' ?>
                            </a>
                        </th>
                        <th>Status Stok</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-title">Tidak Ada Produk Ditemukan</div>
                                    <div class="empty-desc">
                                        <?php if (!empty($search) || !empty($categoryId)): ?>
                                            Tidak ada produk yang cocok dengan kriteria pencarian Anda. Coba kata kunci lain atau reset filter.
                                        <?php else: ?>
                                            Belum ada produk terdaftar dalam basis data inventaris.
                                        <?php endif; ?>
                                    </div>
                                    <?php if (Auth::isAdmin()): ?>
                                        <a href="<?= baseUrl('index.php?page=products&action=create') ?>" class="btn btn-primary btn-sm">+ Daftarkan Produk Pertama</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td class="mono"><strong><?= e($row['sku']) ?></strong></td>
                                <td>
                                    <a href="<?= baseUrl('index.php?page=products&action=detail&id=' . $row['id']) ?>" style="font-weight: 600;">
                                        <?= e($row['name']) ?>
                                    </a>
                                </td>
                                <td><?= e($row['category_name'] ?: 'Tanpa Kategori') ?></td>
                                <td class="mono"><?= Sanitize::rupiah($row['price']) ?></td>
                                <td>
                                    <?php 
                                        $currStock = (int)$row['stock'];
                                        $minStock = max(1, (int)$row['minimum_stock']);
                                        $pct = min(100, round(($currStock / $minStock) * 100));
                                        $fillClass = $currStock === 0 ? 'danger' : ($currStock <= $minStock ? 'warning' : 'safe');
                                    ?>
                                    <div class="stock-progress-wrap">
                                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                            <strong><?= $currStock ?></strong> <span style="font-size: 0.75rem; color: var(--text-subtle);">/ min <?= (int)$row['minimum_stock'] ?></span>
                                        </div>
                                        <div class="stock-progress-bar" data-tooltip="Tersedia: <?= $pct ?>% dari batas minimum">
                                            <div class="stock-progress-fill <?= $fillClass ?>" style="width: <?= max(6, $pct) ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ((int)$row['stock'] === 0): ?>
                                        <span class="badge badge-danger">
                                            <span class="badge-dot"></span>
                                            <span>Habis</span>
                                        </span>
                                    <?php elseif ((int)$row['stock'] <= (int)$row['minimum_stock']): ?>
                                        <span class="badge badge-warning">
                                            <span class="badge-dot"></span>
                                            <span>Menipis</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-success">
                                            <span class="badge-dot"></span>
                                            <span>Aman</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="<?= baseUrl('index.php?page=products&action=detail&id=' . $row['id']) ?>" class="btn btn-secondary btn-sm" data-tooltip="Lihat Kartu Stok Produk">Detail</a>
                                    <?php if (Auth::isAdmin()): ?>
                                        <a href="<?= baseUrl('index.php?page=products&action=edit&id=' . $row['id']) ?>" class="btn btn-secondary btn-sm" data-tooltip="Edit Data Produk">Ubah</a>
                                        
                                        <?php if ((int)$row['movement_count'] === 0): ?>
                                            <form method="POST" action="<?= baseUrl('index.php?page=products&action=delete') ?>" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-secondary btn-sm" style="opacity: 0.5; cursor: not-allowed;" title="Produk tidak dapat dihapus karena memiliki riwayat mutasi stok (Protected)">Terkunci</button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginasi (PRD 7.2) -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <div>
                    Menampilkan halaman <strong><?= $page ?></strong> dari <strong><?= $totalPages ?></strong> (Total <strong><?= $totalRecords ?></strong> item)
                </div>
                <div class="pagination-pages">
                    <?php if ($page > 1): ?>
                        <a href="<?= makePageUrl($page - 1, $sortBy, $sortDir, $search, $categoryId) ?>" class="page-btn">&laquo; Sebelumnya</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?= makePageUrl($i, $sortBy, $sortDir, $search, $categoryId) ?>" class="page-btn <?= ($i === $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= makePageUrl($page + 1, $sortBy, $sortDir, $search, $categoryId) ?>" class="page-btn">Berikutnya &raquo;</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
