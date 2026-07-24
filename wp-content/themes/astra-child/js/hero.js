(function () {
	var hero = document.querySelector('.smkn1-hero');
	if (!hero) return;

	var slides = hero.querySelectorAll('.smkn1-slide');
	var dots   = hero.querySelectorAll('.smkn1-dot button');
	if (slides.length < 2) return;

	var kini = 0, timer;

	function ke(i) {
		slides[kini].classList.remove('aktif');
		if (dots[kini]) dots[kini].classList.remove('aktif');
		kini = (i + slides.length) % slides.length;
		slides[kini].classList.add('aktif');
		if (dots[kini]) dots[kini].classList.add('aktif');
	}

	function jalan() { timer = setInterval(function () { ke(kini + 1); }, 6000); }
	function henti() { clearInterval(timer); }

	dots.forEach(function (d) {
		d.addEventListener('click', function () {
			henti();
			ke(parseInt(this.dataset.ke, 10));
			jalan();
		});
	});

	hero.addEventListener('mouseenter', henti);
	hero.addEventListener('mouseleave', jalan);
	jalan();
})();