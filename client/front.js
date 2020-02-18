import beautify from 'js-beautify'
/**  @var {CodeMirror} CodeMirror */
import CodeMirror from 'codemirror'
import 'codemirror/mode/xml/xml'
import 'codemirror/addon/search/search'
import 'codemirror/addon/search/searchcursor'
import 'codemirror/addon/search/jump-to-line'
import 'codemirror/addon/dialog/dialog'
import 'codemirror/addon/dialog/dialog.css'
import 'cm-show-invisibles'

class NbspTool {
	/**  @var {CodeMirror} inputEditor */
	inputEditor;
	/**  @var {CodeMirror} inputEditor */
	outputEditor;
	/**  @type {{mode: string, theme: string, lineWrapping: boolean}} */
	editorOptions = {
		showInvisibles: true,
		mode: 'xml',
		//lineWrapping: true,
		theme: 'one-dark'
	};
	/**  @var {HTMLTextAreaElement} input */
	input;
	/**  @var {HTMLTextAreaElement} input */
	output;
	/**  @var {HTMLDivElement} input */
	preview;
	/**  @var {Nbsp}*/
	nbsp;

	constructor(input, output, preview) {
		this.input = input;
		this.output = output;
		this.preview = preview;
		this.nbsp = new Nbsp();
		this.init();
	}

	setLanguage(lang) {
		if (lang) {
			this.lang.value = lang;
			this.preview.lang = lang;
			localStorage.setItem('lang', lang);
			this.update();
		}
	}

