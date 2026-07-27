<?php
/** Hasil pencarian lintas jenis konten. */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$label = [
	'post'     => 'Berita',
	'page'     => 'Halaman',
	'jurusan'  => 'Program Keahlian',
	'guru'     => 'Guru & Tendik',
	'prestasi' => 'Prestasi',
	'galeri'   => 'Galeri',
	'agenda'   => 'Agenda',
];
?>

<section class="smkn1-detail-kepala">
	<div class="smkn1-wadah">
		<h1>Hasil Pencarian</h1>
		<p>
			<?php
			global $wp_query;
			printf(
				'%d hasil untuk "%s"',
				(int) $wp_query->found_posts,
				esc_html( get_search_query() )
			);
			?>
		</p>
	</div>
</section>

<section class="smkn1-seksi">
	<div class="smkn1-wadah">
		<?php get_search_form(); ?>

		<?php if ( have_posts() ) : ?>
			<div class="smkn1-grid" style="margin-top:28px">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="smkn1-kartu">
						<span class="smkn1-kode"><?php echo esc_html( $label[ get_post_type() ] ?? get_post_type() ); ?></span>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="smkn1-ringkas"><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: get_the_content(), 22 ) ); ?></p>
						<div class="smkn1-meta">
							<span><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span>
							<a class="smkn1-tombol" href="<?php the_permalink(); ?>">Buka</a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( [ 'mid_size' => 2 ] ); ?>
		<?php else : ?>
			<p class="smkn1-catatan">Tidak ada hasil yang cocok. Coba kata kunci lain.</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer();
