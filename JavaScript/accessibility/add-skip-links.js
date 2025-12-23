/**
 * Adds skip links to the page
 */
function add_skip_links(mainContentSelector, footerSelector) {
    if (document.querySelector('.skip-link')) {
        console.log('Skip links already exist on the page');
        return;
    }
    
    // if mainContentSelector or footerSelector is not set, return
    if (!mainContentSelector || !footerSelector) {
        console.error('mainContentSelector and footerSelector must be set');
        return;
    }

    function createSkipLink(targetId, label) {
        const skipLink = document.createElement('a');
        skipLink.setAttribute('class', 'skip-link');
        skipLink.textContent = label;
        skipLink.setAttribute('href', `#${targetId}`);
        return skipLink;
    }

    const footerEl = document.querySelector(footerSelector);
    if (footerEl) {
        // if footerEl id is not set, set it to 'footer'
        if (!footerEl.id) {
            footerEl.setAttribute('id', 'footer');
        }

        // Create a skip link to the footer
        const footerSkipLink = createSkipLink(footerEl.id, 'Skip to footer');
        document.body.insertBefore(footerSkipLink, document.body.firstChild);
    }

    const mainContentEl = document.querySelector(mainContentSelector);
    if (mainContentEl) {
        // if mainContentEl id is not set, set it to 'main-content'
        if (!mainContentEl.id) {
            mainContentEl.setAttribute('id', 'main-content');
        }

        // Create a skip link to the main content
        const contentSkipLink = createSkipLink(mainContentEl.id, 'Skip to content');
        document.body.insertBefore(contentSkipLink, document.body.firstChild);
    }

    function addSkipLinkStyles() {
        const style = document.createElement('style');
        style.textContent = `
        .skip-link {
            position: fixed;
            top: -100px;
            left: 50px;
            z-index: 9999;
        }

        .skip-link:focus {
            top: 13px;
        }
    `;
        document.head.appendChild(style);
    }

    // Call this function to add styles
    addSkipLinkStyles();
}// add_skip_links