(function () {
	'use strict';

	const header = document.querySelector('.site-header');
	if (header) {
		const updateHeader = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		updateHeader();
		window.addEventListener('scroll', updateHeader, { passive: true });
	}

	const navLinks = Array.from(document.querySelectorAll('.mobile-nav a[href*="#"]'));
	const updateNavigation = function () {
		const currentHash = window.location.hash || '#explore';
		navLinks.forEach(function (link) {
			const target = new URL(link.href, window.location.href);
			const active = target.origin === window.location.origin
				&& target.pathname === window.location.pathname
				&& target.hash === currentHash;
			link.classList.toggle('is-active', active);
			if (active) {
				link.setAttribute('aria-current', 'page');
			} else {
				link.removeAttribute('aria-current');
			}
		});
	};
	window.addEventListener('hashchange', updateNavigation);
	updateNavigation();
})();
