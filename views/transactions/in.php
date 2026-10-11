<?php
/**
 * View Catat Barang Masuk (IN)
 */
?>
<div style="margin-bottom: 1.5rem;">
    <a href="<?= baseUrl('index.php?page=transactions&action=history') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
        &larr; Riwayat Transaksi
    </a>
    <h2 style="font-size: 1.5rem; font-weight: 700;">Pencatatan Barang Masuk (Stock In)</h2>
    <p class="form-hint" style="margin: 0;">Mencatat penambahan stok barang dari supplier atau retur ke dalam gudang.</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <div>
            <strong>Terdapat kendala transaksi:</strong>
            <ul style="margin: 0.5rem 0 0 1.25rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 680px;">
    <div class="card-body">
        <form method="POST" action="<?= baseUrl('index.php?page=transactions&action=in') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">

            <div class="form-group">
                <label for="product_id" class="form-label">Pilih Produk <span class="required">*</span></label>
                <select id="product_id" name="product_id" class="form-select" required autofocus>
                    <option value="">Pilih item produk yang masuk...</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($data['product_id'] == $p['id']) ? 'selected' : '' ?>>
                            [<?= e($p['sku']) ?>] <?= e($p['name']) ?> (Stok saat ini: <?= (int)$p['stock'] ?> unit)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="quantity" class="form-label">Jumlah Barang Masuk <span class="required">*</span></label>
                    <input type="number" min="1" id="quantity" name="quantity" class="form-input" placeholder="Minimal 1 unit" value="<?= e($data['quantity']) ?>" required>
                    <p class="form-hint">Kuantitas harus berupa bilangan bulat lebih dari nol.</p>
                </div>

                <div class="form-group">
                    <label for="reference_number" class="form-label">Nomor Dokumen / Surat Jalan</label>
                    <input type="text" id="reference_number" name="reference_number" class="form-input mono" placeholder="Contoh: PO-2026-001" value="<?= e($data['reference_number']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Catatan Transaksi</label>
                <textarea id="notes" name="notes" class="form-textarea" rows="3" placeholder="Informasi tambahan, nama kurir, atau kondisi penerimaan barang..."><?= e($data['notes']) ?></textarea>
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
                    <span>Simpan Transaksi Masuk</span>
                </button>
                <a href="<?= baseUrl('index.php?page=transactions&action=history') ?>" class="btn btn-secondary">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>
