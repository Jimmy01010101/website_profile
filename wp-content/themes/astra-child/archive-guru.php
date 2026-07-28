<?php
/** Arsip Guru & Tendik, dikelompokkan menurut jenis PTK. */
if (!defined('ABSPATH'))
    exit;
get_header();

$semua = get_posts([
    'post_type' => 'guru',
    'numberposts' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
]);

/**
 * Hanya tiga kelompok yang sah. Nilai jenis_ptk yang tidak dikenali
 * dimasukkan ke Guru, bukan dijadikan kelompok baru, supaya data yang
 * kotor tidak memunculkan judul aneh berupa angka NUPTK.
 */
$sah = ['Kepala Sekolah', 'Guru', 'Tenaga Kependidikan'];
$kelompok = array_fill_keys($sah, []);
$meragukan = 0;

foreach ($semua as $g) {
    $jenis = trim((string) get_field('jenis_ptk', $g->ID));

    if (!in_array($jenis, $sah, true)) {
        if ('' !== $jenis) {
            $meragukan++;
        }
        $jenis = 'Guru';
    }

    $kelompok[$jenis][] = $g;
}
?>

<section class="smkn1-detail-kepala">
    <div class="smkn1-wadah">
        <h1>Guru &amp; Tenaga Kependidikan</h1>
        <p><?php echo count($semua); ?> pendidik dan tenaga kependidikan</p>
    </div>
</section>

<section class="smkn1-seksi">
    <div class="smkn1-wadah">

        <?php if ($meragukan && current_user_can('manage_options')): ?>
            <div class="smkn1-catatan" style="margin-bottom:24px">
                <strong>Catatan untuk pengelola:</strong>
                <?php echo (int) $meragukan; ?> data memiliki isian Jenis PTK yang tidak dikenali
                dan untuk sementara ditampilkan pada kelompok Guru.
                Periksa kembali berkas impor atau perbaiki lewat
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=guru')); ?>">daftar Guru &amp; Tendik</a>.
                Pesan ini hanya terlihat oleh administrator.
            </div>
        <?php endif; ?>

        <?php if (!$semua): ?>
            <p class="smkn1-catatan">Belum ada data pendidik dan tenaga kependidikan.</p>
        <?php endif; ?>

        <?php foreach ($kelompok as $judul => $daftar):
            if (!$daftar) {
                continue;
            } ?>

            <h2 class="smkn1-judul-seksi">
                <?php echo esc_html($judul); ?>
                <small>(<?php echo count($daftar); ?>)</small>
            </h2>

            <div class="smkn1-guru-grid">
                <?php foreach ($daftar as $g):

                    $gelar_b = trim((string) get_field('gelar_belakang', $g->ID));
                    $jabatan = trim((string) get_field('jabatan', $g->ID));
                    $status = trim((string) get_field('status_kepegawaian', $g->ID));
                    $mengajar = trim((string) get_field('mengajar', $g->ID));

                    /* Status kepegawaian yang sah, supaya lencana tidak menampilkan sampah. */
                    $status_sah = ['PNS', 'PPPK', 'Guru Honor Sekolah', 'Tenaga Honor Sekolah'];
                    if (!in_array($status, $status_sah, true)) {
                        $status = '';
                    }

                    /* Gelar wajar paling panjang belasan karakter; lebih dari itu pasti salah kolom. */
                    if (mb_strlen($gelar_b) > 18) {
                        $gelar_b = '';
                    }

                    $nama = $g->post_title . ($gelar_b ? ', ' . $gelar_b : '');
                    ?>
                    <article class="smkn1-guru">
                        <a class="smkn1-guru-tautan" href="<?php echo esc_url(get_permalink($g)); ?>">
                            <div class="smkn1-guru-foto">
                                <?php echo has_post_thumbnail($g->ID)
                                    ? get_the_post_thumbnail($g->ID, 'medium')
                                    : '<span class="smkn1-guru-inisial">' . esc_html(mb_substr($g->post_title, 0, 1)) . '</span>'; ?>
                            </div>
                            <h3><?php echo esc_html($nama); ?></h3>
                        </a>

                        <?php if ($jabatan): ?>
                            <p class="smkn1-guru-jabatan"><?php echo esc_html(wp_trim_words($jabatan, 8)); ?></p>
                        <?php endif; ?>

                        <?php if ($mengajar): ?>
                            <p class="smkn1-guru-mapel"><?php echo esc_html(wp_trim_words($mengajar, 10)); ?></p>
                        <?php endif; ?>

                        <?php if ($status): ?>
                            <span class="smkn1-guru-status"><?php echo esc_html($status); ?></span>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>
    </div>
</section>

<?php get_footer();