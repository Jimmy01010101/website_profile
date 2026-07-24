<?php
if (!defined('ABSPATH'))
	exit;

/** Baca CSV atau XLSX menjadi array baris. Tanpa library eksternal. */
class SMKN1_Sheet_Reader
{

	public static function read($path, $ext = null)
	{
		$ext = strtolower($ext ?: pathinfo($path, PATHINFO_EXTENSION));

		if ('csv' === $ext || 'txt' === $ext) {
			return self::read_csv($path);
		}
		if ('xlsx' === $ext) {
			return self::read_xlsx($path);
		}
		return new WP_Error('format', 'Format tidak didukung. Gunakan .csv atau .xlsx');
	}

	private static function read_csv($path)
	{
		$fh = fopen($path, 'r');
		if (!$fh) {
			return new WP_Error('baca', 'File tidak bisa dibaca.');
		}

		// Tebak pemisah dari baris pertama.
		$first = fgets($fh);
		$delim = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';
		rewind($fh);

		// Buang BOM UTF-8 kalau ada.
		if (fread($fh, 3) !== "\xEF\xBB\xBF") {
			rewind($fh);
		}

		$rows = [];
		while (($row = fgetcsv($fh, 0, $delim)) !== false) {
			$row = array_map(static fn($c) => trim((string) $c), $row);
			if ('' !== implode('', $row)) {
				$rows[] = $row;
			}
		}
		fclose($fh);

		return $rows ?: new WP_Error('kosong', 'File CSV kosong.');
	}

	private static function read_xlsx($path)
	{
		if (!class_exists('ZipArchive')) {
			return new WP_Error('zip', 'Ekstensi PHP zip tidak aktif. Gunakan file CSV.');
		}

		$zip = new ZipArchive();
		if (true !== $zip->open($path)) {
			return new WP_Error('zip', 'File XLSX rusak atau tidak bisa dibuka.');
		}

		// Teks di XLSX disimpan terpisah di sharedStrings, sel hanya menyimpan indeksnya.
		$shared = [];
		$sst_xml = $zip->getFromName('xl/sharedStrings.xml');
		if (false !== $sst_xml) {
			$sst = simplexml_load_string($sst_xml);
			if ($sst) {
				foreach ($sst->si as $si) {
					$shared[] = trim(strip_tags($si->asXML()));
				}
			}
		}

		$sheet_xml = $zip->getFromName('xl/worksheets/sheet1.xml');
		$zip->close();

		if (false === $sheet_xml) {
			return new WP_Error('sheet', 'Sheet pertama tidak ditemukan.');
		}

		$xml = simplexml_load_string($sheet_xml);
		if (!$xml) {
			return new WP_Error('sheet', 'Isi sheet tidak bisa dibaca.');
		}

		$rows = [];
		foreach ($xml->sheetData->row as $r) {
			$cells = [];
			foreach ($r->c as $c) {
				$idx = self::col_index((string) $c['r']);
				$type = (string) $c['t'];

				if ('s' === $type) {
					$val = $shared[(int) $c->v] ?? '';
				} elseif ('inlineStr' === $type) {
					$val = strip_tags($c->is->asXML());
				} else {
					$val = isset($c->v) ? (string) $c->v : '';
				}
				$cells[$idx] = trim($val);
			}

			if (!$cells)
				continue;

			// Isi kolom kosong supaya posisi indeks tetap konsisten.
			$row = [];
			for ($i = 0, $max = max(array_keys($cells)); $i <= $max; $i++) {
				$row[$i] = $cells[$i] ?? '';
			}
			if ('' !== implode('', $row)) {
				$rows[] = $row;
			}
		}

		return $rows ?: new WP_Error('kosong', 'Sheet tidak berisi data.');
	}

	/** Ubah referensi sel (contoh "AB12") menjadi indeks kolom mulai 0. */
	private static function col_index($ref)
	{
		preg_match('/^([A-Z]+)/', $ref, $m);
		$n = 0;
		foreach (str_split($m[1] ?? 'A') as $ch) {
			$n = $n * 26 + (ord($ch) - 64);
		}
		return $n - 1;
	}
}