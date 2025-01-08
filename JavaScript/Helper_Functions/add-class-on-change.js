/**
 * Add class to elements on change
 * @param {string} selector 
 * @param {string} newClass 
 */
const addClassOnChange = (selector, newClass) => {
	const selectEls = document.querySelectorAll(selector);
	
	selectEls.forEach(selectEl => {
		selectEl.addEventListener('change', function() {
			if (!this.classList.contains(newClass)) {
				this.classList.add(newClass);
			}
		})
	})
}

addClassOnChange('.ems-form-input select', 'ems-selected');