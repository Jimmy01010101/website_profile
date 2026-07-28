<?php
if (!defined('ABSPATH'))
	exit;

/** Cocokkan kolom file ke field ACF, lalu simpan sebagai post. */
class SMKN1_Importer
{

	/** Peta kolom per post type. Setiap field punya beberapa nama alias. */
	public static function schema($post_type)
	{

		$all = [
			'jurusan' => [
				'label' => 'Jurusan',
				'title' => ['nama jurusan', 'jurusan', 'nama', 'judul'],
				'excerpt' => ['deskripsi', 'ringkasan', 'keterangan'],
				'fields' => [
					'kode_jurusan' => ['kode jurusan', 'kode', 'singkatan'],
					'bidang_keahlian' => ['bidang keahlian', 'bidang'],
					'program_keahlian' => ['program keahlian', 'program'],
					'kuota_siswa' => ['kuota siswa', 'kuota', 'daya tampung'],
					'link_daftar' => ['link pendaftaran', 'link daftar', 'link', 'url'],
				],
			],

			'guru' => [
				'label' => 'Guru & Tendik',
				'title' => ['nama', 'nama lengkap', 'nama ptk'],
				'fields' => [
					'nuptk' => ['nuptk'],
					'nip' => ['nip'],
					'jenis_kelamin' => ['jenis kelamin', 'jk'],
					'status_kepegawaian' => ['status kepegawaian', 'status'],
					'jenis_ptk' => ['jenis ptk', 'jenis'],
					'gelar_depan' => ['gelar depan'],
					'gelar_belakang' => ['gelar belakang', 'gelar'],
					'jenjang' => ['jenjang pendidikan', 'jenjang'],
					'prodi' => ['jurusan prodi', 'prodi', 'program studi'],
					'jabatan' => ['jabatan ptk', 'jabatan'],
					'mengajar' => ['mengajar', 'mata pelajaran', 'mapel'],
					'tugas_tambahan' => ['tugas tambahan', 'tugas'],
					'tmt_kerja' => ['tmt kerja', 'tmt'],
				],
				// Kolom foto tidak masuk ACF, dipasang sebagai Featured Image.
				'thumbnail' => ['foto', 'nama file foto', 'gambar'],
			],
		];

		return $all[$post_type] ?? null;
	}

	public static function post_types()
	{
		return ['jurusan', 'guru'];
	}

	/** Header contoh untuk template CSV. */
	public static function template_headers($post_type)
	{
		$schema = self::schema($post_type);
		if (!$schema)
			return [];

		$head = [ucwords($schema['title'][0])];
		foreach (array_keys($schema['fields']) as $name) {
			$head[] = ucwords(str_replace('_', ' ', $name));
		}
		if (!empty($schema['excerpt'])) {
			$head[] = ucwords($schema['excerpt'][0]);
		}
		if (!empty($schema['thumbnail'])) {
			$head[] = 'Foto';
		}
		return $head;
	}

