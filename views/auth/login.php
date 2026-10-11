<?php
/**
 * View Login
 */
?>
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
        </div>
        <h1 class="auth-title"><?= e(APP_NAME) ?></h1>
        <p class="auth-subtitle">Masuk untuk mengelola data inventaris dan mutasi stok</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error" role="alert">
            <span><?= e($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= baseUrl('index.php?page=login') ?>">
        <input type="hidden" name="csrf_token" value="<?= e(Sanitize::csrfToken()) ?>">

        <div class="form-group">
            <label for="username" class="form-label">Nama Pengguna (Username)</label>
            <input type="text" id="username" name="username" class="form-input" value="<?= e($username) ?>" required autofocus autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi (Password)</label>
            <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
            <span>Masuk ke Sistem</span>
        </button>
    </form>

    <div class="demo-account-box">
        <div class="demo-account-title">Kredensial Akun Bawaan (Demo):</div>
        <div><strong>Administrator:</strong> username <code>admin</code> / password <code>admin123</code></div>
        <div style="margin-top: 4px;"><strong>Staf Petugas:</strong> username <code>staff</code> / password <code>staff123</code></div>
    </div>
</div>
