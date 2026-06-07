# Website Perpustakaan PHP + Supabase PostgreSQL

Proyek ini siap diletakkan di `htdocs/perpustakaan` dan dibuka lewat `http://localhost/perpustakaan`.

## Struktur Folder

- `admin/` panel backend admin.
- `member/` panel anggota.
- `auth/` login, registrasi, logout.
- `config/database.php` konfigurasi koneksi Supabase PostgreSQL.
- `includes/` helper dan layout header/footer.
- `assets/css/style.css` stylesheet utama untuk semua halaman.
- `uploads/covers/` folder upload cover buku.
- `uploads/profiles/` folder upload foto anggota.
- `database/schema.sql` SQL tabel awal dan akun admin.

## Setup

1. Buat project Supabase, lalu buka SQL Editor.
2. Jalankan isi file `database/schema.sql`.
3. Edit `config/database.php`:
   - `DB_HOST` isi host database Supabase.
   - `DB_PASS` isi password database Supabase.
   - `BASE_URL` sesuaikan bila folder hosting berubah.
4. Pastikan ekstensi PHP `pdo_pgsql` aktif di XAMPP.
5. Buka `http://localhost/perpustakaan`.

## Akun Awal

Schema membuat akun:

- Email: `admin@perpustakaan.test`
- Password: `admin123`

## Catatan

- Denda otomatis dihitung Rp1.000 per hari setelah tanggal jatuh tempo.
- Pengajuan pinjam dari anggota berstatus `pending`.
- Admin menyetujui pinjaman, stok tersedia otomatis berkurang.
- Saat pengembalian dikonfirmasi, stok otomatis bertambah dan denda final disimpan.
