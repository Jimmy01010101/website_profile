<?php
/**
 * Plugin Name:       SMKN 1 Bengkayang - Core
 * Description:       Custom post type Jurusan & Guru untuk website profil sekolah.
 * Version:           1.0.0
 * Author:            Jimmy Person
 * Text Domain:       smkn1-core
 * Requires PHP:      8.0
 */

// Cegah akses langsung lewat browser.
// ABSPATH hanya ada kalau file dipanggil DARI DALAM WordPress.
if (!defined('ABSPATH')) {
    exit;
}

define('SMKN1_CORE_PATH', plugin_dir_path(__FILE__));
define('SMKN1_CORE_URL', plugin_dir_url(__FILE__));

require_once SMKN1_CORE_PATH . 'includes/post-types.php';
require_once SMKN1_CORE_PATH . 'includes/taxonomies.php';
require_once SMKN1_CORE_PATH . 'includes/acf-fields.php';
require_once SMKN1_CORE_PATH . 'includes/acf-fields-guru.php';
require_once SMKN1_CORE_PATH . 'includes/acf-fields-konten.php';
require_once SMKN1_CORE_PATH . 'includes/settings-page.php';

if (is_admin()) {
    require_once SMKN1_CORE_PATH . 'includes/admin/import-page.php';
}

register_activation_hook(__FILE__, function () {
    smkn1_register_post_types();
    smkn1_register_taxonomies();
    smkn1_seed_terms();
    smkn1_seed_settings();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, 'flush_rewrite_rules');

/**
 * Jalan SEKALI saat plugin diaktifkan.
 * Tanpa flush, URL /jurusan/ akan 404.
 */
register_activation_hook(__FILE__, function () {
    smkn1_register_post_types();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules();
});