/**
 * Modern Admin Page Animations & Transitions
 *
 * Smooth fade-in, slide-up animations for professional feel.
 *
 * @package elemenane
 */

(function() {
	'use strict';

	/**
	 * Lightweight fade in on page load - NO stagger for performance.
	 */
	function initPageAnimations() {
		// Immediately show content - no blocking
		document.body.classList.add('ane-loaded');

		// Optional subtle fade for main elements only
		setTimeout(function() {
			const mainElements = document.querySelectorAll('.wrap > h1, .wrap > h2, .postbox');
			mainElements.forEach(function(element) {
				element.classList.add('ane-animate-in');
			});
		}, 50); // Very short delay
	}

	/**
	 * Smooth scroll for anchor links.
	 */
	function initSmoothScroll() {
		document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
			anchor.addEventListener('click', function(e) {
				const targetId = this.getAttribute('href');

				if (targetId === '#' || targetId === '#wpbody-content') {
					return;
				}

				const target = document.querySelector(targetId);

				if (target) {
					e.preventDefault();
					target.scrollIntoView({
						behavior: 'smooth',
						block: 'start'
					});
				}
			});
		});
	}

	/**
	 * Add ripple effect to buttons.
	 */
	function initRippleEffect() {
		const buttons = document.querySelectorAll('.button, .button-primary, .button-secondary');

		buttons.forEach(function(button) {
			button.addEventListener('click', function(e) {
				const ripple = document.createElement('span');
				const rect = this.getBoundingClientRect();
				const size = Math.max(rect.width, rect.height);
				const x = e.clientX - rect.left - size / 2;
				const y = e.clientY - rect.top - size / 2;

				ripple.style.width = ripple.style.height = size + 'px';
				ripple.style.left = x + 'px';
				ripple.style.top = y + 'px';
				ripple.classList.add('ane-ripple');

				this.appendChild(ripple);

				setTimeout(function() {
					ripple.remove();
				}, 600);
			});
		});
	}

	/**
	 * Animate notices (success, error, warning).
	 */
	function initNoticeAnimations() {
		const notices = document.querySelectorAll('.notice, .updated, .error');

		notices.forEach(function(notice) {
			notice.classList.add('ane-notice-animate');

			// Auto-dismiss success notices after 5 seconds
			if (notice.classList.contains('notice-success') || notice.classList.contains('updated')) {
				setTimeout(function() {
					notice.style.opacity = '0';
					notice.style.transform = 'translateX(100%)';

					setTimeout(function() {
						notice.remove();
					}, 300);
				}, 5000);
			}
		});
	}

	/**
	 * Add hover effect to table rows.
	 */
	function initTableRowAnimations() {
		const tables = document.querySelectorAll('.widefat, .wp-list-table');

		tables.forEach(function(table) {
			const rows = table.querySelectorAll('tbody tr');

			rows.forEach(function(row) {
				row.addEventListener('mouseenter', function() {
					this.style.transform = 'scale(1.01)';
				});

				row.addEventListener('mouseleave', function() {
					this.style.transform = 'scale(1)';
				});
			});
		});
	}

	/**
	 * Animate progress bars.
	 */
	function initProgressBarAnimations() {
		const progressBars = document.querySelectorAll('.ane-breakdown-progress__fill');

		progressBars.forEach(function(bar) {
			const targetWidth = bar.style.width;
			bar.style.width = '0%';

			setTimeout(function() {
				bar.style.width = targetWidth;
			}, 500);
		});
	}

	/**
	 * Add skeleton loading for async content.
	 */
	function addSkeletonLoader(container) {
		container.classList.add('ane-skeleton-loading');

		setTimeout(function() {
			container.classList.remove('ane-skeleton-loading');
			container.classList.add('ane-skeleton-loaded');
		}, 300);
	}

	/**
	 * Disabled page transitions - causes "leaving page" prompts.
	 */
	function initPageTransitions() {
		// Disabled - interferes with ACF AJAX and causes unwanted navigation delays
		return;
	}

	/**
	 * Disabled counting animation for performance.
	 */
	function initCountingAnimations() {
		// Disabled - causes slow initial page load
		return;
	}

	/**
	 * Disabled parallax for performance.
	 */
	function initParallaxEffect() {
		// Disabled - causes jank on scroll
		return;
	}

	/**
	 * Initialize all animations.
	 */
	function init() {
		initPageAnimations();
		initSmoothScroll();
		initRippleEffect();
		initNoticeAnimations();
		initTableRowAnimations();
		initProgressBarAnimations();
		initPageTransitions();
		initCountingAnimations();
		initParallaxEffect();
	}

	// Initialize when DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	// Expose addSkeletonLoader for async content
	window.aneAddSkeletonLoader = addSkeletonLoader;

})();
