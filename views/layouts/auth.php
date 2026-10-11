<?php
/**
 * Layout Khusus Autentikasi (Login)
 */
require_once __DIR__ . '/../../app/Helpers/Sanitize.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body>
<div class="auth-wrapper">
    <?= $content ?>
</div>
<script src="<?= baseUrl('assets/js/app.js') ?>"></script>
</body>
</html>
