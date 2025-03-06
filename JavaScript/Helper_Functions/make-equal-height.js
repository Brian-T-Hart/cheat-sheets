/**
 * Set elements to have equal height based on the tallest element
 * @param {string} selector The CSS selector for the elements to adjust
 * @param {boolean} runOnResize True to run the function on window resize, false to run only once
 */
function makeEqualHeight(selector, runOnResize = true) {
	const adjustHeight = () => {
		const elements = document.querySelectorAll(selector);
		
		if (elements.length === 0) return;

		elements.forEach(el => el.style.height = ""); // Reset before measuring

		const maxHeight = Array.from(elements).reduce((max, el) => Math.max(max, el.offsetHeight), 0);

		elements.forEach(el => el.style.height = `${maxHeight}px`);
	};

	adjustHeight();
	
	if (runOnResize) window.addEventListener("resize", adjustHeight);
}

// makeEqualHeight('.card-p-txt-wrap', false);