<?php
/** Beranda: hero, statistik, sambutan, jurusan, berita, agenda, prestasi, galeri, CTA. */
if (!defined('ABSPATH'))
    exit;

get_header();
?>

<?php /* ================= 1. HERO ================= */
$slides = get_posts(['post_type' => 'slide', 'numberposts' => 5, 'orderby' => 'menu_order', 'order' => 'ASC']);
if ($slides): ?>
    <section class="smkn1-hero">
        <?php foreach ($slides as $i => $s):
            $bg = get_the_post_thumbnail_url($s->ID, 'full');
            $teks = get_field('tombol_teks', $s->ID);
            $url = get_field('tombol_url', $s->ID); ?>
            <div class="smkn1-slide <?php echo 0 === $i ? 'aktif' : ''; ?>"
                style="<?php echo $bg ? 'background-image:url(' . esc_url($bg) . ')' : ''; ?>">
                <div class="smkn1-slide-isi">
                    <h1>
                        <?php echo esc_html($s->post_title); ?>
                    </h1>
                    <?php if ($sub = get_field('subjudul', $s->ID)): ?>
                        <p>
                            <?php echo esc_html($sub); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($teks && $url): ?>
                        <a href="<?php echo esc_url($url); ?>" class="smkn1-tombol besar">
                            <?php echo esc_html($teks); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (count($slides) > 1): ?>
            <div class="smkn1-dot">
                <?php foreach ($slides as $i => $s): ?>
                    <button data-ke="<?php echo (int) $i; ?>" class="<?php echo 0 === $i ? 'aktif' : ''; ?>"
                        aria-label="Slide <?php echo (int) $i + 1; ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php else: ?>
    <section class="smkn1-hero kosong">
        <div class="smkn1-slide aktif">
            <div class="smkn1-slide-isi">
                <h1>
                    <?php echo esc_html(smkn1_opt('nama_sekolah')); ?>
                </h1>
                <p>
                    <?php echo esc_html(smkn1_opt('tagline')); ?>
                </p>
                <a href="<?php echo esc_url(get_post_type_archive_link('jurusan')); ?>" class="smkn1-tombol besar">Lihat
                    Program Keahlian</a>
            </div>
        </div>
    </section>
<?php endif; ?>


<?php /* ================= 2. STATISTIK ================= */ ?>
<section class="smkn1-statistik">
    <div class="ast-container">
        <div class="smkn1-stat-grid">
            <?php for ($n = 1; $n <= 4; $n++):
                $angka = smkn1_opt("stat{$n}_angka");
                $label = smkn1_opt("stat{$n}_label");
                if (!$angka)
                    continue; ?>
                <div class="smkn1-stat">
                    <span class="smkn1-stat-angka">
                        <?php echo esc_html($angka); ?>
                    </span>
                    <span class="smkn1-stat-label">
                        <?php echo esc_html($label); ?>
                    </span>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>


<?php /* ================= 3. SAMBUTAN ================= */
$sambutan = smkn1_opt('sambutan_isi');
if ($sambutan):
    $foto_id = smkn1_opt('sambutan_foto_id'); ?>
    <section class="smkn1-seksi">
        <div class="ast-container">
            <div class="smkn1-sambutan">
                <?php if ($foto_id): ?>
                    <div class="smkn1-sambutan-foto">
                        <?php echo wp_get_attachment_image($foto_id, 'medium_large'); ?>
                        <strong>
                            <?php echo esc_html(smkn1_opt('nama_kepsek')); ?>
                        </strong>
                        <span>Kepala Sekolah</span>
                    </div>
                <?php endif; ?>
                <div class="smkn1-sambutan-isi">
                    <h2 class="smkn1-judul-seksi">Sambutan Kepala Sekolah</h2>
                    <?php echo wpautop(esc_html($sambutan)); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>


