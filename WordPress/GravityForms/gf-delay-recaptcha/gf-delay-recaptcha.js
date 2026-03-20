(function () {
     
    // Load the ReCAPTCHA script
    function loadRecaptcha() {
        if (window.gfRecaptchaLoaded) return;
        window.gfRecaptchaLoaded = true;

        let recaptchaScript = document.createElement('script');
        recaptchaScript.src = getRecaptchaScriptSrc();
        recaptchaScript.async = true;
        recaptchaScript.defer = true;

        // Handle script loading errors
        recaptchaScript.onerror = function() {
            console.error('Failed to load reCAPTCHA script from:', recaptchaScript.src);
        };

        document.body.appendChild(recaptchaScript);
    }// loadRecaptcha

    // Get the ReCAPTCHA script source from the global variable or use the default
    function getRecaptchaScriptSrc() {
        let recaptchaSrc = 'https://www.recaptcha.net/recaptcha/api.js?render=explicit';

        if (
            typeof GFDelayReCaptchaData !== 'undefined' &&
            GFDelayReCaptchaData.recaptchaSrc &&
            typeof GFDelayReCaptchaData.recaptchaSrc === 'string' &&
            GFDelayReCaptchaData.recaptchaSrc.trim() !== ''
        ) {
            recaptchaSrc = GFDelayReCaptchaData.recaptchaSrc.trim();
        }

        return recaptchaSrc;
    }// getRecaptchaScriptSrc

    // Load the ReCAPTCHA script when the form is interacted with
    document.addEventListener('focusin', loadRecaptcha, { once: true });
    document.addEventListener('pointerdown', loadRecaptcha, { once: true });

})();