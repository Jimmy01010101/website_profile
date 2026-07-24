<?php
if (!defined('ABSPATH'))
    exit;
get_header();

while (have_posts()):
    the_post();
    $kode = get_field('kode_jurusan');
    $bidang = get_field('bidang_keahlian');
    $program = get_field('program_keahlian');
    $kuota = get_field('kuota_siswa');
    $link = get_field('link_daftar');
    ?>

    <section class="smkn1-detail-kepala">
        <div class="smkn1-wadah">
            <?php if ($kode): ?><span class="smkn1-kode besar">
                    <?php echo esc_html($kode); ?>
                </span>
            <?php endif; ?>
            <h1>
                <?php the_title(); ?>
            </h1>
            <?php if ($bidang): ?>
                <p>
                    <?php echo esc_html($bidang); ?>
                </p>
            <?php endif; ?>
        </div>
    </section>

    <section class="smkn1-seksi">
        <div class="smkn1-wadah">
            <div class="smkn1-detail-grid">

                <div class="smkn1-detail-isi">
                    <?php if (has_post_thumbnail()): ?>
                        <div class="smkn1-detail-gambar">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    <?php the_content(); ?>
                </div>

                <aside class="smkn1-detail-sisi">
                    <div class="smkn1-info-kotak">
                        <h3>Informasi</h3>
                        <dl>
                            <?php if ($bidang): ?>
                                <dt>Bidang Keahlian</dt>
                                <dd>
                                    <?php echo esc_html($bidang); ?>
                                </dd>
                            <?php endif; ?>
                            <?php if ($program): ?>
                                <dt>Program Keahlian</dt>
                                <dd>
                                    <?php echo esc_html($program); ?>
                                </dd>
                            <?php endif; ?>
                            <?php if ($kuota): ?>
                                <dt>Daya Tampung</dt>
                                <dd>
                                    <?php echo esc_html($kuota); ?> siswa
                                </dd>
                            <?php endif; ?>
                        </dl>
                        <?php if ($link): ?>
                            <a class="smkn1-tombol besar blok" href="<?php echo esc_url($link); ?>" target="_blank"
                                rel="noopener">Daftar Sekarang</a>
                        <?php endif; ?>
                    </div>

                    <div class="smkn1-info-kotak">
                        <h3>Konsentrasi Lain</h3>
                        <ul class="smkn1-daftar-tautan">
                            <?php foreach (get_posts(['post_type' => 'jurusan', 'numberposts' => -1, 'exclude' => [get_the_ID()], 'orderby' => 'title', 'order' => 'ASC']) as $j): ?>
                                <li><a href="<?php echo esc_url(get_permalink($j)); ?>">
                                        <?php echo esc_html($j->post_title); ?>
                                    </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </aside>

            </div>
        </div>
    </section>

<?php endwhile;
get_footer();