# Botani Phanerogamae — Next.js + Supabase + Drizzle

Aplikasi full-stack Botani Phanerogamae berbasis Next.js yang siap dijalankan di Vercel. Semua kredensial dan alamat deployment dibaca dari environment variable; tidak ada akun, password, URL Supabase, atau service key yang ditanam di source code.

## Stack

- Next.js `16.3.1` dengan App Router
- React `19.2.0`
- TypeScript `5.9.3` dalam strict mode
- Tailwind CSS `4.3.0`
- tRPC `11.18.0` + TanStack Query
- Drizzle ORM `0.45.2` + PostgreSQL.js untuk query database server-side
- Supabase Auth, PostgreSQL, Row Level Security, dan Storage
- Vercel untuk hosting aplikasi

## Fitur yang dimigrasikan

- Halaman publik: beranda, katalog/filter tumbuhan, detail spesimen, daftar materi, reader subbab, dan halaman tentang.
- Autentikasi email/password dengan tiga role: `mahasiswa`, `dosen`, dan `admin`.
- Dashboard role-based dan pemeriksaan akses di server, tRPC, serta RLS database.
- CRUD spesimen, morfologi, taksonomi, gambar, bab, subbab, kuis, bank soal, dan pengguna.
- Pengerjaan kuis bertimer, resume attempt, kalkulasi nilai transaksional, riwayat, serta pembahasan hasil.
- Seed 5 dataset observasi, gambar lokal, 9 bab, dan 62 subbab ke Supabase.
- Kunci jawaban disimpan pada tabel terpisah sehingga tidak dapat dibaca mahasiswa sebelum penilaian.

## Struktur penting

```text
app/                    App Router pages dan tRPC route handler
components/             UI publik, auth, dashboard, dan form pengelolaan
lib/supabase/           Supabase client server, browser, admin, dan storage
db/schema.ts            Schema Drizzle typed untuk seluruh tabel aplikasi
db/client.ts            Koneksi Drizzle via Supabase Transaction Pooler
server/api/routers/     Seluruh backend tRPC per domain
supabase/migrations/    Schema PostgreSQL, RLS, trigger, dan RPC database
scripts/                Migration runner, seed dataset, dan promosi role pengguna
types/database.ts       Tipe database tanpa explicit any
data/                   Sumber 9 bab dan 62 subbab
json-data/              Sumber data observasi tumbuhan
storages/images/        Sumber gambar yang diunggah oleh seed script
```

## Menjalankan pemeriksaan lokal

### Cron harian langsung di Supabase

Tabel `system_heartbeat` terdaftar di `db/schema.ts`. Migrasi
`supabase/migrations/202609090001_system_heartbeat.sql` memasang tabel dengan
RLS tertutup, fungsi internal, extension `pg_cron`, serta job
`botani-daily-heartbeat` pada `0 17 * * *` UTC (**00.00 WIB**).
Setiap hit memperbarui satu baris: `last_seen`, `source`, dan `hit_count`.

Drizzle adalah ORM proyek. Runner `npm run db:migrate` menggunakan SQL ber-checksum
untuk mengelola tabel, RLS, fungsi, dan extension. Jangan menggantikannya dengan
`drizzle-kit push`: deklarasi tabel saja tidak memasang fungsi dan jadwal cron.

Persiapan production:

1. `npm run env:production` membuat file privat `.env.production` dari kredensial
   database lokal, tanpa akun seed, dan tidak menimpa file production yang sudah ada.
   Verifikasi target database dan isi `NEXT_PUBLIC_SITE_URL` dengan domain HTTPS
   production yang sebenarnya.
2. `npm run db:migrate:production` membaca `.env.production` secara eksplisit,
   tanpa fallback ke `.env.local`. `DIRECT_URL` harus memakai role pemilik database
   yang boleh mengaktifkan `pg_cron`. Setelah migrasi berhasil, job langsung aktif.
3. Impor variabel aplikasi ke Vercel Production dan deploy versi ini untuk
   menghapus jadwal Vercel lama. File lokal yang diabaikan Git tidak otomatis
   menjadi environment Vercel.

