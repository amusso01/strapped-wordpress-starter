/**
 * Vanilla JS alternative to arrive.js – watches for DOM elements using MutationObserver.
 * Zero dependencies.
 *
 * @param {string} selector - CSS selector to watch for
 * @param {Object} options - { existing: boolean } - if true, also run on elements already in DOM
 * @param {Function} callback - (element) => void
 * @returns {MutationObserver} - observer instance (call .disconnect() to stop)
 */
export function onElementArrive(selector, options, callback) {
	if (typeof options === "function") {
		callback = options;
		options = {};
	}
	const { existing = false } = options;

	const observer = new MutationObserver((mutations) => {
		for (const mutation of mutations) {
			for (const node of mutation.addedNodes) {
				if (node.nodeType !== 1) continue;
				if (node.matches?.(selector)) callback(node);
				for (const el of node.querySelectorAll?.(selector) ?? []) {
					callback(el);
				}
			}
		}
	});

	if (existing) {
		document.querySelectorAll(selector).forEach(callback);
	}

	observer.observe(document.body, { childList: true, subtree: true });
	return observer;
}
