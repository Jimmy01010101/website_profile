<?php
if (!defined('ABSPATH'))
    exit;

add_action('init', 'smkn1_register_taxonomies');

function smkn1_register_taxonomies()
{

    register_taxonomy('bidang_keahlian', 'jurusan', [
        'labels' => [
            'name' => 'Bidang Keahlian',
            'singular_name' => 'Bidang Keahlian',
            'add_new_item' => 'Tambah Bidang Keahlian',
            'menu_name' => 'Bidang Keahlian',
        ],
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'bidang-keahlian'],
    ]);

    register_taxonomy('kelompok_ptk', 'guru', [
        'labels' => [
            'name' => 'Kelompok PTK',
            'singular_name' => 'Kelompok PTK',
            'menu_name' => 'Kelompok PTK',
        ],
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'kelompok-ptk'],
    ]);

    register_taxonomy('kategori_galeri', 'galeri', [
        'labels' => [
            'name' => 'Kategori Album',
            'singular_name' => 'Kategori Album',
            'menu_name' => 'Kategori',
        ],
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'kategori-galeri'],
    ]);

    register_taxonomy('tingkat_prestasi', 'prestasi', [
        'labels' => [
            'name' => 'Tingkat',
            'singular_name' => 'Tingkat',
            'menu_name' => 'Tingkat',
        ],
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'tingkat-prestasi'],
    ]);
}

/** Isi otomatis istilah awal saat plugin diaktifkan. */
function smkn1_seed_terms()
{

    $awal = [
        'bidang_keahlian' => ['Agribisnis dan Agriteknologi', 'Bisnis dan Manajemen', 'Pariwisata', 'Teknologi Informasi'],
        'kelompok_ptk' => ['Kepala Sekolah', 'Guru', 'Tenaga Kependidikan'],
        'kategori_galeri' => ['Kegiatan Sekolah', 'Praktik Kejuruan', 'Ekstrakurikuler', 'Upacara & Peringatan'],
        'tingkat_prestasi' => ['Sekolah', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'],
    ];

    foreach ($awal as $tax => $terms) {
        foreach ($terms as $t) {
            if (!term_exists($t, $tax)) {
                wp_insert_term($t, $tax);
            }
        }
    }
}