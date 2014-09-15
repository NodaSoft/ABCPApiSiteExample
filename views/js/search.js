$(document).ready(function () {

    /**
     * Переходим на второй этап поиска, кликнув на строке из первого этапа
     */
    $('tr.firstStepRow').on('click', function (e) {
        var link = $(this).data('link');
        if (link) {
            window.location = link;
        }
    });

    /**
     * Отправляем поисковой запрос (если пустое поле ввода - обводим его красной рамкой и не отправляем запрос)
     */
    $('#submitSearchButton').click(function () {
        if ($("#number").val().length > 0) {
            $(this).button('loading');
            $("form[name = 'searchForm']").submit();
        } else {
            $("input[name='number']").focus().parent().addClass("has-error");
        }
    });

    /**
     * При вводе в строку поиска убираем красную рамку и по нажатию Enter меняем текст кнопки поиска
     */
    $("#number").on("click keypress",function (e) {
        var code = (e.keyCode ? e.keyCode : e.charCode);
        if(code == 13) {
            $('#submitSearchButton').button('loading');
        }
        $(this).parent().removeClass("has-error");
    });

    var searchTips = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.number);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit: 10,
        minLength: 3,
        remote: {
            url: '/ajax.php?action=getSearchTips&params[number]=%QUERY',
            filter: function (data) {
                return data.result;
            }
        }
    });

    searchTips.initialize();

    $('#number').typeahead(null, {
        displayKey: 'number',
        minLength: 3,
        highlight: true,
        source: searchTips.ttAdapter(),
        templates: {
            suggestion: Handlebars.compile('<a href="/?number={{number}}&brand={{brand}}"><p><strong>{{brand}}</strong> – {{number}}</p></a>')
        }
    }).on('typeahead:selected', function(event, selection) {
        var item = $(this);
        var brandInput = $('<input/>', {
            type: 'hidden',
            name: 'brand',
            value: selection.brand
        });
        item.before(brandInput);
        item.closest("form").submit();
    });
});
