<?php
/**
 * View Catat Barang Keluar (OUT) dengan Validasi Ketersediaan Stok
 */
?>
<div style="margin-bottom: 1.5rem;">
    <a href="<?= baseUrl('index.php?page=transactions&action=history') ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 0.5rem;">
        &larr; Riwayat Transaksi
    </a>
    <h2 style="font-size: 1.5rem; font-weight: 700;">Pencatatan Barang Keluar (Stock Out)</h2>
    <p class="form-hint" style="margin: 0;">Mencatat pengeluaran barang untuk kebutuhan operasional, penjualan, atau distribusi lab.</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <div>
            <strong>Gagal memproses pengeluaran barang:</strong>
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
        <form method="POST" action="<?= baseUrl('index.php?page=transactions&action=out') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">

            <div class="form-group">
                <label for="transactionProductSelect" class="form-label">Pilih Produk <span class="required">*</span></label>
                <select id="transactionProductSelect" name="product_id" class="form-select" required autofocus>
                    <option value="">Pilih item produk yang akan dikeluarkan...</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" data-stock="<?= (int)$p['stock'] ?>" <?= ($data['product_id'] == $p['id']) ? 'selected' : '' ?>>
                            [<?= e($p['sku']) ?>] <?= e($p['name']) ?> (Stok tersedia: <?= (int)$p['stock'] ?> unit)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p id="availableStockHint" class="form-hint">Pilih produk untuk melihat sisa stok tersedia.</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="transactionQuantityInput" class="form-label">Jumlah Barang Keluar <span class="required">*</span></label>
                    <input type="number" min="1" id="transactionQuantityInput" name="quantity" class="form-input" placeholder="Minimal 1 unit" value="<?= e($data['quantity']) ?>" required>
                    <p class="form-hint">Jumlah tidak boleh melebihi sisa stok fisik yang tersedia.</p>
                </div>

                <div class="form-group">
                    <label for="reference_number" class="form-label">Nomor Surat Jalan / Bukti Pengeluaran</label>
                    <input type="text" id="reference_number" name="reference_number" class="form-input mono" placeholder="Contoh: DO-2026-001" value="<?= e($data['reference_number']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Keterangan / Tujuan Pengeluaran</label>
                <textarea id="notes" name="notes" class="form-textarea" rows="3" placeholder="Nama penerima, keperluan divisi, nomor tiket, atau alasan pengeluaran barang..."><?= e($data['notes']) ?></textarea>
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                    <span>Simpan Transaksi Keluar</span>
                </button>
                <a href="<?= baseUrl('index.php?page=transactions&action=history') ?>" class="btn btn-secondary">
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>
</div>
