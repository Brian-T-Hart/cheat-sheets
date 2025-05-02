// Gravity Forms Spam Timer Script
// This script adds a hidden input field to the Gravity Forms form to track the submission start time.
document.addEventListener('DOMContentLoaded', function () {
    const formId = `gform_${gfSpamTimerData.formId}`;
    const form = document.getElementById(formId);

    // Check if the form exists and if the hidden input field is already present
    if (!form || form.querySelector('input[name="gf_submission_start"]')) return;

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'gf_submission_start';
    input.className = 'gform_hidden';
    input.value = gfSpamTimerData.timestamp;
    form.appendChild(input);
});