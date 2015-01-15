@extends(view)
@block(title)
Edit "{{title}}"
@endblock

@block(content)
<form method="post">
    <div>
        <h1>Edit</h1>
        <textarea name=code>{{code}}</textarea>
    </div>
    <div>
        <input type="submit">
    </div>
</form>
<form action="?p={{page}}&a=upload" method=POST enctype=multipart/form-data>
    <h1>Upload File</h1>
    <input type=file name=file>
    <input type=submit>
</form>
<div>
    <h1>Preview</h1>
    <div id=preview>{{content}}</div>
</div>
@endblock

@block(script)
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
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
                    $.post('?p={{page}}&a=preview', data).then(
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
</script>
@endblock
