<?php
if (!defined('ABSPATH'))
	exit;

/**
 * Membuat halaman statis wajib sebuah situs profil sekolah beserta isi awalnya.
 * Dijalankan sekali saat plugin diaktifkan; halaman yang sudah ada tidak diubah.
 */
function smkn1_buat_halaman_statis()
{

	$nama = smkn1_opt('nama_sekolah', 'SMK Negeri 1 Bengkayang');
	$npsn = smkn1_opt('npsn');
	$berdiri = smkn1_opt('tahun_berdiri');
	$kepsek = smkn1_opt('nama_kepsek');
	$alamat = smkn1_opt('alamat');

	$misi_html = '';
	foreach (smkn1_misi() as $m) {
		$misi_html .= '<li>' . esc_html($m) . "</li>\n";
	}

	$halaman = [

		'profil' => [
			'judul' => 'Profil Sekolah',
			'ringkas' => 'Sekilas tentang ' . $nama,
			'isi' => "<p>{$nama} adalah sekolah menengah kejuruan negeri yang berdiri sejak tahun {$berdiri} dan terdaftar dengan NPSN {$npsn}. Sekolah ini berkedudukan di " . str_replace("\n", ', ', $alamat) . ".</p>\n"
				. "<p>Saat ini sekolah menyelenggarakan tujuh konsentrasi keahlian yang mencakup bidang agribisnis, bisnis dan manajemen, pariwisata, serta teknologi informasi. Seluruh program disusun untuk membekali peserta didik dengan kompetensi yang sesuai kebutuhan dunia kerja.</p>\n"
				. "<h2>Identitas Sekolah</h2>\n"
				. "<ul>\n<li>Nama: {$nama}</li>\n<li>NPSN: {$npsn}</li>\n<li>Tahun Berdiri: {$berdiri}</li>\n<li>Kepala Sekolah: {$kepsek}</li>\n<li>Status: Negeri</li>\n</ul>\n",
			'anak' => [],
		],

		'visi-misi' => [
			'judul' => 'Visi & Misi',
			'ringkas' => 'Arah dan komitmen penyelenggaraan pendidikan',
			'isi' => "<h2>Visi</h2>\n<blockquote><p>" . esc_html(smkn1_opt('visi')) . "</p></blockquote>\n"
				. "<h2>Misi</h2>\n<ol>\n{$misi_html}</ol>\n",
		],

		'sambutan-kepala-sekolah' => [
			'judul' => 'Sambutan Kepala Sekolah',
			'ringkas' => 'Pengantar dari ' . $kepsek,
			'isi' => '<p>' . esc_html(smkn1_opt('sambutan_isi', 'Selamat datang di laman resmi ' . $nama . '.')) . "</p>\n"
				. "<p style=\"text-align:right\"><strong>{$kepsek}</strong><br>Kepala Sekolah</p>\n",
		],

		'sarana-prasarana' => [
			'judul' => 'Sarana & Prasarana',
			'ringkas' => 'Fasilitas penunjang kegiatan belajar',
			'isi' => "<p>Sekolah menyediakan berbagai sarana dan prasarana untuk menunjang kegiatan pembelajaran teori maupun praktik kejuruan.</p>\n"
				. "<p><em>Daftar rinci ruang dan fasilitas dapat ditambahkan di sini, atau disajikan sebagai tabel melalui TablePress.</em></p>\n",
		],

		'spmb' => [
			'judul' => 'SPMB',
			'ringkas' => 'Sistem Penerimaan Murid Baru',
			'isi' => "<p>" . esc_html(smkn1_opt('cta_teks')) . "</p>\n"
				. "<h2>Konsentrasi Keahlian yang Dibuka</h2>\n"
				. "<p>Tujuh konsentrasi keahlian tersedia dengan daya tampung yang berbeda. Rincian lengkap beserta tautan pendaftaran masing-masing dapat dilihat pada halaman Program Keahlian.</p>\n"
				. "<h2>Alur Pendaftaran</h2>\n"
				. "<ol>\n<li>Menyiapkan berkas persyaratan.</li>\n<li>Mengisi formulir pendaftaran sesuai konsentrasi keahlian yang dipilih.</li>\n<li>Mengikuti proses seleksi sesuai jadwal.</li>\n<li>Melakukan daftar ulang bagi yang dinyatakan diterima.</li>\n</ol>\n",
		],

		'kontak' => [
			'judul' => 'Kontak',
			'ringkas' => 'Alamat dan cara menghubungi sekolah',
			'isi' => "<h2>Alamat</h2>\n<p>" . nl2br(esc_html($alamat)) . "</p>\n"
				. "<h2>Kontak</h2>\n<ul>\n"
				. (smkn1_opt('email') ? '<li>Surel: ' . esc_html(smkn1_opt('email')) . "</li>\n" : '')
				. (smkn1_opt('telepon') ? '<li>Telepon: ' . esc_html(smkn1_opt('telepon')) . "</li>\n" : '')
				. (smkn1_opt('jam') ? '<li>Jam Layanan: ' . esc_html(smkn1_opt('jam')) . "</li>\n" : '')
				. "</ul>\n",
		],
	];

	$dibuat = [];

	foreach ($halaman as $slug => $h) {

		if (get_page_by_path($slug, OBJECT, ['page'])) {
			continue;
		}

		$id = wp_insert_post([
			'post_type' => 'page',
			'post_title' => $h['judul'],
			'post_name' => $slug,
			'post_excerpt' => $h['ringkas'] ?? '',
			'post_content' => $h['isi'],
			'post_status' => 'publish',
		], true);

		if (!is_wp_error($id)) {
			$dibuat[$slug] = $id;
		}
	}

	return $dibuat;
}
