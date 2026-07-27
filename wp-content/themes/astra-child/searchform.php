<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<form role="search" method="get" class="smkn1-cari" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="smkn1-cari-input">Cari</label>
	<input type="search" id="smkn1-cari-input" name="s"
	       value="<?php echo esc_attr( get_search_query() ); ?>"
	       placeholder="Cari jurusan, guru, berita...">
	<button type="submit">Cari</button>
</form>
