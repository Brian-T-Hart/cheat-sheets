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
}