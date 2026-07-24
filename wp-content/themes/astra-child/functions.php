<?php
if (!defined('ABSPATH'))
    exit;

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

/** Urutkan arsip jurusan dan guru sesuai urutan impor, bukan tanggal. */
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
});