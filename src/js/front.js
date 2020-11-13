import prettier from 'prettier/standalone'
import html from 'prettier/parser-html'
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

function parseConfig(tasks) {
    return Object.values(tasks).map(([regex, replacement]) => {
        return [new RegExp(regex,   'ig'), replacement]
    });
}


class NbspTool {

    tasks = {};

    /**  @var {CodeMirror} inputEditor */
    inputEditor;
    /**  @var {CodeMirror} inputEditor */
    outputEditor;
    /**  @type {{mode: string, theme: string, lineWrapping: boolean}} */
    editorOptions = {
        showInvisibles: true,
        mode: 'xml',
        lineWrapping: true,
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

    inlineNodes = ['a', 'abbr', 'acronym', 'b', 'bdo', 'big', 'br', 'button', 'cite', 'code', 'dfn', 'em', 'i', 'img', 'input', 'kbd', 'label', 'map', 'object', 'output', 'q', 'samp', 'script', 'select', 'small', 'span', 'strong', 'sub', 'sup', 'textarea', 'time', 'tt', 'var'];

    defaultLanguage = 'cs';

    constructor(input, output, preview) {
        this.input = input;
        this.output = output;
        this.preview = preview;
        this.nbsp = new Nbsp(this.tasks);
        this.init();
    }

    setLanguage(lang) {
        if (lang && this.lang !== lang) {
            this.lang.value = lang;
            this.preview.lang = lang;
            localStorage.setItem('lang', lang);
            if (!this.tasks[lang]) {
                import(`./tasks/${lang}.js`).then(({default: config}) => {
                    this.tasks[lang] = parseConfig(config);
                    this.update();
                });
            } else {
                this.update();
            }
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
        this.lang.addEventListener('change', () => this.setLanguage(this.lang.value));
        this.setLanguage(localStorage.getItem('lang') || this.defaultLanguage);
        //this.update();
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
            //.replace(/\n{3}/g, '\n\n')
            .replace(/(\n+)\s+/g, '$1')
            .replace(/(&nbsp;| ) /g, ' ')
            //.replace(/\s+/g, ' ')
            .replace(/<\/(\w+)>\n/g, '</$1> ')
            .replace(/ {2,}/g, ' ')
        ;
        this.apply(this.preview);
        this.preview.innerHTML = this.preview.innerHTML
            .replace(/([,])&nbsp;/g, '$1 ');

        this.output.value = beautify.html(this.preview.innerHTML);
        /*this.output.value = prettier.format(this.preview.innerHTML, {
            parser: 'html',
            plugins: [html],
            printWidth: 999999999999999
        });*/
        this.outputEditor.setValue(this.output.value);
    }

    apply(el) {
        let walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null);
        /**  @var {Node} node  */
        let node;
        while (node = walker.nextNode()) {
            let replacement = this.nbsp.replace(node.textContent, this.findLang(node));
            if (!node.nextSibling || !this.inlineNodes.includes(node.nextSibling.nodeName.toLowerCase())) {
                replacement = replacement.replace(/[ &nbsp;\xc2\xa0]$/, '');
            }
            node.textContent = replacement;
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


class Nbsp {
    tasks = {};

    constructor(tasks) {
        this.tasks = tasks;
    }

    /**
     * @param {string} text
     * @param {string} lang
     */
    replace(text, lang) {
        /**  @var {RegExp} regex  */
        for (let [regex, replacement] of this.tasks[lang]) {
            text = text.replace(regex, replacement);
        }
        return text;
    }
}

window.addEventListener('DOMContentLoaded', (event) => {

    /**  @var {HTMLDivElement} el */
    for (let el of document.querySelectorAll('[data-nbsp-tool]')) {
        el.NbspTool = new NbspTool(
            el.querySelector('#input'),
            el.querySelector('#output'),
            el.querySelector('#preview')
        );
    }
    for (let el of document.querySelectorAll('textarea[data-code-mirror]')) {
        if (!el.CodeMirror) {
            el.CodeMirror = CodeMirror.fromTextArea(el, JSON.parse(el.dataset.codeMirror));
            let timeout;
            el.CodeMirror.on('keyup', () => {
                clearTimeout(timeout);
                setTimeout(() => {
                    el.CodeMirror.save()
                }, 500);
            });
            el.CodeMirror.on('change', () => {
                el.CodeMirror.save()
            });
        }
    }
});