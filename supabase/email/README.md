# Pasang email konfirmasi Botani di production

Perubahan kode di repository **tidak otomatis mengubah pengaturan Supabase Auth**. Template HTML di direktori ini harus disimpan di dashboard Supabase. `SUPABASE_SECRET_KEY` dan koneksi database aplikasi tidak memberi izin untuk mengubah konfigurasi Auth; cara otomatis memerlukan token Supabase Management API yang terpisah.

## 1. Benahi tujuan tautan konfirmasi

1. Buka Supabase Dashboard dan pilih project `rlhtgxafqqxbokpvwfzn`.
2. Buka **Authentication → URL Configuration**.
3. Ubah **Site URL** menjadi `https://www.botaniapps.site`, lalu simpan.
4. Di **Redirect URLs**, tambahkan `https://www.botaniapps.site/auth/callback`, lalu simpan. Biarkan URL lokal yang sudah ada bila masih dipakai untuk pengembangan.

`botaniapps.site` saat ini mengalihkan ke `www.botaniapps.site`, jadi URL production yang dipakai di semua tempat adalah versi `www`. Site URL yang masih `http://localhost:3000` menyebabkan fallback konfirmasi mengarah ke komputer lokal. URL lama yang sudah berada di email pengguna tidak ikut berubah.

## 2. Pasang desain email

1. Di project Supabase yang sama, buka **Authentication → Email Templates** (pada beberapa tampilan: **Authentication → Emails → Templates**).
2. Pilih template **Confirm sign up / Confirm signup**. Jangan mengubah template Magic Link atau Reset Password untuk tugas ini.
3. Isi **Subject**: `Konfirmasi akun Botani Phanerogamae`.
4. Ganti seluruh isi **Body / HTML** dengan seluruh isi [`confirm-signup.html`](./confirm-signup.html), lalu simpan. Jika editor mempunyai pilihan visual/kode, gunakan mode HTML/kode agar markup tidak berubah.
5. Pastikan tombol tetap memiliki `href="{{ .ConfirmationURL }}"`. Jangan menggantinya dengan URL login biasa: tautan Supabase ini membawa token konfirmasi.

Template menentukan **isi** email saja. Templat ini tidak mengubah nama atau alamat pengirim.

## 3. Atur nama dan alamat pengirim

Supabase menggunakan pengirim bawaannya sampai **custom SMTP** diaktifkan. Untuk pengirim `Botani Phanerogamae <no-reply@botaniapps.site>`:

1. Siapkan mailbox atau layanan pengiriman email yang mendukung SMTP dan domain `botaniapps.site`. Alamat `no-reply@botaniapps.site` harus benar-benar disiapkan/diizinkan oleh penyedia; menuliskannya di Supabase saja tidak membuat alamat tersebut.
2. Ikuti instruksi penyedia untuk menambahkan record SPF, DKIM, dan DMARC di DNS Hostinger. Jangan menghapus record A/CNAME situs atau record MX email yang sudah digunakan.
3. Buka **Authentication → SMTP Settings** di Supabase, aktifkan custom SMTP, lalu masukkan host, port, username, password dari penyedia; **Sender email** `no-reply@botaniapps.site` dan **Sender name** `Botani Phanerogamae`. Simpan dan gunakan fitur kirim email uji jika tersedia.

Tanpa kredensial SMTP, langkah 1–2 tetap dapat memperbaiki tautan dan tampilan email, tetapi nama pengirim tetap bawaan Supabase. Jangan memasukkan password SMTP ke repo atau mengirimkannya dalam chat.

## 4. Samakan Vercel dan deploy kode

1. Buka **Vercel → project Botani → Settings → Environment Variables**.
2. Edit `NEXT_PUBLIC_SITE_URL` khusus **Production** menjadi `https://www.botaniapps.site` tanpa tanda kutip jika mengisi kolom Value. Simpan.
3. Deploy perubahan kode di repository ini ke branch production. Perubahan callback/login dan template lokal belum ada di Vercel sebelum deployment baru.
4. Pastikan deployment terbaru berstatus Ready. Perubahan environment variable juga hanya berlaku untuk deployment baru.

## 5. Uji dari awal sampai akhir

1. Buka `https://www.botaniapps.site/register` dan daftar dengan **alamat email uji baru** yang belum terdaftar. Jika belum memasang custom SMTP, pengiriman dari SMTP bawaan Supabase dapat dibatasi ke anggota team project.
2. Email yang diterima harus bersubjek `Konfirmasi akun Botani Phanerogamae`, menampilkan desain Botani, dan tombol **Konfirmasi email saya**. Jika SMTP telah diatur, nama pengirim harus `Botani Phanerogamae`.
3. Klik tombol. Supabase memverifikasi token, mengembalikan browser ke `https://www.botaniapps.site/auth/callback`, lalu aplikasi membawa pengguna ke `https://www.botaniapps.site/login?confirmed=<UUID acak>` dengan pesan bahwa akun sudah aktif. UUID ini hanya penanda tampilan, bukan token keamanan; verifikasi akun tetap dilakukan Supabase.
4. Masuk dengan email dan kata sandi yang baru didaftarkan. Pastikan dashboard akun terbuka.

Jika masih menuju `localhost`, periksa lagi Site URL Supabase, Redirect URLs, nilai env **Vercel Production**, dan apakah email yang diuji dikirim **setelah** perubahan tersimpan. Jika template sudah bagus tetapi pengirim masih Supabase, custom SMTP belum aktif atau belum berhasil diverifikasi.

Rujukan: [Supabase Redirect URLs](https://supabase.com/docs/guides/auth/redirect-urls), [Supabase Email Templates](https://supabase.com/docs/guides/auth/auth-email-templates), [Supabase Custom SMTP](https://supabase.com/docs/guides/auth/auth-smtp).
