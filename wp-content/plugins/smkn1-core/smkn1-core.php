<?php
/**
 * Plugin Name:       SMKN 1 Bengkayang - Core
 * Description:       Jenis konten, taksonomi, pengaturan situs, impor data, pengamanan, dan SEO untuk website profil sekolah.
 * Version:           1.1.0
 * Author:            Jimmy Person
 * Text Domain:       smkn1-core
 * Requires PHP:      8.0
 * Requires at least: 6.2
 */

// Cegah akses langsung lewat browser.
// ABSPATH hanya ada kalau file dipanggil DARI DALAM WordPress.
if (!defined('ABSPATH')) {
    exit;
}

define('SMKN1_CORE_PATH', plugin_dir_path(__FILE__));
define('SMKN1_CORE_URL', plugin_dir_url(__FILE__));
define('SMKN1_CORE_VERSION', '1.1.0');

require_once SMKN1_CORE_PATH . 'includes/post-types.php';
require_once SMKN1_CORE_PATH . 'includes/taxonomies.php';
require_once SMKN1_CORE_PATH . 'includes/acf-fields.php';
require_once SMKN1_CORE_PATH . 'includes/acf-fields-guru.php';
require_once SMKN1_CORE_PATH . 'includes/acf-fields-konten.php';
require_once SMKN1_CORE_PATH . 'includes/settings-page.php';
require_once SMKN1_CORE_PATH . 'includes/hardening.php';
require_once SMKN1_CORE_PATH . 'includes/seo.php';
require_once SMKN1_CORE_PATH . 'includes/halaman-statis.php';

if (is_admin()) {
    require_once SMKN1_CORE_PATH . 'includes/admin/import-page.php';
}

/**
 * Jalan sekali saat plugin diaktifkan.
 * Jenis konten dan taksonomi didaftarkan lebih dulu supaya aturan
 * permalink-nya ikut tersimpan saat flush; tanpa itu /jurusan/ akan 404.
 * Flush diletakkan paling akhir agar halaman statis yang baru dibuat
 * ikut terdaftar.
 */
register_activation_hook(__FILE__, function () {
    smkn1_register_post_types();
    smkn1_register_taxonomies();
    smkn1_seed_terms();
    smkn1_seed_settings();
    smkn1_buat_halaman_statis();
    flush_rewrite_rules();
});

/** Bersihkan aturan permalink supaya tidak meninggalkan jejak 404. */
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');