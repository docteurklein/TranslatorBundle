var Knp = {
    Translator: function(options) {
        this.options = jQuery.extend({}, this.defaults, options);
        this.init();
    }
};

Knp.Translator.prototype = {
    constructor: Knp.Translator,

    defaults: {
        config: {},
        form: null,
        timerIn: null
    },
    options: {},

    /**
     * Initialization
     */
    init: function() {
        this.createForm();
        this.bindEvents();
    },
    createForm: function() {
        this.form = jQuery('<div/>',{
                'id': 'knplabs-translator-container'
            }).append(
                jQuery('<form/>', {
                    'id': 'knplabs-translator-form',
                    'action': this.options.url,
                    'method': 'POST',
                    'class': 'translator-form'
                }),
                jQuery('<div/>', {
                    'class': 'error',
                    'html' : '',
                    'style': 'display:none'
                })
            );

        jQuery('body').append(this.form);
    },
    appendSubForm: function(element) {

        var id = element.attr("data-id");

        this.form.find('form').append(
            jQuery('<div/>', {'class': 'form-input-container', 'data-id': id})
            .append(
                jQuery('<label/>', { 'for': 'knplabs-translator-id-'+id, 'html': 'id'  }),
                jQuery('<input/>',{
                    'type': 'text',
                    'name': 'trans['+id+'][id]',
                    'class': 'id',
                    'id': 'knplabs-translator-id-'+id,
                    'value': element.attr('data-id')
                }),
                jQuery('<label/>', { 'for': 'knplabs-translator-value-'+id, 'html': 'Value'  }),
                jQuery('<input/>', {
                    'type': 'text',
                    'name': 'trans['+id+'][value]',
                    'class': 'value',
                    'id': 'knplabs-translator-value-'+id,
                    'value': element.attr('data-value')
                }),
                jQuery('<label/>', { 'for': 'knplabs-translator-domain-'+id, 'html': 'Domain'  }),
                jQuery('<input/>', {
                    'type': 'text',
                    'name': 'trans['+id+'][domain]',
                    'class': 'domain',
                    'id': 'knplabs-translator-domain-'+id,
                    'value': element.attr('data-domain')
                }),
                jQuery('<label/>', { 'for': 'knplabs-translator-locale-'+id, 'html': 'Locale'  }),
                jQuery('<input/>', {
                    'type': 'text',
                    'name': 'trans['+id+'][locale]',
                    'class': 'locale',
                    'id': 'knplabs-translator-locale-'+id,
                    'value': element.attr('data-locale')
                }),
                jQuery('<button/>', {
                    'type': 'submit',
                    'text': 'Submit'
                })
            )
        );

        var x = element.offset().left;
        if (element.offset().left + this.form.width() > jQuery('body').width()) {
            x = jQuery('body').width() - this.form.width();
        }

        var y = element.offset().top;
        if (element.offset().top + this.form.height() > jQuery('body').height()) {
            y = jQuery('body').height() - this.form.height();
        }

        this.form.css({
            left: x,
            top: y
        });
    },
    bindEvents: function() {
        var self = this;
        jQuery('ins.knp-translator').bind('mouseover dblclick', function(e){
            self.timerIn = setTimeout(function() {
                self.handleEvent(e)
            }, 800)
        });
        jQuery('ins.knp-translator').bind('mouseleave', function(){
            clearTimeout(self.timerIn);

        });
        jQuery('ins.knp-translator').bind('dblclick', function(e){self.handleEvent(e)});
        jQuery('#knplabs-translator-container').bind('mouseleave', function(e){
            jQuery(this).removeClass('open');
        });

        this.form.find('form').submit(function(e){
            e.preventDefault();
            var form = jQuery(this);
            jQuery.ajax({
                type: "PUT",
                url: form.attr('action'),
                data: form.serialize(),
                success: function(data) {
                    var trans = jQuery('.knp-translator[data-id="'+
                        jQuery(form
                            .find('.form-input-container'))
                            .attr('data-id')
                        +'"]');

                    trans.attr('data-value', data.trans);
                    trans.text(data.trans);
                },
                error:function (xhr){
                    var response = jQuery.parseJSON(xhr.responseText);
                    var error = self.form.find('.error');
                    error.text(response.error)
                    error.show();
                }
            });
        });
    },
    handleEvent: function(event) {
        event.preventDefault();
        this.form.find('.form-input-container').remove();
        this.form.find('.error').hide();
        this.appendSubForm(jQuery(event.currentTarget));
        this.form.addClass('open');
        this.form.focus();
    }
}