	/**
	 * Jalankan impor.
	 *
	 * @param string $post_type  jurusan | guru
	 * @param array  $rows       hasil SMKN1_Sheet_Reader
	 * @param int    $header_row nomor baris header (mulai 1)
	 * @param bool   $dry_run    true = hanya simulasi, tidak menyimpan
	 */
	public static function run($post_type, array $rows, $header_row = 1, $dry_run = true)
	{

		$schema = self::schema($post_type);
		if (!$schema) {
			return new WP_Error('tipe', 'Jenis data tidak dikenali.');
		}
		if (!isset($rows[$header_row - 1])) {
			return new WP_Error('header', "Baris header ke-{$header_row} tidak ada di file.");
		}

		$headers = array_map([__CLASS__, 'norm'], $rows[$header_row - 1]);
		$data = array_slice($rows, $header_row);

		$col_title = self::find($headers, $schema['title']);
		if (null === $col_title) {
			return new WP_Error(
				'kolom',
				'Kolom nama tidak ditemukan. Header harus memuat salah satu: ' . implode(', ', $schema['title'])
			);
		}

		$col_excerpt = !empty($schema['excerpt']) ? self::find($headers, $schema['excerpt']) : null;

		$col_fields = [];
		foreach ($schema['fields'] as $name => $aliases) {
			$i = self::find($headers, $aliases);
			if (null !== $i) {
				$col_fields[$name] = $i;
			}
		}

		$col_foto = !empty($schema['thumbnail']) ? self::find($headers, $schema['thumbnail']) : null;

		$log = [];
		$stats = ['baru' => 0, 'perbarui' => 0, 'lewati' => 0, 'gagal' => 0, 'foto' => 0, 'foto_gagal' => 0];

		foreach ($data as $n => $row) {
			$baris = $header_row + $n + 1;
			$nama = sanitize_text_field($row[$col_title] ?? '');

			if ('' === $nama) {
				$stats['lewati']++;
				$log[] = ['baris' => $baris, 'aksi' => 'LEWATI', 'nama' => '(kosong)', 'pesan' => 'Kolom nama kosong'];
				continue;
			}

			$slug = sanitize_title($nama);
			$ada = get_page_by_path($slug, OBJECT, [$post_type]);
			$aksi = $ada ? 'PERBARUI' : 'BARU';
			$pesan = '';

			$nilai = [];
			foreach ($col_fields as $name => $i) {
				$nilai[$name] = self::sanitize_field($name, $row[$i] ?? '');
			}

			// Tolak baris yang isinya jelas tergeser kolom.
			$cacat = self::periksa_baris($post_type, $nama, $nilai);
			if ($cacat) {
				$stats['gagal']++;
				$log[] = ['baris' => $baris, 'aksi' => 'TOLAK', 'nama' => $nama, 'pesan' => implode('; ', $cacat)];
				continue;
			}

			if ($dry_run) {
				$stats['BARU' === $aksi ? 'baru' : 'perbarui']++;
				$catatan = 'simulasi';
				if (null !== $col_foto) {
					$nf = trim((string) ($row[$col_foto] ?? ''));
					if ('' !== $nf) {
						$catatan = self::cari_lampiran($nf) ? 'simulasi, foto ditemukan' : 'simulasi, foto TIDAK ada';
					}
				}
				$log[] = ['baris' => $baris, 'aksi' => $aksi, 'nama' => $nama, 'pesan' => $catatan];
				continue;
			}

			$postarr = [
				'post_type' => $post_type,
				'post_title' => $nama,
				'post_name' => $slug,
				'post_status' => 'publish',
			];
			if (null !== $col_excerpt) {
				$postarr['post_excerpt'] = sanitize_textarea_field($row[$col_excerpt] ?? '');
			}
			if ($ada) {
				$postarr['ID'] = $ada->ID;
			}

			$post_id = $ada ? wp_update_post($postarr, true) : wp_insert_post($postarr, true);

			if (is_wp_error($post_id)) {
				$stats['gagal']++;
				$log[] = ['baris' => $baris, 'aksi' => 'GAGAL', 'nama' => $nama, 'pesan' => $post_id->get_error_message()];
				continue;
			}

			foreach ($nilai as $name => $v) {
				if (function_exists('update_field')) {
					update_field($name, $v, $post_id);
				} else {
					update_post_meta($post_id, $name, $v);
				}
			}

			$pesan = "#{$post_id}";

			// Foto opsional: cocokkan nama file ke Media Library.
			if (null !== $col_foto) {
				$nf = trim((string) ($row[$col_foto] ?? ''));
				if ('' !== $nf) {
					$att = self::cari_lampiran($nf);
					if ($att) {
						set_post_thumbnail($post_id, $att);
						$stats['foto']++;
						$pesan .= ' + foto';
					} else {
						$stats['foto_gagal']++;
						$pesan .= ' (foto tidak ada)';
					}
				}
			}

			$stats['BARU' === $aksi ? 'baru' : 'perbarui']++;
			$log[] = ['baris' => $baris, 'aksi' => $aksi, 'nama' => $nama, 'pesan' => $pesan];
		}

		return [
			'log' => $log,
			'stats' => $stats,
			'terpakai' => array_keys($col_fields),
			'diabaikan' => array_values(array_diff(
				array_filter($rows[$header_row - 1]),
				array_map(
					static fn($i) => $rows[$header_row - 1][$i] ?? '',
					array_merge([$col_title], array_values($col_fields), null !== $col_excerpt ? [$col_excerpt] : [])
				)
			)),
		];
	}

	/** Samakan bentuk teks header supaya "Kode Jurusan" == "kode_jurusan". */
	private static function norm($s)
	{
		$s = strtolower(trim((string) $s));
		$s = preg_replace('/[^a-z0-9]+/', ' ', $s);
		return trim(preg_replace('/\s+/', ' ', $s));
	}

	private static function find(array $headers, array $aliases)
	{
		foreach ($aliases as $alias) {
			$i = array_search(self::norm($alias), $headers, true);
			if (false !== $i)
				return $i;
		}
		return null;
	}

