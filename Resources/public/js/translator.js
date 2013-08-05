var Knp = {
    Translator: function(options) {
        this.options = $.extend({}, this.defaults, options);
        this.init();
    }
};

Knp.Translator.prototype = {
    constructor: Knp.Translator,

    defaults: {
        config: {},
        form: null,
        timer: null
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
        this.form = $('<div/>',{
                'id': 'knplabs-translator-container'
            }).append(
                $('<form/>', {
                    'id': 'knplabs-translator-form',
                    'action': this.options.url,
                    'method': 'POST',
                    'class': 'translator-form'
                }),
                $('<div/>', {
                    'class': 'error',
                    'html' : '',
                    'style': 'display:none'
                })
            );

        $('body').append(this.form);
    },
    appendSubForm: function(element) {

        var id = element.attr("data-id");

        this.form.find('form').append(
            $('<div/>', {'class': 'form-input-container', 'data-id': id})
            .append(
                $('<label/>', { 'for': 'knplabs-translator-id-'+id, 'html': 'id'  }),
                $('<input/>',{
                    'type': 'text',
                    'name': 'trans['+id+'][id]',
                    'class': 'id',
                    'id': 'knplabs-translator-id-'+id,
                    'value': element.attr('data-id')
                }),
                $('<label/>', { 'for': 'knplabs-translator-value-'+id, 'html': 'Value'  }),
                $('<input/>', {
                    'type': 'text',
                    'name': 'trans['+id+'][value]',
                    'class': 'value',
                    'id': 'knplabs-translator-value'+id,
                    'value': element.attr('data-value')
                }),
                $('<label/>', { 'for': 'knplabs-translator-domain-'+id, 'html': 'Domain'  }),
                $('<input/>', {
                    'type': 'text',
                    'name': 'trans['+id+'][domain]',
                    'class': 'domain',
                    'id': 'knplabs-translator-domain'+id,
                    'value': element.attr('data-domain')
                }),
                $('<label/>', { 'for': 'knplabs-translator-locale-'+id, 'html': 'Locale'  }),
                $('<input/>', {
                    'type': 'text',
                    'name': 'trans['+id+'][locale]',
                    'class': 'locale',
                    'id': 'knplabs-translator-locale'+id,
                    'value': element.attr('data-locale')
                }),
                $('<button/>', {
                    'type': 'submit',
                    'text': 'Submit'
                })
            )
        );

        var x = element.offset().left;
        if (element.offset().left + this.form.width() > $('body').width()) {
            x = $('body').width() - this.form.width();
        }

        var y = element.offset().top;
        if (element.offset().top + this.form.height() > $('body').height()) {
            y = $('body').height() - this.form.height();
        }

        this.form.css({
            left: x,
            top: y
        });
    },
    bindEvents: function() {
        var self = this;
        $('ins.knp-translator').bind('mouseover dblclick', function(e){
            self.timer = setTimeout(function() {
                self.handleEvent(e)
            }, 800)
        });
        $('ins.knp-translator').bind('mouseleave', function(){
            clearTimeout(self.timer);
        });
        $('ins.knp-translator').bind('dblclick', function(e){self.handleEvent(e)});
        $('#knplabs-translator-container').bind('mouseleave', function(e){
            $(this).removeClass('open');
        });

        this.form.find('form').submit(function(e){
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: "PUT",
                url: form.attr('action'),
                data: form.serialize(),
                success: function(data) {
                    var trans = $('.knp-translator[data-id="'+
                        $(form
                            .find('.form-input-container'))
                            .attr('data-id')
                        +'"]');

                    trans.attr('data-value', data.trans);
                    trans.text(data.trans);
                },
                error:function (xhr){
                    var response = $.parseJSON(xhr.responseText);
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
        this.appendSubForm($(event.currentTarget));
        this.form.addClass('open');
        this.form.focus();
    }
}