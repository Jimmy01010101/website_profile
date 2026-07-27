<?php
if (!defined('ABSPATH'))
    exit;

/**
 * Fungsi cadangan agar situs tidak mati bila plugin inti atau ACF
 * kebetulan dinonaktifkan. Sengaja hanya dipasang pada permintaan
 * publik: pengaktifan plugin berlangsung di dashboard atau lewat CLI,
 * dan mendeklarasikan fungsi di sana justru menyebabkan tabrakan nama.
 */
if (!is_admin() && !(defined('WP_CLI') && WP_CLI)) {

    if (!function_exists('smkn1_opt')) {
        function smkn1_opt($key, $fallback = '')
        {
            $opt = get_option('smkn1_pengaturan', []);
            return (is_array($opt) && isset($opt[$key]) && '' !== $opt[$key])
                ? $opt[$key]
                : $fallback;
        }
    }

    if (!function_exists('smkn1_misi')) {
        function smkn1_misi()
        {
            $raw = smkn1_opt('misi');
            return array_values(array_filter(array_map('trim', explode("\n", $raw))));
        }
    }

    /* ACF menyimpan nilainya di postmeta dengan nama kunci yang sama. */
    if (!function_exists('get_field')) {
        function get_field($selector, $post_id = null, $format_value = true)
        {
            $post_id = $post_id ?: get_the_ID();
            return $post_id ? get_post_meta($post_id, $selector, true) : '';
        }
    }
}

require_once get_stylesheet_directory() . '/inc/header.php';
require_once get_stylesheet_directory() . '/inc/footer.php';

/** Muat CSS induk lebih dulu, lalu CSS child. */
add_action('wp_enqueue_scripts', function () {

    wp_enqueue_style(
        'astra-parent',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme('astra')->get('Version')
    );

    wp_enqueue_style(
        'smkn1-child',
        get_stylesheet_uri(),
        ['astra-parent'],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'smkn1-nav',
        get_stylesheet_directory_uri() . '/js/nav.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );

    if (is_front_page()) {
        wp_enqueue_script(
            'smkn1-hero',
            get_stylesheet_directory_uri() . '/js/hero.js',
            [],
            wp_get_theme()->get('Version'),
            true
        );
    }
}, 20);

/** Dua lokasi menu: navigasi utama dan tautan cepat di footer. */
add_action('after_setup_theme', function () {
    register_nav_menus([
        'smkn1_utama' => 'Menu Utama',
        'smkn1_footer' => 'Tautan Cepat Footer',
    ]);
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
});

/** Urutan arsip: jurusan menurut abjad, guru menurut urutan impor. */
add_action('pre_get_posts', function ($q) {

    if (is_admin() || !$q->is_main_query()) {
        return;
    }

    if ($q->is_post_type_archive('jurusan')) {
        $q->set('orderby', 'title');
        $q->set('order', 'ASC');
        $q->set('posts_per_page', -1);
    }

    if ($q->is_post_type_archive('guru')) {
        $q->set('orderby', ['menu_order' => 'ASC', 'title' => 'ASC']);
        $q->set('posts_per_page', -1);
    }

    if ($q->is_post_type_archive('agenda')) {
        $q->set('meta_key', 'tanggal_mulai');
        $q->set('orderby', 'meta_value_num');
        $q->set('order', 'ASC');
    }

    /* Pencarian mencakup seluruh jenis konten publik, bukan hanya pos. */
    if ($q->is_search()) {
        $q->set('post_type', ['post', 'page', 'jurusan', 'guru', 'prestasi', 'galeri', 'agenda']);
    }
});

/** Favicon dari Pengaturan Situs. */
add_action('wp_head', function () {
    $id = smkn1_opt('favicon') ?: smkn1_opt('logo_id');
    if (!$id) {
        return;
    }
    $url = wp_get_attachment_image_url($id, 'thumbnail');
    if ($url) {
        echo '<link rel="icon" href="' . esc_url($url) . '">' . "\n";
    }
});