
Ext.namespace('Knplabs');

Knplabs.Translator = Ext.extend(Ext.util.Observable, {

    config: {}
    ,form: null

    ,constructor: function(config) {
        this.addEvents('select');
        this.addEvents('translate');

        Ext.apply(this.config, config, {
            url: ''
        });

        this.form = Ext.get(this.createForm());
        this.form.hide();

        this.bindEvents();
    }

    ,bindEvents: function() {
        Ext.get(document.body).on('dblclick', function(event, target) {

            if(null === target.firstChild) {
                this.form.hide();
                return;
            }
            var expr = /\[T id="(.*)" domain="(\w*)" locale="(\w*)"\](.*)\[\/T\]/;
            var content = target.firstChild.nodeValue;
            if(content.match(expr)) {
                matches = expr.exec(content);

                matches = {
                     id:     matches[1]
                    ,domain: matches[2]
                    ,locale: matches[3]
                    ,value:  matches[4]
                }

                this.select(target, matches);
            }
        }, this);

        this.form.on('submit', function(event) {

            self = this;
            event.stopEvent();
            Ext.Ajax.request({
                form: 'knplabs-translator-form'
                ,method: 'POST'
                ,success: function() {
                    self.form.hide();
                }
            });
        }, this);
    }

    ,select: function(element, matches) {
        this.fireEvent('select', this, element, matches);

        Ext.fly('knplabs-translator-id').dom.value = matches.id;
        Ext.fly('knplabs-translator-domain').dom.value = matches.domain;
        Ext.fly('knplabs-translator-locale').dom.value = matches.locale;
        Ext.fly('knplabs-translator-value').dom.value = matches.value;

        this.form.show();
    }

    ,createForm: function() {
        var form = Ext.DomHelper.append(document.body, {
            id:'knplabs-translator-container'
            ,children: [{
                tag: 'form'
                ,id:'knplabs-translator-form'
                ,action: this.config.url
                ,cls: 'translator-form'
                ,children: [
                     { tag: 'label', for: 'knplabs-translator-id', html: 'id' }
                    ,{ tag: 'input', type: 'text', name: 'id',     cls: 'id', id: 'knplabs-translator-id' }
                    ,{ tag: 'label', for: 'knplabs-translator-value', html: 'Value' }
                    ,{ tag: 'input', type: 'text', name: 'value',  cls: 'value', id: 'knplabs-translator-value' }
                    ,{ tag: 'label', for: 'knplabs-translator-domain', html: 'Domain' }
                    ,{ tag: 'input', type: 'text', name: 'domain', cls: 'domain', id: 'knplabs-translator-domain' }
                    ,{ tag: 'label', for: 'knplabs-translator-locale', html: 'Locale' }
                    ,{ tag: 'input', type: 'text', name: 'locale', cls: 'locale', id: 'knplabs-translator-locale' }
                    ,{ tag: 'input', type: 'submit', value: 'Submit' }
                    ,{ tag: 'input', type: 'hidden', name: '_method', value: 'PUT' }
                ]
            }]
        });

        return form;
    }
});
