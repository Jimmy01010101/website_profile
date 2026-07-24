<?php
if (!defined('ABSPATH'))
    exit;
get_header();
?>

<section class="smkn1-detail-kepala">
    <div class="smkn1-wadah">
        <h1>Prestasi Siswa</h1>
        <p>Capaian peserta didik SMK Negeri 1 Bengkayang</p>
    </div>
</section>

<section class="smkn1-seksi">
    <div class="smkn1-wadah">
        <?php if (have_posts()): ?>
            <div class="smkn1-grid">
                <?php while (have_posts()):
                    the_post();
                    $peringkat = get_field('peringkat');
                    $siswa = get_field('nama_siswa');
                    $lomba = get_field('nama_lomba');
                    $penye = get_field('penyelenggara');
                    $tahun = get_field('tahun');
                    $kelas = get_field('kelas'); ?>
                    <article class="smkn1-kartu prestasi">
                        <?php if ($peringkat): ?><span class="smkn1-peringkat">
                                <?php echo esc_html($peringkat); ?>
                            </span>
                        <?php endif; ?>
                        <h2>
                            <?php the_title(); ?>
                        </h2>
                        <?php if ($siswa): ?>
                            <div class="smkn1-bidang">
                                <?php echo esc_html($siswa); ?>
                                <?php echo $kelas ? ' — ' . esc_html($kelas) : ''; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($lomba): ?>
                            <p class="smkn1-ringkas">
                                <?php echo esc_html($lomba); ?>
                            </p>
                        <?php endif; ?>
                        <div class="smkn1-meta">
                            <span>
                                <?php echo esc_html($penye); ?>
                            </span>
                            <strong>
                                <?php echo esc_html($tahun); ?>
                            </strong>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(['mid_size' => 2]); ?>
        <?php else: ?>
            <p>Belum ada data prestasi.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer();