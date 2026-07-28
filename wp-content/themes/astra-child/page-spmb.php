<?php
/**
 * Halaman SPMB.
 * Menjadi satu-satunya pintu pendaftaran: isi halaman diambil dari editor,
 * lalu daftar konsentrasi keahlian beserta daya tampung dan tautan
 * formulirnya dibangkitkan otomatis dari data jurusan.
 */
if (!defined('ABSPATH'))
    exit;

get_header();

$jurusan = get_posts([
    'post_type' => 'jurusan',
    'numberposts' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
]);

$total_kuota = 0;
$ada_tautan = false;

foreach ($jurusan as $j) {
    $total_kuota += (int) get_field('kuota_siswa', $j->ID);
    if (get_field('link_daftar', $j->ID)) {
        $ada_tautan = true;
    }
}

while (have_posts()):
    the_post(); ?>

    <section class="smkn1-detail-kepala">
        <div class="smkn1-wadah">
            <h1><?php the_title(); ?></h1>
            <p><?php echo esc_html(smkn1_opt('cta_judul', 'Sistem Penerimaan Murid Baru')); ?></p>
        </div>
    </section>

    <?php if ($jurusan): ?>
        <section class="smkn1-statistik">
            <div class="ast-container">
                <div class="smkn1-stat-grid">
                    <div class="smkn1-stat">
                        <span class="smkn1-stat-angka"><?php echo count($jurusan); ?></span>
                        <span class="smkn1-stat-label">Konsentrasi Keahlian</span>
                    </div>
                    <?php if ($total_kuota): ?>
                        <div class="smkn1-stat">
                            <span class="smkn1-stat-angka"><?php echo (int) $total_kuota; ?></span>
                            <span class="smkn1-stat-label">Total Daya Tampung</span>
                        </div>
                    <?php endif; ?>
                    <div class="smkn1-stat">
                        <span class="smkn1-stat-angka"><?php echo esc_html(smkn1_opt('npsn')); ?></span>
                        <span class="smkn1-stat-label">NPSN</span>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="smkn1-seksi">
        <div class="smkn1-wadah">
            <div class="smkn1-halaman">
                <?php the_content(); ?>
            </div>
        </div>
    </section>

    <?php if ($jurusan): ?>
        <section class="smkn1-seksi abu">
            <div class="smkn1-wadah">
                <h2 class="smkn1-judul-seksi">Pilihan Konsentrasi Keahlian</h2>
                <p class="smkn1-sub-seksi" style="text-align:left">
                    Pilih satu konsentrasi keahlian, lalu isi formulir pendaftarannya.
                </p>

                <div class="smkn1-tabel-bungkus">
                    <table class="smkn1-tabel-spmb">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Konsentrasi Keahlian</th>
                                <th>Bidang Keahlian</th>
                                <th class="tengah">Daya Tampung</th>
                                <th class="tengah">Pendaftaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jurusan as $j):
                                $kode = get_field('kode_jurusan', $j->ID);
                                $bidang = get_field('bidang_keahlian', $j->ID);
                                $kuota = get_field('kuota_siswa', $j->ID);
                                $link = get_field('link_daftar', $j->ID);
                                ?>
                                <tr>
                                    <td><span class="smkn1-kode"><?php echo esc_html($kode); ?></span></td>
                                    <td>
                                        <a class="smkn1-tebal" href="<?php echo esc_url(get_permalink($j)); ?>">
                                            <?php echo esc_html($j->post_title); ?>
                                        </a>
                                    </td>
                                    <td class="smkn1-abu-teks"><?php echo esc_html($bidang); ?></td>
                                    <td class="tengah"><?php echo $kuota ? esc_html($kuota) : '&ndash;'; ?></td>
                                    <td class="tengah">
                                        <?php if ($link): ?>
                                            <a class="smkn1-tombol" href="<?php echo esc_url($link); ?>" target="_blank"
                                                rel="noopener">Daftar</a>
                                        <?php else: ?>
                                            <span class="smkn1-abu-teks">Belum dibuka</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!$ada_tautan): ?>
                    <p class="smkn1-catatan" style="margin-top:20px">
                        Tautan formulir pendaftaran belum diisi. Lengkapi kolom Link Pendaftaran
                        pada masing-masing jurusan agar tombol daftar muncul di sini.
                    </p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php
    $agenda = get_posts([
        'post_type' => 'agenda',
        'numberposts' => 5,
        'meta_key' => 'tanggal_mulai',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => [
            ['key' => 'tanggal_mulai', 'value' => gmdate('Ymd'), 'compare' => '>='],
            ['key' => 'jenis_kegiatan', 'value' => 'SPMB', 'compare' => '='],
        ],
    ]);
    if ($agenda): ?>
        <section class="smkn1-seksi">
            <div class="smkn1-wadah">
                <h2 class="smkn1-judul-seksi">Jadwal Penerimaan</h2>
                <div class="smkn1-agenda-list">
                    <?php foreach ($agenda as $a):
                        $ts = strtotime(get_field('tanggal_mulai', $a->ID)); ?>
                        <div class="smkn1-agenda">
                            <div class="smkn1-agenda-tgl">
                                <span class="hari"><?php echo esc_html(date_i18n('j', $ts)); ?></span>
                                <span class="bulan"><?php echo esc_html(date_i18n('M', $ts)); ?></span>
                            </div>
                            <div class="smkn1-agenda-isi">
                                <h3><?php echo esc_html($a->post_title); ?></h3>
                                <div class="smkn1-agenda-meta">
                                    <?php if ($w = get_field('waktu', $a->ID)): ?><span><?php echo esc_html($w); ?></span><?php endif; ?>
                                    <?php if ($l = get_field('lokasi', $a->ID)): ?><span><?php echo esc_html($l); ?></span><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="smkn1-seksi abu">
        <div class="smkn1-wadah">
            <h2 class="smkn1-judul-seksi">Butuh Bantuan?</h2>
            <div class="smkn1-kontak-ringkas">
                <?php if ($al = smkn1_opt('alamat')): ?>
                    <div><strong>Alamat</strong>
                        <p><?php echo nl2br(esc_html($al)); ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($em = smkn1_opt('email')): ?>
                    <div><strong>Surel</strong>
                        <p><a href="mailto:<?php echo esc_attr($em); ?>"><?php echo esc_html($em); ?></a></p>
                    </div>
                <?php endif; ?>
                <?php if ($tl = smkn1_opt('telepon')): ?>
                    <div><strong>Telepon</strong>
                        <p><?php echo esc_html($tl); ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($jm = smkn1_opt('jam')): ?>
                    <div><strong>Jam Layanan</strong>
                        <p><?php echo esc_html($jm); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php endwhile;

get_footer();