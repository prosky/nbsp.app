


class Option {
	/** @var {string} label */
	label;
	/** @var {string} name */
	name;
	/** @var {array} values */
	values;
	/** @var {mixed} default */
	value;
	/** @var {string} type (checkbox|radio|select|text|int)*/
	type;
}

class VisualConfig {

	/**
	 * @param {HTMLElement} el
	 * @param {Option[]} definitions
	 * @param {function} callback
	 */
	constructor(el, definitions, callback) {
		this.el = el;
		this.callback = callback;
		for (let {label, name, values, value, type} of definitions) {
			let labelElement = document.createElement('label');
			let input = document.createElement('input');
			labelElement.innerHTML = label;
			input.id = 'input-' + name;
			labelElement.htmlFor = input.id;
			input.name = name;
			input.addEventListener('change', () => {
				callback(name, input.value);
			});
			el.appendChild(labelElement);
			el.appendChild(input);
		}
	}
}
