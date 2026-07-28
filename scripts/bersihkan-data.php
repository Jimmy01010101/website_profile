<?php
/**
 * Pembersih data.
 * Menghapus sisa draf otomatis dan data guru yang rusak akibat kolom
 * berkas impor tergeser, supaya bisa diimpor ulang dari nol.
 *
 * Jalankan: wpd eval-file /var/www/html/scripts/bersihkan-data.php
 * Tambahkan argumen "hapus" untuk benar-benar menghapus:
 *   wpd eval-file /var/www/html/scripts/bersihkan-data.php hapus
 */

if (!defined('WP_CLI')) {
    exit("Script ini hanya untuk WP-CLI.\n");
}

$benar_hapus = in_array('hapus', (array) ($args ?? []), true);

$jenis_sah = ['Kepala Sekolah', 'Guru', 'Tenaga Kependidikan'];
$status_sah = ['PNS', 'PPPK', 'Guru Honor Sekolah', 'Tenaga Honor Sekolah'];

$hapus = [];

/* ---------- 1. Draf otomatis pada semua jenis konten ---------- */
$draf = get_posts([
    'post_type' => ['guru', 'jurusan', 'prestasi', 'galeri', 'agenda', 'slide', 'page'],
    'post_status' => ['auto-draft', 'draft', 'trash'],
    'numberposts' => -1,
]);

foreach ($draf as $p) {
    $hapus[$p->ID] = [$p->post_type, $p->post_title ?: '(tanpa judul)', 'draf/sampah'];
}

/* ---------- 2. Guru dengan data rusak ---------- */
$guru = get_posts(['post_type' => 'guru', 'numberposts' => -1, 'post_status' => 'any']);

foreach ($guru as $g) {

    $alasan = [];

    $nuptk = trim((string) get_post_meta($g->ID, 'nuptk', true));
    $jenis = trim((string) get_post_meta($g->ID, 'jenis_ptk', true));
    $status = trim((string) get_post_meta($g->ID, 'status_kepegawaian', true));
    $nip = trim((string) get_post_meta($g->ID, 'nip', true));

    /* NUPTK berisi huruf berarti kolom bergeser. */
    if ('' !== $nuptk && !ctype_digit(str_replace(' ', '', $nuptk))) {
        $alasan[] = 'nuptk berisi teks';
    }

    /* Notasi ilmiah muncul saat berkas dibuka di Excel lalu disimpan ulang. */
    foreach (['nuptk' => $nuptk, 'nip' => $nip, 'jenis_ptk' => $jenis] as $k => $v) {
        if (preg_match('/E\+\d+/i', $v)) {
            $alasan[] = "{$k} berupa notasi ilmiah";
        }
    }

    if ('' !== $jenis && !in_array($jenis, $jenis_sah, true)) {
        $alasan[] = 'jenis_ptk tidak dikenali';
    }

    if ('' !== $status && !in_array($status, $status_sah, true)) {
        $alasan[] = 'status tidak dikenali';
    }

    if ($alasan) {
        $hapus[$g->ID] = ['guru', $g->post_title, implode(', ', $alasan)];
    }
}

/* ---------- 3. Guru dengan judul kembar ---------- */
$terlihat = [];
foreach ($guru as $g) {
    if (isset($hapus[$g->ID])) {
        continue;
    }
    $kunci = mb_strtolower(trim($g->post_title));
    if (isset($terlihat[$kunci])) {
        $hapus[$g->ID] = ['guru', $g->post_title, 'judul kembar dengan #' . $terlihat[$kunci]];
    } else {
        $terlihat[$kunci] = $g->ID;
    }
}

/* ---------- Laporan ---------- */
WP_CLI::log(str_repeat('-', 78));
WP_CLI::log(sprintf('%-7s %-10s %-28s %s', 'ID', 'JENIS', 'JUDUL', 'ALASAN'));
WP_CLI::log(str_repeat('-', 78));

foreach ($hapus as $id => $d) {
    WP_CLI::log(sprintf('%-7d %-10s %-28s %s', $id, $d[0], mb_substr($d[1], 0, 27), $d[2]));
}

WP_CLI::log(str_repeat('-', 78));

$sisa_guru = count($guru) - count(array_filter($hapus, fn($d) => 'guru' === $d[0]));

WP_CLI::log(sprintf('Total ditandai : %d', count($hapus)));
WP_CLI::log(sprintf('Guru saat ini  : %d', count($guru)));
WP_CLI::log(sprintf('Guru tersisa   : %d', $sisa_guru));

if (!$benar_hapus) {
    WP_CLI::success('Ini baru pemeriksaan. Tambahkan argumen "hapus" untuk benar-benar menghapus.');
    return;
}

foreach (array_keys($hapus) as $id) {
    wp_delete_post($id, true);
}

WP_CLI::success(sprintf('%d data dihapus permanen.', count($hapus)));