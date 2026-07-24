<?php
/** Arsip Jurusan: grid kartu berisi kode, bidang, kuota, dan tombol daftar. */
if (!defined('ABSPATH'))
    exit;

get_header();
?>

<div class="ast-container">
    <main id="primary" class="site-main">

        <header class="smkn1-kepala">
            <h1>Program &amp; Konsentrasi Keahlian</h1>
            <p>SMK Negeri 1 Bengkayang membuka
                <?php echo esc_html($wp_query->found_posts); ?> konsentrasi keahlian.
            </p>
        </header>

        <?php if (have_posts()): ?>
            <div class="smkn1-grid">
                <?php while (have_posts()):
                    the_post(); ?>
                    <?php
                    $kode = get_field('kode_jurusan');
                    $bidang = get_field('bidang_keahlian');
                    $kuota = get_field('kuota_siswa');
                    $link = get_field('link_daftar');
                    ?>
                    <article class="smkn1-kartu">

                        <?php if ($kode): ?>
                            <span class="smkn1-kode">
                                <?php echo esc_html($kode); ?>
                            </span>
                        <?php endif; ?>

                        <h2><a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a></h2>

                        <?php if ($bidang): ?>
                            <div class="smkn1-bidang">
                                <?php echo esc_html($bidang); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (has_excerpt()): ?>
                            <p class="smkn1-ringkas">
                                <?php echo esc_html(get_the_excerpt()); ?>
                            </p>
                        <?php endif; ?>

                        <div class="smkn1-meta">
                            <span class="smkn1-kuota">
                                <?php if ($kuota): ?>
                                    <strong>
                                        <?php echo esc_html($kuota); ?>
                                    </strong> siswa
                                <?php endif; ?>
                            </span>

                            <?php if ($link): ?>
                                <a class="smkn1-tombol" href="<?php echo esc_url($link); ?>" target="_blank"
                                    rel="noopener">Daftar</a>
                            <?php else: ?>
                                <a class="smkn1-tombol" href="<?php the_permalink(); ?>">Selengkapnya</a>
                            <?php endif; ?>
                        </div>

                    </article>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>Belum ada data jurusan.</p>
        <?php endif; ?>

    </main>
</div>

<?php get_footer(); ?>