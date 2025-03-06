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