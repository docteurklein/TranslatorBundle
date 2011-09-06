Ext.namespace('Knp');

Knp.Translator = Ext.extend(Ext.util.Observable, {

    config: {}
    ,form: null
    ,matchedNodes: []

    ,constructor: function(config) {
        this.addEvents('select');
        this.addEvents('translate');

        Ext.apply(this.config, config, {
            url: ''
            ,expr: /\[T id="(.*)" domain="(\w*)" locale="(\w*)"\](.*)\[\/T\]/
        });

        this.form = Ext.get(this.createForm());
        this.hide();

        this.bindEvents();

        this.initTranslatableNodeList();
    }
    ,reinitialize: function() {
        this.initTranslatableNodeList();
    }
    ,hide: function() {
        this.form.hide(true);

        var el = this.form.select('.error').hide(true);
    }

    ,initTranslatableNodeList: function() {

        var self = this;

        var list = Ext.fly(document.body).select('*');
        list.each(function(node) {

            if(self.isTranslatableNode(node.dom)) {
                var content = node.dom.firstChild.nodeValue;
                var matches = self.config.expr.exec(content);

                self.matchedNodes.push({
                     node:   node.dom
                    ,id:     matches[1]
                    ,domain: matches[2]
                    ,locale: matches[3]
                    ,value:  matches[4]
                });

                node.dom.firstChild.nodeValue = matches[4];
            }
        });
    }

    ,isTranslatableNode: function(node) {

        if(node.firstChild === null
        || node.firstChild.nodeType !== 3) {
            return false;
        }
        var content = node.firstChild.nodeValue;

        return content.match(this.config.expr);
    }

    ,matches: function(target) {
        var matches = null;
        Ext.each(this.matchedNodes, function(match) {
            if(match.node === target) {
                matches = match;
            }
        });

        return matches;
    }

    ,bindEvents: function() {

        Ext.get(document.body).on('mouseover', function(event, target) {

            if(null === target.firstChild || this.form.contains(target)) {
                return;
            }

            if(matches = this.matches(target)) {
                this.select(target, matches);
            }
            else {
                this.hide();
            }
        }, this, {
            buffer: 1000
        });

        this.form.on('submit', function(event) {

            self = this;
            event.stopEvent();
            Ext.Ajax.request({
                form: 'knplabs-translator-form'
                ,method: 'POST'
                ,success: function() {
                    self.hide();
                }
                ,failure: function(xhr) {
                    json = Ext.util.JSON.decode(xhr.responseText);

                    var el = self.form.select('.error').item(0);
                    el.dom.firstChild.nodeValue = json.error;
                    el.show(true);
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

        var el = this.form.select('.error').hide(true);

        this.form.setY(Ext.fly(element).getY());
        this.form.show(true);

        Ext.fly('knplabs-translator-value').dom.focus();
        Ext.fly('knplabs-translator-value').dom.select();
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
            }
            ,{
                 tag: 'div'
                ,cls: 'error'
                ,html: ' '
            }]
        });

        return form;
    }
});
