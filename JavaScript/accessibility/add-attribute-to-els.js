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

add_attribute_to_els('svg', 'role', 'presentation');