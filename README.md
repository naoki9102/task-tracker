# Task Tracker

Sistem manajemen tugas dengan fitur perhitungan remunerasi karyawan berdasarkan kontribusi.

## Arsitektur Solusi

### Diagram Alur Data
```
[Vue.js Frontend] <--> [Laravel API Backend] <--> [MySQL Database]
```

### Penjelasan Alur
- Frontend mengirimkan request (GET, POST, PUT, DELETE) ke Laravel API.
- Backend memproses logika bisnis dan berinteraksi dengan database.
- Database menyimpan data tugas dan kontribusi karyawan.
- Backend mengembalikan data yang kemudian dirender di frontend.

## Penjelasan Desain
- **Frontend** menggunakan Vue.js untuk kemudahan pengelolaan state dan UI reaktif.
- **Backend** menggunakan Laravel karena keunggulan dalam manajemen data, validasi, dan keamanan API.
- **Perhitungan Remunerasi**:
  - Total jam kerja semua karyawan dihitung.
  - Proporsi kontribusi tiap karyawan dihitung berdasarkan jam kerja.
  - Remunerasi dihitung dari total biaya tugas ditambah biaya tambahan, dikalikan dengan persentase kontribusi.
- Detail perhitungan disimpan di database agar transparan dan dapat direview kembali.

## Setup & Deploy

### Prasyarat
- PHP 8.2+
- MySQL 5.7+
- Node.js 14+
- Composer
- NPM

### Instalasi Backend
```bash
cd backend
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

### Instalasi Frontend
```bash
cd frontend
npm install
npm run dev
```

### Konfigurasi Database (.env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_tracker
DB_USERNAME=root
DB_PASSWORD=
```

### Menjalankan Aplikasi
1. Jalankan backend Laravel dengan `php artisan serve`.
2. Jalankan frontend Vue.js dengan `npm run dev`.
3. Akses aplikasi melalui `http://localhost:5137` (frontend) dan `http://localhost:8000/api` (backend).

## Tantangan & Solusi


| Tantangan                  | Solusi                                                           |
|-----------------------------|-----------------------------------------------------------------|
| Distribusi adil biaya       | Hitung berdasarkan persentase jam kerja                         |
| Sinkronisasi data frontend  | Pakai Vuex + optimistic update                                  |
| Validasi data               | Validasi di frontend & backend             |
| Performa loading data       | Pagination + lazy loading + caching                             |
| Error Handling              | Tampilkan pesan error yang user-friendly, log error di server   |
| Responsivitas UI            | Gunakan CSS framework responsif seperti Tailwind                |
| Keamanan API                | Middleware autentikasi, validasi input, proteksi CSRF           |
| Skala Data Besar            | Optimasi query database, gunakan indexing                       |
| Monitoring & Logging        | Integrasi dengan tools monitoring seperti Sentry atau LogRocket |
| Deployment CI/CD            | Otomasi deployment menggunakan GitHub Actions atau GitLab CI    |


### Tantangan Tambahan

- **Kompleksitas perhitungan remunerasi**: Harus memperhitungkan kemungkinan jam kerja yang tidak merata. Solusi: Implementasi unit test untuk fungsi perhitungan.
- **Manajemen hubungan banyak ke banyak (many-to-many)**: Antara tugas dan karyawan. Solusi: Gunakan model pivot `employee_tasks` dengan field tambahan.
- **Konsistensi Data saat update tugas**: Jika jam kerja atau kontribusi berubah, remunerasi harus otomatis dihitung ulang. Solusi: Trigger event recalculation di backend.
- **Pengujian manual berulang**: Saat memperbarui banyak skenario kontribusi. Solusi: Otomatiskan pengujian menggunakan PHPUnit untuk backend dan Cypress untuk frontend.

