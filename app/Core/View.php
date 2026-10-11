<?php
/**
 * View Renderer untuk menyusun tampilan halaman dengan layout terpadu
 */

class View {
    /**
     * Render view di dalam layout tertentu
     * 
     * @param string $viewPath Jalur relatif dalam views/ (misal: 'products/index')
     * @param array $data Data yang dikirim ke view
     * @param string $layout Nama layout ('main', 'auth', 'print', atau null)
     */
    public static function render($viewPath, array $data = [], $layout = 'main') {
        // Ekstrak data menjadi variabel lokal di view
        extract($data);

        // Ambil flash messages lalu hapus dari session
        $flashSuccess = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
        $flashError   = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        // Tangkap konten view ke output buffer
        $viewFile = __DIR__ . '/../../views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("File view tidak ditemukan: " . htmlspecialchars($viewPath));
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Jika tidak ada layout, langsung tampilkan konten view
        if ($layout === null) {
            echo $content;
            return;
        }

        // Render layout pembungkus
        $layoutFile = __DIR__ . '/../../views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            http_response_code(500);
            die("File layout tidak ditemukan: " . htmlspecialchars($layout));
        }

        require $layoutFile;
    }
}
