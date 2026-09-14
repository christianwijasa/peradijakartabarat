# Kantong Magang Advokat — DPC PERADI Jakarta Barat

Platform web untuk mengelola program magang wajib 24 bulan bagi calon advokat (alumni lulus UPA) di bawah DPC PERADI Jakarta Barat, dibangun dari mockup `Platform_Magang_Fase_1`. Dibangun dengan **Laravel 10** dan **MySQL** (PHP **8.1+**).

## Peran pengguna

Aplikasi memiliki tiga peran dengan login terpisah (satu akun = satu peran):

- **Calon Advokat** — dashboard alur magang, cari & melamar lowongan, riwayat lamaran, logbook digital harian, paket berkas sumpah.
- **Law Firm** (advokat pendamping) — dashboard & kuota bimbingan, review pelamar & terbitkan surat penerimaan, review & tanda tangani logbook.
- **Admin DPC** — verifikasi dua level (calon advokat & kantor hukum/pendamping), monitoring kepatuhan logbook dan audit akhir kelayakan sumpah.

## Instalasi lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Buat database MySQL lalu sesuaikan kredensial di `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peradi_jakbar
DB_USERNAME=peradi
DB_PASSWORD=
```

Jalankan migrasi beserta data contoh:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

## Akun demo (password: `password`)

| Peran | Email |
|---|---|
| Calon Advokat | andi@peradijakbar.test |
| Law Firm / Pendamping | hendra@peradijakbar.test |
| Admin DPC | admin@peradijakbar.test |

Registrasi mandiri (`/register`) tersedia untuk peran Calon Advokat. Akun Law Firm dan Admin DPC diprovisi langsung oleh DPC (tidak melalui halaman registrasi publik).

## Struktur data inti

`LawFirm`, `AdvokatPendamping`, `CalonAdvokat`, `Lowongan`, `Lamaran`, `LogbookEntry`, `LogbookRekapBulanan`, `VerifikasiChecklist`, `BerkasSumpah`, `AuditAkhir` — lihat `database/migrations` dan `app/Models`.
