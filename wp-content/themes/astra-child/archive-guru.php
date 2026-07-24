<?php
if (!defined('ABSPATH'))
    exit;
get_header();

$semua = get_posts(['post_type' => 'guru', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC']);

$kelompok = ['Kepala Sekolah' => [], 'Guru' => [], 'Tenaga Kependidikan' => []];
foreach ($semua as $g) {
    $jenis = get_field('jenis_ptk', $g->ID) ?: 'Guru';
    $kelompok[$jenis][] = $g;
}
?>

<section class="smkn1-detail-kepala">
    <div class="smkn1-wadah">
        <h1>Guru &amp; Tenaga Kependidikan</h1>
        <p>
            <?php echo count($semua); ?> pendidik dan tenaga kependidikan
        </p>
    </div>
</section>

<section class="smkn1-seksi">
    <div class="smkn1-wadah">
        <?php foreach ($kelompok as $judul => $daftar):
            if (!$daftar)
                continue; ?>
            <h2 class="smkn1-judul-seksi">
                <?php echo esc_html($judul); ?> <small>(
                    <?php echo count($daftar); ?>)
                </small>
            </h2>
            <div class="smkn1-guru-grid">
                <?php foreach ($daftar as $g):
                    $gelar = get_field('gelar_belakang', $g->ID);
                    $jabatan = get_field('jabatan', $g->ID);
                    $status = get_field('status_kepegawaian', $g->ID);
                    $mengajar = get_field('mengajar', $g->ID); ?>
                    <article class="smkn1-guru">
                        <div class="smkn1-guru-foto">
                            <?php echo has_post_thumbnail($g->ID)
                                ? get_the_post_thumbnail($g->ID, 'medium')
                                : '<span class="smkn1-guru-inisial">' . esc_html(mb_substr($g->post_title, 0, 1)) . '</span>'; ?>
                        </div>
                        <h3>
                            <?php echo esc_html($g->post_title); ?>
                            <?php echo $gelar ? ', ' . esc_html($gelar) : ''; ?>
                        </h3>
                        <?php if ($jabatan): ?>
                            <p class="smkn1-guru-jabatan">
                                <?php echo esc_html($jabatan); ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($mengajar): ?>
                            <p class="smkn1-guru-mapel">
                                <?php echo esc_html(wp_trim_words($mengajar, 12)); ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($status): ?><span class="smkn1-guru-status">
                                <?php echo esc_html($status); ?>
                            </span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php get_footer();