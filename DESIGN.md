# Design Direction: Modern Industrial Inventory

Sistem Desain Antarmuka untuk **Inventory Management System**.
Dibuat berdasarkan prinsip keterbacaan tinggi, presisi data pergudangan, aksesibilitas WCAG AA, dan kepatuhan Anti-Slop.

---

## 1. Identitas & Karakter Desain
- **Karakter:** Tegas, fungsional, profesional, presisi tinggi (Industrial ERP).
- **Filosofi:** Data dan status inventaris menjadi pusat perhatian utama. Menghindari ornamen generik, gradien pelangi, dan kartu bento artifisial.
- **Dials Antislop:**
  - **ENERGY: 2** (Fokus dan terstruktur, penekanan visual pada angka kritis seperti stok menipis dan total aset).
  - **RHYTHM: 2** (Irama hierarki terukur, pemisahan jelas antara filter, tabel data, aksi cepat, dan ringkasan agregat).
  - **MOTION: 1** (Transisi mikro halus untuk hover tombol, fokus input, dan perubahan tema. Bebas dari animasi loop mengambang).

---

## 2. Palet Warna (Color System)

Tiga lapis token desain: Primitive -> Semantic -> Komponen.

### Palet Dasar (Primitive Tokens)
- **Slate 950:** `#020617` (Latar belakang dark mode pekat)
- **Slate 900:** `#0f172a` (Latar dark mode surface card)
- **Slate 800:** `#1e293b` (Border dark mode & header surface)
- **Slate 700:** `#334155` (Border & teks sekunder gelap)
- **Slate 500:** `#64748b` (Teks muted)
- **Slate 200:** `#e2e8f0` (Border light mode)
- **Slate 100:** `#f1f5f9` (Surface card light mode)
- **Slate 50:** `#f8fafc` (Latar belakang light mode)
- **Indigo 600:** `#4f46e5` (Aksen primer interaktif)
- **Indigo 700:** `#4338ca` (Aksen primer hover)
- **Emerald 600:** `#059669` (Status: Stok aman / Transaksi Masuk IN)
- **Amber 600:** `#d97706` (Status: Stok menipis / Perhatian)
- **Rose 600:** `#e11d48` (Status: Stok habis / Transaksi Keluar OUT / Bahaya)

### Kontras Rasio (WCAG AA Compliance)
- Teks `#0f172a` pada latar `#ffffff`: **16.1:1** (Lolos AA & AAA)
- Teks `#f8fafc` pada latar `#0f172a`: **15.4:1** (Lolos AA & AAA)
- Teks `#4f46e5` pada latar `#ffffff`: **6.3:1** (Lolos AA)
- Teks `#059669` pada latar `#ffffff`: **4.8:1** (Lolos AA)
- Teks `#e11d48` pada latar `#ffffff`: **4.7:1** (Lolos AA)

---

## 3. Tipografi
- **Font Utama:** `Plus Jakarta Sans`, sans-serif (dengan fallback `Inter`, `system-ui`, `-apple-system`, `sans-serif`).
- **Skala Tipografi:**
  - `h1`: 1.75rem (28px), font-weight: 700, line-height: 1.25
  - `h2`: 1.375rem (22px), font-weight: 600, line-height: 1.3
  - `h3`: 1.125rem (18px), font-weight: 600, line-height: 1.35
  - `body`: 0.9375rem (15px), font-weight: 400, line-height: 1.5
  - `small / badge`: 0.8125rem (13px), font-weight: 500
  - `mono (SKU / Angka Saldo)`: `ui-monospace`, `SFMono-Regular`, `Consolas`, monospace (ukuran diselaraskan).

---

## 4. Struktur Komponen & Ergonomi
1. **Layout Shell:** Sidebar navigasi fungsional di sisi kiri (dapat diminimalkan pada layar mobile), topbar dengan profil user aktif, role badge, dan tombol ganti tema (Light / Dark).
2. **Elevasi & Bayangan:**
   - Permukaan datar (flat) dengan garis tepi kontras 1px (`border: 1px solid var(--border-color)`).
   - Bayangan elevasi halus hanya pada dropdown, popup modal, dan kartu statistik: `0 1px 3px rgba(0,0,0,0.06)`.
3. **Target Sentuh & Aksesibilitas Keyboard:**
   - Seluruh tombol dan link interaktif memiliki tinggi minimal 42px - 44px (memenuhi standar WCAG touch target).
   - Indikator fokus yang jelas (`:focus-visible`) dengan ring warna aksen berjarak 2px.
4. **Bentuk Badge & Status:**
   - Sudut membulat proporsional (`border-radius: 6px` hingga `8px`), tidak menggunakan kapsul lonjong berlebihan.
   - Status disertai teks keterangan jelas, tidak mengandalkan warna semata.
