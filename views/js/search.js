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
    })
});
