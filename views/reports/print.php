<?php
/**
 * View Cetak Laporan (Print Layout)
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
?>

<div style="border-bottom: 2px solid #0f172a; padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 1.625rem; font-weight: 700; margin-bottom: 0.25rem;"><?= e(APP_NAME) ?></h1>
        <p style="margin: 0; font-size: 1rem; color: #334155;">LAPORAN REKAPITULASI MUTASI & NILAI ASET INVENTARIS GUDANG</p>
    </div>
    <div style="text-align: right; font-size: 0.8125rem; color: #475569;">
        <div>Tanggal Cetak: <strong><?= e($printedAt) ?></strong></div>
        <div>Petugas: <strong><?= e($printedBy) ?></strong></div>
    </div>
</div>

<div style="margin-bottom: 1.25rem; font-size: 0.875rem; background: #f8fafc; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 6px;">
    <strong>Parameter Laporan:</strong> 
    Kategori: <u><?= e($categoryName) ?></u> &bull;
    Periode Mutasi: 
    <u><?= $startDate ? e($startDate) : 'Awal' ?> s/d <?= $endDate ? e($endDate) : 'Sekarang' ?></u>
</div>

<table class="data-table" style="border: 1px solid #94a3b8; width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f1f5f9; border-bottom: 2px solid #94a3b8;">
            <th style="border: 1px solid #cbd5e1; padding: 8px;">No</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">SKU</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Nama Produk</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Kategori</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Harga Satuan</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Total Masuk</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Total Keluar</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Stok Akhir</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Total Nilai Aset</th>
            <th style="border: 1px solid #cbd5e1; padding: 8px;">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($reportData as $row): ?>
            <tr>
                <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;"><?= $no++ ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px; font-family: monospace; font-weight: bold;"><?= e($row['sku']) ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px;"><?= e($row['product_name']) ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px;"><?= e($row['category_name']) ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: right; font-family: monospace;"><?= Sanitize::rupiah($row['price']) ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;"><?= (int) $row['total_in'] ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;"><?= (int) $row['total_out'] ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: center; font-weight: bold;"><?= (int) $row['current_stock'] ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: right; font-family: monospace;"><?= Sanitize::rupiah($row['total_asset_value']) ?></td>
                <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">
                    <?php if ((int)$row['current_stock'] === 0): ?>
                        Habis
                    <?php elseif ((int)$row['current_stock'] <= (int)$row['minimum_stock']): ?>
                        Menipis
                    <?php else: ?>
                        Aman
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #0f172a;">
            <td colspan="5" style="border: 1px solid #cbd5e1; padding: 10px; text-align: right;">TOTAL KESELURUHAN:</td>
            <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;"><?= number_format($totalInAll) ?></td>
            <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;"><?= number_format($totalOutAll) ?></td>
            <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;"><?= number_format($totalStockAll) ?></td>
            <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: right; font-family: monospace;"><?= Sanitize::rupiah($totalValuation) ?></td>
            <td style="border: 1px solid #cbd5e1; padding: 10px; text-align: center;">-</td>
        </tr>
    </tfoot>
</table>

<!-- Kolom Tanda Tangan Pengesahan Dokumen -->
<div style="margin-top: 3.5rem; display: flex; justify-content: space-between; page-break-inside: avoid;">
    <div style="text-align: center; width: 220px;">
        <p style="margin-bottom: 4rem;">Dibuat Oleh,</p>
        <p style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;"><?= e($printedBy) ?></p>
        <p style="font-size: 0.8125rem; color: #475569;">Staf Administrasi Inventaris</p>
    </div>
    <div style="text-align: center; width: 220px;">
        <p style="margin-bottom: 4rem;">Mengetahui / Menyetujui,</p>
        <p style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">Kepala Bagian Logistik</p>
        <p style="font-size: 0.8125rem; color: #475569;">Manajer Operasional Gudang</p>
    </div>
</div>
