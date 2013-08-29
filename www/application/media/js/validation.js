/**
 * ajaxForm Plugin
 * Copyright 2013, Viktor Melnikov
 * Version 2.2 - Updated: August, 29, 2013
 */
;(function ( $, window, document, undefined ) {

    "use strict";

    var pluginName = 'ajaxForm';

    /**
     * Конструктор
     * @param element
     * @param options
     * @constructor
     */
    function Plugin(element, options) {
        this.init(element, options);
    }

    Plugin.prototype = {
        constructor : Plugin,
        init : function(element, options) {
            this.$element = $(element);
            this.options = $.extend( {}, $.fn[pluginName].defaults, options);
            // вешает на форму обработчик отправки формы
            this.$element.on('submit', $.proxy(this.validation, this));
            if (this.options.trigger == 'blur') {
                this.blur();
            }
        },
        /**
         * Создание сообщения с ошибкой сбоку формы.
         * @param input
         */
        buildBalloon : function(input) {
            var balloon,
                inputTopPosition,
                inputLeftPosition,
                inputWidth,
                arrowClass;
            arrowClass = (input.data('placement')) ? input.data('placement') : this.options.placement;
            balloon = $('<div>', {
                class : 'formError arrow-'+arrowClass
            });

            switch (arrowClass) {
                case 'top' :
                    inputTopPosition = - input.height() - 22;
                    inputLeftPosition = -18;
                    break;
                case 'right' :
                    inputTopPosition = this.options.offsetTopBalloon;
                    inputLeftPosition = input.width();
                    break;
                case 'bottom' :
                    inputTopPosition = input.height() + 16;
                    inputLeftPosition = -18;
                    break;
            }

            balloon.html(input.data('error')).css({
                top : inputTopPosition,
                left : inputLeftPosition
            }).addClass('in').insertAfter(input);
        },
        /**
         * Функция, которая вызывается чтобы повесить на элементы формы обработчик события потери фокуса
         */
        blur : function() {
            var form = this.$element,
                data = this.$element.serializeArray(),
                $this = this,
                element,
                blur = this.eventBlur;
            if (this.options.fields.length > 0) {
                data = this.options.fields.concat(data);
            }
            $.each(data, function(i, v) {
                if ( typeof v === 'object') {
                    element = form.find('input[name="'+ v.name+'"], textarea[name="'+ v.name+'"]');
                } else {
                    element = $('#'+v);
                }
                element.on('blur', $.proxy(blur, $this));
            });
        },
        /**
         * Событие возникает при потере фокуса элементом
         * @param e
         */
        eventBlur : function(e) {
            var el = $(e.currentTarget),
                empty = false,
                balloon = el.next();
            if (el.data('req')) {
                switch (el.get(0).nodeName.toLowerCase()) {
                    case 'div' :
                        if (this.delSpace(el.text()) === '') {
                            empty = true;
                        }
                        break;
                    default :
                        if ( this.delSpace(el.val()) === '' || ( el.data('value') && this.delSpace(el.val()) === el.data('value') ) ) {
                            empty = true;
                        }
                        break;
                }
                if (empty) {
                    el.addClass('error');
                    if (balloon.length === 0)
                        this.buildBalloon(el);
                } else {
                    el.removeClass('error').next().remove();
                }
            }
        },
        /**
         * Валидация формы, если всё заполнено - отправка данных формы ajax-запросом
         * @param e
         */
        validation : function(e) {
            e.preventDefault();
            var element,
                is_success = true,
                data = this.$element.serializeArray(),
                field,
                empty = false;

            this.$element.find('input, textarea').removeClass('error').next('.formError').remove();

            if (this.options.fields.length > 0) {
                $.each(this.options.fields, function(i, v) {
                    field = $('#'+v);
                    field.removeClass('error').next().remove();
                });
                data = this.options.fields.concat(data);
            }
            for (var i = 0; i < data.length; i++) {
                if ( typeof data[i] === 'object') {
                    element = this.$element.find('input[name="'+ data[i].name+'"], textarea[name="'+ data[i].name+'"]');
                } else {
                    element = $('#'+data[i]);
                }
                if (element.data('req')) {
                    switch (element.get(0).nodeName.toLowerCase()) {
                        case 'div' :
                            if (this.delSpace(element.text()) === '') {
                                empty = true;
                            }
                            break;
                        case 'input' :
                            if ( this.delSpace(element.val()) === '' || ( element.data('value') && this.delSpace(element.val()) === element.data('value') ) ) {
                                empty = true;
                            }
                            break;
                    }
                    if (empty) {
                        is_success = false;
                        element.addClass('error');
                        this.buildBalloon(element);
                        empty = false;
                    }
                }
            }
            if (is_success) {
                this.ajax();
            }
        },
        /**
         * ajax-отправка данных формы
         */
        ajax : function() {
            var action = this.$element.attr('action'),
                data = '',
                field;
            if (this.options.fields.length > 0) {
                $.each(this.options.fields, function(i, v) {
                    field = $('#'+v);
                    data = data + field.data('name') + '=' + field.html() + '&';
                });
            }
            data = data + this.$element.serialize();
            $.post(
                action,
                data,
                this.options.callback,
                'json'
            );
        },
        /**
         * Функция для удаления пробелов в строке
         * @param string
         * @returns {XML|string|void}
         */
        delSpace : function(string) {
            return string.replace(/\s/g, "");
        }
    };

    $.fn[pluginName] = function(option) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('plugin_' + pluginName),
                options = typeof option == 'object' && option;
            if (!data) $this.data('plugin_' + pluginName, (data = new Plugin(this, options)));
            if (typeof option == 'string') data[option]();

        });
    };

    $.fn[pluginName].defaults = {
        callback : function() {},
        offsetTopBalloon : 0,
        placement : 'right', // top|right|bottom
        trigger : 'submit', // submit|blur
        fields : []
    };

})( jQuery, window, document );