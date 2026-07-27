<?php
/** Detail satu guru: foto, identitas, mata pelajaran, tugas tambahan. */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$gelar_d  = get_field( 'gelar_depan' );
	$gelar_b  = get_field( 'gelar_belakang' );
	$jenis    = get_field( 'jenis_ptk' );
	$jabatan  = get_field( 'jabatan' );
	$status   = get_field( 'status_kepegawaian' );
	$nuptk    = get_field( 'nuptk' );
	$nip      = get_field( 'nip' );
	$jk       = get_field( 'jenis_kelamin' );
	$jenjang  = get_field( 'jenjang' );
	$prodi    = get_field( 'prodi' );
	$mengajar = get_field( 'mengajar' );
	$tugas    = get_field( 'tugas_tambahan' );
	$tmt      = get_field( 'tmt_kerja' );

	$nama_penuh = trim( ( $gelar_d ? $gelar_d . ' ' : '' ) . get_the_title() . ( $gelar_b ? ', ' . $gelar_b : '' ) );
	?>

	<section class="smkn1-detail-kepala">
		<div class="smkn1-wadah">
			<?php if ( $jenis ) : ?><span class="smkn1-kode besar"><?php echo esc_html( $jenis ); ?></span><?php endif; ?>
			<h1><?php echo esc_html( $nama_penuh ); ?></h1>
			<?php if ( $jabatan ) : ?><p><?php echo esc_html( $jabatan ); ?></p><?php endif; ?>
		</div>
	</section>

	<section class="smkn1-seksi">
		<div class="smkn1-wadah">
			<div class="smkn1-detail-grid">

				<div class="smkn1-detail-isi">
					<?php if ( $mengajar ) : ?>
						<h2 class="smkn1-judul-seksi">Mata Pelajaran yang Diampu</h2>
						<ul class="smkn1-daftar-butir">
							<?php foreach ( array_filter( array_map( 'trim', explode( ',', $mengajar ) ) ) as $m ) : ?>
								<li><?php echo esc_html( $m ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $tugas ) : ?>
						<h2 class="smkn1-judul-seksi">Tugas Tambahan</h2>
						<ul class="smkn1-daftar-butir">
							<?php foreach ( array_filter( array_map( 'trim', explode( ',', $tugas ) ) ) as $t ) : ?>
								<li><?php echo esc_html( $t ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( get_the_content() ) : ?>
						<h2 class="smkn1-judul-seksi">Keterangan</h2>
						<?php the_content(); ?>
					<?php endif; ?>

					<?php if ( ! $mengajar && ! $tugas && ! get_the_content() ) : ?>
						<p class="smkn1-catatan">Belum ada keterangan tambahan untuk pendidik ini.</p>
					<?php endif; ?>
				</div>

				<aside class="smkn1-detail-sisi">
					<div class="smkn1-info-kotak">
						<div class="smkn1-guru-foto besar">
							<?php echo has_post_thumbnail()
								? get_the_post_thumbnail( null, 'medium' )
								: '<span class="smkn1-guru-inisial">' . esc_html( mb_substr( get_the_title(), 0, 1 ) ) . '</span>'; ?>
						</div>
						<?php if ( $status ) : ?>
							<p class="smkn1-status-tengah"><span class="smkn1-guru-status"><?php echo esc_html( $status ); ?></span></p>
						<?php endif; ?>
					</div>

					<div class="smkn1-info-kotak">
						<h3>Data Kepegawaian</h3>
						<dl>
							<?php if ( $nuptk ) : ?><dt>NUPTK</dt><dd><?php echo esc_html( $nuptk ); ?></dd><?php endif; ?>
							<?php if ( $nip ) : ?><dt>NIP</dt><dd><?php echo esc_html( $nip ); ?></dd><?php endif; ?>
							<?php if ( $jk ) : ?><dt>Jenis Kelamin</dt><dd><?php echo 'L' === $jk ? 'Laki-laki' : 'Perempuan'; ?></dd><?php endif; ?>
							<?php if ( $jenjang ) : ?><dt>Jenjang Pendidikan</dt><dd><?php echo esc_html( $jenjang ); ?></dd><?php endif; ?>
							<?php if ( $prodi ) : ?><dt>Program Studi</dt><dd><?php echo esc_html( $prodi ); ?></dd><?php endif; ?>
							<?php if ( $tmt ) : ?><dt>TMT Kerja</dt><dd><?php echo esc_html( date_i18n( 'j F Y', strtotime( $tmt ) ) ); ?></dd><?php endif; ?>
						</dl>
					</div>

					<div class="smkn1-info-kotak">
						<h3>Pendidik Lain</h3>
						<ul class="smkn1-daftar-tautan">
							<?php
							$lain = get_posts( [
								'post_type'   => 'guru',
								'numberposts' => 6,
								'exclude'     => [ get_the_ID() ],
								'orderby'     => 'rand',
							] );
							foreach ( $lain as $g ) : ?>
								<li><a href="<?php echo esc_url( get_permalink( $g ) ); ?>"><?php echo esc_html( $g->post_title ); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<a class="smkn1-tombol blok" href="<?php echo esc_url( get_post_type_archive_link( 'guru' ) ); ?>">Lihat Semua</a>
					</div>
				</aside>

			</div>
		</div>
	</section>

<?php endwhile;
get_footer();
