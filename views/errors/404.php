<?php
/**
 * View Error 404: Not Found
 */
?>
<div class="empty-state" style="padding: 4rem 1.5rem;">
    <div style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 0.5rem;">404</div>
    <div class="empty-title" style="font-size: 1.25rem;">Halaman Tidak Ditemukan</div>
    <div class="empty-desc">
        Halaman dengan rute <code><?= e(isset($page) ? $page : '') ?></code> tidak tersedia di dalam sistem inventaris ini.
    </div>
    <a href="<?= baseUrl('index.php?page=dashboard') ?>" class="btn btn-primary">
        Kembali ke Dashboard Utama
    </a>
</div>
