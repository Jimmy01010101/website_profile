# Website Profil SMK Negeri 1 Bengkayang

Situs profil sekolah berbasis WordPress, dijalankan di atas Docker.

## Teknologi

| Komponen | Versi |
|---|---|
| WordPress | 7.0.x |
| PHP | 8.3 (Apache) |
| MariaDB | 11 |
| Astra (tema induk) | 4.13.x |
| Advanced Custom Fields | 6.8.x (versi gratis) |

Fitur berbayar ACF seperti Options Page dan Repeater **tidak** dipakai.
Pengaturan situs memakai Settings API bawaan WordPress, dan daftar berulang
diganti dengan jenis konten tersendiri atau kolom tetap.

## Menjalankan

```bash
git clone https://github.com/Jimmy01010101/website_profile.git smkn1-wp
cd smkn1-wp
docker compose up -d --build
```

Buka http://localhost:8080 lalu jalankan pemasangan WordPress.

Setelah masuk dashboard:

```bash
alias wpd="docker compose exec -u www-data wordpress wp"
wpd plugin activate smkn1-core
wpd theme activate astra-child
wpd rewrite flush --hard
```

- Situs → http://localhost:8080
- phpMyAdmin → http://localhost:8081

## Struktur

```
compose.yaml                  layanan db, wordpress, phpmyadmin
Dockerfile                    WP-CLI, .htaccess, bersihkan plugin bawaan
scripts/ganti-domain.sh       migrasi domain saat pindah hosting
data/                         contoh berkas impor
wp-content/plugins/smkn1-core sisi data dan pengaturan
wp-content/themes/astra-child sisi tampilan
```

### Plugin `smkn1-core`

| Berkas | Isi |
|---|---|
| `post-types.php` | 6 jenis konten: jurusan, guru, prestasi, galeri, agenda, slide |
| `taxonomies.php` | 4 taksonomi beserta istilah awalnya |
| `acf-fields*.php` | definisi field, ditulis sebagai kode agar masuk Git |
| `settings-page.php` | Pengaturan Situs empat tab |
| `halaman-statis.php` | pembuat enam halaman wajib beserta isinya |
| `hardening.php` | pengamanan untuk model satu admin |
| `seo.php` | meta deskripsi, Open Graph, data terstruktur sekolah |
| `admin/` | halaman impor CSV dan XLSX |

### Tema `astra-child`

Tema turunan Astra. Seluruh template ditulis sendiri: beranda, arsip dan
detail untuk tiap jenis konten, halaman statis, pencarian, dan halaman 404.
Header serta footer menggantikan bawaan Astra lewat hook `astra_header`
dan `astra_footer`.

## Mengelola isi

**Pengaturan Situs** — identitas, kontak, media sosial, statistik beranda,
sambutan kepala sekolah, banner SPMB, visi dan misi.

**Impor Data** — submenu di bawah Jurusan dan Guru & Tendik. Unggah CSV atau
XLSX, tersedia mode uji coba sebelum menyimpan. Baris yang namanya sudah ada
akan diperbarui, bukan diduplikasi.

**Foto guru** bersifat opsional, bisa lewat Featured Image satu per satu atau
lewat kolom Foto pada berkas impor. Guru tanpa foto tampil memakai inisial.

## Yang tidak tersimpan di Git

Isi situs, media, menu, dan pengaturan berada di **basis data**, bukan di
berkas. Cadangkan secara terpisah:

```bash
docker compose exec db mariadb-dump -u wpuser -pwppass123 smkn1_wp > cadangan.sql
docker compose exec -T db mariadb -u wpuser -pwppass123 smkn1_wp < cadangan.sql
```

Berkas unggahan ada di `wp-content/uploads/` dan juga tidak dilacak Git.

## Pindah ke hosting

```bash
./scripts/ganti-domain.sh http://localhost:8080 https://domain-baru.sch.id
```

Skrip akan mencadangkan basis data lebih dulu, menampilkan uji coba, lalu
meminta konfirmasi sebelum mengubah.

## Catatan

- Editor blok dimatikan untuk jenis konten berupa data murni. Editor klasik
  lebih sesuai karena isinya sepenuhnya berupa field, bukan tulisan bebas.
- Berkas di `wp-content` dimiliki `www-data` di dalam container. Setelah
  menambah berkas dari luar, jalankan:
  ```bash
  sudo chown -R $USER:www-data wp-content/plugins/smkn1-core wp-content/themes/astra-child
  chmod -R g+rX wp-content/plugins/smkn1-core wp-content/themes/astra-child
  ```

## Perintah harian

```bash
docker compose up -d      # nyalakan
docker compose stop       # matikan, data tetap aman
docker compose down -v    # hapus semuanya termasuk basis data
```
