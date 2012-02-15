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
            ,expr: /\[T id="([^"]*)" domain="([^"]*)" locale="([^"]*)"\](.*?)\[\/T\]/
            ,showUntranslated: true
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

        var list = Ext.fly(document.body).select('*'); // ouch
        list.each(function(node) {

            self.handleNode(node.dom);
        });
    }

    ,handleNode: function(node) {

        var self = this;

        // node content
        if(node.firstChild !== null && node.firstChild.nodeType === 3) { // Node.TEXT_NODE === 3
            var content = node.firstChild.nodeValue;
            var result = this.checkTranslatableText(content, node);
            if(false !== result) {
                node.firstChild.nodeValue = result;
            }
        }

        // node attributes
        var matched = false;
        Ext.each(node.attributes, function(attribute) {
            var result = self.checkTranslatableText(attribute.value, node);
            if(false !== result) {
                attribute.value = result;
            }
        });
    }

    ,checkTranslatableText: function(text, node) {

        var self = this;
        var matches = this.config.expr.exec(text);
        if(matches === null) {
            return false;
        }
        result = matches[4];

        var newText = matches.input.replace(matches[0], '');
        while (newText.trim()) { // multiple trans tags in same string
            newMatches = this.config.expr.exec(newText);
            newText = '';
            if(newMatches !== null) {
                this.addMatch(node, newMatches);
                newText = newMatches.input.replace(newMatches[0], '');
                result += newMatches[4];
            }
        }

        this.addMatch(node, matches);

        if (this.config.showUntranslated && matches[1] === matches[4]) { // no change between key and value
            Ext.fly(node).addClass('untranslated');
        }

        return result;
    }

    ,addMatch: function(node, matches)
    {
        this.matchedNodes.push({
             node:   node
            ,id:     matches[1]
            ,domain: matches[2]
            ,locale: matches[3]
            ,value:  matches[4]
        });

    }

    ,matches: function(target) {
        var matches = [];
        Ext.each(this.matchedNodes, function(match) {
            if(match.node === target) {
                matches.push(match);
            }
        });

        return matches;
    }

    ,bindEvents: function() {

        var self = this;
        Ext.get(document.body).on('mouseover', this.handleEvent, this, {
            buffer: 1000
        });

        Ext.get(document.body).on('dblclick', this.handleEvent, this);

        this.form.on('submit', function(event) {
            self = this;
            event.stopEvent();
            Ext.Ajax.request({
                form: 'knplabs-translator-form'
                ,method: 'POST'
                ,success: function() {
                    //self.hide();
                }
                ,failure: function(xhr) {
                    json = Ext.util.JSON.decode(xhr.responseText);

                    var el = self.form.select('.error').item(0);
                    el.dom.firstChild.nodeValue = json.error;
                    el.show(true);
                }
            });
        }, this);

        this.form.on('click', function(event) {
            var link = Ext.fly(event.target);
            if (link.hasClass('source-translate')) {
                var id = link.up('.inputs-container').select('.id').item(0).dom.value;
                var sourceLocale = link.up('.inputs-container').select('.source-locale').item(0).dom.value;
                var locale = link.up('.inputs-container').select('.locale').item(0).dom.value;

                var value = link.up('.inputs-container').select('.value');

                self.search(id, sourceLocale, locale, function(response) {
                    value.item(0).dom.value = response;
                });
            }
        });
    }

    ,search: function(id, sourceLocale, locale, callback) {
        var self = this;
        sourceLocale = sourceLocale || 'en';
        locale = locale.split('_')[0];
        Ext.Ajax.request({
            url: 'http://mymemory.translated.net/api/get?q='+ id +'&langpair='+ sourceLocale +'|'+ locale,
            useDefaultXhrHeader: false, // shitty ext-core is shitty, this doesn't work
            success: function(xhr) {
                var json = Ext.util.JSON.decode(xhr.responseText);
                var response = json.responseData.translatedText;
                callback.apply(self, [response]);
            },
        });
    }

    ,handleEvent: function(event, target) {

        if(this.form.contains(target)) {
            return;
        }

        matches = this.matches(target);
        if(matches.length) {
            this.select(target, matches);
        }
        else {
            this.hide();
        }
    }

    ,select: function(element, matches) {
        var self = this;
        this.fireEvent('select', this, element, matches);

        this.form.select('.form-input-container').remove();

        Ext.each(matches, function(match, i) {

            inputs = Ext.fly(self.appendSubForm(self.form.select('form').item(0), i));

            inputs.select('.id').item(0).dom.value = match.id;
            inputs.select('.domain').item(0).dom.value = match.domain;
            inputs.select('.locale').item(0).dom.value = match.locale;
            inputs.select('.value').item(0).dom.value = match.value;
        });

        self.appendSubmit(self.form.select('form').item(0));

        var el = self.form.select('.error').hide(true);
        this.form.setX(Ext.fly(element).getX());
        this.form.setY(Ext.fly(element).getY());
        self.form.show(true);
    }

    ,createForm: function() {
        var form = Ext.DomHelper.append(document.body, {
            id:'knplabs-translator-container'
            ,children: [{
                tag: 'form'
                ,id:'knplabs-translator-form'
                ,action: this.config.url
                ,cls: 'translator-form'
            }
            ,{
                 tag: 'div'
                ,cls: 'error'
                ,html: ' '
            }]
        });

        return form;
    }

    ,appendSubForm: function(form, i) {

        var inputs = Ext.DomHelper.append(form.dom, {
            id: 'form-input-container'
            ,cls: 'form-input-container'
            ,children: [{
                cls: 'inputs-container'
                ,children: [
                     { tag: 'label' ,  for: 'knplabs-translator-id'+i     ,  html: 'id' }
                    ,{ tag: 'input' ,  type: 'text'                       ,  name: 'trans['+i+'][id]'            ,  cls: 'id'            ,  id: 'knplabs-translator-id'+i }
                    ,{ tag: 'input' ,  type: 'text'                       ,  name: 'trans['+i+'][source_locale]' ,  cls: 'source-locale' ,  id: 'knplabs-translator-source-locale'+i }
                    ,{ tag: 'a'     ,  cls: 'source-translate'            ,  id: 'knplabs-translator-source-locale'+i, html: 'Find' }
                    ,{ tag: 'label' ,  for: 'knplabs-translator-value'+i  ,  html: 'Value' }
                    ,{ tag: 'input' ,  type: 'text'                       ,  name: 'trans['+i+'][value]'         ,  cls: 'value'         ,  id: 'knplabs-translator-value'+i }
                    ,{ tag: 'label' ,  for: 'knplabs-translator-domain'+i ,  html: 'Domain' }
                    ,{ tag: 'input' ,  type: 'text'                       ,  name: 'trans['+i+'][domain]'        ,  cls: 'domain'        ,  id: 'knplabs-translator-domain'+i }
                    ,{ tag: 'label' ,  for: 'knplabs-translator-locale'+i ,  html: 'Locale' }
                    ,{ tag: 'input' ,  type: 'text'                       ,  name: 'trans['+i+'][locale]'        ,  cls: 'locale'        ,  id: 'knplabs-translator-locale'+i }
                ]
            }]
        });

        return inputs;
    }

    ,appendSubmit: function(form) {

        var inputs = Ext.DomHelper.append(form.dom, {
            cls: 'form-input-container'
            ,children: [
                 { tag: 'input', type: 'submit', value: 'Submit' }
                ,{ tag: 'input', type: 'hidden', name: '_method', value: 'PUT' }
            ]
        });

        return inputs;
    }
});
