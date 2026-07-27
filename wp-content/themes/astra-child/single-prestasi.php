<?php
/** Detail satu prestasi siswa. */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$peringkat = get_field( 'peringkat' );
	$siswa     = get_field( 'nama_siswa' );
	$lomba     = get_field( 'nama_lomba' );
	$penye     = get_field( 'penyelenggara' );
	$tahun     = get_field( 'tahun' );
	$kelas     = get_field( 'kelas' );
	?>

	<section class="smkn1-detail-kepala">
		<div class="smkn1-wadah">
			<?php if ( $peringkat ) : ?><span class="smkn1-kode besar emas"><?php echo esc_html( $peringkat ); ?></span><?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<?php if ( $siswa ) : ?><p><?php echo esc_html( $siswa ); ?><?php echo $kelas ? ' — ' . esc_html( $kelas ) : ''; ?></p><?php endif; ?>
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
						<h3>Rincian</h3>
						<dl>
							<?php if ( $lomba ) : ?><dt>Nama Lomba</dt><dd><?php echo esc_html( $lomba ); ?></dd><?php endif; ?>
							<?php if ( $peringkat ) : ?><dt>Peringkat</dt><dd><?php echo esc_html( $peringkat ); ?></dd><?php endif; ?>
							<?php if ( $penye ) : ?><dt>Penyelenggara</dt><dd><?php echo esc_html( $penye ); ?></dd><?php endif; ?>
							<?php if ( $tahun ) : ?><dt>Tahun</dt><dd><?php echo esc_html( $tahun ); ?></dd><?php endif; ?>
							<?php
							$tingkat = get_the_terms( get_the_ID(), 'tingkat_prestasi' );
							if ( $tingkat && ! is_wp_error( $tingkat ) ) : ?>
								<dt>Tingkat</dt>
								<dd><?php echo esc_html( implode( ', ', wp_list_pluck( $tingkat, 'name' ) ) ); ?></dd>
							<?php endif; ?>
						</dl>
					</div>

					<div class="smkn1-info-kotak">
						<h3>Prestasi Lain</h3>
						<ul class="smkn1-daftar-tautan">
							<?php foreach ( get_posts( [ 'post_type' => 'prestasi', 'numberposts' => 5, 'exclude' => [ get_the_ID() ] ] ) as $p ) : ?>
								<li><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( $p->post_title ); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<a class="smkn1-tombol blok" href="<?php echo esc_url( get_post_type_archive_link( 'prestasi' ) ); ?>">Lihat Semua</a>
					</div>
				</aside>

			</div>
		</div>
	</section>

<?php endwhile;
get_footer();