Jika hostname direct tidak tersedia dari jaringan lokal, gunakan Session Pooler
dari Dashboard Supabase. Runner transaksi proyek ini juga telah diuji lewat
Transaction Pooler yang sudah dikonfigurasi. Setelah memverifikasi koneksinya,
`node scripts/setup-production-env.mjs --use-pooler-for-migrations` memilih alamat pooler
yang sama untuk migrasi production, setelah mencocokkan project ref.
Pemeriksaan SQL sebelum aktivasi: `npx tsx scripts/check-heartbeat-migration.ts`
(memakai environment lokal secara default; seluruh perubahan di-rollback).

`CRON_SECRET` acak 256-bit (Base64 URL-safe) disiapkan sesuai permintaan, tetapi
tidak dipakai oleh `pg_cron` dan tidak wajib diimpor ke Vercel.
Cron berjalan di database tanpa endpoint HTTP, library cron, atau proses Node permanen.

Verifikasi melalui Supabase SQL Editor:

```sql
select jobname, schedule, active from cron.job
where jobname = 'botani-daily-heartbeat';
select * from public.system_heartbeat;
select status, start_time, end_time from cron.job_run_details
where jobid in (select jobid from cron.job where jobname = 'botani-daily-heartbeat')
order by start_time desc limit 10;
```

Tabel kosong sampai fungsi pertama kali berjalan. Tes manual sebagai pemilik DB:
`select botani_internal.record_heartbeat();`.
Untuk menonaktifkan job ini saja: `select cron.unschedule('botani-daily-heartbeat');`.

