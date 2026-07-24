<?php
if (!defined('ABSPATH'))
    exit;

/**
 * Field group: Detail Guru & Tendik
 * Ditulis manual (bukan hasil export UI) — key dibuat konsisten & mudah dibaca.
 */
add_action('acf/include_fields', function () {

    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_smkn1_guru',
        'title' => 'Detail Guru & Tendik',
        'fields' => [

            [
                'key' => 'field_guru_nuptk',
                'label' => 'NUPTK',
                'name' => 'nuptk',
                'type' => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_guru_nip',
                'label' => 'NIP',
                'name' => 'nip',
                'type' => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_guru_jk',
                'label' => 'Jenis Kelamin',
                'name' => 'jenis_kelamin',
                'type' => 'select',
                'choices' => ['L' => 'Laki-laki', 'P' => 'Perempuan'],
                'allow_null' => 1,
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_guru_jenis_ptk',
                'label' => 'Jenis PTK',
                'name' => 'jenis_ptk',
                'type' => 'select',
                'choices' => [
                    'Kepala Sekolah' => 'Kepala Sekolah',
                    'Guru' => 'Guru',
                    'Tenaga Kependidikan' => 'Tenaga Kependidikan',
                ],
                'default_value' => 'Guru',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_guru_status',
                'label' => 'Status Kepegawaian',
                'name' => 'status_kepegawaian',
                'type' => 'select',
                'choices' => [
                    'PNS' => 'PNS',
                    'PPPK' => 'PPPK',
                    'Guru Honor Sekolah' => 'Guru Honor Sekolah',
                    'Tenaga Honor Sekolah' => 'Tenaga Honor Sekolah',
                ],
                'allow_null' => 1,
                'wrapper' => ['width' => '34'],
            ],
            [
                'key' => 'field_guru_gelar_depan',
                'label' => 'Gelar Depan',
                'name' => 'gelar_depan',
                'type' => 'text',
                'wrapper' => ['width' => '30'],
            ],
            [
                'key' => 'field_guru_gelar_belakang',
                'label' => 'Gelar Belakang',
                'name' => 'gelar_belakang',
                'type' => 'text',
                'wrapper' => ['width' => '30'],
            ],
            [
                'key' => 'field_guru_jenjang',
                'label' => 'Jenjang Pendidikan',
                'name' => 'jenjang',
                'type' => 'text',
                'placeholder' => 'S1 / S2 / D3',
                'wrapper' => ['width' => '40'],
            ],
            [
                'key' => 'field_guru_prodi',
                'label' => 'Jurusan / Program Studi',
                'name' => 'prodi',
                'type' => 'text',
            ],
            [
                'key' => 'field_guru_jabatan',
                'label' => 'Jabatan',
                'name' => 'jabatan',
                'type' => 'text',
                'instructions' => 'Contoh: Guru Bahasa Inggris, Kepala Program Keahlian',
            ],
            [
                'key' => 'field_guru_mengajar',
                'label' => 'Mata Pelajaran Diampu',
                'name' => 'mengajar',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'key' => 'field_guru_tugas',
                'label' => 'Tugas Tambahan',
                'name' => 'tugas_tambahan',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key' => 'field_guru_tmt',
                'label' => 'TMT Kerja',
                'name' => 'tmt_kerja',
                'type' => 'date_picker',
                'display_format' => 'j F Y',
                'return_format' => 'Y-m-d',
            ],
        ],

        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'guru',
                ],
            ],
        ],

        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
        'show_in_rest' => 1,
    ]);
});