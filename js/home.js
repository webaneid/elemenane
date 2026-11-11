/**
 * Landing page: hero slider and horizontal rails. Vanilla JS, no dependencies.
 *
 * Both are plain scroll-snap strips in CSS (they work without JS); this adds buttons, dots, autoplay
 * and keyboard support. Autoplay stops on hover / focus / touch, when the tab is hidden, when the hero is
 * off screen and for users who prefer reduced motion.
 *
 * @package elemenane
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var INTERVAL = 6500;

	function raf(fn) {
		return window.requestAnimationFrame ? window.requestAnimationFrame(fn) : setTimeout(fn, 16);
	}

	function initHero(root) {
		var track = root.querySelector('.ane-hero__track');
		var slides = root.querySelectorAll('.ane-hero__slide');
		var dots = root.querySelectorAll('[data-hero-dot]');
		var prev = root.querySelector('[data-hero-prev]');
		var next = root.querySelector('[data-hero-next]');
		var count = slides.length;
		var index = 0;
		var timer = null;
		var paused = false;
		var visible = true;
		var ticking = false;

		if (!track || count < 2) {
			return;
		}
		root.style.setProperty('--ane-hero-interval', INTERVAL + 'ms');

		function setActive(i) {
			index = i;
			for (var s = 0; s < count; s++) {
				slides[s].classList.toggle('is-active', s === i);
				slides[s].setAttribute('aria-hidden', s === i ? 'false' : 'true');
			}
			for (var d = 0; d < dots.length; d++) {
				var on = d === i;
				dots[d].classList.toggle('is-active', on);
				if (on) {
					dots[d].setAttribute('aria-current', 'true');
				} else {
					dots[d].removeAttribute('aria-current');
				}
			}
		}

		function go(i) {
			i = (i + count) % count;
			track.scrollTo({ left: i * track.clientWidth, behavior: reduceMotion ? 'auto' : 'smooth' });
			setActive(i);
		}

		function stop() {
			root.classList.remove('is-playing');
			if (timer) {
				clearTimeout(timer);
				timer = null;
			}
		}

		function play() {
			stop();
			if (reduceMotion || paused || !visible || document.hidden) {
				return;
			}
			// Restart the dot progress animation.
			void root.offsetWidth;
			root.classList.add('is-playing');
			timer = setTimeout(function () {
				go(index + 1);
				play();
			}, INTERVAL);
		}

		track.addEventListener('scroll', function () {
			if (ticking) {
				return;
			}
			ticking = true;
			raf(function () {
				ticking = false;
				var i = Math.round(track.scrollLeft / track.clientWidth);
				if (i !== index && i >= 0 && i < count) {
					setActive(i);
					play();
				}
			});
		}, { passive: true });

		if (prev) {
			prev.addEventListener('click', function () { go(index - 1); play(); });
		}
		if (next) {
			next.addEventListener('click', function () { go(index + 1); play(); });
		}
		for (var d = 0; d < dots.length; d++) {
			(function (n) {
				dots[n].addEventListener('click', function () { go(n); play(); });
			})(d);
		}

		track.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowRight') { e.preventDefault(); go(index + 1); play(); }
			if (e.key === 'ArrowLeft') { e.preventDefault(); go(index - 1); play(); }
		});

		function pause() { paused = true; stop(); }
		function resume() { paused = false; play(); }
		root.addEventListener('mouseenter', pause);
		root.addEventListener('mouseleave', resume);
		root.addEventListener('focusin', pause);
		root.addEventListener('focusout', resume);
		root.addEventListener('touchstart', pause, { passive: true });
		root.addEventListener('touchend', function () { setTimeout(resume, 4000); }, { passive: true });
		document.addEventListener('visibilitychange', play);
		window.addEventListener('resize', function () { track.scrollLeft = index * track.clientWidth; });

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) {
				visible = entries[0].isIntersecting;
				play();
			}, { threshold: 0.35 }).observe(root);
		}

		setActive(0);
		play();
	}

	function initRail(section) {
		var rail = section.querySelector('.ane-rail');
		var prev = section.querySelector('[data-rail-prev]');
		var next = section.querySelector('[data-rail-next]');
		if (!rail) {
			return;
		}

		function step() {
			return Math.max(rail.clientWidth * 0.85, 200);
		}
		function update() {
			var max = rail.scrollWidth - rail.clientWidth - 2;
			var overflow = max > 0;
			section.classList.toggle('has-overflow', overflow);
			if (prev) { prev.disabled = rail.scrollLeft <= 2; }
			if (next) { next.disabled = rail.scrollLeft >= max; }
		}

		if (prev) {
			prev.addEventListener('click', function () { rail.scrollBy({ left: -step(), behavior: reduceMotion ? 'auto' : 'smooth' }); });
		}
		if (next) {
			next.addEventListener('click', function () { rail.scrollBy({ left: step(), behavior: reduceMotion ? 'auto' : 'smooth' }); });
		}
		rail.addEventListener('scroll', function () { raf(update); }, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	function init() {
		var heroes = document.querySelectorAll('[data-ane-hero]');
		for (var h = 0; h < heroes.length; h++) {
			initHero(heroes[h]);
		}
		var rails = document.querySelectorAll('[data-ane-rail]');
		for (var r = 0; r < rails.length; r++) {
			initRail(rails[r]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
