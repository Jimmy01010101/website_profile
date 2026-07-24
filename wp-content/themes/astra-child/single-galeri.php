<?php
if (!defined('ABSPATH'))
    exit;
get_header();

while (have_posts()):
    the_post();
    $tgl = get_field('tanggal_kegiatan');
    $lokasi = get_field('lokasi');
    $foto = get_attached_media('image', get_the_ID());
    ?>

    <section class="smkn1-detail-kepala">
        <div class="smkn1-wadah">
            <h1>
                <?php the_title(); ?>
            </h1>
            <p>
                <?php if ($tgl): ?>
                    <?php echo esc_html(date_i18n('j F Y', strtotime($tgl))); ?>
                <?php endif; ?>
                <?php if ($lokasi): ?> &middot;
                    <?php echo esc_html($lokasi); ?>
                <?php endif; ?>
            </p>
        </div>
    </section>

    <section class="smkn1-seksi">
        <div class="smkn1-wadah">
            <?php the_content(); ?>

            <?php if ($foto): ?>
                <div class="smkn1-galeri-grid" style="margin-top:24px">
                    <?php foreach ($foto as $f): ?>
                        <a href="<?php echo esc_url(wp_get_attachment_image_url($f->ID, 'full')); ?>" class="smkn1-album"
                            target="_blank" rel="noopener">
                            <?php echo wp_get_attachment_image($f->ID, 'medium_large'); ?>
                            <?php if ($f->post_excerpt): ?>
                                <span class="smkn1-album-judul">
                                    <?php echo esc_html($f->post_excerpt); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="smkn1-catatan">Belum ada foto. Unggah lewat kotak <strong>Media</strong> di halaman edit album ini.
                </p>
            <?php endif; ?>
        </div>
    </section>

<?php endwhile;
get_footer();