<?php
/** Halaman statis: Profil, Sejarah, Sarana, SPMB, dan sebagainya. */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post(); ?>

	<section class="smkn1-detail-kepala">
		<div class="smkn1-wadah">
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
		</div>
	</section>

	<section class="smkn1-seksi">
		<div class="smkn1-wadah">
			<div class="smkn1-halaman">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="smkn1-detail-gambar"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>

				<?php the_content(); ?>

				<?php
				wp_link_pages( [
					'before' => '<nav class="smkn1-halaman-nav">Halaman: ',
					'after'  => '</nav>',
				] );
				?>
			</div>
		</div>
	</section>

<?php endwhile;
get_footer();
