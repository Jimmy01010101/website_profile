<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** Ganti header bawaan Astra dengan header sekolah. */
add_action( 'wp', function () {
	remove_action( 'astra_header', 'astra_header_markup' );
	add_action( 'astra_header', 'smkn1_header_markup' );
} );

function smkn1_header_markup() {

	$logo_id = smkn1_opt( 'logo_id' );
	$nama    = smkn1_opt( 'nama_sekolah', get_bloginfo( 'name' ) );
	$npsn    = smkn1_opt( 'npsn' );
	$telepon = smkn1_opt( 'telepon' );
	$email   = smkn1_opt( 'email' );
	$cta_url = smkn1_opt( 'cta_url' );
	?>
	<header class="smkn1-header">

		<div class="smkn1-topbar">
			<div class="smkn1-wadah">
				<div class="smkn1-topbar-kiri">
					<?php if ( $npsn ) : ?><span>NPSN <?php echo esc_html( $npsn ); ?></span><?php endif; ?>
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<?php endif; ?>
					<?php if ( $telepon ) : ?><span><?php echo esc_html( $telepon ); ?></span><?php endif; ?>
				</div>
				<div class="smkn1-topbar-kanan">
					<?php
					foreach ( [ 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'youtube' => 'YouTube', 'tiktok' => 'TikTok' ] as $k => $l ) :
						$u = smkn1_opt( $k );
						if ( ! $u ) continue; ?>
						<a href="<?php echo esc_url( $u ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $l ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="smkn1-header-utama">
			<div class="smkn1-wadah">

				<a class="smkn1-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( $logo_id ) : ?>
						<?php echo wp_get_attachment_image( $logo_id, 'thumbnail', false, [ 'class' => 'smkn1-logo' ] ); ?>
					<?php endif; ?>
					<span class="smkn1-brand-teks">
						<strong><?php echo esc_html( $nama ); ?></strong>
						<small><?php echo esc_html( smkn1_opt( 'tagline' ) ); ?></small>
					</span>
				</a>

				<button class="smkn1-nav-toggle" aria-label="Buka menu" aria-expanded="false">
					<span></span><span></span><span></span>
				</button>

				<nav class="smkn1-nav" aria-label="Menu utama">
					<?php
					wp_nav_menu( [
						'theme_location' => 'smkn1_utama',
						'container'      => false,
						'menu_class'     => 'smkn1-menu',
						'depth'          => 2,
						'fallback_cb'    => 'smkn1_menu_cadangan',
					] );
					?>
					<?php if ( $cta_url ) : ?>
						<a class="smkn1-tombol smkn1-nav-cta" href="<?php echo esc_url( $cta_url ); ?>">SPMB</a>
					<?php endif; ?>
				</nav>

			</div>
		</div>
	</header>
	<?php
}

/** Tampilkan tautan seadanya bila menu belum disusun. */
function smkn1_menu_cadangan() {
	echo '<ul class="smkn1-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Beranda</a></li>';
	echo '<li><a href="' . esc_url( get_post_type_archive_link( 'jurusan' ) ) . '">Program Keahlian</a></li>';
	echo '<li><a href="' . esc_url( get_post_type_archive_link( 'guru' ) ) . '">Guru &amp; Tendik</a></li>';
	echo '<li><a href="' . esc_url( get_post_type_archive_link( 'prestasi' ) ) . '">Prestasi</a></li>';
	echo '<li><a href="' . esc_url( get_post_type_archive_link( 'galeri' ) ) . '">Galeri</a></li>';
	echo '</ul>';
}
