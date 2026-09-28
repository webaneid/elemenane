/**
 * Product archive filters (no jQuery).
 *
 * - select[data-ane-autocomplete] becomes a searchable combobox.
 * - select[data-ane-autosubmit] submits the form on change.
 * - Empty fields are left out of the URL.
 *
 * Without JavaScript the form still works with plain selects and the Apply
 * button (see inc/product-filter.php).
 *
 * @package elemenane
 */
(function () {
	'use strict';

	var uid = 0;

	// Lowercase and strip accents so "kopi" matches "Kopí".
	function normalize(text) {
		return text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();
	}

	function submit(form) {
		if (form.requestSubmit) {
			form.requestSubmit();
		} else {
			form.submit();
		}
	}

	/**
	 * Turn a select into an accessible autocomplete combobox. The select stays
	 * in the form (hidden) and holds the submitted value.
	 *
	 * @param {HTMLSelectElement} select Select element.
	 */
	function autocomplete(select) {
		var id = 'ane-ac-' + (++uid);
		var options = Array.prototype.map.call(select.options, function (opt) {
			return { value: opt.value, label: opt.textContent.trim(), key: normalize(opt.textContent) };
		});

		var wrap = document.createElement('div');
		wrap.className = 'ane-autocomplete';

		var input = document.createElement('input');
		input.type = 'text';
		input.className = 'ane-autocomplete__input';
		input.id = select.id;
		input.setAttribute('role', 'combobox');
		input.setAttribute('aria-autocomplete', 'list');
		input.setAttribute('aria-expanded', 'false');
		input.setAttribute('aria-controls', id);
		input.setAttribute('autocomplete', 'off');
		input.placeholder = select.dataset.placeholder || '';

		var list = document.createElement('ul');
		list.className = 'ane-autocomplete__list';
		list.id = id;
		list.setAttribute('role', 'listbox');
		list.hidden = true;

		// The label now points at the input.
		select.removeAttribute('id');
		select.hidden = true;
		select.tabIndex = -1;
		select.parentNode.insertBefore(wrap, select);
		wrap.appendChild(input);
		wrap.appendChild(list);
		wrap.appendChild(select);

		var matches = [];
		var active = -1;

		function selectedLabel() {
			var opt = select.options[select.selectedIndex];
			return opt ? opt.textContent.trim() : '';
		}

		function render(query) {
			var q = normalize(query);
			matches = options.filter(function (o) {
				return !q || o.key.indexOf(q) !== -1;
			});
			list.innerHTML = '';

			if (!matches.length) {
				var empty = document.createElement('li');
				empty.className = 'ane-autocomplete__empty';
				empty.textContent = select.dataset.empty || '';
				list.appendChild(empty);
			}

			matches.forEach(function (o, i) {
				var li = document.createElement('li');
				li.id = id + '-' + i;
				li.setAttribute('role', 'option');
				li.className = 'ane-autocomplete__option';
				li.textContent = o.label;
				if (o.value === select.value) {
					li.setAttribute('aria-selected', 'true');
				}
				li.addEventListener('mousedown', function (e) {
					e.preventDefault(); // keep focus in the input
					choose(o);
				});
				list.appendChild(li);
			});

			setActive(matches.length ? 0 : -1);
		}

		function setActive(index) {
			var items = list.querySelectorAll('[role="option"]');
			if (items[active]) {
				items[active].classList.remove('is-active');
			}
			active = index;
			if (items[active]) {
				items[active].classList.add('is-active');
				items[active].scrollIntoView({ block: 'nearest' });
				input.setAttribute('aria-activedescendant', items[active].id);
			} else {
				input.removeAttribute('aria-activedescendant');
			}
		}

		function open() {
			list.hidden = false;
			input.setAttribute('aria-expanded', 'true');
		}

		function close() {
			list.hidden = true;
			input.setAttribute('aria-expanded', 'false');
			input.removeAttribute('aria-activedescendant');
		}

		function choose(o) {
			close();
			input.value = o.label;
			if (select.value !== o.value) {
				select.value = o.value;
				select.dispatchEvent(new Event('change', { bubbles: true }));
			}
		}

		input.value = selectedLabel();

		input.addEventListener('focus', function () {
			input.select();
			render('');
			open();
		});

		input.addEventListener('input', function () {
			render(input.value);
			open();
		});

		input.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
				e.preventDefault();
				if (list.hidden) {
					render('');
					open();
					return;
				}
				var step = e.key === 'ArrowDown' ? 1 : -1;
				setActive((active + step + matches.length) % matches.length);
			} else if (e.key === 'Enter') {
				if (!list.hidden) {
					e.preventDefault();
					if (matches[active]) {
						choose(matches[active]);
					}
				}
			} else if (e.key === 'Escape') {
				close();
				input.value = selectedLabel();
			}
		});

		input.addEventListener('blur', function () {
			close();
			input.value = selectedLabel();
		});

		select.addEventListener('change', function () {
			submit(select.form);
		});
	}

	function init() {
		document.querySelectorAll('.ane-product-filter').forEach(function (form) {
			form.querySelectorAll('select[data-ane-autocomplete]').forEach(autocomplete);

			form.querySelectorAll('select[data-ane-autosubmit]').forEach(function (select) {
				select.addEventListener('change', function () {
					submit(form);
				});
			});

			// Selects submit on change; the button is only needed for prices.
			var button = form.querySelector('.ane-filter-submit');
			if (button && !form.querySelector('[name="harga_min"]')) {
				button.hidden = true;
			}

			// Leave empty and default values out of the URL.
			form.addEventListener('submit', function () {
				Array.prototype.forEach.call(form.elements, function (field) {
					if (!field.name) {
						return;
					}
					if (field.value === '' || (field.name === 'urutkan' && field.value === 'terbaru')) {
						field.disabled = true;
					}
				});
			});

			// Re-enable fields if the page is restored from the back/forward cache.
			window.addEventListener('pageshow', function () {
				Array.prototype.forEach.call(form.elements, function (field) {
					field.disabled = false;
				});
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
