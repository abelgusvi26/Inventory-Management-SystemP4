<?php
/**
 * Layout Khusus Cetak Laporan (Print-Friendly Layout)
 */
require_once __DIR__ . '/../../app/Helpers/Sanitize.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
    <style>
        body { background: #ffffff !important; color: #0f172a !important; padding: 2rem; }
        .print-toolbar { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem; }
        @media print {
            .print-toolbar { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body>
<div class="print-toolbar">
    <div>
        <strong>Mode Cetak Laporan Inventaris</strong>
        <p class="form-hint">Tekan tombol cetak di bawah atau gunakan kombinasi Ctrl+P / Cmd+P.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            <span>Cetak Dokumen</span>
        </button>
        <button type="button" onclick="window.close(); window.history.back();" class="btn btn-secondary">
            <span>Kembali</span>
        </button>
    </div>
</div>

<main>
    <?= $content ?>
</main>

<script>
    // Otomatis tawarkan cetak setelah halaman terbuka
    window.addEventListener('DOMContentLoaded', () => {
        // window.print();
    });
</script>
</body>
</html>
