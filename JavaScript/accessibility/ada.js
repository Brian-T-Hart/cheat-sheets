/**
 * ADA Scripts
 * A collection of scripts to improve the accessibility of the website
 */
class ADAScripts {
    constructor() {
        this.init();
    }

    /**
     * Initializes the ADA scripts to run in all environments
     */
    init() {
        // this.add_aria_labels_to_new_tab_links();
        // this.add_attribute_to_els('svg', 'role', 'presentation');
        // this.simulate_click_on_enter('.team-card');
        // this.add_aria_labels_to_landmark_els('.bricks-nav-menu-wrapper', 'nav-menu');
        // this.add_attribute_to_els('.bricks-video-overlay', 'aria-hidden', 'true');
        // this.add_attribute_to_els('.bricks-video-overlay-icon', 'role', 'button');
    }

    /**
     * Adds or appends aria-label with message that it opens in a new tab
     */
    add_aria_labels_to_new_tab_links() {
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
     * Adds an attribute to elements
     * @param {string} selector The css selector to target the elements
     * @param {string} attr The attribute to add to the element
     * @param {string} value The value of the attribute to add to the element
     */
    add_attribute_to_els(selector, attr, value) {
        const els = document.querySelectorAll(selector);

        els.forEach((el) => {
            // Add attribute to element if it doesn't exist
            if (!el.getAttribute(attr)) {
                el.setAttribute(attr, value);
            }
        })
    }// add_attribute_to_els

    /**
     * Adds aria-label to landmark elements
     * @param {string} selector The css selector to target the elements
     * @param {string} ariaLabel The ariaLabel to add to the element
     */
    add_aria_labels_to_landmark_els(selector, ariaLabel) {
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
    }// add_aria_labels_to_landmark_els

    /**
     * Simulates click on element when Enter key is pressed
     * @param {string} selector
     */
    simulate_click_on_enter(selector) {
        // Get all elements matching the selector
        const elements = document.querySelectorAll(selector);

        // Add event listener to each matched element
        elements.forEach(element => {
            element.addEventListener('keypress', function (event) {
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

}// ADAScripts

document.addEventListener('DOMContentLoaded', () => {
    new ADAScripts();
});