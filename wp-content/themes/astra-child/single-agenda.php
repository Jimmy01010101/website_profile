<?php
/** Detail satu agenda kegiatan. */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$mulai   = get_field( 'tanggal_mulai' );
	$selesai = get_field( 'tanggal_selesai' );
	$waktu   = get_field( 'waktu' );
	$lokasi  = get_field( 'lokasi' );
	$jenis   = get_field( 'jenis_kegiatan' );
	$ts      = $mulai ? strtotime( $mulai ) : 0;
	$lewat   = $ts && $ts < strtotime( 'today' );
	?>

	<section class="smkn1-detail-kepala">
		<div class="smkn1-wadah">
			<?php if ( $jenis ) : ?><span class="smkn1-kode besar"><?php echo esc_html( $jenis ); ?></span><?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<?php if ( $ts ) : ?>
				<p>
					<?php echo esc_html( date_i18n( 'l, j F Y', $ts ) ); ?>
					<?php if ( $selesai ) : ?> &ndash; <?php echo esc_html( date_i18n( 'j F Y', strtotime( $selesai ) ) ); ?><?php endif; ?>
					<?php if ( $lewat ) : ?> <em>(sudah berlalu)</em><?php endif; ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<section class="smkn1-seksi">
		<div class="smkn1-wadah">
			<div class="smkn1-detail-grid">

				<div class="smkn1-detail-isi">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="smkn1-detail-gambar"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>
					<?php the_content(); ?>
				</div>

				<aside class="smkn1-detail-sisi">
					<div class="smkn1-info-kotak">
						<h3>Informasi Kegiatan</h3>
						<dl>
							<?php if ( $ts ) : ?><dt>Tanggal Mulai</dt><dd><?php echo esc_html( date_i18n( 'j F Y', $ts ) ); ?></dd><?php endif; ?>
							<?php if ( $selesai ) : ?><dt>Tanggal Selesai</dt><dd><?php echo esc_html( date_i18n( 'j F Y', strtotime( $selesai ) ) ); ?></dd><?php endif; ?>
							<?php if ( $waktu ) : ?><dt>Waktu</dt><dd><?php echo esc_html( $waktu ); ?></dd><?php endif; ?>
							<?php if ( $lokasi ) : ?><dt>Lokasi</dt><dd><?php echo esc_html( $lokasi ); ?></dd><?php endif; ?>
							<?php if ( $jenis ) : ?><dt>Jenis</dt><dd><?php echo esc_html( $jenis ); ?></dd><?php endif; ?>
						</dl>
					</div>

					<div class="smkn1-info-kotak">
						<h3>Agenda Lain</h3>
						<ul class="smkn1-daftar-tautan">
							<?php
							$lain = get_posts( [
								'post_type'   => 'agenda',
								'numberposts' => 5,
								'exclude'     => [ get_the_ID() ],
								'meta_key'    => 'tanggal_mulai',
								'orderby'     => 'meta_value_num',
								'order'       => 'ASC',
								'meta_query'  => [ [ 'key' => 'tanggal_mulai', 'value' => gmdate( 'Ymd' ), 'compare' => '>=' ] ],
							] );
							if ( $lain ) :
								foreach ( $lain as $a ) : ?>
									<li><a href="<?php echo esc_url( get_permalink( $a ) ); ?>"><?php echo esc_html( $a->post_title ); ?></a></li>
								<?php endforeach;
							else : ?>
								<li>Belum ada agenda mendatang.</li>
							<?php endif; ?>
						</ul>
						<a class="smkn1-tombol blok" href="<?php echo esc_url( get_post_type_archive_link( 'agenda' ) ); ?>">Lihat Semua</a>
					</div>
				</aside>

			</div>
		</div>
	</section>

<?php endwhile;
get_footer();