	init() {
		this.input.value = localStorage.getItem('value');

		this.inputEditor = CodeMirror.fromTextArea(input, this.getEditorOptions());
		this.inputEditor.on('keyup', this.delay(this.#onInputEditorUpdated));
		this.inputEditor.on('change', this.#onInputEditorUpdated);
		this.outputEditor = CodeMirror.fromTextArea(this.output, this.getEditorOptions({readOnly: true}));
		this.input.addEventListener('change', this.#onInputUpdated);
		this.input.addEventListener('keyup', this.delay(this.#onInputUpdated));
		this.output.addEventListener('change', this.#onOutputUpdated);
		document.addEventListener('keydown', this.#onKeyDown);

		this.lang = document.getElementById('lang');
		lang.addEventListener('change', () => this.setLanguage(lang.value));
		this.setLanguage(localStorage.getItem('lang'));
	}

	getEditorOptions(options = {}) {
		return Object.assign({}, this.editorOptions, options);
	}

	delay(callback) {
		return () => {
			clearTimeout(this.timeout);
			this.timeout = setTimeout(callback, 200);
		}
	}

	/**
	 * @param {KeyboardEvent} e
	 */
	#onKeyDown = (e) => {
		if (e.ctrlKey && (e.key === 's')) {
			e.preventDefault();
			this.update();
			return false;
		}
	};

	#onInputUpdated = () => this.update();
	#onOutputUpdated = () => this.outputEditor.setValue(this.output.value);
	#onInputEditorUpdated = () => this.inputEditor.save() || this.update();

	store() {
		localStorage.setItem('value', this.input.value);
	}

	update() {
		this.store();
		this.preview.innerHTML = this.input.value
			.replace(/\n\s+/g, '\n')
			.replace(/<\/(\w+)>\n/g, '</$1> ')
			.replace(/\s+/g, ' ');
		this.apply(this.preview);
		this.output.value = beautify.html(this.preview.innerHTML);
		this.outputEditor.setValue(this.output.value);
	}

	apply(el) {
		let walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
		/**  @var {Node} node  */
		let node;
		while (node = walker.nextNode()) {
			node.textContent = this.nbsp.replace(node.textContent, this.findLang(node));
		}
	}

	/**
	 * @param {Node} node
	 * @returns {string | *}
	 */
	findLang(node) {
		return (node.parentElement?.lang) || (node.parentElement.closest('[lang]')?.lang);
	}

}

const EMPTY = '(^|$|;| |&nbsp;|\\(|\\n|>)';
const TASKS = {
	short_words: [`@${EMPTY}(.{1,3}) @ig`, `$1$2\u00A0`],
	non_breaking_hyphen: [`@(\\w{1})-(\\w+)@ig`, `$1\u2011$2`],
	numbers: [`@(\\d) (\\d)@ig`, `$1\u00A0$2`],
	spaces_in_scales: [`@(\\d) : (\\d)@ig`, `$1\u00A0:\u00A0$2`],
	ordered_number: [`@(\\d\\.) ([0-9a-záčďéěíňóřšťúýž])@ig`, `$1\u00A0$2`],
	abbreviations: [`@${EMPTY}(%keys%) @ig`, '$1$2\u00A0'],
	prepositions: [`@${EMPTY}(%keys%) @ig`, `$1$2\u00A0`],
	conjunctions: [`@${EMPTY}(%keys%) @ig`, `$1$2\u00A0`],
	article: [`@${EMPTY}(%keys%) @ig`, `$1$2\u00A0`],
	units: [`@(\\d) (%keys%)(^|[;\\.!:]| | |\\?|\\n|\\)|<|\\010|\\013|$)@ig`, `$1\u00A0$2$3`]
};

const KEYS = {
	cs: {
		prepositions: "do|kromě|od|u|z|ze|za|proti|naproti|kvůli|vůči|nad|pod|před|za|o|pro|mezi|přes|mimo|při|na|po|v|ve|pod|před|s|za|mezi|se|si|k|je",
		conjunctions: "a|i|o|u",
		abbreviations: "vč.|cca.|č.|čís.|čj.|čp.|fa|fě|fy|kupř.|mj.|např.|p.|pí|popř.|př.|přib.|přibl.|sl.|str.|sv.|tj.|tzn.|tzv.|zvl.",
		units: "m|m²|l|kg|h|°C|Kč|lidí|dní|%|mil"
	}, en: {
		prepositions: "aboard|about|above|across|after|against|ahead of|along|amid|amidst|among|around|are|as|as far as|as of|aside from|at|athwart|atop|be|barring|because of|before|behind|below|beneath|beside|besides|between|beyond|but|by|by means of|circa|concerning|despite|down|during|except|except for|excluding|far from|following|for|from|is|in|in accordance with|in addition to|in case of|in front of|in lieu of|in place of|in spite of|including|inside|instead of|into|like|minus|near|next to|notwithstanding|of|off|on|on account of|on behalf of|on top of|onto|opposite|out|out of|outside|over|past|plus|prior to|regarding|regardless of|save|since|than|through|throughout|till|to|toward|towards|under|underneath|unlike|until|up|upon|versus|via|with|with regard to|within|without",
		conjunctions: "and|at|even|about|or|to",
		article: "a|an|the",
		units: "m|m²|l|kg|h|°C|Kč|peoples|days|moths|%|miles"
	}
};

class Nbsp {
	tasks = {};

	constructor() {
		this.init();
	}

	init() {
		for (let lang of Object.keys(KEYS)) {
			this.tasks[lang] = Object.entries(TASKS).map(([name, [regex, replacement]]) => {
				let keys = KEYS[lang][name];
				if (!keys && regex.indexOf('%keys%') !== -1) {
					return false;
				}
				if (keys) {
					regex = regex.replace('%keys%', keys);
				}
				let matches = regex.match(/^@(?<reg>.*)@(?<flags>\w+)?$/);
				if (matches) {
					let {reg, flags} = matches.groups;
					return [new RegExp(reg, flags), replacement]
				} else {
					console.log(matches, regex);
				}
			}).filter(Boolean);
		}
	}

	/**
	 * @param {string} text
	 * @param {string} lang
	 */
	replace(text, lang) {
		/**  @var {RegExp} regex  */
		for (let [regex, replacement] of this.tasks[lang]) {
			console.log(text, regex, replacement);
			text = text.replace(regex, replacement);
		}
		return text;
	}
}

new NbspTool(
	document.getElementById('input'),
	document.getElementById('output'),
	document.getElementById('preview')
);
