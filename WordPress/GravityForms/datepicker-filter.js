// Set first selectable date for gform datepicker
if (typeof gform !== 'undefined') {
    gform.addFilter('gform_datepicker_options_pre_init', function (optionsObj, formId, fieldId) {
        if (formId == 1 && fieldId == 9 || formId == 2 && fieldId == 9 || formId == 3 && fieldId == 9 || formId == 4 && fieldId == 9) {
            optionsObj.minDate = 0;
            optionsObj.firstDay = 1;
            optionsObj.beforeShowDay = function (date) {
                var day = date.getDay();
                var today = new Date();
                today.setHours(0, 0, 0, 0); // Set the time to midnight to ignore the time component
                return [(date > today), ''];
            };
        }
        return optionsObj;
    });
}