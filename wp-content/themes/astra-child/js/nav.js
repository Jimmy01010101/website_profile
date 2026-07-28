(function () {
	'use strict';

	var tombol = document.querySelector('.smkn1-nav-toggle');
	var nav    = document.querySelector('.smkn1-nav');
	if (!tombol || !nav) return;

	var LEBAR_PONSEL = 782;

	function ponsel() {
		return window.matchMedia('(max-width: ' + LEBAR_PONSEL + 'px)').matches;
	}

	/* ---------- Buka tutup menu utama ---------- */

	function tutupMenu() {
		nav.classList.remove('buka');
		tombol.classList.remove('buka');
		tombol.setAttribute('aria-expanded', 'false');
	}

	tombol.addEventListener('click', function (e) {
		e.stopPropagation();
		var buka = nav.classList.toggle('buka');
		tombol.classList.toggle('buka', buka);
		tombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
	});

	document.addEventListener('click', function (e) {
		if (!nav.classList.contains('buka')) return;
		if (nav.contains(e.target) || tombol.contains(e.target)) return;
		tutupMenu();
	});

	document.addEventListener('keydown', function (e) {
		if ('Escape' === e.key && nav.classList.contains('buka')) {
			tutupMenu();
			tombol.focus();
		}
	});

	/* ---------- Submenu yang bisa dilipat ---------- */

	var induk = nav.querySelectorAll('.smkn1-menu > li.menu-item-has-children');

	induk.forEach(function (li) {

		var tautan = li.querySelector(':scope > a');
		var sub    = li.querySelector(':scope > .sub-menu');
		if (!tautan || !sub) return;

		var pemicu = document.createElement('button');
		pemicu.className = 'smkn1-sub-toggle';
		pemicu.type = 'button';
		pemicu.setAttribute('aria-expanded', 'false');
		pemicu.setAttribute('aria-label', 'Buka submenu ' + tautan.textContent.trim());
		pemicu.innerHTML = '<span></span>';
		tautan.insertAdjacentElement('afterend', pemicu);

		pemicu.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var buka = li.classList.toggle('sub-buka');
			pemicu.setAttribute('aria-expanded', buka ? 'true' : 'false');
		});

		/*
		 * Item induk yang tidak menuju halaman mana pun hanya berfungsi
		 * sebagai pembuka submenu, jadi ketukan padanya diarahkan ke sana.
		 */
		var tujuan = tautan.getAttribute('href');
		if (!tujuan || '#' === tujuan) {
			tautan.addEventListener('click', function (e) {
				e.preventDefault();
				pemicu.click();
			});
		}
	});

	/* Saat kembali ke layar lebar, seluruh lipatan dikembalikan. */
	var ukuranTerakhir = ponsel();

	window.addEventListener('resize', function () {
		var sekarang = ponsel();
		if (sekarang === ukuranTerakhir) return;
		ukuranTerakhir = sekarang;

		tutupMenu();
		nav.querySelectorAll('.sub-buka').forEach(function (li) {
			li.classList.remove('sub-buka');
			var p = li.querySelector(':scope > .smkn1-sub-toggle');
			if (p) p.setAttribute('aria-expanded', 'false');
		});
	});
})();