<?php /* ================= 4. JURUSAN ================= */
$jurusan = get_posts(['post_type' => 'jurusan', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
if ($jurusan): ?>
    <section class="smkn1-seksi abu">
        <div class="ast-container">
            <h2 class="smkn1-judul-seksi tengah">Program &amp; Konsentrasi Keahlian</h2>
            <p class="smkn1-sub-seksi">Pilih bidang yang sesuai minat dan bakatmu</p>

            <div class="smkn1-grid">
                <?php foreach ($jurusan as $j):
                    $kode = get_field('kode_jurusan', $j->ID);
                    $bidang = get_field('bidang_keahlian', $j->ID);
                    $kuota = get_field('kuota_siswa', $j->ID);
                    $link = get_field('link_daftar', $j->ID); ?>
                    <article class="smkn1-kartu">
                        <?php if ($kode): ?><span class="smkn1-kode">
                                <?php echo esc_html($kode); ?>
                            </span>
                        <?php endif; ?>
                        <h2><a href="<?php echo esc_url(get_permalink($j)); ?>">
                                <?php echo esc_html($j->post_title); ?>
                            </a></h2>
                        <?php if ($bidang): ?>
                            <div class="smkn1-bidang">
                                <?php echo esc_html($bidang); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($j->post_excerpt): ?>
                            <p class="smkn1-ringkas">
                                <?php echo esc_html($j->post_excerpt); ?>
                            </p>
                        <?php endif; ?>
                        <div class="smkn1-meta">
                            <span class="smkn1-kuota">
                                <?php if ($kuota): ?><strong>
                                        <?php echo esc_html($kuota); ?>
                                    </strong> siswa
                                <?php endif; ?>
                            </span>
                            <?php if ($link): ?>
                                <a class="smkn1-tombol" href="<?php echo esc_url($link); ?>" target="_blank"
                                    rel="noopener">Daftar</a>
                            <?php else: ?>
                                <a class="smkn1-tombol" href="<?php echo esc_url(get_permalink($j)); ?>">Selengkapnya</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<?php /* ================= 5. BERITA ================= */
$berita = get_posts(['numberposts' => 3]);
if ($berita): ?>
    <section class="smkn1-seksi">
        <div class="ast-container">
            <div class="smkn1-kepala-baris">
                <h2 class="smkn1-judul-seksi">Berita &amp; Pengumuman</h2>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/')); ?>"
                    class="smkn1-lihat-semua">Lihat semua &rarr;</a>
            </div>
            <div class="smkn1-grid">
                <?php foreach ($berita as $b): ?>
                    <article class="smkn1-kartu berita">
                        <?php if (has_post_thumbnail($b->ID)): ?>
                            <a href="<?php echo esc_url(get_permalink($b)); ?>" class="smkn1-thumb">
                                <?php echo get_the_post_thumbnail($b->ID, 'medium_large'); ?>
                            </a>
                        <?php endif; ?>
                        <time class="smkn1-tanggal">
                            <?php echo esc_html(get_the_date('j F Y', $b)); ?>
                        </time>
                        <h2><a href="<?php echo esc_url(get_permalink($b)); ?>">
                                <?php echo esc_html($b->post_title); ?>
                            </a></h2>
                        <p class="smkn1-ringkas">
                            <?php echo esc_html(wp_trim_words($b->post_content, 20)); ?>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<?php /* ================= 6. AGENDA ================= */
$agenda = get_posts([
    'post_type' => 'agenda',
    'numberposts' => 4,
    'meta_key' => 'tanggal_mulai',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => [['key' => 'tanggal_mulai', 'value' => gmdate('Ymd'), 'compare' => '>=']],
]);
if ($agenda): ?>
    <section class="smkn1-seksi abu">
        <div class="ast-container">
            <h2 class="smkn1-judul-seksi">Agenda Terdekat</h2>
            <div class="smkn1-agenda-list">
                <?php foreach ($agenda as $a):
                    $mulai = get_field('tanggal_mulai', $a->ID);
                    $ts = $mulai ? strtotime($mulai) : 0; ?>
                    <div class="smkn1-agenda">
                        <div class="smkn1-agenda-tgl">
                            <span class="hari">
                                <?php echo $ts ? esc_html(date_i18n('j', $ts)) : '—'; ?>
                            </span>
                            <span class="bulan">
                                <?php echo $ts ? esc_html(date_i18n('M', $ts)) : ''; ?>
                            </span>
                        </div>
                        <div class="smkn1-agenda-isi">
                            <h3>
                                <?php echo esc_html($a->post_title); ?>
                            </h3>
                            <div class="smkn1-agenda-meta">
                                <?php if ($w = get_field('waktu', $a->ID)): ?><span>
                                        <?php echo esc_html($w); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($l = get_field('lokasi', $a->ID)): ?><span>
                                        <?php echo esc_html($l); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<?php /* ================= 7. PRESTASI ================= */
$prestasi = get_posts(['post_type' => 'prestasi', 'numberposts' => 4]);
if ($prestasi): ?>
    <section class="smkn1-seksi">
        <div class="ast-container">
            <div class="smkn1-kepala-baris">
                <h2 class="smkn1-judul-seksi">Prestasi Siswa</h2>
                <a href="<?php echo esc_url(get_post_type_archive_link('prestasi')); ?>" class="smkn1-lihat-semua">Lihat
                    semua &rarr;</a>
            </div>
            <div class="smkn1-grid">
                <?php foreach ($prestasi as $p): ?>
                    <article class="smkn1-kartu prestasi">
                        <?php if ($pk = get_field('peringkat', $p->ID)): ?>
                            <span class="smkn1-peringkat">
                                <?php echo esc_html($pk); ?>
                            </span>
                        <?php endif; ?>
                        <h2>
                            <?php echo esc_html($p->post_title); ?>
                        </h2>
                        <?php if ($ns = get_field('nama_siswa', $p->ID)): ?>
                            <div class="smkn1-bidang">
                                <?php echo esc_html($ns); ?>
                            </div>
                        <?php endif; ?>
                        <div class="smkn1-meta">
                            <span>
                                <?php echo esc_html(get_field('penyelenggara', $p->ID)); ?>
                            </span>
                            <strong>
                                <?php echo esc_html(get_field('tahun', $p->ID)); ?>
                            </strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<?php /* ================= 8. GALERI ================= */
$album = get_posts(['post_type' => 'galeri', 'numberposts' => 8]);
if ($album): ?>
    <section class="smkn1-seksi abu">
        <div class="ast-container">
            <div class="smkn1-kepala-baris">
                <h2 class="smkn1-judul-seksi">Galeri Kegiatan</h2>
                <a href="<?php echo esc_url(get_post_type_archive_link('galeri')); ?>" class="smkn1-lihat-semua">Lihat
                    semua &rarr;</a>
            </div>
            <div class="smkn1-galeri-grid">
                <?php foreach ($album as $g): ?>
                    <a href="<?php echo esc_url(get_permalink($g)); ?>" class="smkn1-album">
                        <?php echo has_post_thumbnail($g->ID)
                            ? get_the_post_thumbnail($g->ID, 'medium_large')
                            : '<span class="smkn1-album-kosong"></span>'; ?>
                        <span class="smkn1-album-judul">
                            <?php echo esc_html($g->post_title); ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<?php /* ================= 9. BANNER SPMB ================= */
if ('1' === smkn1_opt('cta_aktif')):
    $cta_url = smkn1_opt('cta_url'); ?>
    <section class="smkn1-cta">
        <div class="ast-container">
            <h2>
                <?php echo esc_html(smkn1_opt('cta_judul')); ?>
            </h2>
            <p>
                <?php echo esc_html(smkn1_opt('cta_teks')); ?>
            </p>
            <?php if ($cta_url): ?>
                <a href="<?php echo esc_url($cta_url); ?>" class="smkn1-tombol besar putih">
                    <?php echo esc_html(smkn1_opt('cta_tombol', 'Selengkapnya')); ?>
                </a>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>