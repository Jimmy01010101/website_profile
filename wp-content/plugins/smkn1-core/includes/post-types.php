<?php
if (!defined('ABSPATH'))
    exit;

/**
 * Hook 'init' = satu-satunya tempat yang benar untuk register_post_type().
 * WordPress yang memanggil fungsi ini, bukan kamu.
 */
add_action('init', 'smkn1_register_post_types');

function smkn1_register_post_types()
{

    /* ---------- CPT: JURUSAN ---------- */
    register_post_type('jurusan', [
        'labels' => [
            'name' => 'Jurusan',
            'singular_name' => 'Jurusan',
            'add_new' => 'Tambah Baru',
            'add_new_item' => 'Tambah Jurusan Baru',
            'edit_item' => 'Edit Jurusan',
            'all_items' => 'Semua Jurusan',
            'search_items' => 'Cari Jurusan',
            'not_found' => 'Belum ada jurusan.',
            'menu_name' => 'Jurusan',
        ],
        'public' => true,   // tampil di frontend + admin
        'has_archive' => true,   // aktifkan halaman /jurusan/
        'show_in_rest' => true,   // REST API + editor Gutenberg
        'menu_icon' => 'dashicons-welcome-learn-more',
        'menu_position' => 20,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'rewrite' => ['slug' => 'jurusan', 'with_front' => false],
        'hierarchical' => false,
    ]);

    /* ---------- CPT: GURU & TENDIK ---------- */
    register_post_type('guru', [
        'labels' => [
            'name' => 'Guru & Tendik',
            'singular_name' => 'Guru',
            'add_new_item' => 'Tambah Guru Baru',
            'edit_item' => 'Edit Guru',
            'all_items' => 'Semua Guru',
            'not_found' => 'Belum ada data guru.',
            'menu_name' => 'Guru & Tendik',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-groups',
        'menu_position' => 21,
        'supports' => ['title', 'thumbnail', 'editor'],
        'rewrite' => ['slug' => 'guru', 'with_front' => false],
    ]);
}