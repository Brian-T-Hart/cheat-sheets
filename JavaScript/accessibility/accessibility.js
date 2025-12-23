/**
 * Adds aria-labels to landmark elements
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

/**
 * Adds or appends aria-label with message that it opens in a new tab
 */
function add_aria_labels_to_new_tab_links() {
	const newTabLinks = document.querySelectorAll('a[target="_blank"]');
	const newTabMessage = '(Opens in a new tab)';

	newTabLinks.forEach(link => {
		let linkText = link.innerText;
		let ariaLabel = link.getAttribute('aria-label');
		let ariaLabelValue = linkText.length > 0 ? `${linkText} ${newTabMessage}` : newTabMessage;
	
		// set aria-label attribute if it does not exist
		if (!ariaLabel) {
			link.setAttribute('aria-label', ariaLabelValue);
		} else {
			link.setAttribute('aria-label', `${ariaLabel} ${newTabMessage}`);
		}
	});
}// add_aria_labels_to_new_tab_links

/**
 * Add attribute to elements
 * @param {string} selector The css selector to target the elements
 * @param {string} attr The attribute to add to the element
 * @param {string} value The value of the attribute to add to the element
 */
function add_attribute_to_els(selector, attr, value) {
    const els = document.querySelectorAll(selector);
    
    els.forEach((el) => {
        // Add aria-label to element if it doesn't exist
        if (!el.getAttribute(attr)) {
            el.setAttribute(attr, value);
        }
    })
}// add_aria_label_to_landmark_els

/**
 * Simulates click on element when Enter key is pressed
 * @param {string} selector
 */
function simulate_click_on_enter(selector) {
    // Get all elements matching the selector
    const elements = document.querySelectorAll(selector);

    // Add event listener to each matched element
    elements.forEach(element => {
        element.addEventListener('keypress', function(event) {
            // Check if the pressed key is Enter
            if (event.key === 'Enter') {
                // Prevent the default Enter key behavior (submitting forms, etc.)
                event.preventDefault();
                // Trigger a click on the element
                element.click();
            }
        });
    });
}// simulate_click_on_enter

/**
 * Swap all elements matching the selector with new elements of the specified tag.
 */
function swap_element_tags(selector, newTag) {
	const validTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
	
	if (!selector || !newTag || !validTags.includes(newTag)) {
		console.log('Invalid selector or newTag argument provided');
		return;
	}
	
	// Get all elements by the selector
    const elements = document.querySelectorAll(selector);

    elements.forEach(el => {
        // Create a new element
        const newEl = document.createElement(newTag);

        // Copy inner HTML to new element
        newEl.innerHTML = el.innerHTML;

        // Copy all attributes to new element
        for (let attr of el.attributes) {
            newEl.setAttribute(attr.name, attr.value);
        }

        // Replace old element with new element in the DOM
        el.replaceWith(newEl);
    });
}// swap_element_tags