Heartbeat internal tidak menjamin Supabase Free bebas pause; dokumentasi pausing
membahas aktivitas query pengguna. Job juga berhenti jika project dipause.
Referensi: [Supabase Cron](https://supabase.com/docs/guides/cron/quickstart),
[project pausing](https://supabase.com/docs/guides/platform/free-project-pausing).

Gambar kartu tumbuhan, detail, dan konten Markdown memakai lazy loading serta
decoding async. Logo memiliki `sizes` 96px agar browser tidak meminta gambar
500px untuk tampilan kecil; logo utama tetap dimuat segera.

```bash
npm install
npm run lint
npm run typecheck
npm run build
```

Node.js minimal `20.9.0`.

### Jika halaman lokal terus menampilkan 404

Pastikan browser membuka alamat yang dicetak oleh `npm run dev`. Jika server lama
masih berjalan ketika struktur `app/` berubah, hentikan server proyek dengan
`Ctrl+C`, lalu jalankan kembali `npm run dev`. Jangan menjalankan dua server untuk
checkout yang sama. Rute seperti `/login` harus menampilkan formulir masuk.

Endpoint tRPC menggunakan nama prosedur, misalnya `/api/trpc/auth.current`;
`/api/trpc` sendiri bukan halaman atau prosedur. URL pengelolaan Laravel
`/manage/plants/create`, `/manage/modules/create`, dan `/manage/quizzes/create`
diarahkan ke rute `/new` yang sesuai.

Error layanan pada detail materi/tumbuhan menampilkan tombol coba lagi; hanya
data yang tidak ditemukan yang menampilkan 404. Jalankan regresinya dengan
`npx tsx --test server/page-error.test.ts server/api/routers/auth.test.ts`.

Beranda dan daftar modul memuat data setelah request masuk sehingga build tidak
bergantung pada ketersediaan database. Jika muncul `ENOTFOUND` saat runtime,
periksa Project URL Supabase dan status proyek; build yang berhasil tidak berarti
koneksi database sudah sehat.

## Setup Supabase dari nol

### 1. Buat project Supabase

1. Masuk ke Supabase Dashboard.
2. Buat project baru dan simpan database password di password manager.
3. Tunggu sampai database berstatus siap.

### 2. Ambil API key dan connection string

Di Supabase Dashboard klik **Connect**, lalu salin:

- Project URL ke `NEXT_PUBLIC_SUPABASE_URL`.
- Publishable key (`sb_publishable_...`) ke `NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY`.

Untuk server key, buka **Settings → API Keys → Publishable and secret API keys**, lalu salin Secret key (`sb_secret_...`) ke `SUPABASE_SECRET_KEY`. Bila belum tersedia, klik **Create new API keys** terlebih dahulu.

Secret key hanya boleh berada di server. Jangan pernah mengubah namanya menjadi berawalan `NEXT_PUBLIC_`.

Kemudian klik tombol **Connect** di halaman project dan salin dua connection string PostgreSQL:

- **Transaction pooler** (umumnya port `6543`) ke `DATABASE_URL`. Ini dipakai Drizzle saat runtime di Vercel.
- **Direct connection** atau **Session pooler** (port `5432`) ke `DIRECT_URL`. Ini dipakai migration runner dan Drizzle tooling.

Gunakan Session pooler untuk `DIRECT_URL` bila jaringan lokal tidak mendukung IPv6. Password database yang mengandung karakter khusus harus di-URL-encode. Jangan menyusun connection string dari tebakan; salin host, user, dan region dari dialog **Connect** project Supabase.

### 3. Buat file environment lokal

PowerShell:

```powershell
Copy-Item .env.example .env.local
```

Bash:

```bash
cp .env.example .env.local
```

`.env.example` sudah memuat seluruh variable yang benar-benar dipakai aplikasi. Ganti semua placeholder `YOUR_...`:

```dotenv
NEXT_PUBLIC_SITE_URL=http://localhost:3000
NEXT_PUBLIC_SUPABASE_URL=https://YOUR_PROJECT_REF.supabase.co
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=sb_publishable_YOUR_KEY
SUPABASE_SECRET_KEY=sb_secret_YOUR_KEY
DATABASE_URL=postgresql://postgres.YOUR_PROJECT_REF:YOUR_URL_ENCODED_DATABASE_PASSWORD@YOUR_POOLER_HOST:6543/postgres?sslmode=require
DIRECT_URL=postgresql://postgres.YOUR_PROJECT_REF:YOUR_URL_ENCODED_DATABASE_PASSWORD@YOUR_POOLER_HOST:5432/postgres?sslmode=require
NEXT_PUBLIC_STORAGE_BUCKET=plant-images
```

Nama bucket bebas. Seed script akan membuat bucket publik dengan nama yang diisi pada `NEXT_PUBLIC_STORAGE_BUCKET`, kemudian menyimpan nama tersebut ke konfigurasi database untuk dipakai RLS Storage.

### 4. Terapkan schema database

Setelah `.env.local` diisi, jalankan:

```bash
npm run db:migrate
```

Perintah ini membaca semua file `.sql` di `supabase/migrations` secara berurutan, menjalankannya dalam transaksi, menyimpan checksum di schema privat `botani_internal`, dan aman dijalankan ulang. Bila migration sudah pernah berhasil, file tersebut akan dilewati. Bila isi migration lama berubah setelah diterapkan, proses berhenti agar riwayat schema tidak rusak.

Alternatif manual bila koneksi database lokal diblokir jaringan:

1. Buka **SQL Editor** di Supabase Dashboard.
2. Buat query baru.
3. Salin seluruh isi `supabase/migrations/202608200001_initial_schema.sql`.
4. Jalankan query satu kali.

Migration membuat seluruh tabel, enum, foreign key, index, trigger profil, RLS policy, policy Storage, dan fungsi transaksi untuk penyimpanan modul, tumbuhan, serta penilaian kuis.

### Arsitektur ORM dan akses database

- Drizzle adalah ORM typed untuk query PostgreSQL dari server tepercaya. Koneksi runtime memakai `DATABASE_URL` dengan `prepare: false`, sesuai Transaction pooler/serverless Vercel.
- Supabase client tetap dipakai untuk autentikasi dan request yang harus membawa sesi/JWT pengguna agar RLS berjalan per user.
- Operasi multi-tabel sensitif seperti submit kuis memakai PostgreSQL RPC agar atomik dan tetap dilindungi authorization database.
- File SQL di `supabase/migrations` adalah sumber utama DDL karena RLS policy, trigger, function, dan Storage policy tidak seluruhnya dapat direpresentasikan oleh schema ORM.
- `db/schema.ts` mencerminkan seluruh tabel publik untuk type-safe query; tidak ada explicit `any`.

### 5. Atur Supabase Auth URL

Di **Authentication → URL Configuration**:

1. Untuk lokal, set Site URL ke `http://localhost:3000`.
2. Tambahkan Redirect URL `http://localhost:3000/auth/callback`.
3. Setelah deployment, tambahkan `https://DOMAIN-VERCEL/auth/callback` dan ubah Site URL ke domain produksi.

Jika email confirmation diaktifkan, pendaftaran akan meminta pengguna membuka tautan konfirmasi. Jika dimatikan, pengguna langsung mendapat sesi setelah registrasi.

### 6. Impor data dan gambar

Setelah `.env.local` lengkap dan migration berhasil:

```bash
npm run db:seed
```

Script ini bersifat idempotent untuk data sumber: data utama di-upsert berdasarkan kode/slug, bucket dibuat berdasarkan environment, gambar di-upload dengan `upsert`, dan lesson sumber disinkronkan ulang. Script tidak membuat akun contoh atau password bawaan.

### 7. Buat administrator pertama

1. Jalankan aplikasi dengan `npm run dev`.
2. Buka `/register` dan daftarkan akun pertama.
3. Jika email confirmation aktif, konfirmasi email akun tersebut.
4. Promosikan akun menggunakan email yang baru didaftarkan:

```bash
npm run user:promote -- EMAIL_ANDA admin
```

Untuk menjadikan akun lain sebagai dosen:

```bash
npm run user:promote -- EMAIL_DOSEN dosen
```

Nilai email dan role berasal dari argumen command, bukan source code.

### 8. Jalankan aplikasi lokal

```bash
npm run dev
```

Buka `http://localhost:3000`.

## Deploy ke Vercel

1. Push repository ke Git provider.
2. Di Vercel pilih **Add New → Project**, lalu import repository.
3. Vercel akan mendeteksi Next.js. Build command tetap `npm run build` dan output mengikuti default Next.js.
4. Tambahkan ketujuh environment variable yang sama dari `.env.local` ke Project Settings → Environment Variables. `DATABASE_URL`, `DIRECT_URL`, dan `SUPABASE_SECRET_KEY` tidak boleh diberi prefix `NEXT_PUBLIC_`.
5. Untuk production, ubah `NEXT_PUBLIC_SITE_URL` menjadi URL Vercel atau custom domain dengan skema `https://`.
6. Deploy project.
7. Kembali ke Supabase Auth URL Configuration dan tambahkan callback domain Vercel seperti pada langkah sebelumnya.
8. Lakukan redeploy jika nilai `NEXT_PUBLIC_*` berubah karena nilai publik tersebut ditanam saat build.

## Catatan keamanan

- Authorization tidak bergantung pada UI. Role diverifikasi di tRPC procedure dan PostgreSQL RLS.
- `SUPABASE_SECRET_KEY` hanya digunakan oleh seed script, promosi role, dan administrasi akun pada server.
- `DATABASE_URL` dan `DIRECT_URL` adalah kredensial database server-only dan tidak pernah diakses Client Component.
- Registrasi publik selalu menghasilkan role `mahasiswa`; pengguna tidak dapat memilih role sendiri.
- Kunci jawaban berada pada `question_keys`, tidak pada baris opsi yang dapat dibaca mahasiswa.
- Jangan commit `.env`, `.env.local`, atau Supabase secret key.

## Perintah

| Perintah | Fungsi |
|---|---|
| `npm run dev` | Menjalankan development server |
| `npm run lint` | Memeriksa lint termasuk larangan explicit `any` |
| `npm run typecheck` | Memeriksa TypeScript strict tanpa emit |
| `npm run build` | Membuat production build |
| `npm run check` | Menjalankan lint, typecheck, dan build |
| `npm run db:migrate` | Menerapkan migration SQL Supabase yang belum dijalankan |
| `npm run db:studio` | Membuka Drizzle Studio dengan `DIRECT_URL` |
| `npm run db:introspect` | Membaca schema aktif dari Supabase melalui Drizzle Kit |
| `npm run db:seed` | Mengimpor dataset, modul, dan gambar ke Supabase |
| `npm run user:promote -- EMAIL ROLE` | Mengubah role akun yang sudah terdaftar |
