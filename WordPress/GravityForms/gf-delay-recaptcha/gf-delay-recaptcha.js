(function () {
    if (typeof loadRecaptcha !== 'function') {

        function loadRecaptcha() {
            if (window.gfRecaptchaLoaded) return;
            window.gfRecaptchaLoaded = true;

            var s = document.createElement('script');
            s.src = getRecaptchaScriptSrc();
            s.async = true;
            s.defer = true;
            document.body.appendChild(s);
        }// loadRecaptcha

        function getRecaptchaScriptSrc() {
            var recaptchaSrc = 'https://www.recaptcha.net/recaptcha/api.js?render=explicit';

            if (typeof GFDelayReCaptchaData !== 'undefined' && GFDelayReCaptchaData.recaptchaSrc) {
                recaptchaSrc = GFDelayReCaptchaData.recaptchaSrc;
            }

            return recaptchaSrc;
        }// getRecaptchaScriptSrc

        // Load when form is interacted with
        document.addEventListener('focusin', loadRecaptcha, { once: true });
        document.addEventListener('touchstart', loadRecaptcha, { once: true });
    }
})();