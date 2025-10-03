// Add focus to an element when the trigger element is clicked
function focus_element_by_trigger(triggerEl, focusEl, validateEl = false, validateClass = false) {
    if (!triggerEl || !focusEl) {
        console.log('Error in focus_element_by_trigger function.');
        return;
    }

    triggerEl.addEventListener('click', function () {
        // Wait for other animations to finish before running checks
        setTimeout(function () {
            if (!validateEl || !validateClass) {
                focusEl.focus();
                console.log('focused');
            }

            else if (validateEl.classList.contains(validateClass)) {
                focusEl.focus();
                console.log('focused 2');
            }

            else {
                console.log('no focus :(')
            }
        }, 500)
    });

}// focus_element_by_trigger

try {
    // Add focus to the desktop search input when the desktop search icon is clicked
    const searchIcon = document.getElementById('search-icon');
    const searchInput = document.querySelector('#search-form input[type=search]');
    const searchForm = document.getElementById('search-form');
    focus_element_by_trigger(searchIcon, searchInput, searchForm, 'open');

    // Add focus to the mobile search input when the mobile search icon is clicked
    const mobileSearchIcon = document.getElementById('mobile-search-icon');
    const mobileSearchInput = document.querySelector('#mobile-search input[type=search]');
    const offCanvas = document.getElementById('brxe-fority');
    focus_element_by_trigger(mobileSearchIcon, mobileSearchInput, offCanvas, 'brx-open');

    // Add focus back to mobile search icon when clicking the off canvas search icon
    const offCanvasSearchIcon = document.getElementById('off-canvas-search-icon');
    focus_element_by_trigger(offCanvasSearchIcon, mobileSearchIcon.parentElement);
} catch (err) {
    console.log(err);
}
