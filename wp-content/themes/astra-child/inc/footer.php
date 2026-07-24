<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** Ganti footer bawaan Astra dengan footer sekolah. */
add_action( 'wp', function () {
	remove_action( 'astra_footer', 'astra_footer_markup' );
	add_action( 'astra_footer', 'smkn1_footer_markup' );
} );

function smkn1_footer_markup() {

	$lat = smkn1_opt( 'latitude' );
	$lng = smkn1_opt( 'longitude' );
	?>
	<footer class="smkn1-footer">
		<div class="smkn1-wadah">
			<div class="smkn1-footer-grid">

				<div class="smkn1-footer-kolom lebar">
					<h3><?php echo esc_html( smkn1_opt( 'nama_sekolah', get_bloginfo( 'name' ) ) ); ?></h3>
					<p class="smkn1-footer-alamat"><?php echo nl2br( esc_html( smkn1_opt( 'alamat' ) ) ); ?></p>
					<?php if ( $jam = smkn1_opt( 'jam' ) ) : ?>
						<p class="smkn1-footer-jam"><?php echo esc_html( $jam ); ?></p>
					<?php endif; ?>
					<div class="smkn1-footer-sosmed">
						<?php
						foreach ( [ 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'whatsapp' => 'WhatsApp' ] as $k => $l ) :
							$u = smkn1_opt( $k );
							if ( ! $u ) continue; ?>
							<a href="<?php echo esc_url( $u ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $l ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="smkn1-footer-kolom">
					<h4>Tautan Cepat</h4>
					<?php
					wp_nav_menu( [
						'theme_location' => 'smkn1_footer',
						'container'      => false,
						'menu_class'     => 'smkn1-footer-menu',
						'depth'          => 1,
						'fallback_cb'    => function () {
							echo '<ul class="smkn1-footer-menu">';
							echo '<li><a href="' . esc_url( get_post_type_archive_link( 'jurusan' ) ) . '">Program Keahlian</a></li>';
							echo '<li><a href="' . esc_url( get_post_type_archive_link( 'guru' ) ) . '">Guru &amp; Tendik</a></li>';
							echo '<li><a href="' . esc_url( get_post_type_archive_link( 'prestasi' ) ) . '">Prestasi</a></li>';
							echo '<li><a href="' . esc_url( get_post_type_archive_link( 'galeri' ) ) . '">Galeri</a></li>';
							echo '<li><a href="' . esc_url( get_post_type_archive_link( 'agenda' ) ) . '">Agenda</a></li>';
							echo '</ul>';
						},
					] );
					?>
				</div>

				<div class="smkn1-footer-kolom">
					<h4>Konsentrasi Keahlian</h4>
					<ul class="smkn1-footer-menu">
						<?php
						foreach ( get_posts( [ 'post_type' => 'jurusan', 'numberposts' => 7, 'orderby' => 'title', 'order' => 'ASC' ] ) as $j ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $j ) ); ?>"><?php echo esc_html( $j->post_title ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="smkn1-footer-kolom">
					<h4>Lokasi</h4>
					<?php if ( $lat && $lng ) : ?>
						<a class="smkn1-peta"
						   href="https://www.google.com/maps?q=<?php echo esc_attr( $lat ); ?>,<?php echo esc_attr( $lng ); ?>"
						   target="_blank" rel="noopener">
							Lihat di Google Maps
						</a>
						<p class="smkn1-koordinat"><?php echo esc_html( $lat ); ?>, <?php echo esc_html( $lng ); ?></p>
					<?php endif; ?>
					<?php if ( $e = smkn1_opt( 'email' ) ) : ?>
						<p><a href="mailto:<?php echo esc_attr( $e ); ?>"><?php echo esc_html( $e ); ?></a></p>
					<?php endif; ?>
					<?php if ( $t = smkn1_opt( 'telepon' ) ) : ?>
						<p><?php echo esc_html( $t ); ?></p>
					<?php endif; ?>
				</div>

			</div>
		</div>

		<div class="smkn1-footer-bawah">
			<div class="smkn1-wadah">
				<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( smkn1_opt( 'nama_sekolah', get_bloginfo( 'name' ) ) ); ?>. Hak cipta dilindungi.</span>
				<?php if ( $n = smkn1_opt( 'npsn' ) ) : ?>
					<span>NPSN <?php echo esc_html( $n ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</footer>
	<?php
}
