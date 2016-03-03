$(function () {
    'use strict';
    $('[name=code]').bind({
    keydown: function (e) {
        if (e.keyCode == 9) {
            e.preventDefault();
            if (e.target) {
                e.target.setRangeText('    ');
                e.target.selectionEnd += 4;
                e.target.selectionStart = e.target.selectionEnd;
            }
        }
    },
    focus: function () {
        var input = $(this);
        if (!input.data('timer')) {
            input.data('old', input.val());
            input.data('timer', setInterval(function () {
                if (input.val() != input.data('old') && !input.data('loading')) {
                    input.data('loading', input.val());
                    var data = {code: input.val()};
                    $.post('?p=' + page + '&a=preview', data).then(
                        function (html) {
                            input.data('old', data.code);
                            $('#preview').html(html);
                        }
                    ).done(function () {
                        input.data('loading', false);
                    });
                }
            }, 1000));
        }
    },
        blur: function () {
            var input = $(this);
            if (input.data('timer')) {
                clearInterval(input.data('timer'));
                input.data('timer', null);
            }
        }
    });
});
