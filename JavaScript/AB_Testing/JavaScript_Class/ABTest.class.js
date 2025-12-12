class ABTest {
    constructor(options) {
        this.cookieName = options.cookieName || 'ab_test_variant';
        this.cookieDays = options.cookieDays || 30;
        this.selector = options.selector; // required
        this.variants = options.variants; // required { A: fn, B: fn }

        if (!this.selector || !this.variants) {
            console.error(new Error('ABTest requires a selector and variants.'));
            return;
        }

        this.init();
    }

    // ------------------------------------------
    // Public Methods
    // ------------------------------------------
    init() {
        this.variant = this.getVariantFromCookie();

        if (!this.variant) {
            this.variant = this.assignVariant();
            this.setCookie(this.cookieName, this.variant, this.cookieDays);
        }

        this.applyVariant();
        this.setupClickTracking();
    }

    // ------------------------------------------
    // Cookie Functions
    // ------------------------------------------
    setCookie(name, value, days) {
        const expiration = new Date();
        expiration.setTime(expiration.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value}; expires=${expiration.toUTCString()}; path=/`;
    }

    getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return match ? match[1] : null;
    }

    getVariantFromCookie() {
        return this.getCookie(this.cookieName);
    }

    assignVariant() {
        return Math.random() < 0.5 ? 'A' : 'B';
    }

    // ------------------------------------------
    // DOM Manipulation
    // ------------------------------------------
    applyVariant() {
        const element = document.querySelector(this.selector);
        if (!element) return console.warn(`ABTest: selector "${this.selector}" not found.`);

        const fn = this.variants[this.variant];
        if (typeof fn === 'function') {
            fn(element);
        } else {
            console.warn(`ABTest: No handler for variant ${this.variant}`);
        }
    }

    // ------------------------------------------
    // Analytics
    // ------------------------------------------
    setupClickTracking() {
        const element = document.querySelector(this.selector);
        if (!element) return;

        element.addEventListener('click', () => {
            // Fire a custom event (listen for this in GA or any analytics tool)
            window.dispatchEvent(new CustomEvent('abTestClick', {
                detail: {
                    variant: this.variant,
                    element: this.selector,
                    timestamp: Date.now()
                }
            }));
        });
    }
}

// example ABTest
const home_hero_test = new ABTest({
    cookieName: 'ypm_ab_test',
    selector: 'a.apply-now',

    variants: {
        A: (el) => {
            el.textContent = 'Buy a Home (A)';
            el.style.backgroundColor = '#0057ff';
        },
        B: (el) => {
            el.textContent = 'Apply Now (B)';
            el.style.backgroundColor = '#00b33c';
        }
    }
});
