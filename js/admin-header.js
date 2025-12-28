/**
 * Mobile Admin Header - Hamburger Menu & Slide-in Panel
 *
 * @package ane
 */

(function() {
	'use strict';

	// Only run on mobile
	if (window.innerWidth > 782) {
		return;
	}

	console.log('[Ane Mobile] Initializing mobile header...');

	/**
	 * Initialize mobile hamburger menu.
	 */
	function initMobileMenu() {
		const hamburger = document.querySelector('#wp-admin-bar-site-name > .ab-item');
		const userAvatar = document.querySelector('#wp-admin-bar-my-account > .ab-item');
		// Disable original profile link
		if (userAvatar) {
			userAvatar.removeAttribute('href'); // benar2 hilangin link
			userAvatar.setAttribute('aria-disabled', 'true');
			userAvatar.style.cursor = 'pointer';
		}

		const body = document.body;

		console.log('[Ane Mobile] Hamburger:', hamburger);
		console.log('[Ane Mobile] User avatar:', userAvatar);

		if (!hamburger) {
			console.log('[Ane Mobile] Hamburger not found, aborting');
			return;
		}

		// Create slide-in menu panel (from left)
		const menuPanel = createMenuPanel();
		body.appendChild(menuPanel);

		// Create user panel (from right)
		const userPanel = createUserPanel();
		body.appendChild(userPanel);

		// Create overlay
		const overlay = document.createElement('div');
		overlay.className = 'ane-mobile-overlay';
		body.appendChild(overlay);

		// Toggle hamburger menu (slide from left)
		hamburger.addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();

			// Close user panel if open
			closeUserPanel();

			const isOpen = body.classList.contains('ane-menu-open');

			if (isOpen) {
				closeMenu();
			} else {
				openMenu();
			}
		});

		// Toggle user dropdown (slide from right)
		if (userAvatar) {
			console.log('[Ane Mobile] Adding click listener to user avatar');

			userAvatar.addEventListener(
				'click',
				function (e) {
					console.log('[Ane Mobile] User avatar clicked!');
					e.preventDefault();
					e.stopPropagation();
					e.stopImmediatePropagation(); // pastikan event lain di element sama nggak jalan

					// Close menu panel if open
					closeMenu();

					const isOpen = body.classList.contains('ane-user-open');

					if (isOpen) {
						closeUserPanel();
					} else {
						openUserPanel();
					}
				},
				{
					capture: true, // handler ini jalan duluan, sebelum handler lain
				}
			);
		} else {
			console.log('[Ane Mobile] User avatar not found, cannot add listener');
		}


		// Close on overlay click
		overlay.addEventListener('click', function() {
			closeMenu();
			closeUserPanel();
		});

		// Open/close menu panel
		function openMenu() {
			body.classList.add('ane-menu-open');
			menuPanel.classList.add('is-open');
			overlay.classList.add('is-visible');
		}

		function closeMenu() {
			body.classList.remove('ane-menu-open');
			menuPanel.classList.remove('is-open');
			if (!body.classList.contains('ane-user-open')) {
				overlay.classList.remove('is-visible');
			}
		}

		// Open/close user panel
		function openUserPanel() {
			body.classList.add('ane-user-open');
			userPanel.classList.add('is-open');
			overlay.classList.add('is-visible');
		}

		function closeUserPanel() {
			body.classList.remove('ane-user-open');
			userPanel.classList.remove('is-open');
			if (!body.classList.contains('ane-menu-open')) {
				overlay.classList.remove('is-visible');
			}
		}

		// Initialize accordion
		initAccordion(menuPanel);
	}

	/**
	 * Create slide-in menu panel.
	 */
	function createMenuPanel() {
		const panel = document.createElement('div');
		panel.className = 'ane-mobile-menu';

		// Clone admin menu
		const adminMenu = document.querySelector('#adminmenu');
		if (adminMenu) {
			const menuClone = adminMenu.cloneNode(true);
			menuClone.id = 'ane-mobile-adminmenu';
			panel.appendChild(menuClone);
		}

		return panel;
	}

	/**
	 * Create user panel (slide from right).
	 */
	function createUserPanel() {
		const panel = document.createElement('div');
		panel.className = 'ane-mobile-user';

		// Clone user submenu
		const userSubmenu = document.querySelector('#wp-admin-bar-my-account .ab-submenu');
		if (userSubmenu) {
			const submenuClone = userSubmenu.cloneNode(true);

			// Replace <a> with <div> in user info to completely prevent navigation
			const userInfoItem = submenuClone.querySelector('#wp-admin-bar-user-info');
			if (userInfoItem) {
				const userInfoLink = userInfoItem.querySelector('a');
				if (userInfoLink) {
					// Create new div with same content
					const userInfoDiv = document.createElement('div');
					userInfoDiv.className = userInfoLink.className;
					userInfoDiv.innerHTML = userInfoLink.innerHTML;

					// Replace <a> with <div>
					userInfoLink.parentNode.replaceChild(userInfoDiv, userInfoLink);
				}
			}

			panel.appendChild(submenuClone);
		}

		return panel;
	}

	/**
	 * Initialize accordion for submenus.
	 */
	function initAccordion(panel) {
		const menuItems = panel.querySelectorAll('li.wp-has-submenu');

		menuItems.forEach(function(item) {
			const link = item.querySelector('a.menu-top');
			const submenu = item.querySelector('.wp-submenu');

			if (!link || !submenu) {
				return;
			}

			// Add plus icon
			const icon = document.createElement('span');
			icon.className = 'ane-accordion-icon';
			icon.innerHTML = '+';
			link.appendChild(icon);

			// Click to toggle
			link.addEventListener('click', function(e) {
				e.preventDefault();

				const isOpen = item.classList.contains('ane-submenu-open');

				// Close other submenus
				menuItems.forEach(function(otherItem) {
					if (otherItem !== item) {
						otherItem.classList.remove('ane-submenu-open');
					}
				});

				// Toggle current submenu
				if (isOpen) {
					item.classList.remove('ane-submenu-open');
				} else {
					item.classList.add('ane-submenu-open');
				}
			});
		});
	}

	/**
	 * Initialize on load.
	 */
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initMobileMenu);
	} else {
		initMobileMenu();
	}

	// Re-initialize on window resize
	window.addEventListener('resize', function() {
		if (window.innerWidth <= 782 && !document.querySelector('.ane-mobile-menu')) {
			initMobileMenu();
		}
	});
})();
