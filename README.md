# Kantong Magang Advokat — DPC PERADI Jakarta Barat

Platform web untuk mengelola program magang wajib 24 bulan bagi calon advokat (alumni lulus UPA) di bawah DPC PERADI Jakarta Barat, dibangun dari mockup `Platform_Magang_Fase_1`. Dibangun dengan **Laravel 11** dan **MySQL**.

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

## Demo Walkthrough Fase 1 — Happy Path

Berikut langkah-langkah untuk menjalankan demo lengkap fitur inti Fase 1:

### 1. Login sebagai Law Firm (Hendra Wibisono)
**Email:** `hendra@peradijakbar.test` | **Password:** `password`

**Buat lowongan magang baru:**
- Navigasi ke **"Lowongan Magang"** dari sidebar
- Klik **"+ Buat lowongan baru"**
- Isi form:
  - Judul: `Magang Calon Advokat — Litigasi Pidana`
  - Deskripsi: `Pendampingan perkara pidana di Pengadilan Negeri Jakarta Barat`
  - Bidang: Pilih `Pidana`, `Litigasi`, `Full-time`
  - Kuota: `3`
- Klik **"Publikasikan lowongan"**
- Lowongan akan muncul dengan status **Aktif**

**Review pelamar dan ubah status lamaran:**
- Navigasi ke **"Pelamar"** dari sidebar
- Cari lamaran dengan status **Terkirim** (contoh: Maya Ramadhani)
- Klik **"Review CV"** → status berubah ke **Review CV**
- Klik **"Jadwalkan Interview"** → status berubah ke **Interview**
- Klik **"Terima"** → status berubah ke **Diterima**, surat penerimaan otomatis diterbitkan
- Coba terima pelamar lagi setelah kuota penuh → akan muncul error **"Kuota lowongan telah penuh"**

**Review dan tandatangani logbook:**
- Navigasi ke **"Review Logbook"**
- Pilih calon advokat dengan entri menunggu TTD (contoh: Andi Prasetyo)
- Untuk entri dengan status **Menunggu ttd**:
  - Klik **"Setujui"** untuk menyetujui entri
  - Atau klik **"Revisi"**, masukkan catatan revisi, dan kirim
- Setelah semua entri bulan berjalan disetujui:
  - Klik **"Tandatangani seluruh entri & rekap bulanan"**
  - Status rekap berubah menjadi **Ditandatangani**

### 2. Login sebagai Calon Advokat (Andi Prasetyo)
**Email:** `andi@peradijakbar.test` | **Password:** `password`

**Lihat dan lamar lowongan:**
- Navigasi ke **"Cari Lowongan"** dari sidebar
- Lihat lowongan yang baru dibuat Law Firm
- Klik **"Lamar posisi ini"** untuk melamar
- Navigasi ke **"Lamaran Saya"** untuk melihat status lamaran

**Kelola logbook dengan revisi:**
- Navigasi ke **"Logbook Digital"**
- Jika ada entri dengan status **Revisi** dan catatan revisi dari pendamping:
  - Klik **"Edit & kirim ulang"**
  - Form edit akan muncul dengan data lama
  - Perbaiki uraian sesuai catatan revisi
  - Klik **"Kirim ulang"**
  - Status berubah kembali ke **Menunggu ttd**

**Tambah catatan logbook baru:**
- Gunakan form **"Tambah catatan harian"** di sidebar kanan
- Pilih jenis kegiatan: `Riset hukum`
- Isi uraian: `Riset yurisprudensi terkait gugatan perdata wanprestasi`
- Durasi: `4` jam
- Klik **"Kirim ke pendamping"**
- Entri baru akan muncul dengan status **Menunggu ttd**

**Upload berkas sumpah:**
- Navigasi ke **"Berkas Sumpah"**
- Untuk dokumen dengan status **Berjalan** atau **Menunggu** (contoh: Sertifikat PKPA):
  - Klik tombol **"Choose File"** di bawah nama dokumen
  - Pilih file PDF/JPG/PNG (max 10MB)
  - Klik **"Unggah"**
  - Status berubah menjadi **Lengkap** dan ukuran file ditampilkan
  - Link **"Unduh berkas"** akan muncul untuk download

