/// <reference path="../../dist/tinymce/tinymce.d.ts" />

import Editor = TinyMCE.Editor

class Inline {

    public gatewayUrl: string

    public editables: NodeListOf<Element>

    public changes: {[id: string]: Item} = {}

    public btns: {[id: string]: HTMLButtonElement} = {}

    public editableConfigs: any = {
        'headings': {
            selector: 'h1.inline-editable, h2.inline-editable, h3.inline-editable, h4.inline-editable, h5.inline-editable, h6.inline-editable',
            toolbar: 'italic strikethrough | nonbreaking | undo redo',
        },
        'inlines': {
            selector: 'span.inline-editable, strong.inline-editable, a.inline-editable',
            forced_root_block: '',
            toolbar: 'bold italic strikethrough | nonbreaking | link | undo redo'
        },
        'blocks': {
            selector: 'div.inline-editable',
            toolbar: 'bold italic strikethrough | nonbreaking | fontsizeselect | styleselect bullist numlist link image | undo redo',
            style_formats: [
                {
                    title: 'Nadpisy',
                    items: [
                        {title: 'Nadpis 1', format: 'h1'},
                        {title: 'Nadpis 2', format: 'h2'},
                        {title: 'Nadpis 3', format: 'h3'},
                        {title: 'Nadpis 4', format: 'h4'},
                        {title: 'Nadpis 5', format: 'h5'},
                        {title: 'Nadpis 6', format: 'h6'}]
                },
                {title: 'Horní index', icon: 'superscript', format: 'superscript'},
                {title: 'Dolní index', icon: 'subscript', format: 'subscript'},
                {title: 'Zarovnání', icon: 'alignleft', items: [
                    {title: 'Doleva', icon: 'alignleft', format: 'alignleft'},
                    {title: 'Na střed', icon: 'aligncenter', format: 'aligncenter'},
                    {title: 'Doprava', icon: 'alignright', format: 'alignright'},
                    {title: 'Do bloku', icon: 'alignjustify', format: 'alignjustify'}]
                }
            ]
        }
    }

    public constructor() {
        let source = document.getElementById('inline-editable-source')

        if (!source) {
            return
        }

        let cssLink = document.createElement('link')
        cssLink.href = source.getAttribute('data-source-css')
        cssLink.setAttribute('rel', 'stylesheet')
        cssLink.setAttribute('type', 'text/css')
        document.head.appendChild(cssLink)

        this.gatewayUrl = source.getAttribute('data-source-gateway-url')

        if (typeof tinymce === 'undefined') {
            let tinymceLink = document.createElement('script')
            tinymceLink.src = source.getAttribute('data-source-tinymce-js')
            let self = this
            tinymceLink.onload = function () {
                self.initUI()
            }
            document.head.insertBefore(tinymceLink, document.head.firstChild)
        } else {
            this.initUI()
        }
    }

    public initUI() {
        let self = this

        let btnEnable = this.btns['enable'] = document.createElement('button')
        btnEnable.innerHTML = '&#x270e;'
        btnEnable.className = 'inline-editable-btn inline-enable'
        btnEnable.addEventListener('click', () => self.enable())
        document.body.appendChild(btnEnable)

        let btnDisable = this.btns['disable'] = document.createElement('button');
        btnDisable.innerHTML = '&#x270e'
        btnDisable.className = 'inline-editable-btn inline-disable inline-hidden'
        btnDisable.addEventListener('click', () => self.disable())
        document.body.appendChild(btnDisable)

        let btnSave = this.btns['save'] = document.createElement('button')
        btnSave.innerHTML = '&#x2714'
        btnSave.className = 'inline-editable-btn inline-save inline-hidden'
        btnSave.addEventListener('click', () => self.saveAll())
        document.body.appendChild(btnSave)

        let btnRevert = this.btns['revert'] = document.createElement('button')
        btnRevert.innerHTML = '&#x2718'
        btnRevert.className = 'inline-editable-btn inline-revert inline-hidden'
        btnRevert.addEventListener('click', () => self.revertAll())
        document.body.appendChild(btnRevert)

        this.initTinymce()
    }

    public initTinymce() {

        let self = this

        this.editables = document.querySelectorAll('*[data-inline-name]')

        this.editablesForeach((el) => {
            el.classList.add('inline-editable')
            el.classList.add('inline-disabled')
        })

        this.backup()

        for (let optionsName in this.editableConfigs) {

            let settings = (<any>Object).assign({
                entity_encoding: 'raw',
                inline: true,
                menubar: false,
                language: 'cs',
                plugins: 'paste link image lists nonbreaking',
                paste_as_text: true,
                theme: 'modern',
                setup: function (editor: Editor) {
                    editor.on('init', () => self.disable());
                    editor.on('keyup change redo undo', function () {
                        self.updateContent(editor)
                    })
                }
            }, this.editableConfigs[optionsName])

            tinymce.init(settings)
        }
    }

    public updateContent(editor: Editor) {

        let el = editor.bodyElement
        let key = el.id

        if (this.changes.hasOwnProperty(key)) {
            this.changes[key].content = editor.getContent()
        } else {
            this.changes[key] = new Item(
                el.dataset['inlineNamespace'],
                el.dataset['inlineLocale'],
                el.dataset['inlineName'],
                editor.getContent()
            )
        }

        this.btns['save'].classList.remove('inline-hidden')
        this.btns['revert'].classList.remove('inline-hidden')
    }

    public saveAll() {

        let self = this
        let xhr = new XMLHttpRequest()

        xhr.open('POST', this.gatewayUrl)
        xhr.setRequestHeader('Content-Type', 'application/json')
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('Success')
            } else {
                alert('Error')
            }

            self.btns['save'].classList.add('inline-hidden')
            self.btns['revert'].classList.add('inline-hidden')
            self.changes = {}
            self.backup()
        }

        xhr.send(JSON.stringify(this.changes))
    }

    public revertAll() {
        this.editablesForeach((el) => el.innerHTML = el.getAttribute('data-inline-backup'))
        this.btns['save'].classList.add('inline-hidden')
        this.btns['revert'].classList.add('inline-hidden')
        this.changes = {}
    }

    public enable() {
        this.btns['enable'].classList.add('inline-hidden')
        this.btns['disable'].classList.remove('inline-hidden')

        this.editablesForeach((el) => {
            el.classList.remove('inline-disabled')
            el.setAttribute('contenteditable', 'true')
        })
    }

    public disable() {
        this.btns['disable'].classList.add('inline-hidden')
        this.btns['enable'].classList.remove('inline-hidden')

        this.editablesForeach((el) => {
            el.classList.add('inline-disabled')
            el.setAttribute('contenteditable', 'false')
        })
    }

    public backup() {
        this.editablesForeach((el) => el.setAttribute('data-inline-backup', el.innerHTML))
    }

    private editablesForeach(callback: (el: Element) => void) {
        for (let i = 0; i < this.editables.length; i++) {
            callback(this.editables[i])
        }
    }
}

class Item {
    public constructor(public namespace: string, public locale: string, public name: string, public content: string) {
    }
}

document.addEventListener('DOMContentLoaded', function () {
    let inline = new Inline()
});

