# 🌈 JULES – Platform Showcase Karya Seni Digital

**𝓙𝓤𝓛𝐸𝓢** adalah platform showcase karya seni digital yang dirancang untuk komunitas kreatif. Platform ini menjadi wadah bagi kreator (Member) untuk membangun portofolio, memfasilitasi interaksi (Like, Favorite, Comment), serta menyelenggarakan kompetisi seni (Challenge) melalui Kurator.

Proyek ini dibangun menggunakan struktur *multi-role* dengan fokus pada pengalaman pengguna yang dinamis dan *design aesthetic* yang modern (skema **Hitam, Putih, dan Biru Aksen**).

---

## ✨ Fitur Utama & Roles

Aplikasi ini memiliki **4 peran pengguna (Role)** dengan hak akses dan tampilan Dashboard yang terpisah:

| Peran | Fokus Utama | Fitur Kunci yang Diimplementasikan |
| :--- | :--- | :--- |
| **🎨 Member** | **Kreasi & Portofolio** | **CRUD Karya**, Like, Favorite, Comment, **Avatar & Background Profil**, Submit Karya ke Challenge. |
| **🏆 Curator** | **Challenge Management** | **CRUD Challenge**, Tinjauan Submissions, **Penetapan Pemenang** (Juara 3-1-2), Dashboard Fokus Challenge. |
| **🛡️ Admin** | **Integritas Platform** | **Manajemen Pengguna (CRUD)**, **Persetujuan Curator (Approval)**, Moderasi Laporan (Take Down/Dismiss), Manajemen Kategori. |
| **👤 Guest** | **Eksplorasi** | Galeri Karya (Masonry Layout), Detail Challenge (termasuk Podium Pemenang), Search & Filter. |

---

## 🎨 Design & Teknologi

- **Design Style**: Modern, Clean, Minimalis, menggunakan skema 60/30/10 (Hitam/Putih/Biru).
- **Interaksi UX**: Efek *Hover* interaktif (Mirip Pinterest/Dribbble) pada semua kartu.
- **Frontend Styling**: Bootstrap 5 + Tailwind CSS Utility (melalui Vite)
- **Backend Framework**: Laravel 12 (PHP)
- **Database**: MySQL/MariaDB

---

## 🚀 Panduan Instalasi (Development)

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal Anda.

### 🔧 Prasyarat
- PHP ≥ 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB

### 📥 1. Clone Repositori
```bash
git clone [https://www.andarepository.com/](https://www.andarepository.com/)
cd [NAMA FOLDER PROYEK]
````

### 📦 2. Install Dependencies

```bash
composer install
npm install
```

### ⚙️ 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`. Atur konfigurasi database (DB\_HOST=127.0.0.1, DB\_PORT=3306, dll.) dan **Timezone** (`Asia/Makassar` atau `Asia/Jakarta`).

### 🔑 4. Generate Key & Migrasi Database

Untuk memastikan *schema* database yang benar:

```bash
php artisan key:generate
php artisan migrate:fresh --seed 
```

### 🖼️ 5. Link Storage (Wajib Gambar)

```bash
php artisan storage:link
```

-----

## ▶️ 6. Jalankan Aplikasi

**Terminal 1 — Laravel Server**

```bash
php artisan serve
```

**Terminal 2 — Vite Build**

```bash
npm run dev
```

Akses aplikasi melalui: 👉 **http://127.0.0.1:8000**

-----

## 📝 Catatan Alur Kritis

### 🔐 Akses Admin (Initial Login)

  - Admin harus dibuat melalui *Database Seeder* atau diubah manual di tabel `users` (`role` = **admin**).

### 🧾 Alur Pendaftaran Kurator

  - User memilih role **Curator** saat registrasi.
  - Status awal adalah **Pending**. User tidak otomatis *login*.
  - **Admin** harus login → `/admin/users` → dan **Menyetujui** akun Kurator tersebut agar mereka dapat mengakses Dashboard.

-----

```
```
