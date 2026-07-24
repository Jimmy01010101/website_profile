<?php
if (!defined('ABSPATH'))
    exit;

/** Daftar semua pengaturan: slug => [label, tipe, nilai awal] */
function smkn1_settings_schema()
{
    return [
        'identitas' => [
            'judul' => 'Identitas Sekolah',
            'fields' => [
                'nama_sekolah' => ['Nama Sekolah', 'text', 'SMK Negeri 1 Bengkayang'],
                'npsn' => ['NPSN', 'text', '30109301'],
                'tagline' => ['Tagline', 'text', 'Berkarakter, Berkompeten, Berjiwa Wirausaha'],
                'akreditasi' => ['Akreditasi', 'text', ''],
                'nama_kepsek' => ['Nama Kepala Sekolah', 'text', 'Fidelis, S.Pd'],
                'tahun_berdiri' => ['Tahun Berdiri', 'text', '2002'],
                'logo_id' => ['Logo Sekolah', 'media', ''],
            ],
        ],
        'kontak' => [
            'judul' => 'Kontak & Lokasi',
            'fields' => [
                'alamat' => ['Alamat', 'textarea', "Jl. Bukit Tinggi, Kelurahan Sebalo\nKecamatan Bengkayang, Kabupaten Bengkayang\nKalimantan Barat 79211"],
                'telepon' => ['Telepon', 'text', ''],
                'email' => ['Email', 'text', 'smknbky@gmail.com'],
                'jam' => ['Jam Operasional', 'text', 'Senin - Jumat, 07.00 - 15.00 WIB'],
                'latitude' => ['Lintang', 'text', '0.8377'],
                'longitude' => ['Bujur', 'text', '109.4883'],
                'instagram' => ['Instagram', 'url', ''],
                'facebook' => ['Facebook', 'url', ''],
                'youtube' => ['YouTube', 'url', ''],
                'tiktok' => ['TikTok', 'url', ''],
                'whatsapp' => ['WhatsApp', 'url', ''],
            ],
        ],
        'beranda' => [
            'judul' => 'Beranda',
            'fields' => [
                'stat1_angka' => ['Statistik 1 — Angka', 'text', '885'],
                'stat1_label' => ['Statistik 1 — Keterangan', 'text', 'Peserta Didik'],
                'stat2_angka' => ['Statistik 2 — Angka', 'text', '50'],
                'stat2_label' => ['Statistik 2 — Keterangan', 'text', 'Guru & Tendik'],
                'stat3_angka' => ['Statistik 3 — Angka', 'text', '7'],
                'stat3_label' => ['Statistik 3 — Keterangan', 'text', 'Konsentrasi Keahlian'],
                'stat4_angka' => ['Statistik 4 — Angka', 'text', '31'],
                'stat4_label' => ['Statistik 4 — Keterangan', 'text', 'Rombongan Belajar'],
                'sambutan_foto_id' => ['Foto Kepala Sekolah', 'media', ''],
                'sambutan_isi' => ['Isi Sambutan', 'textarea', ''],
                'cta_aktif' => ['Tampilkan Banner SPMB', 'checkbox', '1'],
                'cta_judul' => ['Judul Banner', 'text', 'Sistem Penerimaan Murid Baru 2026/2027'],
                'cta_teks' => ['Keterangan Banner', 'textarea', 'Pendaftaran dibuka untuk tujuh konsentrasi keahlian. Lengkapi berkas dan daftarkan diri sesuai jadwal.'],
                'cta_tombol' => ['Teks Tombol', 'text', 'Info SPMB'],
                'cta_url' => ['Tautan Tombol', 'url', ''],
            ],
        ],
        'visimisi' => [
            'judul' => 'Visi & Misi',
            'fields' => [
                'visi' => ['Visi', 'textarea', 'Terwujudnya insan beriman yang berkarakter, berkompeten, berjiwa wirausaha, berwawasan lingkungan dan kompetitif.'],
                'misi' => [
                    'Misi (satu baris satu butir)',
                    'textarea_besar',
                    "Mewujudkan insan yang beriman dan bertakwa kepada Tuhan Yang Maha Esa.\n" .
                    "Mewujudkan pendidikan berkarakter yang sesuai dengan 8 dimensi profil lulusan.\n" .
                    "Mewujudkan sumber daya manusia yang berkompeten di bidang akademik dan non akademik.\n" .
                    "Menanamkan dan menumbuhkan jiwa berwirausaha di dalam dan di luar lingkungan sekolah.\n" .
                    "Mewujudkan sekolah bersih dan sehat serta ramah lingkungan.\n" .
                    "Mencetak wirausahawan yang kompetitif."
                ],
            ],
        ],
    ];
}

