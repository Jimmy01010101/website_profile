<?php
/** Detail berita atau pengumuman. */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post(); ?>

	<section class="smkn1-detail-kepala">
		<div class="smkn1-wadah">
			<?php
			$kat = get_the_category();
			if ( $kat ) : ?>
				<span class="smkn1-kode besar"><?php echo esc_html( $kat[0]->name ); ?></span>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<p><?php echo esc_html( get_the_date( 'l, j F Y' ) ); ?></p>
		</div>
	</section>

	<section class="smkn1-seksi">
		<div class="smkn1-wadah">
			<div class="smkn1-detail-grid">

				<article class="smkn1-detail-isi">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="smkn1-detail-gambar"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>
					<?php the_content(); ?>

					<?php if ( $tags = get_the_tags() ) : ?>
						<div class="smkn1-tag">
							<?php foreach ( $tags as $t ) : ?>
								<a href="<?php echo esc_url( get_tag_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</article>

				<aside class="smkn1-detail-sisi">
					<div class="smkn1-info-kotak">
						<h3>Berita Lainnya</h3>
						<ul class="smkn1-daftar-tautan">
							<?php foreach ( get_posts( [ 'numberposts' => 6, 'exclude' => [ get_the_ID() ] ] ) as $b ) : ?>
								<li><a href="<?php echo esc_url( get_permalink( $b ) ); ?>"><?php echo esc_html( $b->post_title ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="smkn1-info-kotak">
						<h3>Agenda Terdekat</h3>
						<ul class="smkn1-daftar-tautan">
							<?php
							$ag = get_posts( [
								'post_type'   => 'agenda',
								'numberposts' => 4,
								'meta_key'    => 'tanggal_mulai',
								'orderby'     => 'meta_value_num',
								'order'       => 'ASC',
								'meta_query'  => [ [ 'key' => 'tanggal_mulai', 'value' => gmdate( 'Ymd' ), 'compare' => '>=' ] ],
							] );
							if ( $ag ) :
								foreach ( $ag as $a ) : ?>
									<li><a href="<?php echo esc_url( get_permalink( $a ) ); ?>"><?php echo esc_html( $a->post_title ); ?></a></li>
								<?php endforeach;
							else : ?>
								<li>Belum ada agenda.</li>
							<?php endif; ?>
						</ul>
					</div>
				</aside>

			</div>
		</div>
	</section>

<?php endwhile;
get_footer();
