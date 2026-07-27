<?php
if (!defined('ABSPATH'))
	exit;

/**
 * Pengamanan situs profil sekolah.
 * Model penggunanya satu administrator dan banyak pengunjung anonim,
 * jadi seluruh jalur yang berkaitan dengan akun pengguna ditutup.
 */

/* Tidak ada pendaftaran anggota di situs profil sekolah. */
add_filter('option_users_can_register', '__return_zero');

/* Halaman arsip penulis membocorkan username admin ke publik. */
add_action('template_redirect', function () {
	if (is_author()) {
		wp_safe_redirect(home_url('/'), 301);
		exit;
	}
});

add_filter('author_rewrite_rules', '__return_empty_array');

/* Endpoint pengguna di REST API juga membocorkan daftar username. */
add_filter('rest_endpoints', function ($endpoints) {
	if (is_user_logged_in()) {
		return $endpoints;
	}
	unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
	return $endpoints;
});

/* XML-RPC adalah jalur lama yang kerap dipakai menebak sandi. */
add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', function ($headers) {
	unset($headers['X-Pingback']);
	return $headers;
});

/* Sembunyikan versi WordPress dari kode sumber halaman. */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

/**
 * Elementor mendaftarkan generator tag lewat instance objek,
 * sehingga remove_action biasa tidak bisa mencocokkannya.
 * Callback-nya dicari berdasarkan nama kelas, dijalankan pada
 * prioritas 1 agar sempat dibuang sebelum dicetak di prioritas 10.
 */
add_action('wp_head', function () {
	global $wp_filter;

	if (empty($wp_filter['wp_head'])) {
		return;
	}

	foreach ($wp_filter['wp_head']->callbacks as $prioritas => $daftar) {
		foreach ($daftar as $cb) {
			$f = $cb['function'];
			if (
				is_array($f) && isset($f[0]) && is_object($f[0])
				&& false !== stripos(get_class($f[0]), 'GeneratorTag')
			) {
				remove_action('wp_head', $f, $prioritas);
			}
		}
	}
}, 1);

/* Tautan yang tidak dipakai situs profil sekolah. */
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

/* Pesan galat login yang seragam agar tidak membocorkan username yang benar. */
add_filter('login_errors', function () {
	return 'Nama pengguna atau kata sandi salah.';
});

/* Batasi jenis berkas yang boleh diunggah. */
add_filter('upload_mimes', function ($mimes) {
	return [
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png' => 'image/png',
		'webp' => 'image/webp',
		'gif' => 'image/gif',
		'pdf' => 'application/pdf',
		'doc' => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'csv' => 'text/csv',
	];
}, 99);

/* Larang penyuntingan berkas tema dan plugin dari dashboard. */
if (!defined('DISALLOW_FILE_EDIT')) {
	define('DISALLOW_FILE_EDIT', true);
}

/* Komentar tidak dipakai; sembunyikan sisa antarmukanya. */
add_action('admin_menu', function () {
	remove_menu_page('edit-comments.php');
}, 999);

add_action('wp_before_admin_bar_render', function () {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('comments');
});

add_filter('comments_open', '__return_false', 20);
add_filter('pings_open', '__return_false', 20);

/* Header keamanan dasar. */
add_action('send_headers', function () {
	if (is_admin()) {
		return;
	}
	header('X-Content-Type-Options: nosniff');
	header('X-Frame-Options: SAMEORIGIN');
	header('Referrer-Policy: strict-origin-when-cross-origin');
});