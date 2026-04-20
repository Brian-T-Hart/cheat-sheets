/**
 * ADA Dev Scripts
 * A collection of scripts to help test the accessibility of the website
 */
class ADADevScripts {
    constructor() {
        this.init();
    }

    /**
     * Initializes the ADA scripts to run in all environments
     */
    init() {
        console.warn('Running ADADevScripts. Remove from production!');
        this.find_duplicate_ids();
        this.verify_h1_element_count();
    }

    /**
     * Find duplicate ids on the page and log them to the console
    */
    find_duplicate_ids() {
        const ids = {};
        const allTags = document.all || document.getElementsByTagName("*");

        for (let i = 0; i < allTags.length; i++) {
            let id = allTags[i].id;

            if (id) {
                if (ids[id]) {
                    console.warn("Duplicate id: #" + id);
                } else {
                    ids[id] = 1;
                }
            }// if
        }// for
    }

    /**
     * Verify there is only one h1 element on the page
     */
    verify_h1_element_count() {
        const h1Elements = document.querySelectorAll('h1');
        if (h1Elements.length > 1) {
            console.warn('There is more than one h1 element on the page.');
        } else if (h1Elements.length === 0) {
            console.warn('There is no h1 element on the page.');
        } else {
            console.log('Yay! There is one h1 element on the page.');
        }
    }

}// ADADevScripts

document.addEventListener('DOMContentLoaded', () => {
    new ADADevScripts();
});