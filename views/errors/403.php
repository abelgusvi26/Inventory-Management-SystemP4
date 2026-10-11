<?php
/**
 * View Error 403: Forbidden (Akses Dibatasi)
 */
require_once __DIR__ . '/../../app/Helpers/Sanitize.php';
require_once __DIR__ . '/../../config/app.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card" style="text-align: center;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background-color: var(--danger-subtle); color: var(--danger); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
        </div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">403 - Akses Terlarang</h1>
        <p style="font-size: 0.9375rem; color: var(--text-muted); margin-bottom: 1.5rem;">
            Maaf, akun Anda dengan peran <strong>Staf Petugas</strong> tidak memiliki otorisasi untuk mengakses halaman manajemen administrasi pengguna. Fitur ini hanya diperuntukkan bagi <strong>Administrator</strong>.
        </p>
        <a href="<?= baseUrl('index.php?page=dashboard') ?>" class="btn btn-primary" style="width: 100%;">
            Kembali ke Dashboard Utama
        </a>
    </div>
</div>
</body>
</html>
