<?php
if (!defined('ABSPATH'))
    exit;

add_action('acf/include_fields', function () {

    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    /* ---------- PRESTASI ---------- */
    acf_add_local_field_group([
        'key' => 'group_smkn1_prestasi',
        'title' => 'Detail Prestasi',
        'fields' => [
            [
                'key' => 'field_prestasi_siswa',
                'label' => 'Nama Siswa / Tim',
                'name' => 'nama_siswa',
                'type' => 'text',
                'required' => 1,
            ],
            [
                'key' => 'field_prestasi_lomba',
                'label' => 'Nama Lomba',
                'name' => 'nama_lomba',
                'type' => 'text',
                'wrapper' => ['width' => '60'],
            ],
            [
                'key' => 'field_prestasi_peringkat',
                'label' => 'Peringkat',
                'name' => 'peringkat',
                'type' => 'select',
                'choices' => [
                    'Juara 1' => 'Juara 1',
                    'Juara 2' => 'Juara 2',
                    'Juara 3' => 'Juara 3',
                    'Harapan' => 'Juara Harapan',
                    'Finalis' => 'Finalis',
                    'Peserta' => 'Peserta',
                ],
                'wrapper' => ['width' => '40'],
            ],
            [
                'key' => 'field_prestasi_penyelenggara',
                'label' => 'Penyelenggara',
                'name' => 'penyelenggara',
                'type' => 'text',
                'wrapper' => ['width' => '60'],
            ],
            [
                'key' => 'field_prestasi_tahun',
                'label' => 'Tahun',
                'name' => 'tahun',
                'type' => 'number',
                'min' => 2000,
                'max' => 2100,
                'wrapper' => ['width' => '40'],
            ],
            [
                'key' => 'field_prestasi_kelas',
                'label' => 'Kelas / Jurusan',
                'name' => 'kelas',
                'type' => 'text',
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'prestasi']]],
        'show_in_rest' => 1,
    ]);

    /* ---------- GALERI ---------- */
    acf_add_local_field_group([
        'key' => 'group_smkn1_galeri',
        'title' => 'Detail Album',
        'fields' => [
            [
                'key' => 'field_galeri_tanggal',
                'label' => 'Tanggal Kegiatan',
                'name' => 'tanggal_kegiatan',
                'type' => 'date_picker',
                'display_format' => 'j F Y',
                'return_format' => 'Ymd',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_galeri_lokasi',
                'label' => 'Lokasi',
                'name' => 'lokasi',
                'type' => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_galeri_catatan',
                'label' => 'Cara Menambah Foto',
                'name' => 'catatan_foto',
                'type' => 'message',
                'message' => 'Simpan album ini dulu, lalu buka kotak <strong>Foto Album</strong> di bawah untuk mengunggah gambar. Gambar utama diatur lewat <strong>Featured Image</strong>.',
                'new_lines' => 'wpautop',
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'galeri']]],
        'show_in_rest' => 1,
    ]);

    /* ---------- AGENDA ---------- */
    acf_add_local_field_group([
        'key' => 'group_smkn1_agenda',
        'title' => 'Detail Agenda',
        'fields' => [
            [
                'key' => 'field_agenda_mulai',
                'label' => 'Tanggal Mulai',
                'name' => 'tanggal_mulai',
                'type' => 'date_picker',
                'display_format' => 'j F Y',
                'return_format' => 'Ymd',
                'required' => 1,
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_agenda_selesai',
                'label' => 'Tanggal Selesai',
                'name' => 'tanggal_selesai',
                'type' => 'date_picker',
                'display_format' => 'j F Y',
                'return_format' => 'Ymd',
                'instructions' => 'Kosongkan bila hanya satu hari.',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_agenda_waktu',
                'label' => 'Waktu',
                'name' => 'waktu',
                'type' => 'text',
                'placeholder' => '08.00 - 12.00 WIB',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_agenda_lokasi',
                'label' => 'Lokasi',
                'name' => 'lokasi',
                'type' => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_agenda_jenis',
                'label' => 'Jenis Kegiatan',
                'name' => 'jenis_kegiatan',
                'type' => 'select',
                'choices' => [
                    'Akademik' => 'Akademik',
                    'Kesiswaan' => 'Kesiswaan',
                    'SPMB' => 'SPMB',
                    'Ujian' => 'Ujian',
                    'Libur' => 'Hari Libur',
                    'Lainnya' => 'Lainnya',
                ],
                'default_value' => 'Akademik',
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'agenda']]],
        'show_in_rest' => 1,
    ]);

    /* ---------- SLIDE ---------- */
    acf_add_local_field_group([
        'key' => 'group_smkn1_slide',
        'title' => 'Isi Slide',
        'fields' => [
            [
                'key' => 'field_slide_sub',
                'label' => 'Subjudul',
                'name' => 'subjudul',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key' => 'field_slide_tombol_teks',
                'label' => 'Teks Tombol',
                'name' => 'tombol_teks',
                'type' => 'text',
                'placeholder' => 'Daftar Sekarang',
                'wrapper' => ['width' => '40'],
            ],
            [
                'key' => 'field_slide_tombol_url',
                'label' => 'Tautan Tombol',
                'name' => 'tombol_url',
                'type' => 'url',
                'wrapper' => ['width' => '60'],
            ],
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'slide']]],
        'show_in_rest' => 1,
    ]);


});