<?php
/**
 * Arsip Jurusan.
 * Halaman ini murni memperkenalkan konsentrasi keahlian beserta
 * penjelasannya. Urusan pendaftaran seluruhnya dipusatkan di halaman SPMB
 * supaya calon murid tidak menemukan dua pintu masuk yang berbeda.
 */
if (!defined('ABSPATH'))
    exit;

get_header();

$hal_spmb = get_page_by_path('spmb', OBJECT, ['page']);
?>

<section class="smkn1-detail-kepala">
    <div class="smkn1-wadah">
        <h1>Program &amp; Konsentrasi Keahlian</h1>
        <p>
            <?php
            printf(
                '%s menyelenggarakan %d konsentrasi keahlian.',
                esc_html(smkn1_opt('nama_sekolah', get_bloginfo('name'))),
                (int) $wp_query->found_posts
            );
            ?>
        </p>
    </div>
</section>

<section class="smkn1-seksi">
    <div class="smkn1-wadah">

        <?php if (have_posts()): ?>

            <div class="smkn1-grid">
                <?php while (have_posts()):
                    the_post();
                    $kode = get_field('kode_jurusan');
                    $bidang = get_field('bidang_keahlian');
                    ?>
                    <article class="smkn1-kartu">

                        <?php if ($kode): ?>
                            <span class="smkn1-kode"><?php echo esc_html($kode); ?></span>
                        <?php endif; ?>

                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                        <?php if ($bidang): ?>
                            <div class="smkn1-bidang"><?php echo esc_html($bidang); ?></div>
                        <?php endif; ?>

                        <?php if (has_excerpt()): ?>
                            <p class="smkn1-ringkas"><?php echo esc_html(get_the_excerpt()); ?></p>
                        <?php endif; ?>

                        <div class="smkn1-meta rata-kanan">
                            <a class="smkn1-lihat-semua" href="<?php the_permalink(); ?>">Selengkapnya &rarr;</a>
                        </div>

                    </article>
                <?php endwhile; ?>
            </div>

            <?php if ($hal_spmb): ?>
                <div class="smkn1-ajakan">
                    <div>
                        <h2>Tertarik bergabung?</h2>
                        <p>Jadwal, persyaratan, dan formulir pendaftaran setiap konsentrasi keahlian tersedia di halaman
                            penerimaan murid baru.</p>
                    </div>
                    <a class="smkn1-tombol besar" href="<?php echo esc_url(get_permalink($hal_spmb)); ?>">Informasi SPMB</a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p class="smkn1-catatan">Belum ada data konsentrasi keahlian.</p>
        <?php endif; ?>

    </div>
</section>

<?php get_footer();