/** Ambil satu nilai pengaturan. Dipakai di seluruh template. */
function smkn1_opt($key, $fallback = '')
{
    $opt = get_option('smkn1_pengaturan', []);
    return (isset($opt[$key]) && '' !== $opt[$key]) ? $opt[$key] : $fallback;
}

/** Misi disimpan sebagai teks, dipecah jadi array per baris. */
function smkn1_misi()
{
    $raw = smkn1_opt('misi');
    return array_values(array_filter(array_map('trim', explode("\n", $raw))));
}

/** Isi nilai awal sekali saja. */
function smkn1_seed_settings()
{
    $ada = get_option('smkn1_pengaturan');
    if (is_array($ada) && $ada) {
        return;
    }
    $awal = [];
    foreach (smkn1_settings_schema() as $grup) {
        foreach ($grup['fields'] as $key => $f) {
            $awal[$key] = $f[2];
        }
    }
    update_option('smkn1_pengaturan', $awal);
}

add_action('admin_init', function () {
    register_setting('smkn1_pengaturan_grup', 'smkn1_pengaturan', [
        'sanitize_callback' => 'smkn1_sanitize_settings',
    ]);
});

function smkn1_sanitize_settings($input)
{
    $lama = get_option('smkn1_pengaturan', []);
    $bersih = is_array($lama) ? $lama : [];

    foreach (smkn1_settings_schema() as $grup) {
        foreach ($grup['fields'] as $key => $f) {
            if (!array_key_exists($key, $input)) {
                continue;
            }
            $nilai = $input[$key];

            if ('url' === $f[1]) {
                $bersih[$key] = esc_url_raw($nilai);
            } elseif (in_array($f[1], ['textarea', 'textarea_besar'], true)) {
                $bersih[$key] = sanitize_textarea_field($nilai);
            } elseif ('media' === $f[1]) {
                $bersih[$key] = absint($nilai);
            } elseif ('checkbox' === $f[1]) {
                $bersih[$key] = $nilai ? '1' : '';
            } else {
                $bersih[$key] = sanitize_text_field($nilai);
            }
        }
    }

    // Checkbox tidak terkirim saat tidak dicentang, jadi ditangani terpisah.
    if (isset($input['_grup_aktif']) && 'beranda' === $input['_grup_aktif']) {
        $bersih['cta_aktif'] = !empty($input['cta_aktif']) ? '1' : '';
    }

    return $bersih;
}

add_action('admin_menu', function () {

    add_menu_page(
        'Pengaturan Situs',
        'Pengaturan Situs',
        'manage_options',
        'smkn1-identitas',
        'smkn1_render_settings',
        'dashicons-admin-customizer',
        3
    );

    foreach (smkn1_settings_schema() as $slug => $grup) {
        add_submenu_page(
            'smkn1-identitas',
            $grup['judul'],
            $grup['judul'],
            'manage_options',
            'smkn1-' . $slug,
            'smkn1_render_settings'
        );
    }
});

add_action('admin_enqueue_scripts', function ($hook) {
    if (false !== strpos($hook, 'smkn1-')) {
        wp_enqueue_media();
    }
});

