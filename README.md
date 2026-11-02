<div align="center">

  <h1>Bengkel Tracker Admin & Backend API</h1>
  <p><b>Platform Backend & Panel Administrasi Manajemen Pelacakan Layanan Bengkel</b></p>

  [![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
  [![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev/)
  [![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
  [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](./LICENSE)

  <br />

  [Laporkan Bug](https://github.com/fahmiwulidan/bengkel-tracker-admin/issues) •
  [Minta Fitur](https://github.com/fahmiwulidan/bengkel-tracker-admin/issues)

</div>

---

## Ringkasan Proyek

**Bengkel Tracker Admin & Backend API** adalah pusat kendali administrator dan penyedia layanan RESTful API untuk ekosistem aplikasi **Bengkel Tracker**[cite: 24]. Platform ini bertanggung jawab mengelola master data perbengkelan, jadwal dan penugasan mekanik, riwayat perbaikan kendaraan, pemantauan koordinat SIG/GIS, serta rekapitulasi laporan transaksi pembayaran.

> [!NOTE]
> Aplikasi ini dikembangkan menggunakan framework **Laravel** dengan bundler **Vite** serta telah dilengkapi dengan konfigurasi **Docker** untuk mempermudah proses komputasi server dan penggelaran (*deployment*)[cite: 24, 25, 26].

---

## Teknologi Utama

| Layer | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel (PHP) | Framework web utama pengolah logika bisnis & RESTful API service[cite: 25, 26] |
| **Frontend Admin** | Blade Templating & Vite | Dashboard administrasi web interaktif dan teroptimasi[cite: 25, 26] |
| **Containerization** | Docker & Docker Compose | Lingkungan isolasi aplikasi untuk kemudahan deployment[cite: 24, 25] |
| **Database & ORM** | Eloquent ORM & MySQL | Pengelolaan data relasional transaksional dan pelacakan[cite: 24, 26] |

---

## Fitur Unggulan

- **RESTful API Service** — Endpoint API terpusat untuk komunikasi data *real-time* dengan aplikasi *mobile* (`bengkel-tracker-app`)[cite: 24].
- **Dashboard Manajemen Bengkel** — Panel kendali terpusat untuk pengelola dalam memantau operasional bengkel, daftar antrean servis, dan status mekanik.
- **Dukungan Container Docker** — Dilengkapi berkas `Dockerfile`, `.dockerignore`, `entrypoint.sh`, dan `Procfile` untuk penyetelan kontainer server instan[cite: 25].
- **Manajemen Transaksi & Laporan** — Rekapitulasi pembayaran dan riwayat pengerjaan kendaraan secara akurat.

---

## Struktur Direktori

```text
bengkel-tracker-admin/
├── api/              # Berkas & konfigurasi khusus REST API
├── app/              # Core aplikasi Laravel (Controllers, Models, Middleware)
├── bootstrap/        # Skrip inisialisasi framework & cache
├── config/           # Berkas konfigurasi sistem (app, database, auth, dll)
├── database/         # Migrasi, seeder, & pabrik data (factories)
├── docker/           # Berkas konfigurasi tambahan untuk Docker environment
├── public/           # Entry point aplikasi web (`index.php`) & aset publik
├── resources/        # Tampilan Blade, komponen UI, & aset mentah (CSS/JS)
├── routes/           # Penataan alur URL web (`web.php`) & API (`api.php`)
├── storage/          # Penyimpanan log sistem, berkas unggahan, & cache
├── tests/            # Pengujian otomatis (Unit & Feature Testing)
├── Dockerfile        # Berkas instruksi pembuatan kontainer Docker
├── Procfile          # Konfigurasi perintah eksekusi proses deployment
├── entrypoint.sh     # Skrip shell pembuka layanan kontainer
├── vite.config.js    # Konfigurasi bundler aset Vite
└── README.md         # Dokumentasi utama repositori
```

---

<div align="center">
  <sub>Dikembangkan dengan ❤️ oleh <a href="https://github.com/fahmiwulidan">Fahmi Wulidan</a></sub>
</div>
