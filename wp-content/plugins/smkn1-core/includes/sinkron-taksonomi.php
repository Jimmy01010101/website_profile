<?php
if (!defined('ABSPATH'))
    exit;

/**
 * Menjembatani field dan taksonomi.
 *
 * Bidang keahlian dan jenis PTK tersimpan sebagai field karena itulah bentuk
 * datanya di Dapodik dan pada berkas impor. Namun taksonomi tetap diperlukan
 * agar kolom pada daftar admin terisi dan penyaringan berfungsi. Daripada
 * meminta admin mengisi dua kali, nilai field disalin otomatis ke taksonomi
 * setiap kali data disimpan.
 */

/** Peta: post type => [ nama field => taksonomi ] */
function smkn1_peta_sinkron()
{
    return [
        'jurusan' => ['bidang_keahlian' => 'bidang_keahlian'],
        'guru' => ['jenis_ptk' => 'kelompok_ptk'],
    ];
}

/** Salin nilai field ke taksonomi untuk satu post. */
function smkn1_sinkron_satu($post_id)
{

    $tipe = get_post_type($post_id);
    $peta = smkn1_peta_sinkron();

    if (!isset($peta[$tipe])) {
        return;
    }

    foreach ($peta[$tipe] as $field => $taksonomi) {

        $nilai = get_post_meta($post_id, $field, true);
        $nilai = is_string($nilai) ? trim($nilai) : '';

        if ('' === $nilai) {
            wp_set_object_terms($post_id, [], $taksonomi);
            continue;
        }

        $istilah = term_exists($nilai, $taksonomi);

        /* Hanya istilah yang sudah terdaftar yang dipakai. Nilai asing
           tidak dibuatkan istilah baru supaya data kotor hasil impor
           tidak mengotori daftar taksonomi. */
        if (!$istilah) {
            wp_set_object_terms($post_id, [], $taksonomi);
            continue;
        }

        wp_set_object_terms($post_id, [(int) $istilah['term_id']], $taksonomi);
    }
}

/* Jalan setiap kali data disimpan, baik lewat editor maupun impor. */
add_action('save_post', function ($post_id) {
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    smkn1_sinkron_satu($post_id);
}, 30);

/* ACF menulis field setelah save_post, jadi disinkronkan ulang. */
add_action('acf/save_post', 'smkn1_sinkron_satu', 30);

/** Sinkronkan seluruh data sekaligus. Dipanggil lewat WP-CLI atau tombol admin. */
function smkn1_sinkron_semua()
{

    $jumlah = 0;

    foreach (array_keys(smkn1_peta_sinkron()) as $tipe) {
        $daftar = get_posts([
            'post_type' => $tipe,
            'numberposts' => -1,
            'fields' => 'ids',
            'post_status' => 'any',
        ]);

        foreach ($daftar as $id) {
            smkn1_sinkron_satu($id);
            $jumlah++;
        }
    }

    return $jumlah;
}

/**
 * Alat pemeriksa data.
 * Mengembalikan daftar isian yang tidak sesuai pilihan yang tersedia,
 * biasanya akibat kolom berkas impor tergeser.
 */
function smkn1_periksa_data_guru()
{

    $jenis_sah = ['Kepala Sekolah', 'Guru', 'Tenaga Kependidikan'];
    $status_sah = ['PNS', 'PPPK', 'Guru Honor Sekolah', 'Tenaga Honor Sekolah'];

    $masalah = [];

    foreach (get_posts(['post_type' => 'guru', 'numberposts' => -1]) as $g) {

        $catat = [];

        $jenis = trim((string) get_post_meta($g->ID, 'jenis_ptk', true));
        if ('' !== $jenis && !in_array($jenis, $jenis_sah, true)) {
            $catat[] = 'jenis_ptk: ' . $jenis;
        }

        $status = trim((string) get_post_meta($g->ID, 'status_kepegawaian', true));
        if ('' !== $status && !in_array($status, $status_sah, true)) {
            $catat[] = 'status: ' . $status;
        }

        $nuptk = trim((string) get_post_meta($g->ID, 'nuptk', true));
        if ('' !== $nuptk && !ctype_digit(str_replace(' ', '', $nuptk))) {
            $catat[] = 'nuptk bukan angka: ' . $nuptk;
        }

        $gelar = trim((string) get_post_meta($g->ID, 'gelar_belakang', true));
        if (mb_strlen($gelar) > 18) {
            $catat[] = 'gelar terlalu panjang: ' . mb_substr($gelar, 0, 25);
        }

        if ($catat) {
            $masalah[$g->ID] = ['nama' => $g->post_title, 'catatan' => $catat];
        }
    }

    return $masalah;
}