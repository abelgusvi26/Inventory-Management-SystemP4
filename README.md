# Inventory Management System (Inventory Hub)

Sistem Informasi Manajemen Inventaris Berbasis **PHP Native Modern Modular / MVC & MySQL** untuk mata kuliah **Pemrograman Web (Semester 3 Sistem Informasi)** sesuai spesifikasi pada `Product Requirement Document (PRD).md`.

---

## 1. 9 Fitur Wajib PRD & Implementasi

| No | Fitur Wajib | Keterangan & File Implementasi |
|:---:|:---|:---|
| **1** | **Authentication** | Login aman dengan sanitasi, `password_verify()`, dan session fixation protection (`session_regenerate_id(true)`). Implementasi: `app/Controllers/AuthController.php`. |
| **2** | **Logout** | Logout aman yang membersihkan `$_SESSION`, mematikan session, dan menghapus session cookie. Implementasi: `app/Helpers/Auth.php`. |
| **3** | **Authorization** | Pembatasan akses berbasis peran (`admin` vs `staff`). Staff dilarang keras mengakses administrasi pengguna dengan respon `HTTP 403 Forbidden`. Implementasi: `app/Helpers/Auth.php`, `views/errors/403.php`. |
| **4** | **Master Data** | CRUD lengkap Kategori, Produk, dan Pemasok (Supplier) dengan validasi form server-side dan perlindungan referensial. Implementasi: `app/Controllers/ProductController.php`, `CategoryController.php`, `SupplierController.php`. |
| **5** | **Many-to-Many** | Relasi banyak-ke-banyak antara Produk dan Supplier melalui junction table `product_suppliers` dengan composite primary key `(product_id, supplier_id)` dan harga pasok (`supplier_price`). Implementasi: `app/Models/ProductSupplier.php`. |
| **6** | **Transaksi Mutasi Stok** | Pencatatan Barang Masuk (`IN`) dan Barang Keluar (`OUT`) menggunakan **Database Transaction PDO** (`beginTransaction`, `FOR UPDATE`, `commit`, `rollBack`). Validasi stok keluar tidak boleh melebihi stok tersedia. Implementasi: `app/Models/StockMovement.php`, `app/Controllers/TransactionController.php`. |
| **7** | **Listing Produk** | Pencarian berdasarkan nama produk atau SKU, filter kategori, sorting kolom aman berbasis allowlist, dan paginasi yang mempertahankan seluruh parameter URL. Implementasi: `app/Models/Product.php`, `views/products/index.php`. |
| **8** | **Dashboard & Laporan** | Dashboard dengan 5 widget metrik agregat, tabel peringatan stok rendah/habis, mutasi terkini, serta halaman Laporan Inventaris teragregasi dengan filter tanggal, tampilan cetak (print view), dan ekspor CSV/Excel. Implementasi: `app/Controllers/DashboardController.php`, `ReportController.php`. |
| **9** | **Security** | Prepared Statements 100% pada seluruh query PDO (anti SQL Injection), escaping `htmlspecialchars()` pada seluruh render tampilan (anti XSS), CSRF token validation pada seluruh form mutasi data, dan validasi sintaksis/semantik server-side. Implementasi: `app/Helpers/Sanitize.php`, `Validator.php`. |

---

## 2. Kredensial Akun Bawaan (Default Seed)

| Peran (Role) | Username | Kata Sandi | Hak Akses |
|:---|:---|:---|:---|
| **Administrator** | `admin` | `admin123` | Akses penuh: Master data (Kategori, Produk, Supplier), Relasi Many-to-Many, Transaksi Mutasi, Laporan, dan Manajemen Pengguna. |
| **Staf Petugas** | `staff` | `staff123` | Akses operasional: Melihat katalog produk, mencatat Barang Masuk (`IN`), mencatat Barang Keluar (`OUT`), riwayat mutasi, dan melihat laporan. *Dilarang mengakses manajemen pengguna (HTTP 403).* |

---

## 3. Skema Relasional Database (MySQL)

Sistem menggunakan database engine **InnoDB** dengan charset **utf8mb4**:
1. **One-to-One:** `users` $\leftrightarrow$ `user_profiles` (FK `user_id` dengan constraint `UNIQUE` dan `ON DELETE CASCADE`).
2. **One-to-Many:** `categories` $\rightarrow$ `products` (FK `category_id` dengan `ON DELETE RESTRICT` untuk mencegah penghapusan kategori yang masih menampung produk).
3. **Many-to-Many:** `products` $\leftrightarrow$ `suppliers` melalui tabel junction `product_suppliers` (Composite PK `(product_id, supplier_id)` dengan `supplier_price`).
4. **Audit History Transaksi:** `stock_movements` mencatat histori mutasi barang masuk dan keluar dengan relasi FK ke `products` (`ON DELETE RESTRICT`) dan `users` (`ON DELETE RESTRICT`).

---

## 4. Panduan Menjalankan Aplikasi

### Persyaratan Lingkungan:
- PHP 5.6+ / PHP 7.x / PHP 8.x (dengan ekstensi `pdo` dan `pdo_mysql`)
- MySQL / MariaDB (XAMPP Server)

### Langkah Instalasi:

1. **Pastikan MySQL XAMPP Aktif:**
   Nyalakan modul MySQL pada XAMPP Control Panel.

2. **Jalankan Migrasi Database Otomatis:**
   Buka terminal di direktori proyek ini, lalu jalankan:
   ```bash
   php database/migrate.php
   ```
   *Atau* Anda dapat mengimpor file `database/schema.sql` lalu `database/seed.sql` langsung melalui antarmuka phpMyAdmin.

3. **Jalankan Web Server Lokal:**
   Anda dapat menjalankan built-in server PHP:
   ```bash
   php -S localhost:8000
   ```
   Lalu buka browser Anda di: `http://localhost:8000`

   *Atau* jika ditempatkan di dalam folder `htdocs` XAMPP:
   `http://localhost/InventoryManagementSystem/`

---

## 5. Formula Mutasi Stok & Integritas Bisnis

Formula konsistensi mutasi stok:
$$\text{Stok Akhir} = \text{Stok Awal} + \text{Total Barang Masuk (IN)} - \text{Total Barang Keluar (OUT)}$$

Aturan pengamanan bisnis:
- Stok fisik tidak boleh bernilai negatif.
- Barang keluar (`OUT`) akan diverifikasi secara atomik menggunakan `SELECT stock ... FOR UPDATE`. Jika jumlah permintaan melebihi stok yang ada, transaksi otomatis dibatalkan (`rollBack`) dan menampilkan pesan peringatan.
- Produk yang telah memiliki riwayat mutasi pada tabel `stock_movements` tidak dapat dihapus sembarangan untuk menjaga keutuhan audit inventaris.
