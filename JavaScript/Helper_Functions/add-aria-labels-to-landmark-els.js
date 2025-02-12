/**
 * Add aria-label to landmark elements
 * @param {string} selector The css selector to target the elements
 * @param {string} ariaLabel The ariaLabel to add to the element
 */
const add_aria_labels_to_landmark_els = (selector, ariaLabel) => {
	const els = document.querySelectorAll(selector);
	
	els.forEach((el, index) => {
        // Add id to element if it doesn't exist
        if (!el.id) {
            el.setAttribute('id', `${ariaLabel}-${index + 1}`);
        }

        // Add aria-label to element if it doesn't exist
        if (!el.getAttribute('aria-label')) {
            el.setAttribute('aria-label', `${ariaLabel}-${index + 1}`);
        }
	})
}// add_aria_label_to_landmark_els

add_aria_labels_to_landmark_els('.bricks-nav-menu-wrapper', 'nav-menu');