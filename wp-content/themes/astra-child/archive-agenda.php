<?php
if (!defined('ABSPATH'))
    exit;
get_header();
?>

<section class="smkn1-detail-kepala">
    <div class="smkn1-wadah">
        <h1>Agenda Kegiatan</h1>
        <p>Jadwal kegiatan sekolah</p>
    </div>
</section>

<section class="smkn1-seksi">
    <div class="smkn1-wadah">
        <?php if (have_posts()): ?>
            <div class="smkn1-agenda-list">
                <?php while (have_posts()):
                    the_post();
                    $mulai = get_field('tanggal_mulai');
                    $selesai = get_field('tanggal_selesai');
                    $ts = $mulai ? strtotime($mulai) : 0;
                    $lewat = $ts && $ts < strtotime('today'); ?>
                    <div class="smkn1-agenda <?php echo $lewat ? 'lewat' : ''; ?>">
                        <div class="smkn1-agenda-tgl">
                            <span class="hari">
                                <?php echo $ts ? esc_html(date_i18n('j', $ts)) : '—'; ?>
                            </span>
                            <span class="bulan">
                                <?php echo $ts ? esc_html(date_i18n('M Y', $ts)) : ''; ?>
                            </span>
                        </div>
                        <div class="smkn1-agenda-isi">
                            <h3>
                                <?php the_title(); ?>
                            </h3>
                            <div class="smkn1-agenda-meta">
                                <?php if ($selesai): ?><span>s/d
                                        <?php echo esc_html(date_i18n('j F Y', strtotime($selesai))); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($w = get_field('waktu')): ?><span>
                                        <?php echo esc_html($w); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($l = get_field('lokasi')): ?><span>
                                        <?php echo esc_html($l); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($j = get_field('jenis_kegiatan')): ?><span class="smkn1-agenda-jenis">
                                        <?php echo esc_html($j); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if (get_the_content()): ?>
                                <p class="smkn1-ringkas">
                                    <?php echo esc_html(wp_trim_words(get_the_content(), 24)); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(['mid_size' => 2]); ?>
        <?php else: ?>
            <p>Belum ada agenda.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer();