document.addEventListener('DOMContentLoaded', function () {
    const delay = 4000; // in milliseconds
    console.log(`Delaying ${delay / 1000} seconds`);

    setTimeout(function () {
        console.log('Adding hidden input to forms');

        const forms = document.querySelectorAll('form[id^="gform_"]');
        forms.forEach(form => {
            if (!form.querySelector('input[name="gf_st_custom_1"]')) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'gf_st_custom_1';
                input.value = 'ny3vNfD6tU0J';
                form.appendChild(input);
            }
        });
    }, delay);
});