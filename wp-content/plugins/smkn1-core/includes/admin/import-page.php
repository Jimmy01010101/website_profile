<?php
if (!defined('ABSPATH'))
	exit;

require_once __DIR__ . '/class-sheet-reader.php';
require_once __DIR__ . '/class-importer.php';

/** Submenu "Impor Data" di bawah Jurusan dan Guru & Tendik. */
add_action('admin_menu', function () {
	foreach (SMKN1_Importer::post_types() as $pt) {
		add_submenu_page(
			'edit.php?post_type=' . $pt,
			'Impor Data',
			'Impor Data',
			'manage_options',
			'smkn1-import-' . $pt,
			'smkn1_render_import_page'
		);
	}
});

/** Unduh template CSV. */
add_action('admin_post_smkn1_template', function () {
	if (!current_user_can('manage_options')) {
		wp_die('Akses ditolak.');
	}
	check_admin_referer('smkn1_template');

	$pt = sanitize_key($_GET['type'] ?? '');
	if (!in_array($pt, SMKN1_Importer::post_types(), true)) {
		wp_die('Jenis data tidak dikenali.');
	}

	nocache_headers();
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=template-' . $pt . '.csv');

	$out = fopen('php://output', 'w');
	fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8 dengan benar
	fputcsv($out, SMKN1_Importer::template_headers($pt));
	fclose($out);
	exit;
});

function smkn1_render_import_page()
{

	if (!current_user_can('manage_options')) {
		wp_die('Akses ditolak.');
	}

	$pt = str_replace('smkn1-import-', '', sanitize_key($_GET['page'] ?? ''));
	if (!in_array($pt, SMKN1_Importer::post_types(), true)) {
		$pt = 'jurusan';
	}

	$schema = SMKN1_Importer::schema($pt);
	$hasil = null;
	$error = null;

	if (isset($_POST['smkn1_import_nonce']) && wp_verify_nonce($_POST['smkn1_import_nonce'], 'smkn1_import')) {

		$header_row = max(1, absint($_POST['header_row'] ?? 1));
		$dry_run = isset($_POST['dry_run']);
		$file = $_FILES['smkn1_file'] ?? null;

		if (!$file || UPLOAD_ERR_OK !== $file['error'] || !is_uploaded_file($file['tmp_name'])) {
			$error = 'File gagal diunggah.';
		} else {
			$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

			if (!in_array($ext, ['csv', 'xlsx'], true)) {
				$error = 'Hanya file .csv atau .xlsx yang diterima.';
			} else {
				$rows = SMKN1_Sheet_Reader::read($file['tmp_name'], $ext);

				if (is_wp_error($rows)) {
					$error = $rows->get_error_message();
				} else {
					$hasil = SMKN1_Importer::run($pt, $rows, $header_row, $dry_run);
					if (is_wp_error($hasil)) {
						$error = $hasil->get_error_message();
						$hasil = null;
					}
				}
			}
		}
	}

	$template_url = wp_nonce_url(
		admin_url('admin-post.php?action=smkn1_template&type=' . $pt),
		'smkn1_template'
	);
	?>
	<div class="wrap">
		<h1>Impor Data <?php echo esc_html($schema['label']); ?></h1>

		<p>
			Unggah file <code>.csv</code> atau <code>.xlsx</code>. Baris pertama harus berisi nama kolom.
			Data yang namanya sudah ada akan diperbarui, bukan diduplikasi.
		</p>

		<?php if ($error): ?>
			<div class="notice notice-error">
				<p><?php echo esc_html($error); ?></p>
			</div>
		<?php endif; ?>

		<div class="card" style="max-width:820px">
			<h2 style="margin-top:0">Kolom yang dikenali</h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th style="width:32%">Nama kolom</th>
						<th>Alias yang juga diterima</th>
						<th style="width:14%">Wajib</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong><?php echo esc_html(ucwords($schema['title'][0])); ?></strong></td>
						<td><?php echo esc_html(implode(', ', array_slice($schema['title'], 1))); ?></td>
						<td><span style="color:#b32d2e">Ya</span></td>
					</tr>
					<?php foreach ($schema['fields'] as $name => $aliases): ?>
						<tr>
							<td><?php echo esc_html(ucwords(str_replace('_', ' ', $name))); ?></td>
							<td><?php echo esc_html(implode(', ', $aliases)); ?></td>
							<td>Tidak</td>
						</tr>
					<?php endforeach; ?>
					<?php if (!empty($schema['excerpt'])): ?>
						<tr>
							<td><?php echo esc_html(ucwords($schema['excerpt'][0])); ?></td>
							<td><?php echo esc_html(implode(', ', array_slice($schema['excerpt'], 1))); ?></td>
							<td>Tidak</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<p style="padding:12px 0 0">
				<a href="<?php echo esc_url($template_url); ?>" class="button">Unduh template CSV</a>
				<span class="description">Kolom yang tidak dikenali akan diabaikan.</span>
			</p>
		</div>

		<form method="post" enctype="multipart/form-data" style="margin-top:20px">
			<?php wp_nonce_field('smkn1_import', 'smkn1_import_nonce'); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="smkn1_file">File</label></th>
					<td><input type="file" name="smkn1_file" id="smkn1_file" accept=".csv,.xlsx" required></td>
				</tr>
				<tr>
					<th scope="row"><label for="header_row">Baris header</label></th>
					<td>
						<input type="number" name="header_row" id="header_row" value="1" min="1" style="width:80px">
						<p class="description">Ubah kalau nama kolom tidak berada di baris pertama.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Mode</th>
					<td>
						<label><input type="checkbox" name="dry_run" value="1" checked> Uji coba dulu (tidak menyimpan apa
							pun)</label>
						<p class="description">Hilangkan centang untuk benar-benar menyimpan data.</p>
					</td>
				</tr>
			</table>
			<?php submit_button('Proses File'); ?>
		</form>

		<?php if ($hasil): ?>
			<?php $s = $hasil['stats']; ?>
			<h2>Hasil</h2>
			<div class="notice notice-<?php echo $s['gagal'] ? 'warning' : 'success'; ?>">
				<p>
					<strong><?php echo esc_html($s['baru']); ?></strong> baru,
					<strong><?php echo esc_html($s['perbarui']); ?></strong> diperbarui,
					<strong><?php echo esc_html($s['lewati']); ?></strong> dilewati,
					<strong><?php echo esc_html($s['gagal']); ?></strong> gagal.
					<?php if (isset($_POST['dry_run'])): ?>
						<em>— ini masih uji coba, belum ada yang tersimpan.</em>
					<?php endif; ?>
				</p>
			</div>

			<?php if ($hasil['terpakai']): ?>
				<p class="description">Field terisi: <?php echo esc_html(implode(', ', $hasil['terpakai'])); ?></p>
			<?php endif; ?>

			<table class="widefat striped" style="max-width:820px">
				<thead>
					<tr>
						<th style="width:80px">Baris</th>
						<th style="width:110px">Aksi</th>
						<th>Nama</th>
						<th style="width:26%">Catatan</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($hasil['log'] as $b): ?>
						<tr>
							<td><?php echo esc_html($b['baris']); ?></td>
							<td><?php echo esc_html($b['aksi']); ?></td>
							<td><?php echo esc_html($b['nama']); ?></td>
							<td><?php echo esc_html($b['pesan']); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}