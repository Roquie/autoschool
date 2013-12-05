/**
 * Developer: V. Melnikov
 * Date: 06.10.13
 * Time: 0:15
 */
$.fn.editable.defaults.mode = 'inline';
$.fn.editable.defaults.ajaxOptions = {
    dataType: 'json'
};
$.fn.editableform.defaults.url = $('.data').data('url');
$.fn.editable.defaults.emptytext = 'Пусто';
$.fn.editableform.defaults.success = function(response, newValue) {
    if (response.status === 'empty') return response.msg;
    if (response.status === 'error') {
        noty({
            type : response.status,
            message : response.msg
        });
        return false;
    }
    if (response.status === 'success') {
        switch (response.msg) {
            case 'imya' :
                $('#userName').text(newValue);
                break;
            case 'famil' :
                $('#userFamil').text(newValue);
                break;
        }
    }
};
$.fn.editableform.defaults.error = function(response, newValue) {
    noty({
        type : 'error',
        message : 'На сервере допущена ошибка, свяжитесь с администратором и сообщите нам о ней. Спасибо'
    });
};
$(function() {

    moment.lang('ru');
    $('.data_rojdeniya').editable({
        format: 'DD.MM.YYYY',
        viewformat: 'DD.MM.YYYY',
        combodate: {
            firstItem: 'name',
            minYear: 1960,
            maxYear: 2013
        }
    });

    getData($('#education'));
    getData($('#grajdanstvo'));

    $('.pasport_data_vyda4i').editable({
        format: 'DD.MM.YYYY',
        viewformat: 'DD.MM.YYYY',
        combodate: {
            firstItem: 'name',
            minYear: 1960,
            maxYear: 2013
        }
    });

    $('.table').find('a').each(function() {
        if ($(this).attr('id') == null) {
            $(this).editable('disable');
        }
    }).on('click', function(e) {
        e.preventDefault();
    });

    $('body').on('click', '#enable', function() {
        $(this).hide();
        $('#disable').show();
        $('.editable').editable('enable');
    }).on('click', '#disable', function() {
        $('.editable').editable('disable');
        $(this).hide();
        $('#enable').show();
    });

    function getData(obj) {
        var project = [];
        $.post(
            obj.data('action'),
            function(data)
            {
                project = data.results;
                obj.editable({
                    source: project,
                    select2 : {
                        width: 200
                    }
                });
                obj.editable('disable');
            },
            'json'
        );
    }

});