function smkn1_render_settings()
{

    $page = sanitize_key($_GET['page'] ?? 'smkn1-identitas');
    $slug = str_replace('smkn1-', '', $page);
    $skema = smkn1_settings_schema();

    if (!isset($skema[$slug])) {
        $slug = 'identitas';
    }
    $grup = $skema[$slug];
    ?>
    <div class="wrap">
        <h1>Pengaturan Situs</h1>

        <nav class="nav-tab-wrapper" style="margin-bottom:20px">
            <?php foreach ($skema as $s => $g): ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=smkn1-' . $s)); ?>"
                    class="nav-tab <?php echo $s === $slug ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html($g['judul']); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <form method="post" action="options.php">
            <?php settings_fields('smkn1_pengaturan_grup'); ?>
            <input type="hidden" name="smkn1_pengaturan[_grup_aktif]" value="<?php echo esc_attr($slug); ?>">

            <table class="form-table" role="presentation">
                <?php foreach ($grup['fields'] as $key => $f):
                    list($label, $tipe, $awal) = $f;
                    $nilai = smkn1_opt($key);
                    ?>
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                        </th>
                        <td>
                            <?php if ('textarea' === $tipe): ?>
                                <textarea id="<?php echo esc_attr($key); ?>"
                                    name="smkn1_pengaturan[<?php echo esc_attr($key); ?>]" rows="4"
                                    class="large-text"><?php echo esc_textarea($nilai); ?></textarea>

                            <?php elseif ('textarea_besar' === $tipe): ?>
                                <textarea id="<?php echo esc_attr($key); ?>"
                                    name="smkn1_pengaturan[<?php echo esc_attr($key); ?>]" rows="8"
                                    class="large-text"><?php echo esc_textarea($nilai); ?></textarea>

                            <?php elseif ('checkbox' === $tipe): ?>
                                <label>
                                    <input type="checkbox" name="smkn1_pengaturan[<?php echo esc_attr($key); ?>]" value="1" <?php checked($nilai, '1'); ?>>
                                    Aktif
                                </label>

                            <?php elseif ('media' === $tipe):
                                $img = $nilai ? wp_get_attachment_image_url($nilai, 'medium') : ''; ?>
                                <div class="smkn1-media" data-target="<?php echo esc_attr($key); ?>">
                                    <img src="<?php echo esc_url($img); ?>"
                                        style="max-width:180px;display:<?php echo $img ? 'block' : 'none'; ?>;margin-bottom:8px;border:1px solid #ccd0d4">
                                    <input type="hidden" name="smkn1_pengaturan[<?php echo esc_attr($key); ?>]"
                                        id="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($nilai); ?>">
                                    <button type="button" class="button smkn1-pilih">Pilih Gambar</button>
                                    <button type="button" class="button smkn1-hapus"
                                        style="display:<?php echo $img ? 'inline-block' : 'none'; ?>">Hapus</button>
                                </div>

                            <?php else: ?>
                                <input type="<?php echo 'url' === $tipe ? 'url' : 'text'; ?>" id="<?php echo esc_attr($key); ?>"
                                    name="smkn1_pengaturan[<?php echo esc_attr($key); ?>]"
                                    value="<?php echo esc_attr($nilai); ?>" class="regular-text">
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <?php submit_button('Simpan Pengaturan'); ?>
        </form>
    </div>

    <script>
        jQuery(function ($) {
            $('.smkn1-media').each(function () {
                var box = $(this), frame;

                box.on('click', '.smkn1-pilih', function (e) {
                    e.preventDefault();
                    if (frame) { frame.open(); return; }
                    frame = wp.media({ title: 'Pilih Gambar', multiple: false });
                    frame.on('select', function () {
                        var a = frame.state().get('selection').first().toJSON();
                        box.find('input[type=hidden]').val(a.id);
                        box.find('img').attr('src', a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url).show();
                        box.find('.smkn1-hapus').show();
                    });
                    frame.open();
                });

                box.on('click', '.smkn1-hapus', function (e) {
                    e.preventDefault();
                    box.find('input[type=hidden]').val('');
                    box.find('img').hide();
                    $(this).hide();
                });
            });
        });
    </script>
    <?php
}