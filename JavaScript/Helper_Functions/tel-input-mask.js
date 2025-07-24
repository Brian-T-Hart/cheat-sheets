/**
 * Sets an input mask for telephone number inputs.
 * The mask formats the input as (XXX) XXX-XXXX.
 */
function setInputMask() {
  const telInputs = document.querySelectorAll('input[type=tel]');
  
  telInputs.forEach((telInput) => {
	telInput.addEventListener('input', function (e) {
  		let x = e.target.value.replace(/\D/g, '').substring(0, 10);
		let formatted = '';
		if (x.length > 0) formatted += '(' + x.substring(0, 3);
		if (x.length >= 4) formatted += ') ' + x.substring(3, 6);
		if (x.length >= 7) formatted += '-' + x.substring(6, 10);
		e.target.value = formatted;
	})
  })
}

setInputMask();