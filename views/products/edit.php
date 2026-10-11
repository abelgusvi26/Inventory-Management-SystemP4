<?php
/**
 * View Ubah Data Produk
 */
?>
<div style="margin-bottom: 1.5rem;">
    <a href="<?= baseUrl('index.php?page=products&action=detail&id=' . $product['id']) ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
        &larr; Kembali ke Detail Produk
    </a>
    <h2 style="font-size: 1.5rem; font-weight: 700;">Ubah Data Produk: <?= e($product['name']) ?></h2>
    <p class="form-hint" style="margin: 0;">Perbarui informasi produk, harga jual, dan relasi pemasok supplier.</p>
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

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= baseUrl('index.php?page=products&action=edit&id=' . $product['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kode SKU (Terkunci)</label>
                    <input type="text" class="form-input mono" value="<?= e($product['sku']) ?>" disabled>
                    <p class="form-hint">Kode SKU tidak dapat diubah untuk menjaga integritas riwayat mutasi.</p>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Kategori Produk <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($data['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap Produk <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-input" value="<?= e($data['name']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price" class="form-label">Harga Jual Satuan (IDR) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0" id="price" name="price" class="form-input" value="<?= e($data['price']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Stok Fisik Saat Ini (Hanya via Transaksi)</label>
                    <input type="text" class="form-input" value="<?= (int) $product['stock'] ?> unit" disabled>
                    <p class="form-hint">Perubahan stok wajib dilakukan lewat formulir Barang Masuk atau Barang Keluar.</p>
                </div>

                <div class="form-group">
                    <label for="minimum_stock" class="form-label">Batas Stok Minimum <span class="required">*</span></label>
                    <input type="number" min="0" id="minimum_stock" name="minimum_stock" class="form-input" value="<?= e($data['minimum_stock']) ?>" required>
                </div>
            </div>

            <!-- Many-to-Many Relasi Supplier -->
            <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <h3 style="font-size: 1.0625rem; font-weight: 600; margin-bottom: 0.5rem;">Relasi Pemasok / Supplier (Many-to-Many)</h3>
                <p class="form-hint" style="margin-bottom: 1rem;">Perbarui daftar pemasok dan harga pasok untuk produk ini.</p>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Pilih</th>
                                <th>Nama Supplier</th>
                                <th>Kontak Person</th>
                                <th>Harga Beli / Suplai (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allSuppliers as $s): ?>
                                <?php 
                                    $suppData = isset($data['suppliers'][$s['id']]) ? $data['suppliers'][$s['id']] : array();
                                    $isChecked = !empty($suppData['selected']);
                                    $suppPrice = isset($suppData['price']) ? $suppData['price'] : '';
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="suppliers[<?= $s['id'] ?>][selected]" value="1" <?= $isChecked ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                    </td>
                                    <td><strong><?= e($s['name']) ?></strong></td>
                                    <td><?= e($s['contact_person'] ?: '-') ?> (<?= e($s['phone'] ?: '-') ?>)</td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="suppliers[<?= $s['id'] ?>][price]" class="form-input" placeholder="Harga beli dari supplier ini" value="<?= e($suppPrice) ?>" style="max-width: 240px;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">
                    <span>Simpan Perubahan</span>
                </button>
                <a href="<?= baseUrl('index.php?page=products&action=detail&id=' . $product['id']) ?>" class="btn btn-secondary">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>
