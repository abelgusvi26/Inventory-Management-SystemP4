<?php
/**
 * View Tambah Produk Baru
 */
?>
<div style="margin-bottom: 1.5rem;">
    <a href="<?= baseUrl('index.php?page=products') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
        &larr; Kembali ke Katalog
    </a>
    <h2 style="font-size: 1.5rem; font-weight: 700;">Tambah Produk Baru</h2>
    <p class="form-hint" style="margin: 0;">Daftarkan item barang baru beserta harga jual, batas stok minimum, dan relasi supplier pemasok.</p>
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
        <form method="POST" action="<?= baseUrl('index.php?page=products&action=create') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="sku" class="form-label">Kode SKU <span class="required">*</span></label>
                    <input type="text" id="sku" name="sku" class="form-input mono" placeholder="Contoh: PRD-MN-001" value="<?= e($data['sku']) ?>" required>
                    <p class="form-hint">SKU harus bersifat unik di seluruh katalog produk.</p>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Kategori Produk <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">Pilih Kategori...</option>
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
                <input type="text" id="name" name="name" class="form-input" placeholder="Masukkan nama dan spesifikasi produk" value="<?= e($data['name']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price" class="form-label">Harga Jual Satuan (IDR) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0" id="price" name="price" class="form-input" placeholder="Contoh: 1500000" value="<?= e($data['price']) ?>" required>
                    <p class="form-hint">Harga tidak boleh bernilai negatif.</p>
                </div>

                <div class="form-group">
                    <label for="stock" class="form-label">Saldo Stok Awal <span class="required">*</span></label>
                    <input type="number" min="0" id="stock" name="stock" class="form-input" value="<?= e(isset($data['stock']) ? $data['stock'] : '0') ?>" required>
                    <p class="form-hint">Akan otomatis dicatat sebagai transaksi mutasi masuk perdana jika &gt; 0.</p>
                </div>

                <div class="form-group">
                    <label for="minimum_stock" class="form-label">Batas Stok Minimum <span class="required">*</span></label>
                    <input type="number" min="0" id="minimum_stock" name="minimum_stock" class="form-input" value="<?= e(isset($data['minimum_stock']) ? $data['minimum_stock'] : '5') ?>" required>
                    <p class="form-hint">Peringatan restock aktif bila stok fisik berada pada atau di bawah angka ini.</p>
                </div>
            </div>

            <!-- Many-to-Many Relasi Supplier (PRD Bagian 5 & Fitur Wajib 5) -->
            <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <h3 style="font-size: 1.0625rem; font-weight: 600; margin-bottom: 0.5rem;">Relasi Pemasok / Supplier (Many-to-Many)</h3>
                <p class="form-hint" style="margin-bottom: 1rem;">Satu produk dapat dipasok oleh banyak supplier beserta harga beli yang berbeda.</p>

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
                            <?php if (empty($suppliers)): ?>
                                <tr>
                                    <td colspan="4" style="color: var(--text-subtle); text-align: center; padding: 1.5rem;">
                                        Belum ada supplier terdaftar. Tambahkan data supplier terlebih dahulu pada menu Supplier.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($suppliers as $s): ?>
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
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">
                    <span>Simpan Produk</span>
                </button>
                <a href="<?= baseUrl('index.php?page=products') ?>" class="btn btn-secondary">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>
