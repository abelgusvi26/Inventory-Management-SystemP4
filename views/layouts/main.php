<?php
/**
 * Main Layout Shell
 */
require_once __DIR__ . '/../../app/Helpers/Auth.php';
require_once __DIR__ . '/../../app/Helpers/Sanitize.php';

$currentUser = Auth::user();
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$currentAction = isset($_GET['action']) ? $_GET['action'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> - Sistem Informasi Inventaris</title>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body>
<div class="app-layout">
    <!-- Sidebar Navigasi -->
    <aside class="sidebar" aria-label="Menu Navigasi Utama">
        <div class="sidebar-header">
            <a href="<?= baseUrl('index.php?page=dashboard') ?>" class="brand-logo">
                <div class="brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                </div>
                <span><?= e(APP_NAME) ?></span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <a href="<?= baseUrl('index.php?page=dashboard') ?>" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                <span>Dashboard</span>
            </a>

            <div class="nav-section-title">Master Data</div>
            <a href="<?= baseUrl('index.php?page=products') ?>" class="nav-item <?= $currentPage === 'products' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                <span>Katalog Produk</span>
            </a>
            <a href="<?= baseUrl('index.php?page=categories') ?>" class="nav-item <?= $currentPage === 'categories' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Kategori</span>
            </a>
            <a href="<?= baseUrl('index.php?page=suppliers') ?>" class="nav-item <?= $currentPage === 'suppliers' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <span>Pemasok / Supplier</span>
            </a>

            <div class="nav-section-title">Operasional Transaksi</div>
            <a href="<?= baseUrl('index.php?page=transactions&action=in') ?>" class="nav-item <?= ($currentPage === 'transactions' && $currentAction === 'in') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
                <span>Barang Masuk (IN)</span>
            </a>
            <a href="<?= baseUrl('index.php?page=transactions&action=out') ?>" class="nav-item <?= ($currentPage === 'transactions' && $currentAction === 'out') ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m5 12 7-7 7 7"/></svg>
                <span>Barang Keluar (OUT)</span>
            </a>
            <a href="<?= baseUrl('index.php?page=transactions&action=history') ?>" class="nav-item <?= ($currentPage === 'transactions' && ($currentAction === 'history' || $currentAction === '')) ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Riwayat Mutasi Stok</span>
            </a>

            <div class="nav-section-title">Analisis & Laporan</div>
            <a href="<?= baseUrl('index.php?page=reports') ?>" class="nav-item <?= $currentPage === 'reports' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                <span>Laporan Inventaris</span>
            </a>

            <?php if (Auth::isAdmin()): ?>
            <div class="nav-section-title">Administrasi</div>
            <a href="<?= baseUrl('index.php?page=users') ?>" class="nav-item <?= $currentPage === 'users' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Manajemen User</span>
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            Versi <?= e(APP_VERSION) ?> &bull; Semester 3
        </div>
    </aside>

    <!-- Area Konten Utama -->
    <div class="main-area">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Buka Menu Navigasi">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
                <h1 class="topbar-title">Sistem Manajemen Inventaris</h1>
            </div>

            <div class="topbar-right">
                <!-- Theme Switcher -->
                <button type="button" class="theme-toggle-btn" id="themeToggleBtn" aria-label="Ganti Tema">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    <span>Mode Gelap</span>
                </button>

                <!-- Info User -->
                <div class="user-badge-group">
                    <div class="user-avatar" aria-hidden="true">
                        <?= strtoupper(substr(isset($currentUser['username']) ? $currentUser['username'] : 'U', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <?php 
                            $nameDisplay = (isset($currentUser['full_name']) && $currentUser['full_name']) ? $currentUser['full_name'] : (isset($currentUser['username']) ? $currentUser['username'] : 'User');
                            $roleDisplay = isset($currentUser['role']) ? $currentUser['role'] : 'guest';
                        ?>
                        <span class="user-name"><?= e($nameDisplay) ?></span>
                        <span class="user-role badge <?= $roleDisplay === 'admin' ? 'badge-primary' : 'badge-neutral' ?>">
                            <?= e($roleDisplay) ?>
                        </span>
                    </div>
                </div>

                <a href="<?= baseUrl('index.php?page=logout') ?>" class="btn btn-secondary btn-sm" title="Keluar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    <span>Keluar</span>
                </a>
            </div>
        </header>

        <!-- Body Konten -->
        <main class="content-wrapper" id="mainContent">
            <!-- Flash Pesan Sukses / Error -->
            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success" role="alert">
                    <span><?= e($flashSuccess) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashError)): ?>
                <div class="alert alert-error" role="alert">
                    <span><?= e($flashError) ?></span>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<script src="<?= baseUrl('assets/js/app.js') ?>"></script>
</body>
</html>
