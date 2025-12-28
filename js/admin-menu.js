/**
 * Lightweight Admin Menu - Tooltips + Click to Open Submenu
 *
 * @package ane
 */

(function() {
	'use strict';

	/**
	 * Add tooltips to menu items.
	 */
	function initTooltips() {
		const menuItems = document.querySelectorAll('#adminmenu li.menu-top');

		menuItems.forEach(function(item) {
			const link = item.querySelector('a');
			const menuName = item.querySelector('.wp-menu-name');

			if (link && menuName) {
				const tooltipText = menuName.textContent.trim();

				// Create tooltip element
				const tooltip = document.createElement('span');
				tooltip.className = 'ane-tooltip';
				tooltip.textContent = tooltipText;

				// Create tooltip arrow
				const arrow = document.createElement('span');
				arrow.className = 'ane-tooltip-arrow';

				// Append to body instead of link to avoid affecting layout
				document.body.appendChild(tooltip);
				document.body.appendChild(arrow);

				// Show/hide on hover with dynamic positioning
				link.addEventListener('mouseenter', function() {
					if (!item.classList.contains('opensub') && !item.classList.contains('current')) {
						// Get link position
						const rect = link.getBoundingClientRect();

						// Position tooltip
						tooltip.style.position = 'fixed';
						tooltip.style.left = '70px';
						tooltip.style.top = rect.top + (rect.height / 2) + 'px';
						tooltip.style.transform = 'translateY(-50%)';

						// Position arrow
						arrow.style.position = 'fixed';
						arrow.style.left = '62px';
						arrow.style.top = rect.top + (rect.height / 2) + 'px';
						arrow.style.transform = 'translateY(-50%)';

						// Show
						tooltip.classList.add('ane-tooltip-show');
						arrow.classList.add('ane-tooltip-show');
					}
				});

				link.addEventListener('mouseleave', function() {
					tooltip.classList.remove('ane-tooltip-show');
					arrow.classList.remove('ane-tooltip-show');
				});
			}
		});
	}

	/**
	 * Click to toggle submenu - Submenu stays open (static).
	 */
	function initSubmenuToggle() {
		const menuItems = document.querySelectorAll('#adminmenu li.menu-top.wp-has-submenu');
		let activeItem = null; // Track which menu is open

		menuItems.forEach(function(item) {
			const link = item.querySelector('a');

			if (link) {
				// Click handler
				link.addEventListener('click', function(e) {
					e.preventDefault(); // ALWAYS prevent - toggle submenu

					const isOpen = item.classList.contains('opensub');

					if (isOpen) {
						// Click same menu → close it
						item.classList.remove('opensub');
						item.setAttribute('data-ane-locked', 'false');
						activeItem = null;

						// Always remove body class when closing submenu
						document.body.classList.remove('has-open-submenu');
					} else {
						// Click different menu → close others, open this
						menuItems.forEach(function(otherItem) {
							otherItem.classList.remove('opensub');
							otherItem.setAttribute('data-ane-locked', 'false');
						});

						item.classList.add('opensub');
						item.setAttribute('data-ane-locked', 'true');
						activeItem = item;
						document.body.classList.add('has-open-submenu');
					}
				});

				// MutationObserver to prevent class removal by WordPress
				const observer = new MutationObserver(function(mutations) {
					mutations.forEach(function(mutation) {
						if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
							const isLocked = item.getAttribute('data-ane-locked') === 'true';
							const hasOpensub = item.classList.contains('opensub');

							// If locked but opensub was removed → re-add it
							if (isLocked && !hasOpensub) {
								item.classList.add('opensub');
							}
						}
					});
				});

				// Start observing
				observer.observe(item, {
					attributes: true,
					attributeFilter: ['class']
				});
			}
		});
	}

	/**
	 * Initialize everything.
	 */
	function init() {
		initTooltips();
		initSubmenuToggle();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
