<?php
/**
 * Seeder: 7 Konsentrasi Keahlian SMKN 1 Bengkayang
 * Jalankan: wpd eval-file /var/www/html/scripts/seed-jurusan.php
 * Idempotent: aman dijalankan berkali-kali (cek slug, tidak bikin duplikat).
 */

if (!defined('WP_CLI')) {
    exit("Script ini hanya untuk WP-CLI.\n");
}

$jurusan = [
    ['title' => 'Agribisnis Tanaman', 'kode' => 'AT', 'bidang' => 'Agribisnis dan Agriteknologi', 'program' => 'Agribisnis Tanaman', 'kuota' => 68, 'link' => 'https://forms.gle/BkdKJ1wgJTuSg48v7', 'excerpt' => 'Konsentrasi Agribisnis Tanaman Pangan dan Hortikultura.'],
    ['title' => 'Akuntansi dan Keuangan Lembaga', 'kode' => 'AKL', 'bidang' => 'Bisnis dan Manajemen', 'program' => 'Akuntansi dan Keuangan Lembaga', 'kuota' => 68, 'link' => 'https://forms.gle/i4xxMv1QDn7wE8p29', 'excerpt' => 'Konsentrasi Akuntansi.'],
    ['title' => 'Manajemen Perkantoran dan Layanan Bisnis', 'kode' => 'MPLB', 'bidang' => 'Bisnis dan Manajemen', 'program' => 'Manajemen Perkantoran dan Layanan Bisnis', 'kuota' => 36, 'link' => 'https://forms.gle/ktjUmZzV64qNAEqT7', 'excerpt' => 'Konsentrasi Manajemen Perkantoran.'],
    ['title' => 'Pemasaran', 'kode' => 'PM', 'bidang' => 'Bisnis dan Manajemen', 'program' => 'Pemasaran', 'kuota' => 62, 'link' => 'https://forms.gle/Wru1G3NL1GZCCHho8', 'excerpt' => 'Konsentrasi Bisnis Digital.'],
    ['title' => 'Kuliner', 'kode' => 'KL', 'bidang' => 'Pariwisata', 'program' => 'Kuliner', 'kuota' => 26, 'link' => 'https://forms.gle/oext4F2F8egpoR3J6', 'excerpt' => 'Konsentrasi Kuliner (Tata Boga).'],
    ['title' => 'Teknik Jaringan Komputer dan Telekomunikasi', 'kode' => 'TKJT', 'bidang' => 'Teknologi Informasi', 'program' => 'Teknik Jaringan Komputer dan Telekomunikasi', 'kuota' => 69, 'link' => 'https://forms.gle/oRgJBB7CVKZvgJHJ9', 'excerpt' => 'Konsentrasi Teknik Komputer dan Jaringan.'],
    ['title' => 'Rekayasa Perangkat Lunak', 'kode' => 'RPL', 'bidang' => 'Teknologi Informasi', 'program' => 'Pengembangan Perangkat Lunak dan Gim', 'kuota' => 34, 'link' => 'https://forms.gle/jsGpTWN6gVuUKdKe8', 'excerpt' => 'Konsentrasi Rekayasa Perangkat Lunak.'],
];

$dibuat = 0;
$diupdate = 0;

foreach ($jurusan as $j) {
    $slug = sanitize_title($j['title']);
    $existing = get_page_by_path($slug, OBJECT, 'jurusan');

    $postarr = [
        'post_type' => 'jurusan',
        'post_title' => $j['title'],
        'post_name' => $slug,
        'post_excerpt' => $j['excerpt'],
        'post_status' => 'publish',
    ];

    if ($existing) {
        $postarr['ID'] = $existing->ID;
        $post_id = wp_update_post($postarr, true);
        $aksi = 'UPDATE';
        $diupdate++;
    } else {
        $post_id = wp_insert_post($postarr, true);
        $aksi = 'CREATE';
        $dibuat++;
    }

    if (is_wp_error($post_id)) {
        WP_CLI::warning("GAGAL {$j['title']}: " . $post_id->get_error_message());
        continue;
    }

    // update_field() = ACF, otomatis menulis pasangan meta _field_xxx
    update_field('kode_jurusan', $j['kode'], $post_id);
    update_field('bidang_keahlian', $j['bidang'], $post_id);
    update_field('program_keahlian', $j['program'], $post_id);
    update_field('kuota_siswa', $j['kuota'], $post_id);
    update_field('link_daftar', $j['link'], $post_id);

    WP_CLI::log(sprintf('%-6s #%-3d %-6s %s', $aksi, $post_id, $j['kode'], $j['title']));
}

WP_CLI::success("Selesai. Dibuat: {$dibuat}, Diperbarui: {$diupdate}");