<?php
/**
 * Menyusun ulang menu utama dengan struktur yang lazim dipakai situs sekolah.
 * Jalankan: wpd eval-file /var/www/html/scripts/susun-menu.php
 *
 * Item arsip memakai jenis post_type_archive dan halaman memakai post_type,
 * keduanya menyimpan rujukan objek dan bukan alamat, sehingga menu tetap
 * benar ketika situs dipindah ke domain lain.
 */

if (!defined('WP_CLI')) {
    exit("Script ini hanya untuk WP-CLI.\n");
}

$nama_menu = 'Menu Utama';
$menu = wp_get_nav_menu_object($nama_menu);

if (!$menu) {
    $id = wp_create_nav_menu($nama_menu);
    $menu = wp_get_nav_menu_object($id);
}

/* Kosongkan dulu supaya tidak menumpuk saat dijalankan ulang. */
foreach ((array) wp_get_nav_menu_items($menu->term_id) as $item) {
    wp_delete_post($item->ID, true);
}

$tambah = function ($judul, $tipe, $objek, $induk = 0) use ($menu) {

    $arg = [
        'menu-item-title' => $judul,
        'menu-item-status' => 'publish',
        'menu-item-parent-id' => $induk,
    ];

    if ('tautan' === $tipe) {
        $arg['menu-item-type'] = 'custom';
        $arg['menu-item-url'] = $objek;
    }

    if ('halaman' === $tipe) {
        $hal = get_page_by_path($objek, OBJECT, ['page']);
        if (!$hal) {
            WP_CLI::warning("Halaman '{$objek}' belum ada, item '{$judul}' dilewati.");
            return 0;
        }
        $arg['menu-item-type'] = 'post_type';
        $arg['menu-item-object'] = 'page';
        $arg['menu-item-object-id'] = $hal->ID;
    }

    if ('arsip' === $tipe) {
        if (!post_type_exists($objek)) {
            WP_CLI::warning("Jenis konten '{$objek}' belum terdaftar, item '{$judul}' dilewati.");
            return 0;
        }
        $arg['menu-item-type'] = 'post_type_archive';
        $arg['menu-item-object'] = $objek;
    }

    $id = wp_update_nav_menu_item($menu->term_id, 0, $arg);

    if (is_wp_error($id)) {
        WP_CLI::warning("Gagal menambah '{$judul}': " . $id->get_error_message());
        return 0;
    }

    WP_CLI::log(($induk ? '   - ' : ' + ') . $judul);
    return $id;
};

WP_CLI::log('Menyusun menu utama:');

$tambah('Beranda', 'tautan', '/');

$profil = $tambah('Profil', 'halaman', 'profil');
if ($profil) {
    $tambah('Visi & Misi', 'halaman', 'visi-misi', $profil);
    $tambah('Sambutan Kepala Sekolah', 'halaman', 'sambutan-kepala-sekolah', $profil);
    $tambah('Sarana & Prasarana', 'halaman', 'sarana-prasarana', $profil);
}

$akademik = $tambah('Akademik', 'tautan', '#');
$tambah('Program Keahlian', 'arsip', 'jurusan', $akademik);
$tambah('Guru & Tendik', 'arsip', 'guru', $akademik);

$informasi = $tambah('Informasi', 'tautan', '#');
$tambah('Prestasi', 'arsip', 'prestasi', $informasi);
$tambah('Agenda', 'arsip', 'agenda', $informasi);
$tambah('Galeri', 'arsip', 'galeri', $informasi);

$tambah('SPMB', 'halaman', 'spmb');
$tambah('Kontak', 'halaman', 'kontak');

/* Pastikan menu terpasang di lokasi tema. */
$lokasi = get_theme_mod('nav_menu_locations', []);
$lokasi['smkn1_utama'] = $menu->term_id;
set_theme_mod('nav_menu_locations', $lokasi);

$jumlah = count((array) wp_get_nav_menu_items($menu->term_id));
WP_CLI::success("Menu tersusun dengan {$jumlah} item dan terpasang di lokasi Menu Utama.");