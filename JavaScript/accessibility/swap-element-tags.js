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
}

swap_element_tags('h3.sm-accordion-title', 'h2');