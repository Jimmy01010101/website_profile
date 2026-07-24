(function () {
	var tombol = document.querySelector('.smkn1-nav-toggle');
	var nav    = document.querySelector('.smkn1-nav');
	if (!tombol || !nav) return;

	tombol.addEventListener('click', function () {
		var buka = nav.classList.toggle('buka');
		tombol.classList.toggle('buka', buka);
		tombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
	});

	// Tutup saat klik di luar menu.
	document.addEventListener('click', function (e) {
		if (!nav.classList.contains('buka')) return;
		if (nav.contains(e.target) || tombol.contains(e.target)) return;
		nav.classList.remove('buka');
		tombol.classList.remove('buka');
		tombol.setAttribute('aria-expanded', 'false');
	});
})();
