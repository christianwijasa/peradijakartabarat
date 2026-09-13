# Demo Accounts — Kantong Magang Advokat

Quick reference for all seeded demo accounts (password: `password` for all).

## Law Firm Accounts

### Hendra Wibisono (Wibisono & Rekan)
- **Email:** `hendra@peradijakbar.test`
- **Role:** Law Firm / Advokat Pendamping
- **Status:** Verified
- **Use for:**
  - Creating lowongans
  - Reviewing and accepting pelamar
  - Reviewing logbook entries
  - Signing monthly recap

## Calon Advokat Accounts

### Andi Prasetyo (Main Demo Account)
- **Email:** `andi@peradijakbar.test`
- **Kode CA:** CA-2025-0417
- **Status:** Active, 14/24 months
- **Firm:** Wibisono & Rekan
- **Pendamping:** Dr. Hendra Wibisono
- **Use for:**
  - Applying to lowongans
  - Managing logbook (has pending entries)
  - Uploading berkas sumpah
  - Testing revision workflow

### Rizky Alamsyah
- **Email:** `rizky@peradijakbar.test`
- **Kode CA:** CA-2024-0288
- **Status:** Active, 22/24 months
- **Firm:** Wibisono & Rekan
- **Audit Status:** Dalam proses
- **Use for:**
  - Testing near-completion scenarios
  - Admin audit workflow

### Maya Ramadhani
- **Email:** `maya@peradijakbar.test`
- **Kode CA:** CA-2026-0058
- **Status:** Pending verification
- **Lamaran Status:** Interview (at Wibisono & Rekan)
- **Use for:**
  - Testing lamaran status transitions
  - Admin verification workflow

### Other Calon Advokat Accounts
- **Nadia Kusuma:** `nadia@peradijakbar.test` (11/24 months, full logbook)
- **Fajar Nugroho:** `fajar@peradijakbar.test` (3/24 months, late logbook)
- **Bagus Setiawan:** `bagus@peradijakbar.test` (pending verification, needs fixes)
- **Laras Nuraini:** `laras@peradijakbar.test` (pending verification)
- **Dewi Anggraini:** `dewi@peradijakbar.test` (24/24 months, lulus audit)
- **Yoga Permana:** `yoga@peradijakbar.test` (24/24 months, berkas kurang)

## Admin DPC Account

### Sekretariat DPC
- **Email:** `admin@peradijakbar.test`
- **Role:** Admin DPC Jakarta Barat
- **Use for:**
  - Verifying calon advokat
  - Verifying law firms
  - Setting kuota for firms
  - Monitoring logbook compliance
  - Setting audit status (lulus_audit, dalam_proses, berkas_kurang)

## Test Scenarios by Account

### Happy Path (Full Flow)
1. **Admin** (`admin@peradijakbar.test`):
   - Verify pending firm (Wijaya Legal Consult)
   - Verify pending calon (Maya Ramadhani)

2. **Law Firm** (`hendra@peradijakbar.test`):
   - Create new lowongan
   - Review Maya's lamaran: terkirim → review_cv → interview → diterima
   - Review Andi's logbook, send one entry back for revision
   - Bulk sign monthly recap

3. **Calon** (`andi@peradijakbar.test`):
   - Apply to new lowongan
   - Edit and resubmit revised logbook entry
   - Add new logbook entry
   - Upload berkas (Sertifikat PKPA, Sertifikat Lulus UPA, Ijazah)

4. **Admin** (`admin@peradijakbar.test`):
   - Monitor logbook compliance chart
   - Set audit status for Rizky to "lulus_audit"

5. **Calon** (`rizky@peradijakbar.test`):
   - Download oath package (now unlocked)

### Quota Validation
1. **Law Firm** (`hendra@peradijakbar.test`):
   - Create lowongan with kuota = 1
   - Accept first lamaran → Success
   - Try to accept second lamaran → Error: "Kuota lowongan telah penuh"

### Revision Loop
1. **Law Firm** (`hendra@peradijakbar.test`):
   - Go to Review Logbook → Select Andi
   - Send entry back with revisi note

2. **Calon** (`andi@peradijakbar.test`):
   - Go to Logbook Digital
   - See red revisi banner with firm's note
   - Click "Edit & kirim ulang"
   - Update entry and resubmit
   - Status changes back to menunggu_ttd

### File Upload/Download
1. **Calon** (`andi@peradijakbar.test`):
   - Go to Berkas Sumpah
   - Upload Sertifikat PKPA (PDF/JPG/PNG, max 10MB)
   - Status changes to "Lengkap"
   - Download link appears
   - Click download → file downloads successfully

## Default Passwords

All accounts use password: **`password`**

## Existing Lowongans (Seeded)

1. **Magang Calon Advokat — Litigasi Korporasi** (Santika, Hartono & Partners)
   - Bidang: Litigasi, Kepailitan, Full-time
   - Kuota: 10

2. **Magang Calon Advokat — Hukum Keluarga** (Kantor Hukum Ardiansyah)
   - Bidang: Perdata, Keluarga, Hybrid
   - Kuota: 10

3. **Magang Calon Advokat — Bantuan Hukum** (LBH Kampus Universitas Trisakti)
   - Bidang: Pidana, Prodeo, Full-time
   - Kuota: 10

4. **Magang Calon Advokat — Litigasi Perdata & Kepailitan** (Wibisono & Rekan)
   - Bidang: Litigasi, Korporasi, Full-time
   - Kuota: 10
   - Has existing lamarans from Maya, Bagus, Laras, Rina, Andi
