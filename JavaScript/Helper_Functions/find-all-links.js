class FindAllLinks {
    static STORAGE_KEY = 'findAllLinks.localUrls';

    /**
     * Find all same-origin links on the current page.
     * @returns {string[]} Local URLs found on this page.
     */
    static find() {
        const links = new Set();
        const anchors = document.querySelectorAll('a[href]');

        anchors.forEach(anchor => {
            const href = anchor.getAttribute('href');

            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }

            try {
                const absoluteUrl = new URL(href, window.location.href);

                if (absoluteUrl.origin !== window.location.origin) {
                    return;
                }

                // Normalize by dropping hashes so in-page anchors are treated as the same URL.
                absoluteUrl.hash = '';
                links.add(absoluteUrl.toString());
            } catch (_error) {
                // Ignore invalid href values.
            }
        });

        return [...links];
    }

    static getStoredLinks() {
        try {
            const raw = localStorage.getItem(FindAllLinks.STORAGE_KEY);
            const parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed : [];
        } catch (_error) {
            return [];
        }
    }

    static saveStoredLinks(links) {
        localStorage.setItem(FindAllLinks.STORAGE_KEY, JSON.stringify(links));
    }

    /**
     * Merge local links from this page into the persistent localStorage array.
     * @returns {string[]} Updated deduplicated local URL array.
     */
    static addCurrentPageLinksToStore() {
        const storedLinks = FindAllLinks.getStoredLinks();
        const pageLinks = FindAllLinks.find();
        const mergedLinks = [...new Set([...storedLinks, ...pageLinks])];

        FindAllLinks.saveStoredLinks(mergedLinks);
        return mergedLinks;
    }

    static getTSV(includeHeader = true) {
        const links = FindAllLinks.addCurrentPageLinksToStore();
        const rows = includeHeader ? ['url', ...links] : links;
        return rows.join('\n');
    }

    static logForSheets(includeHeader = true) {
        // One console entry avoids per-line VM references when copying.
        console.log(FindAllLinks.getTSV(includeHeader));
    }

    static copyForSheets(includeHeader = true) {
        const tsv = FindAllLinks.getTSV(includeHeader);

        if (typeof copy === 'function') {
            copy(tsv);
            console.log('Copied links for Google Sheets.');
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard
                .writeText(tsv)
                .then(() => console.log('Copied links for Google Sheets.'))
                .catch(() => console.log(tsv));
            return;
        }

        console.log(tsv);
    }

    static clearStoredLinks() {
        localStorage.removeItem(FindAllLinks.STORAGE_KEY);
    }
}
FindAllLinks.logForSheets();