### 3. Login sebagai Admin DPC
**Email:** `admin@peradijakbar.test` | **Password:** `password`

**Verifikasi calon advokat dan law firm:**
- Navigasi ke **"Verifikasi"** dari sidebar
- Tab **Calon Advokat**: Review calon advokat yang menunggu verifikasi
  - Periksa checklist verifikasi
  - Klik **"Setujui & verifikasi"** untuk calon yang memenuhi syarat
- Tab **Kantor Hukum**: Review kantor hukum yang menunggu verifikasi
  - Periksa checklist verifikasi
  - Klik **"Tetapkan kuota & verifikasi"** untuk kantor yang memenuhi syarat

**Monitoring dan audit akhir:**
- Navigasi ke **"Monitoring & Audit"**
- Lihat **Kepatuhan logbook per kantor hukum** dengan persentase real-time
- Lihat **Peringatan sistem** untuk alert logbook terlambat, kuota penuh, dll.
- Di bagian **Audit akhir · siap sumpah**:
  - Untuk calon advokat mendekati bulan ke-24 (contoh: Rizky Alamsyah):
    - Klik **"Ubah status"**
    - Pilih status audit:
      - **Lulus audit** — jika logbook lengkap dan berkas lengkap
      - **Dalam proses** — jika masih ada rekap belum ditandatangani
      - **Berkas kurang** — jika berkas belum lengkap
    - Masukkan catatan untuk calon advokat
    - Klik **"Simpan"**
  - Status **Lulus audit** + berkas lengkap → membuka akses download paket berkas sumpah untuk calon advokat

### 4. Validasi Happy Path Lengkap

**Skenario sukses end-to-end:**
1. ✅ Admin DPC verifikasi firm → Law Firm dapat buat lowongan
2. ✅ Firm buat lowongan aktif dengan kuota 3
3. ✅ Calon advokat lamar lowongan
4. ✅ Firm review lamaran: terkirim → review_cv → interview → diterima
5. ✅ Surat penerimaan otomatis diterbitkan dan tersimpan di berkas calon
6. ✅ Firm tolak accept saat kuota penuh → error clear muncul
7. ✅ Calon isi logbook harian → Firm review dan revisi
8. ✅ Calon edit & resubmit entri revisi → status kembali menunggu ttd
9. ✅ Firm tandatangani bulk rekap bulanan
10. ✅ Calon upload berkas sumpah → status lengkap
11. ✅ Admin set audit status lulus_audit dengan catatan
12. ✅ Calon dapat download paket berkas setelah audit lulus + berkas lengkap

## Fitur Implementasi Fase 1

### ✅ Sudah Diimplementasi
- **Firm Lowongan CRUD** — Create, edit, activate, deactivate lowongan (hanya firm terverifikasi)
- **Lamaran Mid-Status Actions** — Transisi status: terkirim → review_cv → interview → diterima | tidak_lanjut
- **Hard Quota Check** — Validasi kuota penuh saat terima lamaran dengan pesan error jelas
- **Logbook Revisi Loop** — Calon dapat edit & resubmit entri dengan status "revisi"
- **Berkas File Upload/Download** — Upload berkas sumpah dengan storage lokal + download
- **Admin Audit Actions** — Set status audit: lulus_audit | dalam_proses | berkas_kurang dengan catatan
- **Acceptance Letter** — Generate surat penerimaan otomatis saat lamaran diterima

### 🚧 Out of Scope (Fase 2)
- Mobile app, WhatsApp integration
- Payment processing
- PKPA/UPA registration flows
- Pengadilan Tinggi export ZIP package
- Email notifications (gunakan log driver untuk sekarang)

## Testing

Jalankan test suite untuk memvalidasi fitur:

```bash
php artisan test --filter=FirmLowonganTest
php artisan test --filter=LamaranStatusTest
php artisan test --filter=LogbookRevisiTest
```
