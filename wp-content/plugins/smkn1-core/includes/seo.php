<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * SEO dasar tanpa plugin tambahan.
 * Menyediakan meta deskripsi, Open Graph untuk berbagi ke media sosial,
 * dan data terstruktur sekolah agar dikenali mesin pencari.
 */

/* Ringkasan halaman untuk hasil pencarian dan pratinjau tautan. */
function smkn1_deskripsi_halaman() {

	if ( is_front_page() ) {
		$d = smkn1_opt( 'tagline' ) . '. ' . wp_strip_all_tags( smkn1_opt( 'visi' ) );
	} elseif ( is_singular() ) {
		$d = get_the_excerpt() ?: wp_strip_all_tags( get_the_content() );
	} elseif ( is_post_type_archive( 'jurusan' ) ) {
		$d = 'Daftar program dan konsentrasi keahlian di ' . smkn1_opt( 'nama_sekolah' ) . '.';
	} elseif ( is_post_type_archive( 'guru' ) ) {
		$d = 'Daftar pendidik dan tenaga kependidikan di ' . smkn1_opt( 'nama_sekolah' ) . '.';
	} else {
		$d = smkn1_opt( 'nama_sekolah' ) . ' — ' . smkn1_opt( 'tagline' );
	}

	return wp_trim_words( wp_strip_all_tags( $d ), 30, '' );
}

add_action( 'wp_head', function () {

	$judul = is_front_page()
		? smkn1_opt( 'nama_sekolah' ) . ' — ' . smkn1_opt( 'tagline' )
		: wp_get_document_title();

	$desk = smkn1_deskripsi_halaman();
	$url  = is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) );

	$gambar = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$gambar = get_the_post_thumbnail_url( null, 'large' );
	} elseif ( $logo = smkn1_opt( 'logo_id' ) ) {
		$gambar = wp_get_attachment_image_url( $logo, 'large' );
	}

	echo "\n<!-- SEO SMKN 1 Bengkayang -->\n";
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desk ) );
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() ? 'article' : 'website' );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $judul ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desk ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( smkn1_opt( 'nama_sekolah' ) ) );
	printf( '<meta property="og:locale" content="id_ID">' . "\n" );
	if ( $gambar ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $gambar ) );
	}

	printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	echo "<!-- /SEO -->\n\n";
}, 1 );

/* Data terstruktur sekolah, hanya di halaman depan. */
add_action( 'wp_head', function () {

	if ( ! is_front_page() ) {
		return;
	}

	$sosmed = array_values( array_filter( [
		smkn1_opt( 'instagram' ),
		smkn1_opt( 'facebook' ),
		smkn1_opt( 'youtube' ),
		smkn1_opt( 'tiktok' ),
	] ) );

	$data = [
		'@context'    => 'https://schema.org',
		'@type'       => 'School',
		'name'        => smkn1_opt( 'nama_sekolah' ),
		'description' => smkn1_deskripsi_halaman(),
		'url'         => home_url( '/' ),
		'address'     => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => wp_strip_all_tags( smkn1_opt( 'alamat' ) ),
			'addressRegion'   => 'Kalimantan Barat',
			'addressCountry'  => 'ID',
		],
	];

	if ( $e = smkn1_opt( 'email' ) )    { $data['email'] = $e; }
	if ( $t = smkn1_opt( 'telepon' ) )  { $data['telephone'] = $t; }
	if ( $sosmed )                      { $data['sameAs'] = $sosmed; }
	if ( $l = smkn1_opt( 'logo_id' ) )  { $data['logo'] = wp_get_attachment_image_url( $l, 'medium' ); }

	if ( smkn1_opt( 'latitude' ) && smkn1_opt( 'longitude' ) ) {
		$data['geo'] = [
			'@type'     => 'GeoCoordinates',
			'latitude'  => smkn1_opt( 'latitude' ),
			'longitude' => smkn1_opt( 'longitude' ),
		];
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 2 );

/* Jenis konten tanpa halaman sendiri tidak perlu masuk peta situs. */
add_filter( 'wp_sitemaps_post_types', function ( $tipe ) {
	unset( $tipe['slide'] );
	return $tipe;
} );