	/**
	 * Cari lampiran Media Library dari nama file.
	 * Terima "guru-budi.jpg" maupun "guru-budi" tanpa ekstensi.
	 * Kembalikan ID attachment atau 0.
	 */
	private static function cari_lampiran($nama_file)
	{
		global $wpdb;

		$nama_file = trim($nama_file);
		if ('' === $nama_file)
			return 0;

		// Kalau berupa URL, coba petakan langsung.
		if (preg_match('#^https?://#i', $nama_file)) {
			$id = attachment_url_to_postid($nama_file);
			if ($id)
				return $id;
			$nama_file = basename(parse_url($nama_file, PHP_URL_PATH));
		}

		$dasar = sanitize_file_name(pathinfo($nama_file, PATHINFO_FILENAME));

		// Cocokkan pada _wp_attached_file (mis. 2026/07/guru-budi.jpg).
		$id = $wpdb->get_var($wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta}
			 WHERE meta_key = '_wp_attached_file'
			   AND meta_value LIKE %s
			 ORDER BY post_id DESC LIMIT 1",
			'%/' . $wpdb->esc_like($dasar) . '.%'
		));
		if ($id)
			return (int) $id;

		// Cadangan: cocokkan pada slug attachment.
		$att = get_posts([
			'post_type' => 'attachment',
			'name' => $dasar,
			'posts_per_page' => 1,
			'fields' => 'ids',
		]);
		return $att ? (int) $att[0] : 0;
	}

	/**
	 * Deteksi baris yang kolomnya tergeser.
	 * Gejala paling sering: berkas dibuka di Excel lalu disimpan ulang
	 * sehingga angka panjang berubah jadi notasi ilmiah, atau nama yang
	 * mengandung spasi terpecah ke kolom sebelahnya.
	 */
	private static function periksa_baris($post_type, $nama, array $nilai)
	{
		$cacat = [];

		foreach ($nilai as $k => $v) {
			if (is_string($v) && preg_match('/^-?\\d+(\\.\\d+)?E\\+\\d+$/i', trim($v))) {
				$cacat[] = "kolom {$k} berupa notasi ilmiah, simpan berkas sebagai CSV tanpa dibuka di Excel";
			}
		}

		if ('guru' !== $post_type) {
			return $cacat;
		}

		if (
			isset($nilai['nuptk']) && '' !== $nilai['nuptk']
			&& !ctype_digit(str_replace(' ', '', $nilai['nuptk']))
		) {
			$cacat[] = 'NUPTK berisi teks, kemungkinan kolom bergeser';
		}

		if (
			isset($nilai['nip']) && '' !== $nilai['nip']
			&& !ctype_digit(str_replace(' ', '', $nilai['nip']))
		) {
			$cacat[] = 'NIP berisi teks, kemungkinan kolom bergeser';
		}

		$jenis_sah = ['Kepala Sekolah', 'Guru', 'Tenaga Kependidikan'];
		if (
			isset($nilai['jenis_ptk']) && '' !== $nilai['jenis_ptk']
			&& !in_array($nilai['jenis_ptk'], $jenis_sah, true)
		) {
			$cacat[] = 'Jenis PTK harus salah satu dari: ' . implode(', ', $jenis_sah);
		}

		$status_sah = ['PNS', 'PPPK', 'Guru Honor Sekolah', 'Tenaga Honor Sekolah'];
		if (
			isset($nilai['status_kepegawaian']) && '' !== $nilai['status_kepegawaian']
			&& !in_array($nilai['status_kepegawaian'], $status_sah, true)
		) {
			$cacat[] = 'Status Kepegawaian harus salah satu dari: ' . implode(', ', $status_sah);
		}

		if (isset($nilai['gelar_belakang']) && mb_strlen($nilai['gelar_belakang']) > 18) {
			$cacat[] = 'Gelar Belakang terlalu panjang, kemungkinan kolom bergeser';
		}

		return $cacat;
	}

	private static function sanitize_field($name, $raw)
	{
		$raw = trim((string) $raw);

		// Dapodik memakai tanda hubung untuk data kosong.
		if (in_array($raw, ['-', '-, -', '-, -, -'], true)) {
			$raw = '';
		}

		if ('link_daftar' === $name) {
			return esc_url_raw($raw);
		}
		if ('kuota_siswa' === $name) {
			return '' === $raw ? '' : absint($raw);
		}
		if ('tmt_kerja' === $name) {
			return self::norm_date($raw);
		}
		if ('jenis_kelamin' === $name) {
			$v = strtoupper(substr($raw, 0, 1));
			return in_array($v, ['L', 'P'], true) ? $v : '';
		}
		if (in_array($name, ['mengajar', 'tugas_tambahan'], true)) {
			return sanitize_textarea_field($raw);
		}
		return sanitize_text_field($raw);
	}

	/** ACF date_picker menyimpan format Ymd. Terima teks tanggal maupun serial Excel. */
	private static function norm_date($raw)
	{
		if ('' === $raw)
			return '';

		if (is_numeric($raw) && $raw > 20000 && $raw < 80000) {
			return gmdate('Ymd', ((int) $raw - 25569) * 86400);
		}
		$ts = strtotime(str_replace('/', '-', $raw));
		return $ts ? gmdate('Ymd', $ts) : '';
	}
}