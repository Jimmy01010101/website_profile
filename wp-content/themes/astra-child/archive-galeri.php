<?php
if (!defined('ABSPATH'))
    exit;
get_header();
?>

<section class="smkn1-detail-kepala">
    <div class="smkn1-wadah">
        <h1>Galeri Kegiatan</h1>
        <p>Dokumentasi kegiatan sekolah</p>
    </div>
</section>

<section class="smkn1-seksi">
    <div class="smkn1-wadah">
        <?php if (have_posts()): ?>
            <div class="smkn1-galeri-grid">
                <?php while (have_posts()):
                    the_post();
                    $jml = count(get_attached_media('image', get_the_ID())); ?>
                    <a href="<?php the_permalink(); ?>" class="smkn1-album">
                        <?php echo has_post_thumbnail()
                            ? get_the_post_thumbnail(null, 'medium_large')
                            : '<span class="smkn1-album-kosong"></span>'; ?>
                        <span class="smkn1-album-judul">
                            <?php the_title(); ?>
                            <?php if ($jml): ?><em>
                                    <?php echo (int) $jml; ?> foto
                                </em>
                            <?php endif; ?>
                        </span>
                    </a>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(['mid_size' => 2]); ?>
        <?php else: ?>
            <p>Belum ada album.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer();