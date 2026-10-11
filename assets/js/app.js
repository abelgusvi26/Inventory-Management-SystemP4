/**
 * Script Interaktivitas Aplikasi Inventory
 * Mengelola Tema (Light/Dark), Navigasi Mobile, dan Validasi Formulir Dinamis
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Pengelola Tema Terang / Gelap (R-21 & R-34)
    initThemeManager();

    // 2. Navigasi Mobile Drawer (R-03)
    initMobileNav();

    // 3. Helper Dinamis Stok Keluar (Stock Out Availability Indicator)
    initStockOutHelper();
});

function initThemeManager() {
    const themeBtn = document.getElementById('themeToggleBtn');
    const root = document.documentElement;
    
    // Cek preferensi tersimpan atau preferensi sistem operasi
    const savedTheme = localStorage.getItem('inventory_theme');
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');

    applyTheme(initialTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const currentTheme = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
            localStorage.setItem('inventory_theme', newTheme);
        });
    }

    function applyTheme(theme) {
        if (theme === 'dark') {
            root.setAttribute('data-theme', 'dark');
            if (themeBtn) {
                themeBtn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                    <span>Mode Terang</span>
                `;
            }
        } else {
            root.removeAttribute('data-theme');
            if (themeBtn) {
                themeBtn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    <span>Mode Gelap</span>
                `;
            }
        }
    }
}

function initMobileNav() {
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.querySelector('.sidebar');

    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !mobileBtn.contains(e.target) && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }
}

function initStockOutHelper() {
    const productSelect = document.getElementById('transactionProductSelect');
    const quantityInput = document.getElementById('transactionQuantityInput');
    const stockHint = document.getElementById('availableStockHint');

    if (productSelect && quantityInput && stockHint) {
        function updateStockAvailability() {
            const selectedOpt = productSelect.options[productSelect.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) {
                stockHint.textContent = 'Pilih produk untuk melihat sisa stok tersedia.';
                stockHint.className = 'form-hint';
                quantityInput.removeAttribute('max');
                return;
            }

            const currentStock = parseInt(selectedOpt.getAttribute('data-stock') || '0', 10);
            quantityInput.setAttribute('max', currentStock);

            if (currentStock <= 0) {
                stockHint.textContent = 'Peringatan: Stok produk ini kosong (0 unit). Tidak dapat mengeluarkan barang.';
                stockHint.className = 'form-hint text-danger';
                quantityInput.disabled = true;
            } else {
                stockHint.textContent = `Sisa stok tersedia saat ini: ${currentStock} unit.`;
                stockHint.className = 'form-hint';
                quantityInput.disabled = false;
            }
        }

        productSelect.addEventListener('change', updateStockAvailability);
        updateStockAvailability();
    }
}
