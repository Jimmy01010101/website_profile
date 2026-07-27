<?php
/** Halaman tidak ditemukan. */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="smkn1-detail-kepala">
	<div class="smkn1-wadah">
		<h1>Halaman Tidak Ditemukan</h1>
		<p>Alamat yang kamu tuju tidak tersedia atau sudah dipindahkan.</p>
	</div>
</section>

<section class="smkn1-seksi">
	<div class="smkn1-wadah">
		<div class="smkn1-404">
			<p>Coba cari lewat kotak di bawah, atau langsung menuju halaman berikut:</p>

			<?php get_search_form(); ?>

			<div class="smkn1-grid" style="margin-top:32px">
				<a class="smkn1-kartu tautan-cepat" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<h2>Beranda</h2>
					<p class="smkn1-ringkas">Kembali ke halaman utama</p>
				</a>
				<a class="smkn1-kartu tautan-cepat" href="<?php echo esc_url( get_post_type_archive_link( 'jurusan' ) ); ?>">
					<h2>Program Keahlian</h2>
					<p class="smkn1-ringkas">Tujuh konsentrasi keahlian</p>
				</a>
				<a class="smkn1-kartu tautan-cepat" href="<?php echo esc_url( get_post_type_archive_link( 'guru' ) ); ?>">
					<h2>Guru &amp; Tendik</h2>
					<p class="smkn1-ringkas">Pendidik dan tenaga kependidikan</p>
				</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer();
